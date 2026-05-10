<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Inbox</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    Notifications
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl space-y-4 sm:px-6 lg:px-8">
            @forelse ($notifications as $notification)
                <section class="rounded-[2rem] border p-6 shadow-sm {{ $notification->read_at ? 'border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900' : 'border-orange-200 bg-orange-50 dark:border-orange-500/30 dark:bg-orange-500/10' }}">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.25em] {{ $notification->read_at ? 'text-slate-500 dark:text-slate-400' : 'text-orange-700 dark:text-orange-300' }}">
                                {{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $notification->type)) }}
                            </p>
                            <h3 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $notification->title }}</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $notification->body }}</p>
                            <p class="mt-3 text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @if (! $notification->read_at)
                            <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-orange-700 dark:bg-orange-500/20 dark:text-orange-300">
                                New
                            </span>
                        @endif
                    </div>

                    <div class="mt-5 flex items-center gap-3">
                        @if ($notification->action_url)
                            <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">
                                    Open
                                </button>
                            </form>
                        @endif

                        @if (! $notification->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                @csrf
                                <input type="hidden" name="stay" value="1">
                                <button type="submit" class="text-sm font-semibold text-slate-500 transition hover:text-orange-600 dark:text-slate-300 dark:hover:text-orange-300">
                                    Mark read
                                </button>
                            </form>
                        @endif
                    </div>
                </section>
            @empty
                <section class="rounded-[2rem] border border-dashed border-slate-300 bg-white px-6 py-12 text-center shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <p class="text-lg font-semibold text-slate-900 dark:text-white">No notifications yet</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Scan-ready alerts and failures will show up here first. Email can layer on afterwards.</p>
                </section>
            @endforelse

            <div>
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
