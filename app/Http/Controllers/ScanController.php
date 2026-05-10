<?php

namespace App\Http\Controllers;

use App\Jobs\DispatchScanToEngine;
use App\Models\CreditLedger;
use App\Models\Scan;
use App\Services\Ebay\EbayCategorySuggestionService;
use App\Support\ScanKeywordGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function __construct(protected ScanKeywordGuard $keywordGuard)
    {
    }

    public function index(Request $request): View
    {
        $team = $request->user()->currentTeam;

        abort_unless($team, 403);

        return view('scans.index', [
            'team' => $team,
            'scans' => $team->scans()->latest()->paginate(10),
        ]);
    }

    public function create(Request $request, EbayCategorySuggestionService $ebayCategories): View
    {
        $team = $request->user()->currentTeam;

        abort_unless($team, 403);

        return view('scans.create', [
            'team' => $team,
            'hasEbayCategorySuggestions' => $ebayCategories->hasCredentials(),
            'marketplaces' => [
                'ebay-uk' => 'eBay UK',
                'ebay-us' => 'eBay US',
                'ebay-de' => 'eBay Germany',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $team = $request->user()->currentTeam;

        abort_unless($team, 403);

        $validated = $request->validate([
            'keyword' => ['required', 'string', 'max:255'],
            'marketplace' => ['required', 'string', 'max:50'],
            'ebay_category_id' => ['nullable', 'string', 'max:50'],
            'competitor_store_url' => ['nullable', 'url', 'max:500'],
        ]);

        if ($this->keywordGuard->isTooBroad($validated['keyword'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'keyword' => $this->keywordGuard->message(),
                ]);
        }

        if ($team->credit_balance < 1) {
            return back()
                ->withInput()
                ->withErrors([
                    'keyword' => 'Your team does not have enough credits to queue a scan.',
                ]);
        }

        $scan = DB::transaction(function () use ($request, $team, $validated) {
            $scan = Scan::create([
                'team_id' => $team->id,
                'user_id' => $request->user()->id,
                'scan_type' => 'keyword',
                'keyword' => $validated['keyword'],
                'marketplace' => $validated['marketplace'],
                'ebay_category_id' => $validated['ebay_category_id'] ?? null,
                'competitor_store_url' => $validated['competitor_store_url'] ?? null,
                'status' => 'queued',
                'reserved_credits' => 1,
                'queued_at' => now(),
            ]);

            CreditLedger::create([
                'team_id' => $team->id,
                'user_id' => $request->user()->id,
                'type' => 'scan_reservation',
                'amount' => -1,
                'description' => "Reserved 1 credit for scan #{$scan->id}.",
            ]);

            return $scan;
        });

        DispatchScanToEngine::dispatch($scan->id)->afterCommit();

        return redirect()
            ->route('scans.submitted', $scan)
            ->with('status', 'Scan queued. Ghostfrog is building your report now.');
    }

    public function submitted(Request $request, Scan $scan): View|RedirectResponse
    {
        abort_unless($request->user()->currentTeam?->is($scan->team), 404);

        if (in_array($scan->status, ['completed', 'failed'], true)) {
            return redirect()->route('scans.show', $scan);
        }

        $scan->load(['team', 'user']);

        return view('scans.submitted', [
            'scan' => $scan,
        ]);
    }

    public function submittedStatus(Request $request, Scan $scan): JsonResponse
    {
        abort_unless($request->user()->currentTeam?->is($scan->team), 404);

        return response()->json([
            'status' => $scan->status,
            'ready' => in_array($scan->status, ['completed', 'failed'], true),
            'show_url' => route('scans.show', $scan),
            'queued_at' => optional($scan->queued_at ?? $scan->created_at)->toIso8601String(),
            'engine_job_id' => $scan->engine_job_id,
        ]);
    }

    public function show(Request $request, Scan $scan): View
    {
        abort_unless($request->user()->currentTeam?->is($scan->team), 404);

        $scan->load(['report', 'user', 'team', 'evidenceListings']);

        return view('scans.show', [
            'scan' => $scan,
        ]);
    }

    public function retry(Request $request, Scan $scan): RedirectResponse
    {
        abort_unless($request->user()->currentTeam?->is($scan->team), 404);

        if ($scan->status !== 'failed') {
            return redirect()
                ->route('scans.show', $scan)
                ->with('status', 'Only failed scans can be retried.');
        }

        if ($scan->team->credit_balance < 1) {
            return redirect()
                ->route('scans.show', $scan)
                ->withErrors([
                    'retry' => 'Your team does not have enough credits to retry this scan.',
                ]);
        }

        DB::transaction(function () use ($request, $scan): void {
            $scan->report()->delete();
            $scan->evidenceListings()->delete();

            $scan->forceFill([
                'status' => 'queued',
                'engine_job_id' => null,
                'queued_at' => now(),
                'engine_dispatched_at' => null,
                'processing_started_at' => null,
                'completed_at' => null,
                'failed_at' => null,
                'failure_reason' => null,
            ])->save();

            CreditLedger::create([
                'team_id' => $scan->team_id,
                'user_id' => $request->user()->id,
                'type' => 'scan_reservation',
                'amount' => -1,
                'description' => "Reserved 1 credit for retry of scan #{$scan->id}.",
            ]);
        });

        DispatchScanToEngine::dispatch($scan->id)->afterCommit();

        return redirect()
            ->route('scans.submitted', $scan)
            ->with('status', 'Scan retried. Ghostfrog is rebuilding the report now.');
    }

    public function feedback(Request $request, Scan $scan): RedirectResponse
    {
        abort_unless($request->user()->currentTeam?->is($scan->team), 404);
        abort_unless($scan->report && $scan->status === 'completed', 404);

        $validated = $request->validate([
            'feedback_rating' => ['required', 'in:helpful,not_helpful'],
            'feedback_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $reportMeta = $scan->report->report_meta ?? [];
        $baseQuality = (int) data_get($reportMeta, 'quality_score', 0);
        $adjustment = $validated['feedback_rating'] === 'helpful' ? 8 : -12;
        $qualityLoopScore = max(0, min(100, $baseQuality + $adjustment));

        $scan->report->forceFill([
            'feedback_rating' => $validated['feedback_rating'],
            'feedback_notes' => $validated['feedback_notes'] ?? null,
            'feedback_submitted_at' => now(),
            'report_meta' => array_merge($reportMeta, [
                'feedback_loop_state' => $validated['feedback_rating'] === 'helpful'
                    ? 'validated_by_customer'
                    : 'needs_review',
                'feedback_adjustment' => $adjustment,
                'quality_loop_score' => $qualityLoopScore,
                'quality_feedback_summary' => $validated['feedback_rating'] === 'helpful'
                    ? 'Customer marked this report as helpful.'
                    : 'Customer marked this report as not helpful and it should be reviewed.',
            ]),
        ])->save();

        return redirect()
            ->route('scans.show', $scan)
            ->with('status', 'Thanks. Your report feedback has been saved for the Ghostfrog quality loop.');
    }
}
