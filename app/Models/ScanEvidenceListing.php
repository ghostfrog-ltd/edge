<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanEvidenceListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_id',
        'rank',
        'ebay_item_id',
        'title',
        'condition',
        'price_value',
        'price_currency',
        'item_web_url',
        'image_url',
        'seller_username',
        'seller_feedback_percentage',
        'seller_feedback_score',
        'shipping_summary',
        'buying_options',
        'category_id',
        'category_name',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'price_value' => 'decimal:2',
            'buying_options' => 'array',
            'raw_payload' => 'array',
        ];
    }

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }
}
