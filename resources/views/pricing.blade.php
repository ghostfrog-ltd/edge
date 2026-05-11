<x-guest-layout>
    <section class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
        <div class="mx-auto flex max-w-6xl flex-col gap-6 px-6 py-16 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Pricing</p>
                <h1 class="mt-5 text-4xl font-semibold text-slate-900 dark:text-white sm:text-5xl">Choose the Fuzzynode plan that fits your eBay workflow.</h1>
                <p class="mt-6 text-lg leading-8 text-slate-600 dark:text-slate-300">
                    Start with a lightweight plan, then add top-up credits whenever you need extra scans. The pricing page reads from the same admin-managed catalog the workspace billing screen uses.
                </p>
            </div>
            <div class="rounded-[2rem] border border-orange-200 bg-orange-50 px-6 py-5 text-sm leading-7 text-orange-900 shadow-sm dark:border-orange-500/30 dark:bg-orange-500/10 dark:text-orange-100">
                One credit equals one successful scan and one finished Edge Report.
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-12 dark:bg-slate-950">
        <div class="mx-auto max-w-6xl space-y-10 px-6">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">Subscriptions</p>
                <h2 class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">Monthly plans</h2>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                @foreach ($plans as $plan)
                    <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">{{ $plan['price_label'] }}</p>
                                <h3 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $plan['name'] }}</h3>
                            </div>
                            @if (($plan['key'] ?? null) === 'free')
                                <span class="rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:bg-slate-800 dark:text-slate-300">Included</span>
                            @else
                                <span class="rounded-full bg-emerald-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Subscription</span>
                            @endif
                        </div>

                        <p class="mt-6 text-base leading-8 text-slate-600 dark:text-slate-300">{{ $plan['summary'] }}</p>

                        <div class="mt-6 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Credits included</p>
                            <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $plan['credits_label'] }}</p>
                        </div>

                        <div class="mt-6">
                            @auth
                                <a href="{{ route('billing.index') }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                                    Open billing
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                                    Create account
                                </a>
                            @endauth
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-12 dark:bg-slate-950">
        <div class="mx-auto max-w-6xl space-y-10 px-6">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">Top-ups</p>
                <h2 class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">One-off credit boosts</h2>
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                @foreach ($topUps as $pack)
                    <section class="rounded-[2rem] border border-slate-200 bg-slate-50 p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">{{ $pack['price_label'] }}</p>
                        <h3 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $pack['name'] }}</h3>
                        <p class="mt-4 text-base leading-8 text-slate-600 dark:text-slate-300">{{ $pack['summary'] }}</p>
                        <div class="mt-6 rounded-[1.5rem] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Credits</p>
                            <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $pack['credits'] }} credits</p>
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </section>
</x-guest-layout>
