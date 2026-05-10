<?php

namespace Tests\Feature;

use App\Models\CreditLedger;
use App\Models\PricingOffer;
use App\Models\Team;
use App\Models\User;
use App\Services\Billing\TeamBillingFulfillmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamBillingFulfillmentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_session_completed_grants_top_up_credits_once(): void
    {
        config()->set('billing.top_ups.boost_25.stripe_price_id', 'price_topup_25');

        $team = $this->createTeamWithStripeId('cus_topup_123');
        $service = app(TeamBillingFulfillmentService::class);

        $payload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_123',
                    'customer' => $team->stripe_id,
                    'mode' => 'payment',
                    'payment_status' => 'paid',
                    'metadata' => [
                        'ghostfrog_type' => 'credit_top_up',
                        'top_up_key' => 'boost_25',
                        'credits' => '25',
                    ],
                ],
            ],
        ];

        $service->handleWebhook($payload);
        $service->handleWebhook($payload);

        $this->assertDatabaseCount('credit_ledgers', 1);
        $this->assertDatabaseHas('credit_ledgers', [
            'team_id' => $team->id,
            'type' => 'stripe_top_up',
            'amount' => 25,
            'reference' => 'stripe_checkout:cs_test_123',
        ]);
    }

    public function test_invoice_payment_succeeded_grants_subscription_credits_once(): void
    {
        PricingOffer::query()
            ->where('key', 'starter')
            ->update([
                'stripe_price_id' => 'price_starter_monthly',
                'monthly_credits' => 50,
                'credits_label' => '50 credits each month',
            ]);

        $team = $this->createTeamWithStripeId('cus_sub_123');
        $service = app(TeamBillingFulfillmentService::class);

        $payload = [
            'type' => 'invoice.payment_succeeded',
            'data' => [
                'object' => [
                    'id' => 'in_test_123',
                    'number' => 'GF-0001',
                    'customer' => $team->stripe_id,
                    'subscription' => 'sub_test_123',
                    'lines' => [
                        'data' => [
                            [
                                'price' => [
                                    'id' => 'price_starter_monthly',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $service->handleWebhook($payload);
        $service->handleWebhook($payload);

        $this->assertDatabaseCount('credit_ledgers', 1);
        $this->assertDatabaseHas('credit_ledgers', [
            'team_id' => $team->id,
            'type' => 'subscription_credit',
            'amount' => 50,
            'reference' => 'stripe_invoice:in_test_123',
        ]);
    }

    public function test_invoice_payment_succeeded_grants_subscription_credits_from_modern_pricing_shape(): void
    {
        PricingOffer::query()
            ->where('key', 'starter')
            ->update([
                'stripe_price_id' => 'price_starter_monthly',
                'monthly_credits' => 50,
                'credits_label' => '50 credits each month',
            ]);

        $team = $this->createTeamWithStripeId('cus_sub_456');
        $service = app(TeamBillingFulfillmentService::class);

        $payload = [
            'type' => 'invoice.payment_succeeded',
            'data' => [
                'object' => [
                    'id' => 'in_test_456',
                    'number' => 'GF-0002',
                    'customer' => $team->stripe_id,
                    'subscription' => 'sub_test_456',
                    'lines' => [
                        'data' => [
                            [
                                'pricing' => [
                                    'price_details' => [
                                        'price' => 'price_starter_monthly',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $service->handleWebhook($payload);

        $this->assertDatabaseHas('credit_ledgers', [
            'team_id' => $team->id,
            'type' => 'subscription_credit',
            'amount' => 50,
            'reference' => 'stripe_invoice:in_test_456',
        ]);
    }

    protected function createTeamWithStripeId(string $stripeId): Team
    {
        $user = User::factory()->create();

        return Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
            'stripe_id' => $stripeId,
        ]);
    }
}
