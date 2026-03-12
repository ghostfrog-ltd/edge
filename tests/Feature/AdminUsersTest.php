<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_list_and_search(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $adminTeam = Team::factory()->create([
            'user_id' => $admin->id,
            'personal_team' => true,
        ]);
        $admin->forceFill(['current_team_id' => $adminTeam->id])->save();

        $match = User::factory()->create(['name' => 'Alice Seller']);
        $other = User::factory()->create(['name' => 'Bob Trader']);

        $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'Alice']));

        $response->assertOk();
        $response->assertSee('Alice Seller');
        $response->assertDontSee('Bob Trader');
    }

    public function test_admin_can_view_user_detail_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $adminTeam = Team::factory()->create([
            'user_id' => $admin->id,
            'personal_team' => true,
        ]);
        $admin->forceFill(['current_team_id' => $adminTeam->id])->save();

        $user = User::factory()->create([
            'name' => 'Casey Merchant',
            'email' => 'casey@example.com',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.show', $user));

        $response->assertOk();
        $response->assertSee('Casey Merchant');
        $response->assertSee('casey@example.com');
    }
}
