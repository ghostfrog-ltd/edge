<x-guest-layout
    title="Help & Support | Fuzzynode Ebay Edge"
    meta-description="Get help with Fuzzynode Ebay Edge billing, account access, scan reports, and feature requests through the built-in support form."
>
    <div class="min-h-screen bg-slate-100 dark:bg-slate-950">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
                <section class="rounded-[2rem] mb-6 border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Help & Support</p>
                    <h1 class="mt-4 text-3xl font-semibold text-slate-900 dark:text-white">Send a support request</h1>
                    <p class="mt-4 text-base leading-8 text-slate-600 dark:text-slate-300">
                        Use this form for billing issues, account access, scan problems, feature requests, or anything else that needs a human response.
                    </p>

                    <div class="mt-8 space-y-4">
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">What happens next</p>
                            <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">We store the request as a support ticket, send it to both support inboxes, and give you a ticket reference so the issue is easy to track.</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-slate-50 p-5 dark:bg-slate-800">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Good reasons to use this page</p>
                            <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">Payment questions, login trouble, weird scan results, broken PDFs, feature ideas, or anything in the product that feels off.</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    @if (session('status'))
                        <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-500/10 dark:text-emerald-300">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('support.store') }}" class="mt-0 space-y-6">
                        @csrf

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="name" class="text-sm font-semibold text-slate-900 dark:text-white">Name</label>
                                <input id="name" name="name" type="text" value="{{ old('name', $user?->name) }}" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                @error('name')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="email" class="text-sm font-semibold text-slate-900 dark:text-white">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email', $user?->email) }}" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                @error('email')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="category" class="text-sm font-semibold text-slate-900 dark:text-white">Category</label>
                                <select id="category" name="category" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                    @foreach ($categories as $value => $label)
                                        <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="subject" class="text-sm font-semibold text-slate-900 dark:text-white">Subject</label>
                                <input id="subject" name="subject" type="text" value="{{ old('subject') }}" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                @error('subject')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="message" class="text-sm font-semibold text-slate-900 dark:text-white">Message</label>
                            <textarea id="message" name="message" rows="8" class="mt-2 w-full rounded-[1rem] border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white">{{ old('message') }}</textarea>
                            @error('message')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit" class="inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-5 py-2 text-sm font-semibold text-slate-950 transition duration-200 hover:scale-105 hover:border-black hover:bg-black hover:text-white hover:shadow-lg hover:shadow-black/20 dark:hover:border-white dark:hover:bg-white dark:hover:text-slate-950 dark:hover:shadow-white/20">
                            Send support request
                        </button>
                    </form>
                </section>
            </div>
        </div>
    </div>
</x-guest-layout>
