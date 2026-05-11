<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Scan queued</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    We are building your eBay gap report
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('scans.index') }}" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                    Scan history
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-[2rem] border border-emerald-200 bg-emerald-50 px-6 py-5 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-500/10 dark:text-emerald-300">
                    {{ session('status') }}
                </div>
            @endif

            <section
                x-data="{
                    status: '{{ $scan->status }}',
                    pollUrl: '{{ route('scans.submitted-status', $scan) }}',
                    showUrl: '{{ route('scans.show', $scan) }}',
                    message: 'Fuzzynode is gathering live eBay evidence, checking schema signals, and building your Missing 3.',
                    init() {
                        this.poll();
                        this.timer = setInterval(() => this.poll(), 5000);
                    },
                    async poll() {
                        try {
                            const response = await fetch(this.pollUrl, {
                                headers: { Accept: 'application/json' },
                                credentials: 'same-origin',
                            });

                            if (!response.ok) {
                                return;
                            }

                            const data = await response.json();
                            this.status = data.status;

                            if (data.status === 'processing') {
                                this.message = 'The engine has accepted your scan and is working through the report now.';
                            }

                            if (data.ready) {
                                clearInterval(this.timer);
                                window.location.href = data.show_url ?? this.showUrl;
                            }
                        } catch (error) {
                            // Stay calm and let the next poll retry.
                        }
                    },
                }"
                class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-orange-700 dark:bg-orange-500/10 dark:text-orange-300">
                                <span x-text="status"></span>
                            </span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                {{ strtoupper(str_replace('-', ' ', $scan->marketplace)) }}
                            </span>
                        </div>

                        <h3 class="mt-6 text-3xl font-semibold leading-tight text-slate-900 dark:text-white">
                            {{ $scan->keyword }}
                        </h3>

                        <p class="mt-5 max-w-3xl text-base leading-8 text-slate-600 dark:text-slate-300" x-text="message">
                            Fuzzynode is gathering live eBay evidence, checking schema signals, and building your Missing 3.
                        </p>

                        <div class="mt-6 rounded-[1.5rem] border border-orange-200 bg-orange-50 p-5 dark:border-orange-900 dark:bg-orange-500/10">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-700 dark:text-orange-300">What to expect</p>
                            <p class="mt-3 text-sm leading-7 text-slate-700 dark:text-slate-200">
                                This usually takes a few minutes. You do not need to stay on this page. Fuzzynode will update your inbox and send an email when the scan is ready.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800/80">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Queued at</p>
                            <p class="mt-3 text-lg font-semibold text-slate-900 dark:text-white">{{ optional($scan->queued_at ?? $scan->created_at)->format('j M Y, H:i') }}</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800/80">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Team</p>
                            <p class="mt-3 text-lg font-semibold text-slate-900 dark:text-white">{{ $scan->team->name }}</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800/80">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Reserved credit</p>
                            <p class="mt-3 text-lg font-semibold text-slate-900 dark:text-white">{{ $scan->reserved_credits }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
