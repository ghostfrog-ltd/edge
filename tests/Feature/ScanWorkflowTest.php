<?php

namespace Tests\Feature;

use App\Models\CreditLedger;
use App\Models\Scan;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ScanWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_can_queue_a_scan_and_reserve_a_credit(): void
    {
        Queue::fake();

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
        ]);

        $scan = Scan::first();

        $response->assertRedirect(route('scans.submitted', $scan));

        $this->assertDatabaseHas('scans', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'scan_type' => 'keyword',
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

    public function test_team_cannot_queue_a_broad_keyword_scan(): void
    {
        Queue::fake();

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

        $response = $this->from(route('scans.create'))
            ->actingAs($user)
            ->post(route('scans.store'), [
                'keyword' => 'Apple',
                'marketplace' => 'ebay-uk',
            ]);

        $response->assertRedirect(route('scans.create'));
        $response->assertSessionHasErrors('keyword');
        $this->assertDatabaseCount('scans', 0);
        $this->assertDatabaseCount('credit_ledgers', 1);
    }

    public function test_team_can_queue_a_scan_with_optional_category_and_competitor_context(): void
    {
        Queue::fake();

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
            'keyword' => 'Honda PCX 125 Spares/Repair',
            'ebay_category_id' => '179753',
            'competitor_store_url' => 'https://www.ebay.co.uk/str/rival-parts-store',
            'marketplace' => 'ebay-uk',
        ]);

        $scan = Scan::first();

        $response->assertRedirect(route('scans.submitted', $scan));

        $this->assertDatabaseHas('scans', [
            'id' => $scan->id,
            'scan_type' => 'keyword',
            'keyword' => 'Honda PCX 125 Spares/Repair',
            'ebay_category_id' => '179753',
            'competitor_store_url' => 'https://www.ebay.co.uk/str/rival-parts-store',
        ]);
    }

    public function test_team_can_view_submitted_scan_progress_page(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        $scan = Scan::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'scan_type' => 'keyword',
            'keyword' => 'Vintage Seiko diver',
            'marketplace' => 'ebay-uk',
            'status' => 'queued',
            'reserved_credits' => 1,
            'queued_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('scans.submitted', $scan))
            ->assertOk()
            ->assertSee('We are building your eBay gap report')
            ->assertSee('This usually takes a few minutes');
    }

    public function test_submitted_page_redirects_to_detail_once_scan_is_done(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        $scan = Scan::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'scan_type' => 'keyword',
            'keyword' => 'Vintage Seiko diver',
            'marketplace' => 'ebay-uk',
            'status' => 'completed',
            'reserved_credits' => 1,
            'consumed_credits' => 1,
            'queued_at' => now(),
            'completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('scans.submitted', $scan))
            ->assertRedirect(route('scans.show', $scan));
    }
}
