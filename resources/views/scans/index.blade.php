<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">History</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    Scan history
                </h2>
            </div>
            <a href="{{ route('scans.create') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-600">
                New scan
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">{{ $team->name }}</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">{{ $scans->total() }} scans captured</h3>
                    </div>
                    <div class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        {{ $team->credit_balance }} credits remaining
                    </div>
                </div>

                <div class="mt-8 overflow-hidden rounded-[1.5rem] border border-slate-200 dark:border-slate-800">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50 dark:bg-slate-800">
                            <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <th class="px-6 py-4">Keyword</th>
                                <th class="px-6 py-4">Marketplace</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Credits</th>
                                <th class="px-6 py-4">Queued</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                            @forelse ($scans as $scan)
                                <tr class="text-sm text-slate-600 dark:text-slate-300">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('scans.show', $scan) }}" class="font-semibold text-slate-900 hover:text-orange-600 dark:text-white dark:hover:text-orange-300">
                                            {{ $scan->keyword }}
                                        </a>
                                        @if ($scan->category)
                                            <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">{{ $scan->category }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">{{ strtoupper(str_replace('-', ' ', $scan->marketplace)) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                            {{ $scan->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $scan->reserved_credits }}</td>
                                    <td class="px-6 py-4">{{ optional($scan->queued_at ?? $scan->created_at)->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">No scans yet for this team.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $scans->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
