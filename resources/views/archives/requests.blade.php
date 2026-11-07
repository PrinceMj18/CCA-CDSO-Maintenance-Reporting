
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
                        Archived Maintenance Requests
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Review and restore previously archived maintenance requests.
                    </p>
                </div>
            </div>

            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                Only completed or rejected requests can be archived. Restoring preserves the request's status and history.
            </p>
        </div>

        <!-- BACK BUTTON -->
        <a href="{{ auth()->user()->role->name === 'superadmin' ? route('superadminDashboard') : route('maintenanceRequest.admin') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-600 shadow-sm transition hover:bg-gray-50 hover:text-[#52693A] dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">

            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 12H5m7 7-7-7 7-7" />
            </svg>

            Back to Requests
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
                    placeholder="Search ticket, title, or location..."
                    aria-label="Search archived maintenance requests"
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
                <a href="{{ request()->url() }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    Clear
                </a>
            @endif

        </form>
    </div>


    <!-- ARCHIVED REQUESTS TABLE -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">

        <!-- TABLE HEADER -->
        <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <h2 class="text-sm font-bold text-gray-800 dark:text-white">
                Archived Request Records
            </h2>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                View request details and restore records when necessary.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-sm">

                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:bg-gray-700/60 dark:text-gray-300">
                    <tr>
                        <th class="px-5 py-4">Ticket</th>
                        <th class="px-5 py-4">Request</th>
                        <th class="px-5 py-4">Location</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Date Archived</th>
                        <th class="px-5 py-4 text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">

                    @forelse($requests as $item)

                        <tr class="transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-700/30">

                            <!-- TICKET NUMBER -->
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="font-mono text-xs font-semibold text-gray-600 dark:text-gray-300">
                                    {{ $item->ticket_number ?? ('#'.$item->id) }}
                                </span>
                            </td>

                            <!-- REQUEST -->
                            <td class="px-5 py-4">
                                <div class="max-w-xs">
                                    <p class="font-semibold text-gray-800 dark:text-white">
                                        {{ $item->title }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Requested by:
                                        {{ $item->user?->fname }} {{ $item->user?->lname }}
                                    </p>
                                </div>
                            </td>

                            <!-- LOCATION -->
                            <td class="px-5 py-4">
                                <div class="flex max-w-[200px] items-start gap-2 text-gray-600 dark:text-gray-300">
                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-gray-400"
                                        fill="none" stroke="currentColor"
                                        stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z" />
                                        <circle cx="12" cy="10" r="2.5" />
                                    </svg>

                                    <span class="text-sm">
                                        {{ $item->location }}
                                    </span>
                                </div>
                            </td>

                            <!-- STATUS -->
                            <td class="px-5 py-4">
                                <span class="inline-flex whitespace-nowrap rounded-md border border-[#DCE6D4] bg-[#EAF0E5] px-3 py-1 text-xs font-semibold text-[#52693A]">
                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                </span>
                            </td>

                            <!-- DATE ARCHIVED -->
                            <td class="whitespace-nowrap px-5 py-4 text-gray-600 dark:text-gray-300">
                                {{ $item->deleted_at?->format('M d, Y') }}
                            </td>

                            <!-- ACTION -->
                            <td class="px-5 py-4 text-center">
                                <button
                                    type="button"
                                    data-restore-request-action="{{ auth()->user()->role->name === 'superadmin' ? route('superadmin.archives.requests.restore', $item->id) : route('admin.archives.requests.restore', $item->id) }}"
                                    data-restore-request-name="{{ $item->ticket_number ?? ('#'.$item->id) }} — {{ $item->title }}"
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
                            <td colspan="6" class="px-5 py-14 text-center">

                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-700">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                        stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7h5l2 2h11v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z" />
                                    </svg>
                                </div>

                                <p class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    No archived requests found
                                </p>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    @if(request('search'))
                                        Try searching with a different ticket number, title, or location.
                                    @else
                                        Archived maintenance requests will appear here.
                                    @endif
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($requests->hasPages())
            <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-700">
                {{ $requests->withQueryString()->links() }}
            </div>
        @endif

    </div>

</div>


<!-- RESTORE CONFIRMATION DIALOG -->
<dialog
    id="restoreRequestDialog"
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
            Restore Maintenance Request?
        </h2>

        <p id="restoreRequestMessage"
            class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
            The request will return to active records with its existing status and history.
        </p>

        <form id="restoreRequestForm" method="POST" class="mt-6 flex justify-end gap-3">
            @csrf

            <!-- CANCEL -->
            <button
                type="button"
                id="cancelRestoreRequest"
                class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Cancel
            </button>

            <!-- CONFIRM RESTORE -->
            <button
                type="submit"
                class="rounded-lg bg-[#607844] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#52693A] focus:outline-none focus:ring-2 focus:ring-[#607844]/40 focus:ring-offset-2">
                Restore Request
            </button>

        </form>

    </div>
</dialog>


<!-- RESTORE DIALOG SCRIPT -->
<script>
(() => {
    const dialog = document.getElementById('restoreRequestDialog');
    const form = document.getElementById('restoreRequestForm');
    const message = document.getElementById('restoreRequestMessage');
    const cancelButton = document.getElementById('cancelRestoreRequest');

    if (!dialog || !form || !message || !cancelButton) return;

    document.querySelectorAll('[data-restore-request-action]').forEach(button => {
        button.addEventListener('click', () => {
            form.action = button.dataset.restoreRequestAction;

            message.textContent =
                `Restore ${button.dataset.restoreRequestName}? It will return to active records with its existing status and history.`;

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
