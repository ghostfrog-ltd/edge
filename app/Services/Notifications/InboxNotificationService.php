<?php

namespace App\Services\Notifications;

use App\Mail\ScanCompletedMail;
use App\Mail\ScanFailedMail;
use App\Models\InboxNotification;
use App\Models\Scan;
use Illuminate\Support\Facades\Mail;

class InboxNotificationService
{
    public function scanCompleted(Scan $scan): void
    {
        InboxNotification::create([
            'user_id' => $scan->user_id,
            'team_id' => $scan->team_id,
            'type' => 'scan_completed',
            'title' => 'Scan ready: '.$scan->keyword,
            'body' => 'Your scan has completed and the report is ready to view.',
            'action_url' => route('scans.show', $scan),
        ]);

        Mail::to($scan->user->email)->send(new ScanCompletedMail($scan->fresh('report')));
    }

    public function scanFailed(Scan $scan, string $reason): void
    {
        InboxNotification::create([
            'user_id' => $scan->user_id,
            'team_id' => $scan->team_id,
            'type' => 'scan_failed',
            'title' => 'Scan failed: '.$scan->keyword,
            'body' => $reason,
            'action_url' => route('scans.show', $scan),
        ]);

        Mail::to($scan->user->email)->send(new ScanFailedMail($scan, $reason));
    }
}
