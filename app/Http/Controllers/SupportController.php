<?php

namespace App\Http\Controllers;

use App\Mail\SupportTicketSubmittedMail;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function show(Request $request): View
    {
        return view('support', [
            'categories' => $this->categories(),
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:billing,account,scan-report,bug,feature,other'],
            'message' => ['required', 'string', 'min:20'],
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $request->user()?->id,
            'team_id' => $request->user()?->current_team_id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        $ticket->forceFill([
            'reference' => sprintf('GF-SUP-%06d', $ticket->id),
        ])->save();

        Mail::to(config('support.recipients'))
            ->send(new SupportTicketSubmittedMail($ticket->fresh(['user', 'team'])));

        return redirect()
            ->route('support.show')
            ->with('status', "Support request {$ticket->reference} has been sent.");
    }

    protected function categories(): array
    {
        return [
            'billing' => 'Billing',
            'account' => 'Account access',
            'scan-report' => 'Scan or report issue',
            'bug' => 'Bug report',
            'feature' => 'Feature request',
            'other' => 'Other',
        ];
    }
}
