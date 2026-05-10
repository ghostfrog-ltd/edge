<?php

namespace App\Services\Billing;

use App\Models\PricingOffer;
use Illuminate\Support\Arr;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;

class StripePricingSyncService
{
    public function sync(PricingOffer $offer): PricingOffer
    {
        if ((int) $offer->amount_minor < 1) {
            throw new \RuntimeException('Free offers do not need a Stripe product or price.');
        }

        if (! filled(config('cashier.secret'))) {
            throw new \RuntimeException('Stripe secret key is missing from configuration.');
        }

        Stripe::setApiKey(config('cashier.secret'));

        $stripeProduct = $offer->stripe_product_id
            ? Product::update($offer->stripe_product_id, $this->productPayload($offer))
            : Product::create($this->productPayload($offer));

        $offer->forceFill([
            'stripe_product_id' => $stripeProduct->id,
        ])->save();

        if (! $this->currentStripePriceMatches($offer)) {
            $price = Price::create($this->pricePayload($offer));

            $offer->forceFill([
                'stripe_price_id' => $price->id,
            ])->save();
        }

        return $offer->fresh();
    }

    protected function currentStripePriceMatches(PricingOffer $offer): bool
    {
        if (! filled($offer->stripe_price_id)) {
            return false;
        }

        $price = Price::retrieve($offer->stripe_price_id);

        if (($price->unit_amount ?? null) !== $offer->amount_minor) {
            return false;
        }

        if (($price->currency ?? null) !== strtolower($offer->currency)) {
            return false;
        }

        if (($price->product ?? null) !== $offer->stripe_product_id) {
            return false;
        }

        if ($offer->isPlan()) {
            return Arr::get($price->toArray(), 'recurring.interval') === ($offer->billing_interval ?: 'month');
        }

        return Arr::get($price->toArray(), 'recurring') === null;
    }

    protected function productPayload(PricingOffer $offer): array
    {
        return [
            'name' => $offer->name,
            'description' => $offer->summary,
            'active' => $offer->is_active,
            'metadata' => [
                'ghostfrog_offer_key' => $offer->key,
                'ghostfrog_offer_type' => $offer->type,
            ],
        ];
    }

    protected function pricePayload(PricingOffer $offer): array
    {
        $payload = [
            'product' => $offer->stripe_product_id,
            'currency' => strtolower($offer->currency),
            'unit_amount' => $offer->amount_minor,
            'metadata' => [
                'ghostfrog_offer_key' => $offer->key,
                'ghostfrog_offer_type' => $offer->type,
            ],
        ];

        if ($offer->isPlan()) {
            $payload['recurring'] = [
                'interval' => $offer->billing_interval ?: 'month',
            ];
        }

        return $payload;
    }
}
