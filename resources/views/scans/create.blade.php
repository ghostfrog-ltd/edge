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
                    <form
                        method="POST"
                        action="{{ route('scans.store') }}"
                        class="space-y-6"
                        x-data="ghostfrogCategorySuggestions({
                            endpoint: '{{ route('scans.ebay-category-suggestions') }}',
                            keyword: @js(old('keyword', '')),
                            marketplace: @js(old('marketplace', 'ebay-uk')),
                            selectedCategoryId: @js(old('ebay_category_id', '')),
                            selectedCategoryLabel: @js(old('ebay_category_id', '') ? 'Selected category ID '.old('ebay_category_id') : ''),
                            initialSuggestions: []
                        })"
                    >
                        @csrf

                        <div>
                            <x-label for="keyword" value="Keyword or niche" />
                            <x-input id="keyword" name="keyword" type="text" class="mt-2 block w-full" x-model="keyword" :value="old('keyword')" autofocus placeholder="e.g. Refurbished Apple iPad Pro 12.9 5th Gen" />
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">This is the main required input. Use a specific product, family, or niche rather than a broad one-word search.</p>
                            <p class="mt-2 text-sm text-orange-600 dark:text-orange-300">Avoid broad one-word scans like "Apple" or "Lego". Specific product families work much better.</p>
                            <x-input-error for="keyword" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="ebay_category_id" value="eBay category ID (optional)" />
                            <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                                <select id="ebay_category_id" name="ebay_category_id" x-model="selectedCategoryId" @change="syncSelectedLabel()" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-orange-400 dark:focus:ring-orange-400">
                                    <option value="">Optional: select an eBay category</option>
                                    <template x-for="suggestion in suggestions" :key="suggestion.id">
                                        <option :value="suggestion.id" x-text="suggestion.label"></option>
                                    </template>
                                </select>
                                <button
                                    type="button"
                                    @click="fetchSuggestions"
                                    @disabled(! $hasEbayCategorySuggestions)
                                    class="inline-flex shrink-0 items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-orange-600 disabled:cursor-not-allowed disabled:bg-slate-400 disabled:text-white dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400 dark:disabled:bg-slate-700 dark:disabled:text-slate-300"
                                >
                                    <span x-show="! loading">Suggest from eBay</span>
                                    <span x-show="loading" x-cloak>Loading...</span>
                                </button>
                            </div>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Enter a keyword first, then click `Suggest from eBay` to load matching categories for the selected marketplace.</p>
                            @unless ($hasEbayCategorySuggestions)
                                <p class="mt-2 text-sm text-orange-600 dark:text-orange-300">Real eBay category suggestions are not configured yet. Add `EBAY_CLIENT_ID` and `EBAY_CLIENT_SECRET` to `.env` to enable this button.</p>
                            @endunless
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400" x-show="selectedCategoryLabel" x-text="selectedCategoryLabel" x-cloak></p>
                            <p class="mt-2 text-sm text-orange-600 dark:text-orange-300" x-show="error" x-text="error" x-cloak></p>
                            <x-input-error for="ebay_category_id" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="competitor_store_url" value="Competitor store URL (optional)" />
                            <x-input id="competitor_store_url" name="competitor_store_url" type="url" class="mt-2 block w-full" :value="old('competitor_store_url')" placeholder="https://www.ebay.co.uk/str/rival-parts-store" />
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Add this if you want the analysis engine to compare your niche against a known rival store.</p>
                            <x-input-error for="competitor_store_url" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="marketplace" value="Marketplace" />
                            <select id="marketplace" name="marketplace" x-model="marketplace" class="mt-2 block w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:border-orange-400 dark:focus:ring-orange-400">
                                @foreach ($marketplaces as $value => $label)
                                    <option value="{{ $value }}" @selected(old('marketplace', 'ebay-uk') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="marketplace" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between rounded-[1.5rem] bg-slate-50 px-5 py-4 dark:bg-slate-800">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">Credit impact</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Each queued scan currently reserves 1 credit.</p>
                            </div>
                            <span class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $team->credit_balance }}</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="submit" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-6 py-3 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                                Queue scan
                            </button>
                            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">
                                Back to dashboard
                            </a>
                        </div>
                    </form>
                </section>

                <aside class="rounded-[2rem] border border-slate-200 bg-white p-8 text-slate-900 shadow-sm dark:border-slate-800 dark:bg-slate-950 dark:text-white dark:shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Current behavior</p>
                    <div class="mt-6 space-y-5 text-sm text-slate-600 dark:text-slate-300">
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                            The Laravel app stores the keyword or niche first, then enriches the scan with category and competitor context if you provide them.
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                            Very broad one-word searches are now blocked so we do not waste credits on weak reports.
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                            This is the right contract for the Python side: team context, keyword or niche, optional category ID, optional competitor store URL, and marketplace.
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
