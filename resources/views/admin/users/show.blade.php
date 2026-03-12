<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Admin</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    {{ $user->name }}
                </h2>
            </div>

            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">
                Back to users
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                <div class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-300">Account overview</p>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                            <p class="text-sm text-slate-300">Email</p>
                            <p class="mt-3 text-lg font-semibold">{{ $user->email }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                            <p class="text-sm text-slate-300">Role</p>
                            <p class="mt-3 text-lg font-semibold">{{ $user->is_admin ? 'Admin' : 'Standard user' }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                            <p class="text-sm text-slate-300">Owned teams</p>
                            <p class="mt-3 text-3xl font-semibold">{{ $ownedTeams->count() }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                            <p class="text-sm text-slate-300">Total scans</p>
                            <p class="mt-3 text-3xl font-semibold">{{ $user->scans()->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Workspace balances</p>
                    <div class="mt-6 space-y-3">
                        @forelse ($ownedTeams as $team)
                            <div class="rounded-[1.25rem] bg-slate-50 px-4 py-4 dark:bg-slate-800">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $team->name }}</p>
                                    <span class="text-sm font-semibold text-emerald-600">{{ $team->credit_balance }} credits</span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $team->scans->count() }} scans</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500 dark:text-slate-400">This user does not own any teams yet.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Recent scans</p>
                    <div class="mt-6 space-y-3">
                        @forelse ($recentScans as $scan)
                            <div class="rounded-[1.25rem] bg-slate-50 px-4 py-4 dark:bg-slate-800">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $scan->keyword }}</p>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:bg-slate-700 dark:text-slate-200">
                                        {{ $scan->status }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $scan->team->name }} · {{ strtoupper(str_replace('-', ' ', $scan->marketplace)) }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500 dark:text-slate-400">No scans yet.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Recent ledger activity</p>
                    <div class="mt-6 space-y-3">
                        @forelse ($recentLedgerEntries as $entry)
                            <div class="rounded-[1.25rem] bg-slate-50 px-4 py-4 dark:bg-slate-800">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $entry->type)) }}</p>
                                    <span class="text-sm font-semibold {{ $entry->amount >= 0 ? 'text-emerald-600' : 'text-orange-600' }}">
                                        {{ $entry->amount >= 0 ? '+' : '' }}{{ $entry->amount }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $entry->team->name }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500 dark:text-slate-400">No credit activity yet.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
