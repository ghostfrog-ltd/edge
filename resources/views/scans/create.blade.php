<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Scan intake</p>
            <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                Queue a new eBay gap scan
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[1.35fr_0.9fr]">
                <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <form method="POST" action="{{ route('scans.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-label for="keyword" value="Keyword or niche" />
                            <x-input id="keyword" name="keyword" type="text" class="mt-2 block w-full" :value="old('keyword')" required autofocus placeholder="e.g. lego castle byers" />
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">This is the search phrase the future Python worker will analyze against eBay competitors.</p>
                            <x-input-error for="keyword" class="mt-2" />
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <x-label for="marketplace" value="Marketplace" />
                                <select id="marketplace" name="marketplace" class="mt-2 block w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-orange-400 dark:focus:ring-orange-400">
                                    @foreach ($marketplaces as $value => $label)
                                        <option value="{{ $value }}" @selected(old('marketplace', 'ebay-uk') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="marketplace" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="category" value="Category (optional)" />
                                <x-input id="category" name="category" type="text" class="mt-2 block w-full" :value="old('category')" placeholder="e.g. LEGO complete sets" />
                                <x-input-error for="category" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-label for="notes" value="Analyst notes (optional)" />
                            <textarea id="notes" name="notes" rows="5" class="mt-2 block w-full rounded-2xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-orange-400 dark:focus:ring-orange-400" placeholder="Any hints, competitor names, or filters we should preserve later...">{{ old('notes') }}</textarea>
                            <x-input-error for="notes" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between rounded-[1.5rem] bg-slate-50 px-5 py-4 dark:bg-slate-800">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">Credit impact</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Each queued scan currently reserves 1 credit.</p>
                            </div>
                            <span class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $team->credit_balance }}</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="submit" class="inline-flex items-center rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-orange-600">
                                Queue scan
                            </button>
                            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">
                                Back to dashboard
                            </a>
                        </div>
                    </form>
                </section>

                <aside class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-300">Current behavior</p>
                    <div class="mt-6 space-y-5 text-sm text-slate-300">
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-4">
                            The Laravel app stores the scan request, attaches it to the active team, and reserves a credit.
                        </div>
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-4">
                            Reports are still placeholders until we connect the FastAPI worker and webhook callback.
                        </div>
                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-4">
                            This is the right contract for the Python side: team context, keyword, category, marketplace, notes.
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
