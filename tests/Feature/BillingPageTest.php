<?php

namespace Tests\Feature;

use App\Models\CreditLedger;
use App\Models\PricingOffer;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_user_can_view_billing_page(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        $this->actingAs($user)
            ->get(route('billing.index'))
            ->assertOk()
            ->assertSee('Monthly plans')
            ->assertSee('Credit boosts')
            ->assertSee('Starter')
            ->assertSee('25-credit boost');
    }

    public function test_success_checkout_banner_shows_active_subscription_message_when_subscription_is_already_settled(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        PricingOffer::query()
            ->where('key', 'starter')
            ->update([
                'stripe_price_id' => 'price_starter_monthly',
                'credits_label' => '50 credits each month',
            ]);

        $team->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_123',
            'stripe_status' => 'active',
            'stripe_price' => 'price_starter_monthly',
        ]);

        $this->actingAs($user)
            ->get(route('billing.index', ['checkout' => 'success']))
            ->assertOk()
            ->assertSee('Starter subscription is active on this workspace.')
            ->assertDontSee('Subscription status and credit top-ups will settle here once the Stripe webhook lands.');
    }

    public function test_success_checkout_banner_shows_top_up_message_when_credits_are_already_settled(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        CreditLedger::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'type' => 'stripe_top_up',
            'amount' => 25,
            'reference' => 'stripe_checkout:cs_test_123',
            'description' => 'Stripe top-up added 25 credits.',
        ]);

        $this->actingAs($user)
            ->get(route('billing.index', ['checkout' => 'success']))
            ->assertOk()
            ->assertSee('Your credit top-up has been added to this workspace.')
            ->assertDontSee('Subscription status and credit top-ups will settle here once the Stripe webhook lands.');
    }
}
