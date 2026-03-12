<x-guest-layout
    title="How Ghostfrog Works | Ghostfrog Ebay Edge"
    meta-description="Learn how Ghostfrog Ebay Edge works, what one scan credit buys, how the Python brain and LLM reasoning fit together, and what is on the roadmap."
>
    <div class="bg-slate-100 py-10 dark:bg-slate-950">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <section class="px-4 sm:px-0">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">How it works</p>
                        <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                            How Ghostfrog works
                        </h2>
                    </div>
                    @auth
                        <a href="{{ route('scans.create') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">
                            Open scan intake
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">
                            Create account
                        </a>
                    @endauth
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-[1.4fr_0.9fr]">
                <div class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-300">Mission</p>
                    <h3 class="mt-4 text-3xl font-semibold leading-tight">Ghostfrog SaaS Factory</h3>
                    <p class="mt-6 max-w-3xl text-base leading-8 text-slate-300">{{ $brief['mission'] }}</p>
                    <p class="mt-4 max-w-3xl text-base leading-8 text-slate-300">{{ $brief['product'] }}</p>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Commercial shape</p>
                    <p class="mt-4 text-base leading-8 text-slate-600 dark:text-slate-300">{{ $brief['businessModel'] }}</p>

                    @php($completedCount = collect($tasks)->where('complete', true)->count())

                    <div class="mt-8 rounded-[1.5rem] bg-orange-50 p-5 dark:bg-orange-500/10">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-700 dark:text-orange-300">Progress</p>
                        <p class="mt-3 text-4xl font-semibold text-slate-900 dark:text-white">{{ $completedCount }}/{{ count($tasks) }}</p>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Core milestones visually tracked so the app story stays easy to follow.</p>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                @foreach ($pillars as $pillar)
                    <article class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">{{ $pillar['title'] }}</p>
                        <p class="mt-4 text-base leading-8 text-slate-600 dark:text-slate-300">{{ $pillar['description'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <article class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">What the Python brain does</p>
                    <p class="mt-4 text-base leading-8 text-slate-600 dark:text-slate-300">{{ $brief['pythonBrain'] }}</p>
                </article>

                <article class="rounded-[2rem] border border-orange-200 bg-orange-50 p-8 shadow-sm dark:border-orange-900 dark:bg-orange-500/10">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-700 dark:text-orange-300">What one credit buys</p>
                    <p class="mt-4 text-base leading-8 text-slate-700 dark:text-slate-200">{{ $brief['creditValue'] }}</p>
                </article>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">How the brain works</p>
                <p class="mt-4 max-w-5xl text-base leading-8 text-slate-600 dark:text-slate-300">{{ $brief['pythonBrainHow'] }}</p>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Roadmap</p>
                        <h3 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">Tasks and delivery status</h3>
                    </div>
                    <div class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        {{ $completedCount }} complete
                    </div>
                </div>

                <div class="mt-8 grid gap-4">
                    @foreach ($tasks as $task)
                        <div class="flex items-start gap-4 rounded-[1.5rem] border px-5 py-5 {{ $task['complete'] ? 'border-emerald-200 bg-emerald-50/70 dark:border-emerald-900 dark:bg-emerald-500/10' : 'border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-800/80' }}">
                            <div class="mt-0.5 shrink-0">
                                @if ($task['complete'])
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-white">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.25 7.25a1 1 0 01-1.415 0l-3-3a1 1 0 111.414-1.42l2.293 2.294 6.543-6.544a1 1 0 011.415 0z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                @else
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-slate-300 bg-white">
                                        <span class="h-3 w-3 rounded-full bg-slate-200"></span>
                                    </span>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h4 class="text-base font-semibold text-slate-900 dark:text-white">{{ $task['title'] }}</h4>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] {{ $task['complete'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-200' }}">
                                        {{ $task['complete'] ? 'Complete' : 'Pending' }}
                                    </span>
                                </div>
                                <p class="mt-2 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $task['detail'] }}</p>

                                @if (! empty($task['subtasks']))
                                    <div class="mt-4 grid gap-2 rounded-[1.25rem] bg-white/70 p-4 dark:bg-slate-950/60">
                                        @foreach ($task['subtasks'] as $subtask)
                                            <div class="flex items-center gap-3 text-sm">
                                                @if ($subtask['complete'])
                                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-600 text-white">
                                                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.25 7.25a1 1 0 01-1.415 0l-3-3a1 1 0 111.414-1.42l2.293 2.294 6.543-6.544a1 1 0 011.415 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                @else
                                                    <span class="flex h-6 w-6 items-center justify-center rounded-full border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-900">
                                                        <span class="h-2.5 w-2.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                                    </span>
                                                @endif

                                                <span class="text-slate-700 dark:text-slate-300">{{ $subtask['title'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-guest-layout>
