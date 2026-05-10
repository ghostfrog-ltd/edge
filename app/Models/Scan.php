<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Scan extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'scan_type',
        'keyword',
        'marketplace',
        'category',
        'ebay_category_id',
        'competitor_store_url',
        'status',
        'engine_job_id',
        'reserved_credits',
        'consumed_credits',
        'notes',
        'queued_at',
        'engine_dispatched_at',
        'processing_started_at',
        'completed_at',
        'failed_at',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'queued_at' => 'datetime',
            'engine_dispatched_at' => 'datetime',
            'processing_started_at' => 'datetime',
            'completed_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(ScanReport::class);
    }

    public function evidenceListings(): HasMany
    {
        return $this->hasMany(ScanEvidenceListing::class)->orderBy('rank');
    }

    public function triggerLabel(): string
    {
        return match ($this->scan_type) {
            'category' => $this->ebay_category_id
                ? 'Category ID '.$this->ebay_category_id
                : 'Category scan',
            'competitor_store' => $this->competitor_store_url ?: 'Competitor store',
            default => $this->keyword,
        };
    }

    public function hasRefundedReservation(): bool
    {
        return CreditLedger::query()
            ->where('team_id', $this->team_id)
            ->where('type', 'scan_refund')
            ->where('description', 'like', '%scan #'.$this->id.'%')
            ->exists();
    }
}
