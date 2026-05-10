<?php

namespace App\Services\Scans;

use App\Models\CreditLedger;
use App\Models\Scan;
use App\Models\ScanReport;
use App\Services\Notifications\InboxNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScanPipelineService
{
    public function __construct(protected InboxNotificationService $notifications)
    {
    }

    public function markDispatching(Scan $scan): void
    {
        $scan->forceFill([
            'status' => 'dispatching',
            'failure_reason' => null,
        ])->save();
    }

    public function markAcceptedByEngine(Scan $scan, string $engineJobId): void
    {
        $scan->forceFill([
            'status' => 'processing',
            'engine_job_id' => $engineJobId,
            'engine_dispatched_at' => now(),
            'processing_started_at' => $scan->processing_started_at ?? now(),
            'failure_reason' => null,
            'failed_at' => null,
        ])->save();
    }

    public function markCompleted(Scan $scan, array $payload): void
    {
        if ($scan->status === 'completed') {
            return;
        }

        DB::transaction(function () use ($scan, $payload): void {
            $scan->forceFill([
                'status' => 'completed',
                'engine_job_id' => $payload['engine_job_id'] ?? $scan->engine_job_id,
                'consumed_credits' => max($scan->consumed_credits, $scan->reserved_credits),
                'completed_at' => now(),
                'failed_at' => null,
                'failure_reason' => null,
            ])->save();

            ScanReport::query()->updateOrCreate(
                ['scan_id' => $scan->id],
                [
                    'summary' => $payload['summary'] ?? null,
                    'missing_three' => $payload['missing_three'] ?? [],
                    'missing_attributes' => $payload['missing_attributes'] ?? [],
                    'schema_audit' => $payload['schema_audit'] ?? [],
                    'voc_insights' => $payload['voc_insights'] ?? [],
                    'competitor_insights' => $payload['competitor_insights'] ?? [],
                    'listing_actions' => $payload['listing_actions'] ?? [],
                    'report_meta' => array_merge(
                        $payload['report_meta'] ?? [],
                        [
                            'evidence_count' => $scan->evidenceListings()->count(),
                            'feedback_loop_state' => data_get($payload, 'report_meta.feedback_loop_state', 'awaiting_customer_feedback'),
                            'quality_loop_score' => data_get($payload, 'report_meta.quality_loop_score', data_get($payload, 'report_meta.quality_score')),
                        ]
                    ),
                    'generated_at' => isset($payload['generated_at'])
                        ? Carbon::parse($payload['generated_at'])
                        : now(),
                ]
            );
        });

        $this->notifications->scanCompleted($scan->fresh());
    }

    public function markFailed(Scan $scan, string $reason): void
    {
        if ($scan->status === 'completed') {
            return;
        }

        DB::transaction(function () use ($scan, $reason): void {
            $scan->forceFill([
                'status' => 'failed',
                'completed_at' => null,
                'failed_at' => now(),
                'failure_reason' => $reason,
            ])->save();

            $this->refundReservedCredit($scan, $reason);
        });

        $this->notifications->scanFailed($scan->fresh(), $reason);
    }

    protected function refundReservedCredit(Scan $scan, string $reason): void
    {
        $alreadyRefunded = CreditLedger::query()
            ->where('team_id', $scan->team_id)
            ->where('type', 'scan_refund')
            ->where('description', 'like', '%scan #'.$scan->id.'%')
            ->exists();

        if ($alreadyRefunded || $scan->reserved_credits < 1) {
            return;
        }

        CreditLedger::create([
            'team_id' => $scan->team_id,
            'user_id' => $scan->user_id,
            'type' => 'scan_refund',
            'amount' => $scan->reserved_credits,
            'description' => 'Refunded '.$scan->reserved_credits.' credit for scan #'.$scan->id.'. '.$reason,
        ]);
    }
}
