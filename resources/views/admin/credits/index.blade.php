<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Admin</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">Credits ledger</h2>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">Back to admin</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <form method="GET" action="{{ route('admin.credits.index') }}" class="flex w-full max-w-xl items-center gap-3">
                    <input type="search" name="search" value="{{ $search }}" placeholder="Search type, description, team, or user" class="block w-full rounded-full border border-slate-300 bg-white px-5 py-3 text-sm text-slate-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400 dark:focus:border-orange-400 dark:focus:ring-orange-400">
                    <button type="submit" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">Search</button>
                </form>

                <div class="mt-8 overflow-hidden rounded-[1.5rem] border border-slate-200 dark:border-slate-800">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-800">
                            <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4">Team</th>
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">When</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                            @forelse ($entries as $entry)
                                <tr class="text-sm text-slate-600 dark:text-slate-300">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $entry->type)) }}</p>
                                        @if ($entry->description)
                                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $entry->description }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">{{ $entry->team->name }}</td>
                                    <td class="px-6 py-4">{{ $entry->user?->name ?? 'System' }}</td>
                                    <td class="px-6 py-4"><span class="font-semibold {{ $entry->amount >= 0 ? 'text-emerald-600' : 'text-orange-600' }}">{{ $entry->amount >= 0 ? '+' : '' }}{{ $entry->amount }}</span></td>
                                    <td class="px-6 py-4">{{ $entry->created_at->format('j M Y, H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">No credit entries found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">{{ $entries->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
