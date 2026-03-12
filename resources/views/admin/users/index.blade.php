<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600 dark:text-orange-300">Admin</p>
                <h2 class="mt-5 text-2xl font-semibold leading-tight text-slate-900 dark:text-white">
                    Users
                </h2>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">
                Back to admin
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">Directory</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Customer accounts</h3>
                    </div>

                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex w-full max-w-xl items-center gap-3">
                        <input
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search by name or email"
                            class="block w-full rounded-full border border-slate-300 bg-white px-5 py-3 text-sm text-slate-900 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400 dark:focus:border-orange-400 dark:focus:ring-orange-400"
                        >
                        <button type="submit" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-orange-600 dark:bg-orange-500 dark:text-slate-950 dark:hover:bg-orange-400">
                            Search
                        </button>
                    </form>
                </div>

                <div class="mt-8 overflow-hidden rounded-[1.5rem] border border-slate-200 dark:border-slate-800">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-800">
                            <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Teams</th>
                                <th class="px-6 py-4">Scans</th>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                            @forelse ($users as $user)
                                <tr class="text-sm text-slate-600 dark:text-slate-300">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.users.show', $user) }}" class="font-semibold text-slate-900 hover:text-orange-600 dark:text-white dark:hover:text-orange-300">
                                            {{ $user->name }}
                                        </a>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                                    </td>
                                    <td class="px-6 py-4">{{ $user->ownedTeams->count() }}</td>
                                    <td class="px-6 py-4">{{ $user->scans->count() }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] {{ $user->is_admin ? 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                                            {{ $user->is_admin ? 'Admin' : 'User' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $user->created_at->format('j M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">No users matched that search.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
