<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Admin</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    Human test plan
                </h2>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                Back to admin
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Why this exists</p>
                <p class="mt-4 max-w-4xl text-base leading-8 text-slate-600 dark:text-slate-300">
                    This is the human final-pass checklist for Ghostfrog. It is here so we test the whole system the way a real customer and operator will experience it, not just the way automated tests see it.
                </p>
            </section>

            <div class="grid gap-6">
                @foreach ($phases as $index => $phase)
                    <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-500 text-sm font-semibold text-slate-950">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">{{ $phase['title'] }}</p>
                                <p class="mt-2 text-base leading-8 text-slate-600 dark:text-slate-300">{{ $phase['goal'] }}</p>
                            </div>
                        </div>

                        <div class="mt-6 rounded-[1.5rem] bg-slate-50 p-6 dark:bg-slate-800/80">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Checks</p>
                            <div class="mt-4 space-y-3">
                                @foreach ($phase['checks'] as $check)
                                    <div class="flex items-start gap-3 rounded-[1.25rem] border border-slate-200 bg-white px-4 py-4 dark:border-slate-700 dark:bg-slate-900">
                                        <span class="mt-1 flex h-6 w-6 items-center justify-center rounded-full border border-slate-300 bg-white dark:border-slate-700 dark:bg-slate-950">
                                            <span class="h-2.5 w-2.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                        </span>
                                        <p class="text-sm leading-7 text-slate-700 dark:text-slate-200">{{ $check }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-5 rounded-[1.5rem] border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-900 dark:bg-emerald-500/10">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700 dark:text-emerald-300">Expected outcome</p>
                            <p class="mt-3 text-sm leading-7 text-slate-700 dark:text-slate-200">{{ $phase['expected'] }}</p>
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
