<?php

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\Http;
use Throwable;

class EngineHealthService
{
    public function snapshot(): array
    {
        $baseUrl = rtrim((string) config('services.ghostfrog_engine.url'), '/');

        if ($baseUrl === '') {
            return [
                'reachable' => false,
                'status' => 'not_configured',
                'error' => 'Fuzzynode engine URL is not configured.',
            ];
        }

        try {
            $payload = Http::acceptJson()
                ->timeout(3)
                ->get($baseUrl.'/health')
                ->throw()
                ->json();
        } catch (Throwable $exception) {
            return [
                'reachable' => false,
                'status' => 'offline',
                'error' => $exception->getMessage(),
            ];
        }

        $failedCallbacks = (int) data_get($payload, 'callbacks_failed', 0);
        $lastCallbackError = data_get($payload, 'last_callback_error');

        return [
            'reachable' => true,
            'status' => $failedCallbacks > 0 && filled($lastCallbackError) ? 'degraded' : 'healthy',
            'version' => data_get($payload, 'version', 'unknown'),
            'provider' => data_get($payload, 'configured_provider', 'unknown'),
            'model' => data_get($payload, 'configured_model', 'unknown'),
            'uptime_seconds' => (int) data_get($payload, 'uptime_seconds', 0),
            'simulated_delay_seconds' => (float) data_get($payload, 'simulated_delay_seconds', 0),
            'active_jobs' => (int) data_get($payload, 'active_jobs', 0),
            'dispatches_total' => (int) data_get($payload, 'dispatches_total', 0),
            'callbacks_completed' => (int) data_get($payload, 'callbacks_completed', 0),
            'callbacks_failed' => $failedCallbacks,
            'last_dispatch_at' => data_get($payload, 'last_dispatch_at'),
            'last_completed_at' => data_get($payload, 'last_completed_at'),
            'last_failed_at' => data_get($payload, 'last_failed_at'),
            'last_job_duration_ms' => data_get($payload, 'last_job_duration_ms'),
            'last_scan_id' => data_get($payload, 'last_scan_id'),
            'last_callback_error' => $lastCallbackError,
            'error' => null,
        ];
    }
}
