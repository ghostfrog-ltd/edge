<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Admin</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">Scans</h2>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">Back to admin</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <form method="GET" action="{{ route('admin.scans.index') }}" class="grid gap-3 md:grid-cols-[1fr_220px_auto]">
                    <input type="search" name="search" value="{{ $search }}" placeholder="Search keyword, team, or user" class="block w-full rounded-full border border-slate-300 bg-white px-5 py-3 text-sm text-slate-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400 dark:focus:border-orange-400 dark:focus:ring-orange-400">
                    <select name="status" class="block w-full rounded-full border border-slate-300 bg-white px-5 py-3 text-sm text-slate-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-orange-400 dark:focus:ring-orange-400">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $scanStatus)
                            <option value="{{ $scanStatus }}" @selected($status === $scanStatus)>{{ ucfirst($scanStatus) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">Filter</button>
                </form>

                <div class="mt-8 overflow-hidden rounded-[1.5rem] border border-slate-200 dark:border-slate-800">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-800">
                            <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <th class="px-6 py-4">Keyword</th>
                                <th class="px-6 py-4">Team</th>
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Queued</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                            @forelse ($scans as $scan)
                                <tr class="text-sm text-slate-600 dark:text-slate-300">
                                    <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white">{{ $scan->keyword }}</td>
                                    <td class="px-6 py-4">{{ $scan->team->name }}</td>
                                    <td class="px-6 py-4">{{ $scan->user->name }}</td>
                                    <td class="px-6 py-4"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $scan->status }}</span></td>
                                    <td class="px-6 py-4">{{ optional($scan->queued_at ?? $scan->created_at)->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">No scans found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">{{ $scans->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
