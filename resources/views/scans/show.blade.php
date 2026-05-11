<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Scan detail</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    {{ $scan->keyword }}
                </h2>
            </div>
            <div class="flex items-center gap-3">
                @if ($scan->status === 'completed' && $scan->report)
                    <a href="{{ route('scans.pdf', $scan) }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                        Download PDF
                    </a>
                @endif
                @if ($scan->status === 'failed')
                    <form method="POST" action="{{ route('scans.retry', $scan) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-full border border-orange-300 bg-orange-50 px-4 py-2 text-sm font-semibold text-orange-700 transition hover:border-orange-400 hover:bg-orange-100 dark:border-orange-500/40 dark:bg-orange-500/10 dark:text-orange-300 dark:hover:border-orange-400 dark:hover:bg-orange-500/20">
                            Retry scan
                        </button>
                    </form>
                @endif
                <a href="{{ route('scans.index') }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                    Back to history
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-6xl gap-6 sm:px-6 lg:grid-cols-[1.4fr_0.9fr] lg:px-8">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                @if (session('status'))
                    <div class="mb-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-500/10 dark:text-emerald-300">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="flex flex-wrap items-center gap-3">
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                        {{ $scan->status }}
                    </span>
                    <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-orange-700">
                        {{ strtoupper(str_replace('-', ' ', $scan->marketplace)) }}
                    </span>
                </div>

                <dl class="mt-8 grid gap-5 md:grid-cols-2">
                    <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800">
                        <dt class="text-sm text-slate-500 dark:text-slate-400">Scan trigger</dt>
                        <dd class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ ucwords(str_replace('_', ' ', $scan->scan_type ?? 'keyword')) }}</dd>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800">
                        <dt class="text-sm text-slate-500">Active team</dt>
                        <dd class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $scan->team->name }}</dd>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800">
                        <dt class="text-sm text-slate-500 dark:text-slate-400">Requested by</dt>
                        <dd class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $scan->user->name }}</dd>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800">
                        <dt class="text-sm text-slate-500 dark:text-slate-400">Reserved credits</dt>
                        <dd class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $scan->reserved_credits }}</dd>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800">
                        <dt class="text-sm text-slate-500 dark:text-slate-400">Queued at</dt>
                        <dd class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ optional($scan->queued_at ?? $scan->created_at)->format('j M Y, H:i') }}</dd>
                    </div>
                    @if ($scan->ebay_category_id)
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">eBay category ID</dt>
                            <dd class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $scan->ebay_category_id }}</dd>
                        </div>
                    @endif
                    @if ($scan->competitor_store_url)
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800">
                            <dt class="text-sm text-slate-500 dark:text-slate-400">Competitor store</dt>
                            <dd class="mt-2 text-lg font-semibold text-slate-900 dark:text-white break-all">{{ $scan->competitor_store_url }}</dd>
                        </div>
                    @endif
                    <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800">
                        <dt class="text-sm text-slate-500 dark:text-slate-400">Engine job id</dt>
                        <dd class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $scan->engine_job_id ?? 'Waiting for dispatch' }}</dd>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800">
                        <dt class="text-sm text-slate-500 dark:text-slate-400">Engine dispatched</dt>
                        <dd class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ optional($scan->engine_dispatched_at)->format('j M Y, H:i') ?? 'Not yet' }}</dd>
                    </div>
                </dl>

                @if ($scan->notes)
                    <div class="mt-6 rounded-[1.5rem] border border-slate-200 px-5 py-4 dark:border-slate-800">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Notes</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $scan->notes }}</p>
                    </div>
                @endif

                <div class="mt-6 rounded-[1.5rem] border border-dashed border-slate-300 px-5 py-6 dark:border-slate-700">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Report status</p>
                    @if ($scan->report)
                        @php($reportMeta = $scan->report->report_meta ?? [])
                        <p class="mt-3 text-base font-semibold text-slate-900 dark:text-white">Report generated</p>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $scan->report->summary }}</p>

                        <div class="mt-6 rounded-[1.25rem] border border-orange-200 bg-orange-50 p-5 dark:border-orange-900 dark:bg-orange-500/10">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-700 dark:text-orange-300">The Missing 3</p>
                            <div class="mt-4 space-y-4">
                                @foreach ($scan->report->missing_three ?? [] as $gap)
                                    <div class="rounded-[1.25rem] border border-orange-200/80 bg-white/80 p-4 dark:border-orange-900/60 dark:bg-slate-900/60">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $gap['title'] }}</p>
                                            <span class="rounded-full bg-orange-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-orange-700 dark:bg-orange-500/20 dark:text-orange-300">
                                                {{ $gap['evidence_source'] }}
                                            </span>
                                            @if (! empty($gap['priority_score']))
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                    {{ $gap['priority_score'] }}/100
                                                </span>
                                            @endif
                                        </div>
                                        <p class="mt-3 text-sm text-slate-700 dark:text-slate-200">{{ $gap['why_it_matters'] }}</p>
                                        <p class="mt-3 text-sm font-medium text-slate-900 dark:text-white">{{ $gap['what_to_add'] }}</p>
                                        @if (! empty($gap['ranking_reason']))
                                            <p class="mt-3 text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">{{ $gap['ranking_reason'] }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 rounded-[1.25rem] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-800/70">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Ranking and quality loop</p>
                                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                                        {{ $scan->report->rankingRationale() ?? 'Fuzzynode ranks these gaps using the live eBay evidence, schema audit, and buyer-friction signals.' }}
                                    </p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] {{ $scan->report->qualityLoopScore() >= 85 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : ($scan->report->qualityLoopScore() >= 70 ? 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300') }}">
                                    {{ $scan->report->qualityBand() }}
                                </span>
                            </div>

                            <div class="mt-5 grid gap-4 md:grid-cols-3">
                                <div class="rounded-[1.25rem] bg-white p-4 dark:bg-slate-900">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Quality loop score</p>
                                    <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $scan->report->qualityLoopScore() }}/100</p>
                                </div>
                                <div class="rounded-[1.25rem] bg-white p-4 dark:bg-slate-900">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Confidence score</p>
                                    <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $scan->report->confidenceScore() }}/100</p>
                                </div>
                                <div class="rounded-[1.25rem] bg-white p-4 dark:bg-slate-900">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Feedback state</p>
                                    <p class="mt-3 text-lg font-semibold text-slate-900 dark:text-white">{{ str_replace('_', ' ', $scan->report->qualityLoopState()) }}</p>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">
                                <span>Summary: {{ data_get($reportMeta, 'llm_provider', 'Unknown') }}</span>
                                <span>Ranking: {{ data_get($reportMeta, 'ranking_provider', 'Unknown') }}</span>
                                <span>Evidence: {{ data_get($reportMeta, 'evidence_count', 0) }}</span>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col gap-4">
                            <div class="min-w-0 rounded-[1.25rem] bg-slate-50 p-4 dark:bg-slate-800">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Missing attributes</p>
                                <ul class="mt-3 space-y-2 text-sm text-slate-700 dark:text-slate-200">
                                    @foreach ($scan->report->missing_attributes ?? [] as $attribute)
                                        <li>{{ $attribute }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="min-w-0 rounded-[1.25rem] bg-slate-50 p-4 dark:bg-slate-800">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Schema audit</p>
                                @if (! empty($scan->report->schema_audit))
                                    <ul class="mt-3 space-y-3 text-sm text-slate-700 dark:text-slate-200">
                                        @foreach ($scan->report->schema_audit as $finding)
                                            <li>
                                                <p class="font-semibold text-slate-900 dark:text-white">{{ $finding['aspect_name'] }}</p>
                                                <p class="mt-1 text-slate-600 dark:text-slate-300">{{ $finding['headline'] }}</p>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">No schema findings were returned for this scan yet.</p>
                                @endif
                            </div>
                            <div class="min-w-0 rounded-[1.25rem] bg-slate-50 p-4 dark:bg-slate-800">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">VoC insights</p>
                                @if (! empty($scan->report->voc_insights))
                                    <ul class="mt-3 space-y-2 text-sm text-slate-700 dark:text-slate-200">
                                        @foreach ($scan->report->voc_insights as $insight)
                                            <li>{{ $insight }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">No voice-of-customer signals were returned for this scan yet.</p>
                                @endif
                            </div>
                            <div class="min-w-0 rounded-[1.25rem] bg-slate-50 p-4 dark:bg-slate-800">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Competitor insights</p>
                                <ul class="mt-3 space-y-2 text-sm text-slate-700 dark:text-slate-200">
                                    @foreach ($scan->report->competitor_insights ?? [] as $insight)
                                        <li>{{ $insight }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="min-w-0 rounded-[1.25rem] bg-slate-50 p-4 dark:bg-slate-800">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Listing actions</p>
                                <ul class="mt-3 space-y-2 text-sm text-slate-700 dark:text-slate-200">
                                    @foreach ($scan->report->listing_actions ?? [] as $action)
                                        <li>{{ $action }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @elseif ($scan->status === 'failed')
                        <p class="mt-3 text-base font-semibold text-slate-900 dark:text-white">Scan failed</p>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $scan->failure_reason ?? 'The engine could not complete this scan.' }}</p>
                        @if ($scan->hasRefundedReservation())
                            <div class="mt-4 rounded-[1.25rem] border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900 dark:bg-emerald-500/10">
                                <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">Your reserved credit has been refunded automatically.</p>
                            </div>
                        @endif
                    @else
                        <p class="mt-3 text-base font-semibold text-slate-900 dark:text-white">Engine bridge active</p>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Laravel has queued the dispatch job. The FastAPI engine will accept the handoff, simulate analysis, and callback here with the first structured report.</p>
                    @endif
                </div>

                <div class="mt-6 rounded-[1.5rem] border border-slate-200 px-5 py-6 dark:border-slate-800">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Live eBay evidence</p>
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                                Step 1 keeps the top active listings we pulled from eBay so the report has visible evidence behind it.
                            </p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            {{ $scan->evidenceListings->count() }} listing{{ $scan->evidenceListings->count() === 1 ? '' : 's' }}
                        </span>
                    </div>

                    @if ($scan->evidenceListings->isNotEmpty())
                        <div class="mt-5 space-y-3">
                            @foreach ($scan->evidenceListings->take(10) as $listing)
                                <div class="flex flex-col gap-4 rounded-[1.25rem] bg-slate-50 p-4 dark:bg-slate-800/80 md:flex-row md:items-start">
                                    <div class="flex items-start gap-4 md:w-full">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white dark:bg-slate-700">
                                            {{ $listing->rank }}
                                        </div>

                                        @if ($listing->image_url)
                                            <img src="{{ $listing->image_url }}" alt="" class="h-20 w-20 rounded-2xl object-cover">
                                        @endif

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $listing->title }}</p>
                                                @if ($listing->buying_options)
                                                    @foreach ($listing->buying_options as $buyingOption)
                                                        <span class="rounded-full bg-white px-2 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500 shadow-sm dark:bg-slate-900 dark:text-slate-300">{{ str_replace('_', ' ', $buyingOption) }}</span>
                                                    @endforeach
                                                @endif
                                            </div>

                                            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-2 text-sm text-slate-600 dark:text-slate-300">
                                                @if ($listing->price_value)
                                                    <span>{{ $listing->price_currency }} {{ number_format((float) $listing->price_value, 2) }}</span>
                                                @endif
                                                @if ($listing->condition)
                                                    <span>{{ $listing->condition }}</span>
                                                @endif
                                                @if ($listing->seller_username)
                                                    <span>Seller: {{ $listing->seller_username }}</span>
                                                @endif
                                                @if ($listing->seller_feedback_percentage)
                                                    <span>{{ $listing->seller_feedback_percentage }}% positive</span>
                                                @endif
                                                @if ($listing->category_id)
                                                    <span>Category {{ $listing->category_id }}</span>
                                                @endif
                                            </div>

                                            @if ($listing->shipping_summary)
                                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $listing->shipping_summary }}</p>
                                            @endif

                                            @if ($listing->item_web_url)
                                                <a href="{{ $listing->item_web_url }}" target="_blank" rel="noreferrer" class="mt-3 inline-flex text-sm font-semibold text-orange-600 transition hover:text-orange-500 dark:text-orange-300 dark:hover:text-orange-200">
                                                    Open listing
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($scan->evidenceListings->count() > 10)
                            <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">
                                Showing the first 10 of {{ $scan->evidenceListings->count() }} stored eBay listings for this scan.
                            </p>
                        @endif
                    @elseif (in_array($scan->status, ['queued', 'dispatching', 'processing'], true))
                        <p class="mt-4 text-sm text-slate-600 dark:text-slate-300">Fuzzynode is fetching the top live listings from eBay for this scan.</p>
                    @elseif ($scan->status === 'completed')
                        <p class="mt-4 text-sm text-slate-600 dark:text-slate-300">No live eBay evidence was stored for this scan. That usually means it completed before Step 1 was enabled or the evidence fetch did not run.</p>
                    @else
                        <p class="mt-4 text-sm text-slate-600 dark:text-slate-300">Evidence will appear here after the Step 1 live eBay fetch runs.</p>
                    @endif
                </div>

                @if ($scan->report)
                    <div class="mt-6 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-800/80">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Report feedback</p>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Tell us whether this eBay report was useful. We will use that signal to improve future report quality.</p>

                        <form method="POST" action="{{ route('scans.feedback', $scan) }}" class="mt-4 space-y-4">
                            @csrf

                            <div class="flex flex-wrap gap-3">
                                <button type="submit" name="feedback_rating" value="helpful" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ ($scan->report->feedback_rating ?? null) === 'helpful' ? 'border-emerald-400 bg-emerald-50 text-emerald-700 dark:border-emerald-400 dark:bg-emerald-500/10 dark:text-emerald-300' : 'border-slate-300 bg-white text-slate-700 hover:border-emerald-300 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-emerald-400 dark:hover:text-emerald-300' }}">
                                    Helpful
                                </button>
                                <button type="submit" name="feedback_rating" value="not_helpful" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ ($scan->report->feedback_rating ?? null) === 'not_helpful' ? 'border-orange-400 bg-orange-50 text-orange-700 dark:border-orange-400 dark:bg-orange-500/10 dark:text-orange-300' : 'border-slate-300 bg-white text-slate-700 hover:border-orange-300 hover:text-orange-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-orange-400 dark:hover:text-orange-300' }}">
                                    Not helpful
                                </button>
                            </div>

                            <div>
                                <label for="feedback_notes" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Optional note</label>
                                <textarea id="feedback_notes" name="feedback_notes" rows="3" class="mt-2 block w-full rounded-2xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:focus:border-orange-400 dark:focus:ring-orange-400" placeholder="What made this eBay report useful or weak?">{{ old('feedback_notes', $scan->report->feedback_notes) }}</textarea>
                                <x-input-error for="feedback_notes" class="mt-2" />
                            </div>

                            @if ($scan->report->feedback_submitted_at)
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Last feedback saved {{ $scan->report->feedback_submitted_at->diffForHumans() }}.
                                </p>
                            @endif
                        </form>
                    </div>
                @endif
            </section>

            <aside class="rounded-[2rem] border border-slate-200 bg-white p-8 text-slate-900 shadow-sm dark:border-slate-800 dark:bg-slate-950 dark:text-white dark:shadow-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Pipeline states</p>
                <div class="mt-6 space-y-4 text-sm">
                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                        <p class="font-semibold text-slate-900 dark:text-white">Queued</p>
                        <p class="mt-2 text-slate-600 dark:text-slate-300">Saved in Laravel with team, user, marketplace, and notes.</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                        <p class="font-semibold text-slate-900 dark:text-white">Processing</p>
                        <p class="mt-2 text-slate-600 dark:text-slate-300">FastAPI accepts the scan, simulates analysis, and prepares the callback payload.</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                        <p class="font-semibold text-slate-900 dark:text-white">Completed</p>
                        <p class="mt-2 text-slate-600 dark:text-slate-300">Callback writes the report into Laravel and the reserved credit becomes a successful scan.</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
