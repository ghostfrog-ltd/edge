<?php

namespace Tests\Feature;

use App\Models\InboxNotification;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InboxNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function makeUser(): array
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->forceFill(['current_team_id' => $team->id])->save();

        return [$user, $team];
    }

    public function test_user_can_view_inbox_notifications(): void
    {
        [$user, $team] = $this->makeUser();

        InboxNotification::create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'type' => 'scan_completed',
            'title' => 'Scan ready: ipad',
            'body' => 'Your scan has completed.',
            'action_url' => '/scans/1',
        ]);

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Scan ready: ipad');
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        [$user, $team] = $this->makeUser();

        $notification = InboxNotification::create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'type' => 'scan_failed',
            'title' => 'Scan failed: ipad',
            'body' => 'Engine unavailable.',
            'action_url' => '/scans/1',
        ]);

        $this->actingAs($user)
            ->post(route('notifications.read', $notification), ['stay' => 1])
            ->assertRedirect(route('notifications.index'));

        $this->assertDatabaseMissing('inbox_notifications', [
            'id' => $notification->id,
            'read_at' => null,
        ]);
    }

    public function test_user_can_fetch_unread_notification_count(): void
    {
        [$user, $team] = $this->makeUser();

        InboxNotification::create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'type' => 'scan_completed',
            'title' => 'Scan ready: ipad',
            'body' => 'Your scan has completed.',
            'action_url' => '/scans/1',
        ]);

        InboxNotification::create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'type' => 'scan_failed',
            'title' => 'Scan failed: ipad',
            'body' => 'Engine unavailable.',
            'action_url' => '/scans/1',
            'read_at' => now(),
        ]);

        $this->actingAs($user)
            ->getJson(route('notifications.unread-count'))
            ->assertOk()
            ->assertJson([
                'count' => 1,
            ]);
    }
}
