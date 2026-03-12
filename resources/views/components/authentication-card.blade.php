<div class="min-h-screen flex flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0 dark:bg-slate-950">
    <div>
        {{ $logo }}
    </div>

    <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg dark:bg-slate-900 dark:shadow-black/30">
        {{ $slot }}
    </div>
</div>
