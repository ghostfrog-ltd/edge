<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Billing</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    {{ $team->name }}
                </h2>
            </div>

            @if ($team->stripe_id)
                <form method="POST" action="{{ route('billing.portal') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                        Open billing portal
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-500/10 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('billing'))
                <div class="rounded-3xl border border-orange-200 bg-orange-50 px-6 py-4 text-sm text-orange-800 dark:border-orange-900 dark:bg-orange-500/10 dark:text-orange-300">
                    {{ $errors->first('billing') }}
                </div>
            @endif

            @if ($checkoutFeedback)
                <div
                    @class([
                        'rounded-3xl px-6 py-4 text-sm',
                        'border border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-500/10 dark:text-emerald-300' => $checkoutFeedback['tone'] === 'success',
                        'border border-orange-200 bg-orange-50 text-orange-800 dark:border-orange-900 dark:bg-orange-500/10 dark:text-orange-300' => $checkoutFeedback['tone'] === 'pending',
                        'border border-slate-200 bg-slate-50 text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300' => $checkoutFeedback['tone'] === 'cancelled',
                    ])
                >
                    {{ $checkoutFeedback['message'] }}
                </div>
            @endif

            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Current billing</p>
                        <h3 class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">
                            {{ $activePlan['name'] ?? 'No active subscription' }}
                        </h3>
                        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600 dark:text-slate-300">
                            @if ($subscription)
                                Stripe status: <span class="font-semibold">{{ str_replace('_', ' ', $subscription->stripe_status) }}</span>.
                                @if ($activePlan)
                                    This plan includes <span class="font-semibold">{{ $activePlan['credits_label'] }}</span>.
                                @endif
                            @else
                                This workspace is still running on starter grant credits and one-off reservations. Pick a plan to turn Fuzzynode into a paid, reusable system.
                            @endif
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                            <p class="text-sm text-slate-600 dark:text-slate-300">Available credits</p>
                            <p class="mt-3 text-4xl font-semibold text-slate-900 dark:text-white">{{ $team->credit_balance }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                            <p class="text-sm text-slate-600 dark:text-slate-300">Stripe customer</p>
                            <p class="mt-3 text-base font-semibold text-slate-900 dark:text-white break-all">{{ $team->stripe_id ?: 'Not created yet' }}</p>
                        </div>
                    </div>
                </div>

                @unless ($stripeConfigured)
                    <div class="mt-6 rounded-[1.5rem] border border-dashed border-orange-300 bg-orange-50 px-5 py-4 text-sm text-orange-800 dark:border-orange-500/40 dark:bg-orange-500/10 dark:text-orange-200">
                        Stripe secret configuration is missing, so checkout buttons are shown as wiring targets rather than live payment actions.
                    </div>
                @endunless
            </section>

            <div class="grid gap-6 xl:grid-cols-2">
                <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">Subscriptions</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Monthly plans</h3>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach ($plans as $plan)
                            <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-600 dark:text-orange-300">{{ $plan['price_label'] }}</p>
                                        <h4 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $plan['name'] }}</h4>
                                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $plan['summary'] }}</p>
                                    </div>
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 shadow-sm dark:bg-slate-900 dark:text-slate-300">
                                        {{ $plan['credits_label'] }}
                                    </span>
                                </div>

                                <div class="mt-4 flex flex-wrap items-center gap-3">
                                    @if ($activePlan && $activePlan['key'] === $plan['key'] && $subscription)
                                        <span class="inline-flex items-center rounded-full border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
                                            Active plan
                                        </span>
                                    @elseif ($plan['key'] === 'free')
                                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                                            Included as starter access
                                        </span>
                                    @elseif ($plan['configured'] && $stripeConfigured)
                                        <form method="POST" action="{{ route('billing.subscribe', $plan['key']) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                                                Choose {{ $plan['name'] }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                                            Stripe price not configured
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">Top-ups</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Credit boosts</h3>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach ($topUps as $pack)
                            <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-600 dark:text-orange-300">{{ $pack['price_label'] }}</p>
                                        <h4 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $pack['name'] }}</h4>
                                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $pack['summary'] }}</p>
                                    </div>
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 shadow-sm dark:bg-slate-900 dark:text-slate-300">
                                        {{ $pack['credits'] }} credits
                                    </span>
                                </div>

                                <div class="mt-4 flex flex-wrap items-center gap-3">
                                    @if ($pack['configured'] && $stripeConfigured)
                                        <form method="POST" action="{{ route('billing.top-up', $pack['key']) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                                                Buy {{ $pack['credits'] }} credits
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                                            Stripe price not configured
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
