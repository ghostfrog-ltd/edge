<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Ghostfrog Ebay Edge</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    {{ $team->name }}
                </h2>
            </div>

            <a
                href="{{ route('scans.create') }}"
                class="inline-flex items-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-600"
            >
                Queue a scan
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-500/10 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            <div>
                <section class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-300">Workspace snapshot</p>
                    <div class="mt-8 grid gap-4 md:grid-cols-3">
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                            <p class="text-sm text-slate-300">Available credits</p>
                            <p class="mt-3 text-4xl font-semibold">{{ $team->credit_balance }}</p>
                            <p class="mt-2 text-sm text-slate-400">Starter balance is live until Stripe top-ups arrive.</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                            <p class="text-sm text-slate-300">Queued or running</p>
                            <p class="mt-3 text-4xl font-semibold">{{ $scanCounts['queued'] }}</p>
                            <p class="mt-2 text-sm text-slate-400">This is the handoff lane for the future Python brain.</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                            <p class="text-sm text-slate-300">Completed scans</p>
                            <p class="mt-3 text-4xl font-semibold">{{ $scanCounts['completed'] }}</p>
                            <p class="mt-2 text-sm text-slate-400">Reports will land here once the analysis service is wired.</p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-[1.7fr_1fr]">
                <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Recent scans</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">What this team has queued</h3>
                        </div>
                        <a href="{{ route('scans.index') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700 dark:text-orange-300 dark:hover:text-orange-200">View all</a>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse ($recentScans as $scan)
                            <a href="{{ route('scans.show', $scan) }}" class="block rounded-[1.5rem] border border-slate-200 px-5 py-4 transition hover:border-orange-300 hover:bg-orange-50/40 dark:border-slate-800 dark:hover:border-orange-500 dark:hover:bg-orange-500/10">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $scan->keyword }}</p>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                            {{ strtoupper(str_replace('-', ' ', $scan->marketplace)) }}
                                            @if ($scan->category)
                                                · {{ $scan->category }}
                                            @endif
                                        </p>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                        {{ $scan->status }}
                                    </span>
                                </div>
                                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">
                                    Queued {{ optional($scan->queued_at ?? $scan->created_at)->diffForHumans() }} · Reserved {{ $scan->reserved_credits }} credit
                                </p>
                            </a>
                        @empty
                            <div class="rounded-[1.5rem] border border-dashed border-slate-300 px-5 py-10 text-center">
                                <p class="text-lg font-semibold text-slate-900 dark:text-white">No scans yet</p>
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Queue your first niche search to test the Laravel-side flow.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Credit activity</p>
                    <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Latest ledger events</h3>

                    <div class="mt-6 space-y-4">
                        @foreach ($recentCredits as $entry)
                            <div class="rounded-[1.5rem] bg-slate-50 px-4 py-4 dark:bg-slate-800/80">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $entry->type)) }}</p>
                                    <span class="text-sm font-semibold {{ $entry->amount >= 0 ? 'text-emerald-600' : 'text-orange-600' }}">
                                        {{ $entry->amount >= 0 ? '+' : '' }}{{ $entry->amount }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $entry->description }}</p>
                                <p class="mt-2 text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">{{ $entry->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
