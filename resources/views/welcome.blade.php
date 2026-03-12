<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Ghostfrog Ebay Edge | Find eBay Listing Gaps Faster</title>
        <meta name="description" content="Ghostfrog Ebay Edge helps eBay sellers scan a niche, compare competitor listings, and spot the missing attributes and actions that improve listing quality.">
        <meta name="robots" content="index,follow">
        <link rel="canonical" href="{{ route('home') }}">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <meta property="og:type" content="website">
        <meta property="og:title" content="Ghostfrog Ebay Edge | Find eBay Listing Gaps Faster">
        <meta property="og:description" content="Scan an eBay niche, compare competitor listings, and turn the evidence into practical listing actions.">
        <meta property="og:url" content="{{ route('home') }}">
        <meta property="og:site_name" content="{{ config('app.name', 'Ghostfrog Ebay Edge') }}">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="Ghostfrog Ebay Edge | Find eBay Listing Gaps Faster">
        <meta name="twitter:description" content="Scan an eBay niche, compare competitor listings, and turn the evidence into practical listing actions.">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <script>
            (() => {
                const stored = localStorage.getItem('ghostfrog-theme') || 'system';
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const resolved = stored === 'system' ? (prefersDark ? 'dark' : 'light') : stored;

                document.documentElement.classList.toggle('dark', resolved === 'dark');
                document.documentElement.style.colorScheme = resolved;
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script type="application/ld+json">
            {
                "@@context": "https://schema.org",
                "@@type": "SoftwareApplication",
                "name": "Ghostfrog Ebay Edge",
                "applicationCategory": "BusinessApplication",
                "operatingSystem": "Web",
                "description": "Ghostfrog Ebay Edge helps eBay sellers scan a niche, compare competitor listings, and identify missing attributes and listing opportunities.",
                "url": "{{ route('home') }}",
                "offers": {
                    "@@type": "Offer",
                    "price": "0",
                    "priceCurrency": "GBP"
                }
            }
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(255,138,61,0.22),_transparent_30%),linear-gradient(180deg,_#f8fafc_0%,_#eef2f7_100%)] dark:bg-[radial-gradient(circle_at_top_left,_rgba(255,138,61,0.18),_transparent_30%),linear-gradient(180deg,_#020617_0%,_#0f172a_100%)]">
            <header class="mx-auto flex max-w-7xl items-center justify-between px-4 py-6 sm:px-6 lg:px-8">
                <a href="/" class="shrink-0">
                    <x-application-logo class="w-auto" />
                </a>

                <nav class="flex items-center gap-3 text-sm font-semibold">
                    <a href="{{ route('how-it-works') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-white/70 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800/80 dark:hover:text-white">
                        How It Works
                    </a>
                    <a href="{{ route('contact.show') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:bg-white/70 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800/80 dark:hover:text-white">
                        Contact
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full bg-slate-900 px-5 py-2.5 text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full bg-slate-900 px-5 py-2.5 text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">
                            Log In
                        </a>
                    @endauth
                </nav>
            </header>

            <main class="mx-auto max-w-7xl px-4 pb-16 pt-8 sm:px-6 lg:px-8 lg:pb-24 lg:pt-12">
                <section class="grid items-center gap-10 lg:grid-cols-[1.2fr_0.9fr]">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-orange-600 dark:text-orange-300">
                            Market Intelligence For eBay Sellers
                        </p>
                        <h1 class="mt-6 max-w-4xl text-5xl font-semibold leading-tight text-slate-950 dark:text-white md:text-6xl">
                            Find the listing gaps your competitors keep missing.
                        </h1>
                        <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600 dark:text-slate-300">
                            Ghostfrog Ebay Edge scans a niche, compares competitor listings, and turns that evidence into a clear report with missing attributes, weak spots, and practical next actions.
                        </p>

                        <div class="mt-8 flex flex-wrap items-center gap-4">
                            @auth
                                <a href="{{ route('scans.create') }}" class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">
                                    Queue Your Next Scan
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">
                                    Create Account
                                </a>
                            @endauth
                            <a href="{{ route('how-it-works') }}" class="rounded-full border border-slate-300 bg-white/80 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-orange-300 hover:text-orange-700 dark:border-slate-700 dark:bg-slate-900/80 dark:text-slate-200 dark:hover:border-orange-400 dark:hover:text-orange-300">
                                See How It Works
                            </a>
                        </div>

                        <div class="mt-10 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-[1.75rem] border border-white/60 bg-white/75 p-5 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-900/75">
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">1 credit</p>
                                <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">1 successful scan</p>
                            </div>
                            <div class="rounded-[1.75rem] border border-white/60 bg-white/75 p-5 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-900/75">
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Output</p>
                                <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">Gap report + actions</p>
                            </div>
                            <div class="rounded-[1.75rem] border border-white/60 bg-white/75 p-5 shadow-sm backdrop-blur dark:border-slate-800 dark:bg-slate-900/75">
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Input</p>
                                <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">Keyword or niche</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-0 rounded-[2.5rem] bg-orange-500/20 blur-3xl dark:bg-orange-400/15"></div>
                        <div class="relative rounded-[2.5rem] border border-white/70 bg-white/85 p-6 shadow-2xl backdrop-blur dark:border-slate-800 dark:bg-slate-900/85">
                            <div class="rounded-[2rem] bg-slate-950 p-6 text-white">
                                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-300">Example scan</p>
                                <h2 class="mt-4 text-3xl font-semibold">lego castle byers</h2>
                                <p class="mt-3 text-sm leading-7 text-slate-300">
                                    Python gathers marketplace evidence, structured code compares attributes, and the LLM highlights the gaps worth acting on.
                                </p>

                                <div class="mt-6 space-y-3">
                                    <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-4">
                                        <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Missing attributes</p>
                                        <p class="mt-2 text-base font-semibold">Condition details, box completeness, minifigure count</p>
                                    </div>
                                    <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-4">
                                        <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Weak competitor signal</p>
                                        <p class="mt-2 text-base font-semibold">Top listings mention sealed bags and manual condition more consistently.</p>
                                    </div>
                                    <div class="rounded-[1.5rem] border border-white/10 bg-orange-500/10 p-4">
                                        <p class="text-xs uppercase tracking-[0.25em] text-orange-300">Recommended next action</p>
                                        <p class="mt-2 text-base font-semibold">Rewrite the title and item specifics around completeness and display the count clearly.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800">
                                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Laravel side</p>
                                    <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">Teams, credits, scan intake, report pages, billing, and product UI.</p>
                                </div>
                                <div class="rounded-[1.5rem] bg-orange-50 p-5 dark:bg-orange-500/10">
                                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-700 dark:text-orange-300">Python brain</p>
                                    <p class="mt-3 text-sm leading-7 text-slate-700 dark:text-slate-200">Search, evidence gathering, comparison logic, and LLM-driven prioritization.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <x-site-footer />
        </div>
    </body>
</html>
