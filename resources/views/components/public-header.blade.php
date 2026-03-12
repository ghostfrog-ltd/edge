<header class="border-b border-slate-200 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-slate-900/90">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ url('/') }}" class="shrink-0">
            <x-application-logo class="w-auto" />
        </a>

        <nav class="flex items-center gap-2 text-sm font-semibold">
            <a href="{{ route('how-it-works') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                How It Works
            </a>
            <a href="{{ route('terms.show') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                Terms
            </a>
            <a href="{{ route('policy.show') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                Privacy
            </a>
            <a href="{{ route('contact.show') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                Contact
            </a>
        </nav>
    </div>
</header>
