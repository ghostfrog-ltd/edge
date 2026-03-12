<?php

namespace Tests\Feature;

use App\Models\CreditLedger;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_view_admin_dashboard_and_roadmap(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $team = Team::factory()->create([
            'user_id' => $admin->id,
            'personal_team' => true,
        ]);

        $admin->forceFill(['current_team_id' => $team->id])->save();

        CreditLedger::create([
            'team_id' => $team->id,
            'user_id' => $admin->id,
            'type' => 'starter_grant',
            'amount' => 50,
            'description' => 'Starter credits for testing.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Platform dashboard');

        $this->actingAs($admin)
            ->get(route('admin.roadmap'))
            ->assertOk()
            ->assertSee('Operator roadmap');
    }

    public function test_non_admin_user_cannot_access_admin_pages(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
