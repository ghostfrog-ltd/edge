<?php

namespace App\Services\Engine;

use App\Models\Scan;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GhostfrogEngineClient
{
    public function dispatchScan(Scan $scan, array $schemaAudit = [], array $intelligenceGathering = []): array
    {
        $baseUrl = rtrim((string) config('services.ghostfrog_engine.url'), '/');

        if ($baseUrl === '') {
            throw new RuntimeException('Ghostfrog engine URL is not configured.');
        }

        $response = $this->request()
            ->post($baseUrl.'/api/v1/scans/dispatch', [
                'scan_id' => $scan->id,
                'team_id' => $scan->team_id,
                'user_id' => $scan->user_id,
                'keyword' => $scan->keyword,
                'marketplace' => $scan->marketplace,
                'category' => $scan->category,
                'scan_type' => $scan->scan_type,
                'ebay_category_id' => $scan->ebay_category_id,
                'competitor_store_url' => $scan->competitor_store_url,
                'schema_audit' => $schemaAudit,
                'intelligence_gathering' => $intelligenceGathering,
                'evidence_count' => $scan->evidenceListings()->count(),
                'notes' => $scan->notes,
                'callback_url' => $this->callbackUrl($scan),
            ])
            ->throw()
            ->json();

        if (! data_get($response, 'engine_job_id')) {
            throw new RuntimeException('Ghostfrog engine did not return an engine job id.');
        }

        return $response;
    }

    protected function request(): PendingRequest
    {
        return Http::acceptJson()
            ->asJson()
            ->timeout((int) config('services.ghostfrog_engine.timeout_seconds', 10))
            ->withOptions([
                'proxy' => [],
                'curl' => [
                    CURLOPT_PROXY => '',
                ],
            ])
            ->withHeaders([
                'X-Ghostfrog-Engine-Secret' => (string) config('services.ghostfrog_engine.shared_secret'),
            ]);
    }

    protected function callbackUrl(Scan $scan): string
    {
        $callbackBaseUrl = rtrim((string) config('services.ghostfrog_engine.callback_base_url'), '/');

        if ($callbackBaseUrl === '') {
            return route('engine.scans.callback', $scan);
        }

        return $callbackBaseUrl.route('engine.scans.callback', $scan, false);
    }
}
