<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Your Fuzzynode scan is ready</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #0f172a; background: #f8fafc; margin: 0; padding: 24px;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 32px;">
        <p style="margin: 0 0 8px; font-size: 12px; letter-spacing: 0.18em; text-transform: uppercase; color: #ea580c; font-weight: 700;">Fuzzynode Ebay Edge</p>
        <h1 style="margin: 0 0 16px; font-size: 28px; color: #020617;">Your scan is ready</h1>

        <p style="margin: 0 0 16px;">Your Fuzzynode scan for <strong>{{ $scan->keyword }}</strong> has completed and the report is ready to view.</p>
        <p style="margin: 0 0 16px;">Marketplace: <strong>{{ strtoupper(str_replace('-', ' ', $scan->marketplace)) }}</strong></p>

        @if ($scan->report?->summary)
            <p style="margin: 0 0 24px;">{{ $scan->report->summary }}</p>
        @endif

        <p style="margin: 0 0 24px;">
            <a href="{{ $accessUrl }}" style="display: inline-block; background: #0f172a; color: #ffffff; text-decoration: none; padding: 12px 20px; border-radius: 9999px; font-weight: 700;">
                Open scan report
            </a>
        </p>

        <p style="margin: 24px 0 0; color: #475569;">Thanks,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
