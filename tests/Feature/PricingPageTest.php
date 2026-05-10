<?php

namespace Tests\Feature;

use App\Models\PricingOffer;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pricing_page_shows_plans_and_top_ups(): void
    {
        $this->get(route('pricing'))
            ->assertOk()
            ->assertSee('Monthly plans')
            ->assertSee('Starter')
            ->assertSee('25-credit boost');
    }

    public function test_admin_can_update_pricing_offer_and_public_page_reads_it(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $team = Team::factory()->create([
            'user_id' => $admin->id,
            'personal_team' => true,
        ]);
        $admin->forceFill(['current_team_id' => $team->id])->save();

        $offer = PricingOffer::query()->where('key', 'starter')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.plans.update', $offer), [
                'type' => 'plan',
                'key' => 'starter',
                'name' => 'Starter',
                'summary' => 'Updated starter summary for testing.',
                'price_label' => 'PS39 / month',
                'amount_display' => '39.00',
                'billing_interval' => 'month',
                'monthly_credits' => 75,
                'credits' => '',
                'credits_label' => '75 credits each month',
                'currency' => 'gbp',
                'sort_order' => 20,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.plans.edit', $offer));

        $this->get(route('pricing'))
            ->assertOk()
            ->assertSee('PS39 / month')
            ->assertSee('75 credits each month')
            ->assertSee('Updated starter summary for testing.');
    }
}
