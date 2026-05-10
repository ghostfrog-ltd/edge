<?php

namespace App\Support;

use App\Models\PricingOffer;
use Illuminate\Support\Facades\Schema;

class BillingCatalog
{
    public function plans(): array
    {
        if ($offers = $this->offersFor('plan')) {
            return $offers;
        }

        return collect(config('billing.plans', []))
            ->map(fn (array $plan, string $key): array => $this->decorate($key, $plan))
            ->values()
            ->all();
    }

    public function topUps(): array
    {
        if ($offers = $this->offersFor('top_up')) {
            return $offers;
        }

        return collect(config('billing.top_ups', []))
            ->map(fn (array $pack, string $key): array => $this->decorate($key, $pack))
            ->values()
            ->all();
    }

    public function plan(string $key): ?array
    {
        if ($offer = $this->offerFor('plan', $key)) {
            return $offer;
        }

        $plan = config("billing.plans.{$key}");

        return is_array($plan) ? $this->decorate($key, $plan) : null;
    }

    public function topUp(string $key): ?array
    {
        if ($offer = $this->offerFor('top_up', $key)) {
            return $offer;
        }

        $pack = config("billing.top_ups.{$key}");

        return is_array($pack) ? $this->decorate($key, $pack) : null;
    }

    public function planForPrice(?string $priceId): ?array
    {
        if (! filled($priceId)) {
            return null;
        }

        if ($this->catalogAvailable()) {
            $offer = PricingOffer::query()
                ->where('type', 'plan')
                ->where('stripe_price_id', $priceId)
                ->where('is_active', true)
                ->first();

            if ($offer) {
                return $this->fromModel($offer);
            }
        }

        foreach ($this->plans() as $plan) {
            if (($plan['stripe_price_id'] ?? null) === $priceId) {
                return $plan;
            }
        }

        return null;
    }

    public function allOffers(): array
    {
        if (! $this->catalogAvailable()) {
            return [
                'plans' => $this->plans(),
                'topUps' => $this->topUps(),
            ];
        }

        return [
            'plans' => $this->offersFor('plan'),
            'topUps' => $this->offersFor('top_up'),
        ];
    }

    protected function offersFor(string $type): array
    {
        if (! $this->catalogAvailable()) {
            return [];
        }

        return PricingOffer::query()
            ->where('type', $type)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (PricingOffer $offer): array => $this->fromModel($offer))
            ->all();
    }

    protected function offerFor(string $type, string $key): ?array
    {
        if (! $this->catalogAvailable()) {
            return null;
        }

        $offer = PricingOffer::query()
            ->where('type', $type)
            ->where('key', $key)
            ->first();

        return $offer ? $this->fromModel($offer) : null;
    }

    protected function catalogAvailable(): bool
    {
        return Schema::hasTable('pricing_offers') && PricingOffer::query()->exists();
    }

    protected function fromModel(PricingOffer $offer): array
    {
        return $this->decorate($offer->key, [
            'name' => $offer->name,
            'price_label' => $offer->price_label,
            'monthly_credits' => $offer->monthly_credits,
            'credits' => $offer->credits,
            'credits_label' => $offer->credits_label,
            'summary' => $offer->summary,
            'stripe_product_id' => $offer->stripe_product_id,
            'stripe_price_id' => $offer->stripe_price_id,
            'currency' => $offer->currency,
            'amount_minor' => $offer->amount_minor,
            'billing_interval' => $offer->billing_interval,
            'type' => $offer->type,
            'is_active' => $offer->is_active,
            'sort_order' => $offer->sort_order,
            'model_id' => $offer->id,
        ]);
    }

    protected function decorate(string $key, array $entry): array
    {
        return $entry + [
            'key' => $key,
            'configured' => filled($entry['stripe_price_id'] ?? null),
        ];
    }
}
