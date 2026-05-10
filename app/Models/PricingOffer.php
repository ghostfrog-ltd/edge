<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingOffer extends Model
{
    protected $fillable = [
        'type',
        'key',
        'name',
        'summary',
        'price_label',
        'currency',
        'amount_minor',
        'billing_interval',
        'monthly_credits',
        'credits',
        'credits_label',
        'stripe_product_id',
        'stripe_price_id',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'amount_minor' => 'integer',
            'monthly_credits' => 'integer',
            'credits' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getAmountDisplayAttribute(): string
    {
        return number_format(((int) $this->amount_minor) / 100, 2, '.', '');
    }

    public function isPlan(): bool
    {
        return $this->type === 'plan';
    }

    public function isTopUp(): bool
    {
        return $this->type === 'top_up';
    }
}
