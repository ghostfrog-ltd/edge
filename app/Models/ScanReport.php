<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_id',
        'summary',
        'missing_three',
        'missing_attributes',
        'schema_audit',
        'voc_insights',
        'competitor_insights',
        'listing_actions',
        'report_meta',
        'feedback_rating',
        'feedback_notes',
        'feedback_submitted_at',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'missing_three' => 'array',
            'missing_attributes' => 'array',
            'schema_audit' => 'array',
            'voc_insights' => 'array',
            'competitor_insights' => 'array',
            'listing_actions' => 'array',
            'report_meta' => 'array',
            'feedback_submitted_at' => 'datetime',
            'generated_at' => 'datetime',
        ];
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    public function confidenceScore(): int
    {
        return $this->normalizedScore(data_get($this->report_meta, 'confidence_score'));
    }

    public function baseQualityScore(): int
    {
        return $this->normalizedScore(data_get($this->report_meta, 'quality_score'));
    }

    public function qualityLoopScore(): int
    {
        $stored = data_get($this->report_meta, 'quality_loop_score');

        if ($stored !== null) {
            return $this->normalizedScore($stored);
        }

        return $this->normalizedScore($this->baseQualityScore() + $this->feedbackAdjustment());
    }

    public function feedbackAdjustment(): int
    {
        return match ($this->feedback_rating) {
            'helpful' => 8,
            'not_helpful' => -12,
            default => 0,
        };
    }

    public function qualityLoopState(): string
    {
        return match ($this->feedback_rating) {
            'helpful' => 'validated_by_customer',
            'not_helpful' => 'needs_review',
            default => (string) data_get($this->report_meta, 'feedback_loop_state', 'awaiting_customer_feedback'),
        };
    }

    public function rankingRationale(): ?string
    {
        $rationale = data_get($this->report_meta, 'ranking_rationale');

        return is_string($rationale) && $rationale !== '' ? $rationale : null;
    }

    public function qualityBand(): string
    {
        $score = $this->qualityLoopScore();

        return match (true) {
            $score >= 85 => 'High confidence',
            $score >= 70 => 'Strong',
            $score >= 55 => 'Watch',
            default => 'Needs review',
        };
    }

    public function formattedSchemaAudit(): array
    {
        return collect($this->schema_audit ?? [])
            ->map(fn (mixed $finding): array => $this->formatSchemaAuditFinding($finding))
            ->filter(fn (array $finding): bool => filled($finding['summary']))
            ->values()
            ->all();
    }

    protected function formatSchemaAuditFinding(mixed $finding): array
    {
        if (is_string($finding)) {
            return [
                'title' => null,
                'summary' => $finding,
                'detail' => null,
            ];
        }

        if (! is_array($finding)) {
            return [
                'title' => null,
                'summary' => 'A schema audit finding was stored for this scan.',
                'detail' => null,
            ];
        }

        $title = $finding['aspect_name'] ?? $finding['label'] ?? $finding['name'] ?? null;
        $summary = $finding['headline']
            ?? ($title ? $title.' is an important eBay schema field for this category.' : null);

        $details = [];

        if (isset($finding['present_count'], $finding['checked_listing_count'])) {
            $details[] = sprintf(
                'Present in %d of %d sampled listings',
                (int) $finding['present_count'],
                (int) $finding['checked_listing_count']
            );
        } elseif (isset($finding['coverage_percent'])) {
            $details[] = sprintf(
                '%d%% coverage across sampled listings',
                (int) $finding['coverage_percent']
            );
        }

        if (! empty($finding['requirement_level'])) {
            $details[] = 'eBay level: '.ucfirst(str_replace('_', ' ', (string) $finding['requirement_level']));
        }

        if (! empty($finding['expected_required_by'])) {
            $details[] = 'Expected to tighten by '.$finding['expected_required_by'];
        }

        return [
            'title' => $title,
            'summary' => $summary,
            'detail' => implode('. ', $details).(count($details) ? '.' : ''),
        ];
    }

    protected function normalizedScore(mixed $value): int
    {
        if (! is_numeric($value)) {
            return 0;
        }

        return max(0, min(100, (int) round((float) $value)));
    }
}
