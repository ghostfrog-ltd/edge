<div style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.6;">
    <h1 style="font-size: 20px; margin-bottom: 16px;">New Fuzzynode support ticket</h1>

    <p style="margin: 0 0 12px;"><strong>Reference:</strong> {{ $ticket->reference }}</p>
    <p style="margin: 0 0 12px;"><strong>From:</strong> {{ $ticket->name }} ({{ $ticket->email }})</p>
    <p style="margin: 0 0 12px;"><strong>Category:</strong> {{ ucfirst(str_replace('-', ' ', $ticket->category)) }}</p>
    <p style="margin: 0 0 12px;"><strong>Subject:</strong> {{ $ticket->subject }}</p>

    @if ($ticket->team)
        <p style="margin: 0 0 12px;"><strong>Workspace:</strong> {{ $ticket->team->name }}</p>
    @endif

    @if ($ticket->user)
        <p style="margin: 0 0 12px;"><strong>User account:</strong> {{ $ticket->user->email }}</p>
    @endif

    <div style="margin-top: 24px; padding: 16px; border: 1px solid #cbd5e1; border-radius: 12px; background: #f8fafc;">
        {!! nl2br(e($ticket->message)) !!}
    </div>
</div>
