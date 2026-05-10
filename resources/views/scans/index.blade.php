<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">History</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    Scan history
                </h2>
            </div>
            <a href="{{ route('scans.create') }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
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
                                <th class="px-6 py-4">Trigger</th>
                                <th class="px-6 py-4">Marketplace</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Credits</th>
                                <th class="px-6 py-4">Queued</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                            @forelse ($scans as $scan)
                                <tr class="text-sm text-slate-600 dark:text-slate-300">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('scans.show', $scan) }}" class="font-semibold text-slate-900 hover:text-orange-600 dark:text-white dark:hover:text-orange-300">
                                            {{ $scan->triggerLabel() }}
                                        </a>
                                        <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                            {{ str_replace('_', ' ', $scan->scan_type ?? 'keyword') }}
                                            @if ($scan->category)
                                                · {{ $scan->category }}
                                            @endif
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">{{ strtoupper(str_replace('-', ' ', $scan->marketplace)) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                            {{ $scan->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $scan->reserved_credits }}</td>
                                    <td class="px-6 py-4">{{ optional($scan->queued_at ?? $scan->created_at)->diffForHumans() }}</td>
                                    <td class="px-6 py-4 text-right">
                                        @if ($scan->status === 'failed')
                                            <form method="POST" action="{{ route('scans.retry', $scan) }}">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center rounded-full border border-orange-300 bg-orange-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-orange-700 transition hover:border-orange-400 hover:bg-orange-100 dark:border-orange-500/40 dark:bg-orange-500/10 dark:text-orange-300 dark:hover:border-orange-400 dark:hover:bg-orange-500/20">
                                                    Retry
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('scans.show', $scan) }}" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 transition hover:text-orange-600 dark:text-slate-400 dark:hover:text-orange-300">
                                                View
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">No scans yet for this team.</td>
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
