<?php

namespace App\Http\Controllers;

use App\Models\InboxNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InboxNotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifications.index', [
            'notifications' => $request->user()
                ->inboxNotifications()
                ->latest()
                ->paginate(12),
        ]);
    }

    public function read(Request $request, InboxNotification $notification): RedirectResponse
    {
        abort_unless($notification->user->is($request->user()), 404);

        $notification->forceFill([
            'read_at' => $notification->read_at ?? now(),
        ])->save();

        if ($request->boolean('stay')) {
            return redirect()->route('notifications.index');
        }

        return redirect($notification->action_url ?: route('notifications.index'));
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'count' => $request->user()->unreadInboxNotifications()->count(),
        ]);
    }
}
