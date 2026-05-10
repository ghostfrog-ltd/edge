<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Services\Scans\ScanPipelineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EngineScanCallbackController extends Controller
{
    public function __invoke(Request $request, Scan $scan, ScanPipelineService $pipeline): JsonResponse
    {
        abort_unless(
            hash_equals(
                (string) config('services.ghostfrog_engine.callback_secret'),
                (string) $request->header('X-Ghostfrog-Callback-Secret')
            ),
            401
        );

        $payload = $request->validate([
            'status' => ['required', 'string', 'in:completed,failed'],
            'engine_job_id' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'missing_three' => ['nullable', 'array'],
            'missing_three.*.title' => ['required_with:missing_three', 'string'],
            'missing_three.*.why_it_matters' => ['required_with:missing_three', 'string'],
            'missing_three.*.what_to_add' => ['required_with:missing_three', 'string'],
            'missing_three.*.evidence_source' => ['required_with:missing_three', 'string'],
            'missing_three.*.priority_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'missing_three.*.ranking_reason' => ['nullable', 'string'],
            'missing_attributes' => ['nullable', 'array'],
            'missing_attributes.*' => ['string'],
            'schema_audit' => ['nullable', 'array'],
            'schema_audit.*.aspect_name' => ['required_with:schema_audit', 'string'],
            'schema_audit.*.requirement_level' => ['required_with:schema_audit', 'string'],
            'schema_audit.*.headline' => ['required_with:schema_audit', 'string'],
            'schema_audit.*.present_count' => ['nullable', 'integer'],
            'schema_audit.*.missing_count' => ['nullable', 'integer'],
            'schema_audit.*.checked_listing_count' => ['nullable', 'integer'],
            'schema_audit.*.coverage_percent' => ['nullable', 'integer'],
            'schema_audit.*.expected_required_by' => ['nullable', 'string'],
            'voc_insights' => ['nullable', 'array'],
            'voc_insights.*' => ['string'],
            'competitor_insights' => ['nullable', 'array'],
            'competitor_insights.*' => ['string'],
            'listing_actions' => ['nullable', 'array'],
            'listing_actions.*' => ['string'],
            'report_meta' => ['nullable', 'array'],
            'failure_reason' => ['nullable', 'string'],
            'generated_at' => ['nullable', 'date'],
        ]);

        if (($payload['status'] ?? null) === 'completed') {
            $pipeline->markCompleted($scan, $payload);
        } else {
            $pipeline->markFailed(
                $scan,
                $payload['failure_reason'] ?? 'The engine reported a failed scan.'
            );
        }

        return response()->json(['ok' => true]);
    }
}
