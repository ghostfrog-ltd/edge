<?php

namespace Tests\Feature;

use App\Models\CreditLedger;
use App\Models\Scan;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminListsTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAdmin(): User
    {
        $admin = User::factory()->create(['is_admin' => true, 'name' => 'Admin User']);
        $team = Team::factory()->create([
            'user_id' => $admin->id,
            'personal_team' => true,
            'name' => 'Admin Team',
        ]);
        $admin->forceFill(['current_team_id' => $team->id])->save();

        return $admin;
    }

    public function test_admin_can_view_products_plans_and_teams_lists(): void
    {
        $admin = $this->makeAdmin();

        Team::factory()->create(['name' => 'Ghost Sellers', 'user_id' => $admin->id]);

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Ghostfrog Ebay Edge');

        $this->actingAs($admin)
            ->get(route('admin.plans.index'))
            ->assertOk()
            ->assertSee('Starter')
            ->assertSee('Free');

        $this->actingAs($admin)
            ->get(route('admin.teams.index', ['search' => 'Ghost']))
            ->assertOk()
            ->assertSee('Ghost Sellers');
    }

    public function test_admin_can_view_scans_and_credit_lists(): void
    {
        $admin = $this->makeAdmin();
        $user = User::factory()->create(['name' => 'Alice Seller']);
        $team = Team::factory()->create([
            'name' => 'Alice Team',
            'user_id' => $user->id,
            'personal_team' => true,
        ]);
        $user->forceFill(['current_team_id' => $team->id])->save();

        Scan::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'keyword' => 'lego castle byers',
            'marketplace' => 'ebay-uk',
            'status' => 'queued',
            'reserved_credits' => 1,
            'queued_at' => now(),
        ]);

        CreditLedger::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'type' => 'starter_grant',
            'amount' => 50,
            'description' => 'Starter credits for Alice Team.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.scans.index', ['search' => 'lego']))
            ->assertOk()
            ->assertSee('lego castle byers');

        $this->actingAs($admin)
            ->get(route('admin.credits.index', ['search' => 'Alice Team']))
            ->assertOk()
            ->assertSee('Starter Grant');
    }
}
