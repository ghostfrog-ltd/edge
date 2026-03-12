<?php

namespace Tests\Feature;

use App\Models\CreditLedger;
use App\Models\Scan;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_can_queue_a_scan_and_reserve_a_credit(): void
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
            'type' => 'starter_grant',
            'amount' => 50,
            'description' => 'Starter credits for testing.',
        ]);

        $response = $this->actingAs($user)->post(route('scans.store'), [
            'keyword' => 'lego castle byers',
            'marketplace' => 'ebay-uk',
            'category' => 'LEGO complete sets',
            'notes' => 'Focus on missing condition details.',
        ]);

        $scan = Scan::first();

        $response->assertRedirect(route('scans.show', $scan));

        $this->assertDatabaseHas('scans', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'keyword' => 'lego castle byers',
            'status' => 'queued',
            'reserved_credits' => 1,
        ]);

        $this->assertDatabaseHas('credit_ledgers', [
            'team_id' => $team->id,
            'type' => 'scan_reservation',
            'amount' => -1,
        ]);
    }

    public function test_team_cannot_queue_a_scan_without_available_credits(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        $response = $this->from(route('scans.create'))
            ->actingAs($user)
            ->post(route('scans.store'), [
                'keyword' => 'omega seamaster dial',
                'marketplace' => 'ebay-uk',
            ]);

        $response->assertRedirect(route('scans.create'));
        $response->assertSessionHasErrors('keyword');
        $this->assertDatabaseCount('scans', 0);
    }
}
