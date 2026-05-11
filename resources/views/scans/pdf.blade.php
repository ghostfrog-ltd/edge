<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Fuzzynode Edge Report</title>
    <style>
        @page {
            margin: 28px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
        }

        h1, h2, h3, p {
            margin: 0;
        }

        .eyebrow {
            color: #ea580c;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.28em;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .lede {
            color: #475569;
            margin-bottom: 20px;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }

        .meta td {
            width: 50%;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            vertical-align: top;
        }

        .meta-label {
            display: block;
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.16em;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .section {
            margin-top: 22px;
        }

        .page-break {
            page-break-before: always;
        }

        .section-title {
            color: #0f172a;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .summary {
            border: 1px solid #fdba74;
            background: #fff7ed;
            border-radius: 10px;
            padding: 14px;
        }

        .gap-card {
            border: 1px solid #fed7aa;
            background: #fffaf5;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .gap-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .muted {
            color: #475569;
        }

        ul {
            margin: 8px 0 0 18px;
            padding: 0;
        }

        li {
            margin-bottom: 5px;
        }

        .panel {
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .evidence-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .evidence-table th,
        .evidence-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .evidence-table th {
            background: #f8fafc;
            font-size: 10px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #64748b;
        }
    </style>
</head>
<body>
    <p class="eyebrow">Fuzzynode Ebay Edge</p>
    <h1 class="page-title">{{ $scan->keyword }}</h1>
    <p class="lede">Downloadable eBay edge report generated from Fuzzynode's live evidence, schema audit, voice-of-customer signals, and gap analysis.</p>

    <table class="meta">
        <tr>
            <td>
                <span class="meta-label">Status</span>
                {{ ucfirst($scan->status) }}
            </td>
            <td>
                <span class="meta-label">Marketplace</span>
                {{ strtoupper(str_replace('-', ' ', $scan->marketplace)) }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="meta-label">Team</span>
                {{ $scan->team->name }}
            </td>
            <td>
                <span class="meta-label">Requested By</span>
                {{ $scan->user->name }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="meta-label">eBay Category ID</span>
                {{ $scan->ebay_category_id ?: 'Not provided' }}
            </td>
            <td>
                <span class="meta-label">Competitor Store</span>
                {{ $scan->competitor_store_url ?: 'Not provided' }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="meta-label">Generated</span>
                {{ optional($scan->report->generated_at ?? $scan->completed_at)->format('j M Y, H:i') }}
            </td>
            <td>
                <span class="meta-label">Evidence Listings</span>
                {{ $scan->evidenceListings->count() }}
            </td>
        </tr>
    </table>

    <div class="section">
        <h2 class="section-title">Report Summary</h2>
        <div class="summary">
            {{ $scan->report->summary }}
        </div>
    </div>

    <div class="section page-break">
        <h2 class="section-title">The Missing 3</h2>
        @forelse ($scan->report->missing_three ?? [] as $gap)
            <div class="gap-card">
                <p class="gap-title">{{ $gap['title'] ?? 'Opportunity' }}</p>
                @if (!empty($gap['why_it_matters']))
                    <p><strong>Why it matters:</strong> <span class="muted">{{ $gap['why_it_matters'] }}</span></p>
                @endif
                @if (!empty($gap['what_to_add']))
                    <p style="margin-top: 6px;"><strong>What to add:</strong> <span class="muted">{{ $gap['what_to_add'] }}</span></p>
                @endif
                @if (!empty($gap['evidence_source']))
                    <p style="margin-top: 6px;"><strong>Evidence:</strong> <span class="muted">{{ $gap['evidence_source'] }}</span></p>
                @endif
            </div>
        @empty
            <p class="muted">No Missing 3 output was stored for this scan.</p>
        @endforelse
    </div>

    <div class="section">
        <div class="panel">
            <h3 class="section-title" style="font-size: 14px; margin-bottom: 6px;">Missing Attributes</h3>
            <ul>
                @forelse ($scan->report->missing_attributes ?? [] as $item)
                    <li>{{ $item }}</li>
                @empty
                    <li class="muted">No missing attribute suggestions were stored.</li>
                @endforelse
            </ul>
        </div>

        <div class="panel">
            <h3 class="section-title" style="font-size: 14px; margin-bottom: 6px;">Listing Actions</h3>
            <ul>
                @forelse ($scan->report->listing_actions ?? [] as $item)
                    <li>{{ $item }}</li>
                @empty
                    <li class="muted">No listing actions were stored.</li>
                @endforelse
            </ul>
        </div>

        <div class="panel">
            <h3 class="section-title" style="font-size: 14px; margin-bottom: 6px;">Schema Audit</h3>
            <ul>
                @forelse ($scan->report->formattedSchemaAudit() as $item)
                    <li>
                        @if ($item['title'])
                            <strong>{{ $item['title'] }}:</strong>
                        @endif
                        {{ $item['summary'] }}
                        @if ($item['detail'])
                            <div class="muted" style="margin-top: 4px;">{{ $item['detail'] }}</div>
                        @endif
                    </li>
                @empty
                    <li class="muted">No schema findings were returned for this scan yet.</li>
                @endforelse
            </ul>
        </div>

        <div class="panel">
            <h3 class="section-title" style="font-size: 14px; margin-bottom: 6px;">VoC Insights</h3>
            <ul>
                @forelse ($scan->report->voc_insights ?? [] as $item)
                    <li>{{ is_array($item) ? ($item['label'] ?? $item['signal'] ?? json_encode($item)) : $item }}</li>
                @empty
                    <li class="muted">No voice-of-customer signals were returned for this scan yet.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">Top eBay Evidence</h2>
        <table class="evidence-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Condition</th>
                    <th>Seller</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($scan->evidenceListings->take(10) as $listing)
                    <tr>
                        <td>{{ $listing->rank }}</td>
                        <td>{{ $listing->title }}</td>
                        <td>
                            @if ($listing->price_value)
                                {{ $listing->price_currency }} {{ number_format((float) $listing->price_value, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $listing->condition ?: '-' }}</td>
                        <td>{{ $listing->seller_username ?: '-' }}</td>
                        <td>{{ $listing->category_id ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No live eBay evidence was stored for this scan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
