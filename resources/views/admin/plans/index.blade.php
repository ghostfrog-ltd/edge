<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Admin</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">Pricing</h2>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.plans.create', ['type' => 'plan']) }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">New plan</a>
                <a href="{{ route('admin.plans.create', ['type' => 'top_up']) }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">New top-up</a>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">Back to admin</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-500/10 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            <section class="rounded-[2rem] border border-orange-200 bg-orange-50 p-6 shadow-sm dark:border-orange-500/30 dark:bg-orange-500/10">
                <p class="text-sm leading-7 text-orange-900 dark:text-orange-100">
                    This is the sellable pricing catalog for <span class="font-semibold">Ghostfrog Ebay Edge</span>. The public pricing page and the workspace billing page both read from the same data you edit here.
                </p>
            </section>

            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">Plans</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Recurring subscriptions</h3>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    @foreach ($plans as $plan)
                        <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">{{ $plan['price_label'] }}</p>
                                    <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">{{ $plan['name'] }}</h3>
                                </div>
                                <span class="rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] {{ $plan['configured'] || ($plan['amount_minor'] ?? 0) === 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                                    {{ ($plan['configured'] || ($plan['amount_minor'] ?? 0) === 0) ? 'ready' : 'needs stripe sync' }}
                                </span>
                            </div>

                            <dl class="mt-6 space-y-4">
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Credit shape</dt>
                                    <dd class="mt-1 text-base text-slate-700 dark:text-slate-200">{{ $plan['credits_label'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Stripe price ID</dt>
                                    <dd class="mt-1 break-all text-base text-slate-700 dark:text-slate-200">{{ $plan['stripe_price_id'] ?: 'Not configured yet' }}</dd>
                                </div>
                            </dl>

                            <p class="mt-6 text-base leading-8 text-slate-600 dark:text-slate-300">{{ $plan['summary'] }}</p>

                            <div class="mt-6 flex flex-wrap items-center gap-3">
                                <a href="{{ route('admin.plans.edit', $plan['model_id']) }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                                    Edit offer
                                </a>
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">Top-ups</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">One-off credit packs</h3>
                    </div>
                </div>

                <div class="grid gap-6 xl:grid-cols-2">
                    @foreach ($topUps as $plan)
                        <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">{{ $plan['price_label'] }}</p>
                                    <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">{{ $plan['name'] }}</h3>
                                </div>
                                <span class="rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] {{ $plan['configured'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                                    {{ $plan['configured'] ? 'ready' : 'needs stripe sync' }}
                                </span>
                            </div>

                            <dl class="mt-6 space-y-4">
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Credits</dt>
                                    <dd class="mt-1 text-base text-slate-700 dark:text-slate-200">{{ $plan['credits'] }} credits</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Stripe price ID</dt>
                                    <dd class="mt-1 break-all text-base text-slate-700 dark:text-slate-200">{{ $plan['stripe_price_id'] ?: 'Not configured yet' }}</dd>
                                </div>
                            </dl>

                            <p class="mt-6 text-base leading-8 text-slate-600 dark:text-slate-300">{{ $plan['summary'] }}</p>

                            <div class="mt-6 flex flex-wrap items-center gap-3">
                                <a href="{{ route('admin.plans.edit', $plan['model_id']) }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                                    Edit offer
                                </a>
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
