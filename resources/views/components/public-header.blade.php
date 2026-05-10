<header class="border-b border-slate-200 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-slate-900/90">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ url('/') }}" class="shrink-0">
            <x-application-logo class="w-auto" />
        </a>

        <nav class="flex items-center gap-2 text-sm font-semibold">
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                    Dashboard
                </a>

                @if (auth()->user()?->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                        Admin
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                    Log In
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                    Create account
                </a>
            @endauth

            <a href="{{ route('pricing') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                Pricing
            </a>
            <a href="{{ route('how-it-works') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                How It Works
            </a>
            <a href="{{ route('terms.show') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                Terms
            </a>
            <a href="{{ route('policy.show') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                Privacy
            </a>
            <a href="{{ route('support.show') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                Help & Support
            </a>
        </nav>
    </div>
</header>
