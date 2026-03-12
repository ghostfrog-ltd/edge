<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Admin</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">Products</h2>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">Back to admin</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">
            <section class="rounded-[2rem] border border-orange-200 bg-orange-50 p-6 shadow-sm dark:border-orange-500/30 dark:bg-orange-500/10">
                <p class="text-sm leading-7 text-orange-900 dark:text-orange-100">
                    Products are separate Ghostfrog tools. Pricing tiers like <span class="font-semibold">Free</span>, <span class="font-semibold">Starter</span>, and <span class="font-semibold">Pro</span> live on the admin plans page instead.
                </p>
            </section>

            @foreach ($products as $product)
                <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">{{ $product['slug'] }}</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">{{ $product['name'] }}</h3>
                        </div>
                        <span class="rounded-full px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] {{ $product['status'] === 'active-build' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                            {{ str_replace('-', ' ', $product['status']) }}
                        </span>
                    </div>
                    <p class="mt-4 text-xs font-semibold uppercase tracking-[0.25em] text-slate-500 dark:text-slate-400">{{ $product['focus'] }}</p>
                    <p class="mt-4 text-base leading-8 text-slate-600 dark:text-slate-300">{{ $product['description'] }}</p>
                </section>
            @endforeach
        </div>
    </div>
</x-app-layout>
