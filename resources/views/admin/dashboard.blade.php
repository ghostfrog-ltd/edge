<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Admin</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    Platform dashboard
                </h2>
            </div>

            <a href="{{ route('admin.roadmap') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">
                Admin roadmap
            </a>
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

            <section class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-300">Operator snapshot</p>
                <div class="mt-8 grid gap-4 md:grid-cols-3 xl:grid-cols-6">
                    <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                        <p class="text-sm text-slate-300">Users</p>
                        <p class="mt-3 text-4xl font-semibold">{{ $stats['users'] }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                        <p class="text-sm text-slate-300">Teams</p>
                        <p class="mt-3 text-4xl font-semibold">{{ $stats['teams'] }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                        <p class="text-sm text-slate-300">Total scans</p>
                        <p class="mt-3 text-4xl font-semibold">{{ $stats['scans'] }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                        <p class="text-sm text-slate-300">Queued scans</p>
                        <p class="mt-3 text-4xl font-semibold">{{ $stats['queuedScans'] }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                        <p class="text-sm text-slate-300">Completed scans</p>
                        <p class="mt-3 text-4xl font-semibold">{{ $stats['completedScans'] }}</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                        <p class="text-sm text-slate-300">Net credits</p>
                        <p class="mt-3 text-4xl font-semibold">{{ $stats['credits'] }}</p>
                    </div>
                </div>
            </section>

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
