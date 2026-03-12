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
        'missing_attributes',
        'competitor_insights',
        'listing_actions',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'missing_attributes' => 'array',
            'competitor_insights' => 'array',
            'listing_actions' => 'array',
            'generated_at' => 'datetime',
        ];
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }
}
