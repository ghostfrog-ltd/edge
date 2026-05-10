<?php

namespace Tests\Feature;

use App\Models\CreditLedger;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_view_admin_dashboard_and_roadmap(): void
    {
        Http::fake([
            'http://127.0.0.1:8001/health' => Http::response([
                'ok' => true,
                'version' => '0.2.0',
                'configured_provider' => 'gemini',
                'configured_model' => 'gemini-2.5-flash',
                'active_jobs' => 0,
                'dispatches_total' => 4,
                'callbacks_completed' => 4,
                'callbacks_failed' => 0,
                'uptime_seconds' => 120,
                'simulated_delay_seconds' => 1.5,
            ]),
        ]);

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
            ->assertSee('Platform dashboard')
            ->assertSee('Engine and queue monitoring')
            ->assertSee('Human test plan');

        $this->actingAs($admin)
            ->get(route('admin.roadmap'))
            ->assertOk()
            ->assertSee('Operator roadmap');

        $this->actingAs($admin)
            ->get(route('admin.test-plan'))
            ->assertOk()
            ->assertSee('Human test plan')
            ->assertSee('Scan to report loop');
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
