
@extends('layouts.apps')

@section('content')

<div class="mx-auto max-w-7xl px-4 py-7 sm:px-6 lg:px-8">

    <!-- PAGE HEADER -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#EAF0E5] text-[#52693A]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor"
                        stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7h5l2 2h11v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 12v5m-2.5-2.5h5" />
                    </svg>
                </div>

                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-800 dark:text-white">
                        Archived Accounts
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Manage and restore previously archived accounts.
                    </p>
                </div>
            </div>

            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                Archived accounts cannot sign in until they are restored.
            </p>
        </div>

        <!-- BACK BUTTON -->
        <a href="{{ route('acc_superadmin') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-600 shadow-sm transition hover:bg-gray-50 hover:text-[#52693A] dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">

            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 12H5m7 7-7-7 7-7" />
            </svg>

            Back to Accounts
        </a>

    </div>


    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900 dark:bg-green-900/20 dark:text-green-300">

            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor"
                stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M5 13l4 4L19 7" />
            </svg>

            <span>{{ session('success') }}</span>
        </div>
    @endif


    <!-- SEARCH SECTION -->
    <div class="mb-5 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">

        <form method="GET" class="flex flex-col gap-3 sm:flex-row">

            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m20 20-4-4" />
                </svg>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by name or email..."
                    aria-label="Search archived accounts"
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-700 outline-none transition placeholder:text-gray-400 focus:border-[#607844] focus:ring-2 focus:ring-[#607844]/15 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>

            <button type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#607844] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#52693A] focus:outline-none focus:ring-2 focus:ring-[#607844]/40 focus:ring-offset-2">

                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m20 20-4-4" />
                </svg>

                Search
            </button>

            @if(request('search'))
                <a href="{{ route('superadmin.archives.accounts') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    Clear
                </a>
            @endif

        </form>
    </div>


    <!-- ARCHIVED ACCOUNTS TABLE -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">

        <!-- TABLE HEADER -->
        <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <h2 class="text-sm font-bold text-gray-800 dark:text-white">
                Archived Account Records
            </h2>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Review archived users and restore their access when needed.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-sm">

                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:bg-gray-700/60 dark:text-gray-300">
                    <tr>
                        <th class="px-5 py-4">Account</th>
                        <th class="px-5 py-4">Role</th>
                        <th class="px-5 py-4">Date Archived</th>
                        <th class="px-5 py-4 text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">

                    @forelse($accounts as $account)

                        <tr class="transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-700/30">

                            <!-- ACCOUNT -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#EAF0E5] text-sm font-bold text-[#52693A]">
                                        {{ strtoupper(substr($account->fname, 0, 1)) }}{{ strtoupper(substr($account->lname, 0, 1)) }}
                                    </div>

                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 dark:text-white">
                                            {{ $account->fname }} {{ $account->lname }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                            {{ $account->email }}
                                        </p>
                                    </div>

                                </div>
                            </td>

                            <!-- ROLE -->
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-md bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    {{ ucfirst($account->role->name ?? 'Unknown') }}
                                </span>
                            </td>

                            <!-- DATE ARCHIVED -->
                            <td class="px-5 py-4 text-gray-600 dark:text-gray-300">
                                {{ $account->deleted_at?->format('M d, Y') }}
                            </td>

                            <!-- ACTION -->
                            <td class="px-5 py-4 text-center">
                                <button
                                    type="button"
                                    data-restore-action="{{ route('superadmin.archives.accounts.restore', $account->id) }}"
                                    data-restore-name="{{ $account->fname }} {{ $account->lname }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-[#DCE6D4] bg-[#EAF0E5] px-3.5 py-2 text-xs font-semibold text-[#52693A] transition hover:border-[#607844] hover:bg-[#DCE6D4] focus:outline-none focus:ring-2 focus:ring-[#607844]/30">

                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 12a9 9 0 1 0 3-6.7M3 4v5h5M12 7v5l3 2" />
                                    </svg>

                                    Restore
                                </button>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="px-5 py-14 text-center">

                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-700">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                        stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7h5l2 2h11v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z" />
                                    </svg>
                                </div>

                                <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    No archived accounts found
                                </p>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @if(request('search'))
                                        Try searching with a different name or email.
                                    @else
                                        Archived accounts will appear here.
                                    @endif
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($accounts->hasPages())
            <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-700">
                {{ $accounts->withQueryString()->links() }}
            </div>
        @endif

    </div>

</div>


<!-- RESTORE CONFIRMATION DIALOG -->
<dialog
    id="restoreAccountDialog"
    class="w-[min(92vw,28rem)] rounded-2xl border-0 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50 dark:bg-gray-800">

    <div class="p-6">

        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#EAF0E5] text-[#52693A]">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 12a9 9 0 1 0 3-6.7M3 4v5h5M12 7v5l3 2" />
            </svg>
        </div>

        <h2 class="text-lg font-bold text-gray-800 dark:text-white">
            Restore Archived Account?
        </h2>

        <p id="restoreAccountMessage"
            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
            This account will be able to sign in again.
        </p>

        <form id="restoreAccountForm" method="POST" class="mt-6 flex justify-end gap-3">
            @csrf

            <!-- CANCEL -->
            <button
                type="button"
                id="cancelRestoreAccount"
                class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Cancel
            </button>

            <!-- CONFIRM RESTORE -->
            <button
                type="submit"
                class="rounded-lg bg-[#607844] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#52693A] focus:outline-none focus:ring-2 focus:ring-[#607844]/40 focus:ring-offset-2">
                Restore Account
            </button>

        </form>

    </div>
</dialog>


<!-- RESTORE DIALOG SCRIPT -->
<script>
(() => {
    const dialog = document.getElementById('restoreAccountDialog');
    const form = document.getElementById('restoreAccountForm');
    const message = document.getElementById('restoreAccountMessage');
    const cancelButton = document.getElementById('cancelRestoreAccount');

    if (!dialog || !form || !message || !cancelButton) return;

    document.querySelectorAll('[data-restore-action]').forEach(button => {
        button.addEventListener('click', () => {
            form.action = button.dataset.restoreAction;

            message.textContent =
                `Restore ${button.dataset.restoreName}? This account will be able to sign in again.`;

            dialog.showModal();
        });
    });

    cancelButton.addEventListener('click', () => {
        dialog.close();
    });

    dialog.addEventListener('click', event => {
        if (event.target === dialog) {
            dialog.close();
        }
    });

    dialog.addEventListener('close', () => {
        form.action = '';
    });
})();
</script>

@endsection
