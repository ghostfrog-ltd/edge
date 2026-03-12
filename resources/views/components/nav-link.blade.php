@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-orange-500 text-sm font-medium leading-5 text-slate-900 focus:border-orange-600 focus:outline-none transition duration-150 ease-in-out dark:text-white dark:border-orange-400 dark:focus:border-orange-300'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-slate-500 hover:text-slate-700 hover:border-slate-300 focus:text-slate-700 focus:border-slate-300 focus:outline-none transition duration-150 ease-in-out dark:text-slate-400 dark:hover:text-slate-200 dark:hover:border-slate-700 dark:focus:text-slate-200 dark:focus:border-slate-700';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
