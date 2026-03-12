<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Scan detail</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    {{ $scan->keyword }}
                </h2>
            </div>
            <a href="{{ route('scans.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">
                Back to history
            </a>
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
                        <dt class="text-sm text-slate-500">Active team</dt>
                        <dd class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $scan->team->name }}</dd>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 p-5">
                        <dt class="text-sm text-slate-500">Requested by</dt>
                        <dd class="mt-2 text-lg font-semibold text-slate-900">{{ $scan->user->name }}</dd>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 p-5">
                        <dt class="text-sm text-slate-500">Reserved credits</dt>
                        <dd class="mt-2 text-lg font-semibold text-slate-900">{{ $scan->reserved_credits }}</dd>
                    </div>
                    <div class="rounded-[1.5rem] bg-slate-50 p-5">
                        <dt class="text-sm text-slate-500">Queued at</dt>
                        <dd class="mt-2 text-lg font-semibold text-slate-900">{{ optional($scan->queued_at ?? $scan->created_at)->format('j M Y, H:i') }}</dd>
                    </div>
                </dl>

                @if ($scan->notes)
                    <div class="mt-6 rounded-[1.5rem] border border-slate-200 px-5 py-4">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Notes</p>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $scan->notes }}</p>
                    </div>
                @endif

                <div class="mt-6 rounded-[1.5rem] border border-dashed border-slate-300 px-5 py-6">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Report status</p>
                    @if ($scan->report)
                        <p class="mt-3 text-base font-semibold text-slate-900">Report generated</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $scan->report->summary }}</p>
                    @else
                        <p class="mt-3 text-base font-semibold text-slate-900">Awaiting Python analysis</p>
                        <p class="mt-2 text-sm text-slate-600">The request is stored correctly. Next we’ll let the worker pick this up, process competitor data, and write the first report back here.</p>
                    @endif
                </div>
            </section>

            <aside class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-300">Pipeline states</p>
                <div class="mt-6 space-y-4 text-sm">
                    <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-4">
                        <p class="font-semibold text-white">Queued</p>
                        <p class="mt-2 text-slate-300">Saved in Laravel with team, user, marketplace, and notes.</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-4">
                        <p class="font-semibold text-white">Processing</p>
                        <p class="mt-2 text-slate-300">Future FastAPI worker claims the scan and starts external analysis.</p>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-4">
                        <p class="font-semibold text-white">Completed</p>
                        <p class="mt-2 text-slate-300">Scan report is written and credits move from reserved to consumed.</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
