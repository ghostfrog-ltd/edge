<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Admin</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    Operator roadmap
                </h2>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">
                Back to admin
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Why this page exists</p>
                <p class="mt-4 max-w-4xl text-base leading-8 text-slate-600 dark:text-slate-300">
                    This is the admin-side memory page so we can keep track of operator work before moving into the Python brain. It keeps the internal roadmap separate from customer-facing pages and makes the next steps obvious.
                </p>
            </section>

            <div class="grid gap-6">
                @foreach ($areas as $area)
                    @php($completed = collect($area['tasks'])->where('complete', true)->count())
                    <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">{{ $area['title'] }}</p>
                                <p class="mt-3 text-base leading-8 text-slate-600 dark:text-slate-300">{{ $area['summary'] }}</p>
                            </div>
                            <div class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                {{ $completed }}/{{ count($area['tasks']) }} complete
                            </div>
                        </div>

                        <div class="mt-6 grid gap-3">
                            @foreach ($area['tasks'] as $task)
                                <div class="flex items-center gap-3 rounded-[1.25rem] border px-4 py-4 {{ $task['complete'] ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-900 dark:bg-emerald-500/10' : 'border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-800/70' }}">
                                    @if ($task['complete'])
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-white">
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.25 7.25a1 1 0 01-1.415 0l-3-3a1 1 0 111.414-1.42l2.293 2.294 6.543-6.544a1 1 0 011.415 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    @else
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-900">
                                            <span class="h-3 w-3 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                        </span>
                                    @endif

                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $task['title'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
