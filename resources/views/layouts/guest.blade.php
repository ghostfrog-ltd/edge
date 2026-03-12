@props([
    'title' => config('app.name', 'Ghostfrog Ebay Edge'),
    'metaDescription' => 'Ghostfrog Ebay Edge helps eBay sellers scan a niche, compare competitor listings, and identify the gaps that matter.',
    'metaRobots' => 'index,follow',
    'canonical' => null,
    'ogType' => 'website',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @php
            $pageTitle = trim($title.'');
            $canonical = $canonical ?: url()->current();
        @endphp

        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $metaDescription }}">
        <meta name="robots" content="{{ $metaRobots }}">
        <link rel="canonical" href="{{ $canonical }}">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <meta property="og:type" content="{{ $ogType }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:url" content="{{ $canonical }}">
        <meta property="og:site_name" content="{{ config('app.name', 'Ghostfrog Ebay Edge') }}">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $metaDescription }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script>
            (() => {
                const stored = localStorage.getItem('ghostfrog-theme') || 'system';
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const resolved = stored === 'system' ? (prefersDark ? 'dark' : 'light') : stored;

                document.documentElement.classList.toggle('dark', resolved === 'dark');
                document.documentElement.style.colorScheme = resolved;
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body>
        <div class="flex min-h-screen flex-col font-sans text-gray-900 antialiased dark:text-slate-100">
            <x-public-header />

            {{ $slot }}

            <x-site-footer />
        </div>

        @livewireScripts
    </body>
</html>
