@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-orange-500 bg-orange-50 py-2 ps-3 pe-4 text-start text-base font-medium text-orange-700 transition duration-150 ease-in-out focus:border-orange-600 focus:bg-orange-100 focus:text-orange-800 focus:outline-none dark:border-orange-400 dark:bg-orange-500/10 dark:text-orange-300 dark:focus:border-orange-300 dark:focus:bg-orange-500/20 dark:focus:text-orange-200'
            : 'block w-full border-l-4 border-transparent py-2 ps-3 pe-4 text-start text-base font-medium text-slate-600 transition duration-150 ease-in-out hover:border-slate-300 hover:bg-slate-50 hover:text-slate-800 focus:border-slate-300 focus:bg-slate-50 focus:text-slate-800 focus:outline-none dark:text-slate-300 dark:hover:border-slate-700 dark:hover:bg-slate-800 dark:hover:text-white dark:focus:border-slate-700 dark:focus:bg-slate-800 dark:focus:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
