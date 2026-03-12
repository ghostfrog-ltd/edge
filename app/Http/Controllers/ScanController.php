<?php

namespace App\Http\Controllers;

use App\Models\CreditLedger;
use App\Models\Scan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function index(Request $request): View
    {
        $team = $request->user()->currentTeam;

        abort_unless($team, 403);

        return view('scans.index', [
            'team' => $team,
            'scans' => $team->scans()->latest()->paginate(10),
        ]);
    }

    public function create(Request $request): View
    {
        $team = $request->user()->currentTeam;

        abort_unless($team, 403);

        return view('scans.create', [
            'team' => $team,
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
            'category' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

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
                'keyword' => $validated['keyword'],
                'marketplace' => $validated['marketplace'],
                'category' => $validated['category'] ?? null,
                'notes' => $validated['notes'] ?? null,
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

        return redirect()
            ->route('scans.show', $scan)
            ->with('status', 'Scan queued. The Laravel side has captured the request and reserved 1 credit.');
    }

    public function show(Request $request, Scan $scan): View
    {
        abort_unless($request->user()->currentTeam?->is($scan->team), 404);

        $scan->load('report', 'user', 'team');

        return view('scans.show', [
            'scan' => $scan,
        ]);
    }
}
