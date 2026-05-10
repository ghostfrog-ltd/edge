<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Admin</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    Platform dashboard
                </h2>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.test-plan') }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-5 py-2 text-sm font-semibold text-slate-700 transition hover:border-orange-300 hover:text-orange-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-orange-400 dark:hover:text-orange-300">
                    Human test plan
                </a>
                <a href="{{ route('admin.roadmap') }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                    Admin roadmap
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-5 py-2 text-sm font-semibold text-slate-700 transition hover:border-orange-300 hover:text-orange-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-orange-400 dark:hover:text-orange-300">
                    Users
                </a>
                <a href="{{ route('admin.teams.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-5 py-2 text-sm font-semibold text-slate-700 transition hover:border-orange-300 hover:text-orange-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-orange-400 dark:hover:text-orange-300">
                    Teams
                </a>
                <a href="{{ route('admin.scans.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-5 py-2 text-sm font-semibold text-slate-700 transition hover:border-orange-300 hover:text-orange-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-orange-400 dark:hover:text-orange-300">
                    Scans
                </a>
                <a href="{{ route('admin.credits.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-5 py-2 text-sm font-semibold text-slate-700 transition hover:border-orange-300 hover:text-orange-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-orange-400 dark:hover:text-orange-300">
                    Credits
                </a>
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-5 py-2 text-sm font-semibold text-slate-700 transition hover:border-orange-300 hover:text-orange-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-orange-400 dark:hover:text-orange-300">
                    Products
                </a>
                <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-5 py-2 text-sm font-semibold text-slate-700 transition hover:border-orange-300 hover:text-orange-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-orange-400 dark:hover:text-orange-300">
                    Plans
                </a>
            </div>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 text-slate-900 shadow-sm dark:border-slate-800 dark:bg-slate-950 dark:text-white dark:shadow-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Operator snapshot</p>
                <div class="mt-8 grid gap-4 md:grid-cols-3 xl:grid-cols-6">
                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                        <p class="text-sm text-slate-600 dark:text-slate-300">Users</p>
                        <p class="mt-3 text-4xl font-semibold text-slate-900 dark:text-white">{{ $stats['users'] }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                        <p class="text-sm text-slate-600 dark:text-slate-300">Teams</p>
                        <p class="mt-3 text-4xl font-semibold text-slate-900 dark:text-white">{{ $stats['teams'] }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                        <p class="text-sm text-slate-600 dark:text-slate-300">Total scans</p>
                        <p class="mt-3 text-4xl font-semibold text-slate-900 dark:text-white">{{ $stats['scans'] }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                        <p class="text-sm text-slate-600 dark:text-slate-300">Queued scans</p>
                        <p class="mt-3 text-4xl font-semibold text-slate-900 dark:text-white">{{ $stats['queuedScans'] }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                        <p class="text-sm text-slate-600 dark:text-slate-300">Completed scans</p>
                        <p class="mt-3 text-4xl font-semibold text-slate-900 dark:text-white">{{ $stats['completedScans'] }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                        <p class="text-sm text-slate-600 dark:text-slate-300">Net credits</p>
                        <p class="mt-3 text-4xl font-semibold text-slate-900 dark:text-white">{{ $stats['credits'] }}</p>
                    </div>
                </div>
            </section>

            <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
                <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Worker health</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Engine and queue monitoring</h3>
                        </div>
                        <span class="rounded-full px-4 py-2 text-sm font-semibold uppercase tracking-[0.2em] {{ $engineHealth['status'] === 'healthy' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : ($engineHealth['status'] === 'degraded' ? 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300') }}">
                            {{ str_replace('_', ' ', $engineHealth['status']) }}
                        </span>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800/80">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Engine provider</p>
                            <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ strtoupper($engineHealth['provider'] ?? 'unknown') }}</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $engineHealth['model'] ?? 'No model reported' }}</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800/80">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Queue backlog</p>
                            <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $queueStats['backlog'] }}</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $queueStats['processingScans'] }} processing · {{ $queueStats['dispatchingScans'] }} dispatching</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800/80">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Engine throughput</p>
                            <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $engineHealth['dispatches_total'] ?? 0 }}</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $engineHealth['callbacks_completed'] ?? 0 }} callbacks completed · {{ $engineHealth['callbacks_failed'] ?? 0 }} failed</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800/80">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Failure watch</p>
                            <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">{{ $queueStats['failedJobs'] }}</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $queueStats['failedScansToday'] }} failed scans in the last 24 hours</p>
                        </div>
                    </div>

                    <div class="mt-5 rounded-[1.5rem] border border-slate-200 px-5 py-5 dark:border-slate-800">
                        @if ($engineHealth['reachable'])
                            <div class="grid gap-3 text-sm text-slate-600 dark:text-slate-300 md:grid-cols-2">
                                <p>Version: <span class="font-semibold text-slate-900 dark:text-white">{{ $engineHealth['version'] }}</span></p>
                                <p>Active engine jobs: <span class="font-semibold text-slate-900 dark:text-white">{{ $engineHealth['active_jobs'] }}</span></p>
                                <p>Simulated delay: <span class="font-semibold text-slate-900 dark:text-white">{{ $engineHealth['simulated_delay_seconds'] }}s</span></p>
                                <p>Last duration: <span class="font-semibold text-slate-900 dark:text-white">{{ $engineHealth['last_job_duration_ms'] ?? 'N/A' }} ms</span></p>
                                <p>Last dispatch: <span class="font-semibold text-slate-900 dark:text-white">{{ $engineHealth['last_dispatch_at'] ?? 'N/A' }}</span></p>
                                <p>Last completion: <span class="font-semibold text-slate-900 dark:text-white">{{ $engineHealth['last_completed_at'] ?? 'N/A' }}</span></p>
                            </div>

                            @if ($engineHealth['last_callback_error'])
                                <p class="mt-4 rounded-[1.25rem] border border-orange-200 bg-orange-50 px-4 py-4 text-sm text-orange-800 dark:border-orange-900 dark:bg-orange-500/10 dark:text-orange-300">
                                    Latest callback issue: {{ $engineHealth['last_callback_error'] }}
                                </p>
                            @endif
                        @else
                            <p class="text-sm text-rose-700 dark:text-rose-300">{{ $engineHealth['error'] }}</p>
                        @endif
                    </div>
                </section>

                <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Report quality loop</p>
                    <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Confidence, feedback, and review pressure</h3>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800/80">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Average quality</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ $qualityStats['averageQuality'] }}/100</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800/80">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Average confidence</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ $qualityStats['averageConfidence'] }}/100</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800/80">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Helpful feedback</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ $qualityStats['helpful'] }}</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800/80">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Needs review</p>
                            <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">{{ $qualityStats['needsReview'] }}</p>
                        </div>
                    </div>

                    <div class="mt-5 rounded-[1.5rem] border border-slate-200 px-5 py-5 dark:border-slate-800">
                        <div class="grid gap-3 text-sm text-slate-600 dark:text-slate-300 md:grid-cols-3">
                            <p>Total reports: <span class="font-semibold text-slate-900 dark:text-white">{{ $qualityStats['reports'] }}</span></p>
                            <p>Awaiting feedback: <span class="font-semibold text-slate-900 dark:text-white">{{ $qualityStats['awaitingFeedback'] }}</span></p>
                            <p>Not helpful: <span class="font-semibold text-slate-900 dark:text-white">{{ $qualityStats['notHelpful'] }}</span></p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse ($qualityAlerts as $report)
                            <div class="rounded-[1.25rem] bg-slate-50 px-4 py-4 dark:bg-slate-800/80">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $report->scan?->keyword ?? 'Unknown scan' }}</p>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $report->scan?->team?->name ?? 'Unknown team' }} · {{ str_replace('_', ' ', $report->qualityLoopState()) }}</p>
                                    </div>
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 shadow-sm dark:bg-slate-950 dark:text-slate-300">
                                        {{ $report->qualityLoopScore() }}/100
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="rounded-[1.25rem] bg-slate-50 px-4 py-4 text-sm text-slate-500 dark:bg-slate-800/80 dark:text-slate-400">No quality alerts right now.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="grid gap-6 xl:grid-cols-[1.5fr_1fr]">
                <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Recent scans</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Latest scan activity across the platform</h3>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse ($recentScans as $scan)
                            <div class="rounded-[1.5rem] border border-slate-200 px-5 py-4 dark:border-slate-800">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $scan->keyword }}</p>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                            {{ $scan->team->name }} · {{ $scan->user->name }} · {{ strtoupper(str_replace('-', ' ', $scan->marketplace)) }}
                                        </p>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                        {{ $scan->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-[1.5rem] border border-dashed border-slate-300 px-5 py-10 text-center text-slate-500 dark:border-slate-700 dark:text-slate-400">
                                No scan activity yet.
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Recent teams</p>
                        <div class="mt-6 space-y-3">
                            @forelse ($recentTeams as $team)
                                <div class="rounded-[1.25rem] bg-slate-50 px-4 py-4 dark:bg-slate-800">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $team->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $team->owner?->name ?? 'No owner' }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500 dark:text-slate-400">No teams yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Credit ledger</p>
                        <div class="mt-6 space-y-3">
                            @forelse ($recentLedgerEntries as $entry)
                                <div class="rounded-[1.25rem] bg-slate-50 px-4 py-4 dark:bg-slate-800">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $entry->type)) }}</p>
                                        <span class="text-sm font-semibold {{ $entry->amount >= 0 ? 'text-emerald-600' : 'text-orange-600' }}">
                                            {{ $entry->amount >= 0 ? '+' : '' }}{{ $entry->amount }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $entry->team->name }}{{ $entry->user ? ' · '.$entry->user->name : '' }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500 dark:text-slate-400">No ledger activity yet.</p>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
