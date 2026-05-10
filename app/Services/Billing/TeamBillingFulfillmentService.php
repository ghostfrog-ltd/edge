<?php

namespace App\Services\Billing;

use App\Models\CreditLedger;
use App\Models\Team;
use App\Support\BillingCatalog;

class TeamBillingFulfillmentService
{
    public function __construct(protected BillingCatalog $catalog)
    {
    }

    public function handleWebhook(array $payload): void
    {
        match (data_get($payload, 'type')) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted((array) data_get($payload, 'data.object', [])),
            'invoice.payment_succeeded' => $this->handleInvoicePaymentSucceeded((array) data_get($payload, 'data.object', [])),
            default => null,
        };
    }

    protected function handleCheckoutSessionCompleted(array $session): void
    {
        if (($session['mode'] ?? null) !== 'payment' || ($session['payment_status'] ?? null) !== 'paid') {
            return;
        }

        if (data_get($session, 'metadata.ghostfrog_type') !== 'credit_top_up') {
            return;
        }

        $team = $this->findTeamByStripeCustomer($session['customer'] ?? null);

        if (! $team) {
            return;
        }

        $reference = 'stripe_checkout:'.($session['id'] ?? '');

        if ($reference === 'stripe_checkout:' || CreditLedger::query()->where('reference', $reference)->exists()) {
            return;
        }

        $pack = $this->catalog->topUp((string) data_get($session, 'metadata.top_up_key'));
        $credits = (int) data_get($session, 'metadata.credits', $pack['credits'] ?? 0);

        if ($credits < 1) {
            return;
        }

        CreditLedger::create([
            'team_id' => $team->id,
            'user_id' => $team->user_id,
            'type' => 'stripe_top_up',
            'amount' => $credits,
            'reference' => $reference,
            'description' => sprintf(
                'Stripe top-up added %d credits via checkout session %s.',
                $credits,
                $session['id']
            ),
        ]);
    }

    protected function handleInvoicePaymentSucceeded(array $invoice): void
    {
        if (! filled($invoice['subscription'] ?? null)) {
            return;
        }

        $team = $this->findTeamByStripeCustomer($invoice['customer'] ?? null);

        if (! $team) {
            return;
        }

        $reference = 'stripe_invoice:'.($invoice['id'] ?? '');

        if ($reference === 'stripe_invoice:' || CreditLedger::query()->where('reference', $reference)->exists()) {
            return;
        }

        $priceIds = collect(data_get($invoice, 'lines.data', []))
            ->map(fn (array $line): ?string => $this->extractInvoiceLinePriceId($line))
            ->filter()
            ->values();

        $plan = $priceIds
            ->map(fn (string $priceId): ?array => $this->catalog->planForPrice($priceId))
            ->first(fn (?array $plan): bool => is_array($plan));

        if (! $plan || (int) ($plan['monthly_credits'] ?? 0) < 1) {
            return;
        }

        CreditLedger::create([
            'team_id' => $team->id,
            'user_id' => $team->user_id,
            'type' => 'subscription_credit',
            'amount' => (int) $plan['monthly_credits'],
            'reference' => $reference,
            'description' => sprintf(
                'Stripe subscription credits for the %s plan via invoice %s.',
                $plan['name'],
                $invoice['number'] ?? $invoice['id']
            ),
        ]);
    }

    protected function extractInvoiceLinePriceId(array $line): ?string
    {
        return data_get($line, 'price.id')
            ?? data_get($line, 'pricing.price_details.price');
    }

    protected function findTeamByStripeCustomer(?string $stripeCustomerId): ?Team
    {
        if (! filled($stripeCustomerId)) {
            return null;
        }

        return Team::query()
            ->where('stripe_id', $stripeCustomerId)
            ->first();
    }
}
