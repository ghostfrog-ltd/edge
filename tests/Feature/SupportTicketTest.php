<?php

namespace Tests\Feature;

use App\Mail\SupportTicketSubmittedMail;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SupportTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_submit_support_ticket_and_email_is_sent_to_support_recipients(): void
    {
        Mail::fake();

        $response = $this->post(route('support.store'), [
            'name' => 'Gary Constable',
            'email' => 'garyconstable80@gmail.com',
            'subject' => 'Need help with a billing question',
            'category' => 'billing',
            'message' => 'I need help understanding why my credits did not update after a test checkout.',
        ]);

        $response->assertRedirect(route('support.show'));

        $ticket = SupportTicket::query()->first();

        $this->assertNotNull($ticket);
        $this->assertSame('open', $ticket->status);
        $this->assertStringStartsWith('GF-SUP-', (string) $ticket->reference);

        Mail::assertSent(SupportTicketSubmittedMail::class, function (SupportTicketSubmittedMail $mail) use ($ticket) {
            return $mail->ticket->is($ticket)
                && $mail->hasTo('info@ghostfrog.co.uk')
                && $mail->hasTo('garyconstable80@gmail.com');
        });
    }
}
