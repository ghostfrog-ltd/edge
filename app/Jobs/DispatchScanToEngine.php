<?php

namespace App\Jobs;

use App\Models\Scan;
use App\Services\Engine\GhostfrogEngineClient;
use App\Services\Ebay\EbayIntelligenceGatheringService;
use App\Services\Ebay\EbayListingEvidenceService;
use App\Services\Ebay\EbaySchemaAuditService;
use App\Services\Scans\ScanPipelineService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;
use Throwable;

class DispatchScanToEngine implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 15;

    public function __construct(public int $scanId)
    {
    }

    public function handle(
        GhostfrogEngineClient $engineClient,
        ScanPipelineService $pipeline,
        EbayListingEvidenceService $listingEvidence,
        EbaySchemaAuditService $schemaAudit,
        EbayIntelligenceGatheringService $intelligenceGathering
    ): void
    {
        $scan = Scan::query()->with('team', 'user')->find($this->scanId);

        if (! $scan || in_array($scan->status, ['completed', 'failed'], true)) {
            return;
        }

        $pipeline->markDispatching($scan);
        try {
            $listingEvidence->syncTopListings($scan->fresh());
        } catch (RuntimeException $exception) {
            if ($this->shouldFailFast($exception)) {
                $pipeline->markFailed(
                    $scan,
                    $this->friendlyFailureReason($scan, $exception)
                );

                return;
            }

            throw $exception;
        }

        $audit = $schemaAudit->audit($scan->fresh());
        $intelligence = $intelligenceGathering->gather($scan->fresh());

        $response = $engineClient->dispatchScan($scan, $audit, $intelligence);

        $pipeline->markAcceptedByEngine(
            $scan->fresh(),
            (string) data_get($response, 'engine_job_id')
        );
    }

    public function failed(Throwable $exception): void
    {
        $scan = Scan::query()->find($this->scanId);

        if (! $scan) {
            return;
        }

        app(ScanPipelineService::class)->markFailed(
            $scan,
            'Engine dispatch failed after retries: '.$exception->getMessage()
        );
    }

    protected function shouldFailFast(RuntimeException $exception): bool
    {
        return str_contains(strtolower($exception->getMessage()), 'no active listings');
    }

    protected function friendlyFailureReason(Scan $scan, RuntimeException $exception): string
    {
        if (! $this->shouldFailFast($exception)) {
            return $exception->getMessage();
        }

        $contextHints = collect([
            filled($scan->ebay_category_id) ? 'try removing the category ID' : null,
            filled($scan->competitor_store_url) ? 'try removing the competitor store filter' : null,
            'or broaden the keyword slightly',
        ])->filter()->implode(', ');

        return 'Ghostfrog could not find enough active eBay listings for this exact scan. '.$contextHints.'. Your reserved credit has been refunded automatically.';
    }
}
