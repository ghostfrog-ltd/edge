<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Admin</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    {{ $formMode === 'create' ? 'Create pricing offer' : 'Edit pricing offer' }}
                </h2>
            </div>
            <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">Back to pricing</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-500/10 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('pricing'))
                <div class="rounded-3xl border border-orange-200 bg-orange-50 px-6 py-4 text-sm text-orange-800 dark:border-orange-900 dark:bg-orange-500/10 dark:text-orange-300">
                    {{ $errors->first('pricing') }}
                </div>
            @endif

            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <form method="POST" action="{{ $formMode === 'create' ? route('admin.plans.store') : route('admin.plans.update', $offer) }}" class="space-y-6">
                    @csrf
                    @if ($formMode === 'edit')
                        @method('PUT')
                    @endif

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="type" class="text-sm font-semibold text-slate-900 dark:text-white">Offer type</label>
                            <select id="type" name="type" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                <option value="plan" @selected(old('type', $offer->type) === 'plan')>Plan</option>
                                <option value="top_up" @selected(old('type', $offer->type) === 'top_up')>Top-up</option>
                            </select>
                            @error('type')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="key" class="text-sm font-semibold text-slate-900 dark:text-white">Key</label>
                            <input id="key" name="key" type="text" value="{{ old('key', $offer->key) }}" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            @error('key')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="name" class="text-sm font-semibold text-slate-900 dark:text-white">Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $offer->name) }}" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            @error('name')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="price_label" class="text-sm font-semibold text-slate-900 dark:text-white">Public price label</label>
                            <input id="price_label" name="price_label" type="text" value="{{ old('price_label', $offer->price_label) }}" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            @error('price_label')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="summary" class="text-sm font-semibold text-slate-900 dark:text-white">Summary</label>
                        <textarea id="summary" name="summary" rows="4" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('summary', $offer->summary) }}</textarea>
                        @error('summary')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-6 md:grid-cols-4">
                        <div>
                            <label for="amount_display" class="text-sm font-semibold text-slate-900 dark:text-white">Amount</label>
                            <input id="amount_display" name="amount_display" type="number" step="0.01" min="0" value="{{ old('amount_display', $offer->exists ? $offer->amount_display : '0.00') }}" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            @error('amount_display')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="currency" class="text-sm font-semibold text-slate-900 dark:text-white">Currency</label>
                            <input id="currency" name="currency" type="text" value="{{ old('currency', $offer->currency ?: 'gbp') }}" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            @error('currency')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="billing_interval" class="text-sm font-semibold text-slate-900 dark:text-white">Billing interval</label>
                            <select id="billing_interval" name="billing_interval" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                <option value="">One-off / none</option>
                                <option value="month" @selected(old('billing_interval', $offer->billing_interval) === 'month')>Monthly</option>
                            </select>
                            @error('billing_interval')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="sort_order" class="text-sm font-semibold text-slate-900 dark:text-white">Sort order</label>
                            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $offer->sort_order ?: 0) }}" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            @error('sort_order')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="monthly_credits" class="text-sm font-semibold text-slate-900 dark:text-white">Monthly credits</label>
                            <input id="monthly_credits" name="monthly_credits" type="number" min="0" value="{{ old('monthly_credits', $offer->monthly_credits) }}" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            @error('monthly_credits')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="credits" class="text-sm font-semibold text-slate-900 dark:text-white">Top-up credits</label>
                            <input id="credits" name="credits" type="number" min="0" value="{{ old('credits', $offer->credits) }}" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            @error('credits')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="credits_label" class="text-sm font-semibold text-slate-900 dark:text-white">Credits label</label>
                        <input id="credits_label" name="credits_label" type="text" value="{{ old('credits_label', $offer->credits_label) }}" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        @error('credits_label')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <label class="inline-flex items-center gap-3 text-sm font-semibold text-slate-900 dark:text-white">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $offer->is_active ?? true)) class="h-5 w-5 rounded border-slate-300 text-orange-600 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-950">
                        Offer is active
                    </label>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Stripe product</p>
                            <p class="mt-3 break-all text-sm text-slate-700 dark:text-slate-200">{{ $offer->stripe_product_id ?: 'Not synced yet' }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Stripe price</p>
                            <p class="mt-3 break-all text-sm text-slate-700 dark:text-slate-200">{{ $offer->stripe_price_id ?: 'Not synced yet' }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button type="submit" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                            {{ $formMode === 'create' ? 'Create offer' : 'Save changes' }}
                        </button>

                        @if ($offer->exists)
                            <form method="POST" action="{{ route('admin.plans.sync-stripe', $offer) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-5 py-2 text-sm font-semibold text-slate-900 transition hover:border-orange-500 hover:text-orange-600 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:hover:border-orange-400 dark:hover:text-orange-300">
                                    Sync to Stripe
                                </button>
                            </form>
                        @endif
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
