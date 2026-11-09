@extends('layouts.apps')

@section('content')
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 12mm;
    }

    html,
    body {
        margin: 0 !important;
        padding: 0 !important;
        width: auto !important;
        min-height: 0 !important;
        height: auto !important;
        overflow: visible !important;
        background: #fff !important;
        color: #111827 !important;
        font-family: Arial, Helvetica, sans-serif !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* Hide application navigation and controls. */
    aside,
    header,
    nav,
    button,
    .no-print,
    .print-hidden,
    .sidebar {
        display: none !important;
    }

    /* Remove shared layout sizing and positioning constraints. */
    #app,
    main,
    main > div,
    body > div {
        width: auto !important;
        max-width: none !important;
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        transform: none !important;
        position: static !important;
    }

    /* Keep the report at a readable A4 content width. */
    .print-page {
        display: block !important;
        position: static !important;
        width: 100% !important;
        max-width: 186mm !important;
        margin: 0 auto !important;
        padding: 0 !important;
        overflow: visible !important;
        box-sizing: border-box !important;
    }

    .print-header {
        display: block !important;
    }

    .print-card {
        width: 100% !important;
        max-width: 100% !important;
        background: #fff !important;
        color: #111827 !important;
        border: 1px solid #d1d5db !important;
        border-radius: 5px !important;
        box-shadow: none !important;
        overflow: visible !important;
        box-sizing: border-box !important;
        break-inside: auto;
        page-break-inside: auto;
    }

    .timeline-item,
    tr,
    img {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .print-break-before {
        break-before: page;
        page-break-before: always;
    }

    /* Prevent attached photos from being stretched or distorted. */
    .print-page img {
        max-width: 100% !important;
        height: auto !important;
        object-fit: contain !important;
    }

    .print-page a {
        color: #111827 !important;
        text-decoration: none !important;
    }

    .print-page table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    .print-page th,
    .print-page td {
        overflow-wrap: anywhere;
    }

    .print-page .overflow-x-auto {
        overflow: visible !important;
    }

    .print-page .hidden.print\\:block,
    .print-page .hidden.print\\:flex {
        display: block !important;
    }

    .print-page .hidden.print\\:flex {
        display: flex !important;
    }
}
</style>

@php
    $statusStyles = [
        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
        'approved' => 'bg-blue-50 text-blue-700 border-blue-200',
        'in_progress' => 'bg-violet-50 text-violet-700 border-violet-200',
        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
    ];

    $currentAssignment = $requestData->assignedPersonnel;
@endphp

<div class="py-5 print-page">
    <!-- Top actions -->

<div class="no-print mb-5">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <!-- Back navigation -->
        <a href="{{ route('maintenanceRequest.admin') }}"
           class="inline-flex w-fit min-h-10 items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-[#607844] dark:text-gray-300 dark:hover:bg-gray-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Maintenance Requests
        </a>

        <!-- Action buttons -->
        <div class="flex w-full items-center gap-2 sm:w-auto">

            @if(in_array($requestData->status, ['completed', 'rejected'], true) && !$requestData->trashed())
                <button type="button" id="openArchiveRequestDialog"
                        class="inline-flex min-h-10 flex-1 items-center justify-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-100 sm:flex-none sm:text-sm">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="1.8"
                              d="M3 6h18M8 6V4h8v2m3 0-1 14H6L5 6"/>
                    </svg>
                    Archive
                </button>
            @endif

            <!-- Mobile: Download PDF -->
            <button type="button" id="downloadRequestPdf"
                    class="inline-flex min-h-10 flex-1 items-center justify-center gap-2 rounded-lg bg-[#607844] px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#52693a] focus:outline-none focus:ring-2 focus:ring-[#607844]/30 focus:ring-offset-2 sm:hidden">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="1.8"
                          d="M12 3v12m0 0 4-4m-4 4-4-4M5 17v3h14v-3"/>
                </svg>
                <span>Download PDF</span>
            </button>

            <!-- Desktop: Print -->
            <button type="button" onclick="window.print()"
                    class="hidden min-h-10 items-center justify-center gap-2 rounded-lg bg-[#607844] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#52693a] focus:outline-none focus:ring-2 focus:ring-[#607844]/30 focus:ring-offset-2 sm:inline-flex">
                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="1.8"
                          d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5h-2m-2-5H8v8h8v-8z"/>
                </svg>
                Print Request
            </button>

        </div>
    </div>
</div>


    @if(session('success'))
        <div class="no-print mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="no-print mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="no-print mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <p class="font-semibold">Please review the following:</p>
            <ul class="mt-1 list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Print header -->
    <div class="hidden print:block mb-4 print-header">
        <div class="flex items-start justify-between border-b-2 border-gray-900 pb-4">
            <div>
                <div class="text-xs font-bold tracking-[0.18em] text-gray-500">CDSO</div>
                <div class="text-2xl font-bold text-gray-900">MAINTENANCE REQUEST RECORD</div>
                <div class="text-xs text-gray-500 mt-1">Official request and maintenance activity record</div>
            </div>
            <div class="text-right">
                <div class="font-mono text-lg font-bold">{{ $requestData->ticket_number }}</div>
                <div class="text-xs text-gray-500">{{ now()->format('M d, Y h:i A') }}</div>
            </div>
        </div>
    </div>

    
@if(in_array($requestData->status, ['completed', 'rejected'], true) && !$requestData->trashed())
    <dialog id="archiveRequestDialog" class="w-[min(92vw,30rem)] rounded-2xl p-0 shadow-2xl backdrop:bg-slate-950/50">
        <div class="p-6">
            <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9 2.1 18a2 2 0 0 0 1.7 3h16.4a2 2 0 0 0 1.7-3l-8.2-14.1a2 2 0 0 0-3.4 0Z"/>
                </svg>
            </div>

            <h2 class="text-lg font-bold text-gray-900">Archive maintenance request?</h2>

            <p class="mt-2 text-sm text-gray-600">
                Archive {{ $requestData->ticket_number ?? ('#'.$requestData->id) }} — {{ $requestData->title }}?
            </p>

            <p class="mt-3 rounded-lg bg-amber-50 p-3 text-xs text-amber-800">
                The request will be hidden from active records but can be restored later. Its history will be preserved.
            </p>

            <form id="archiveRequestForm" method="POST"
                action="{{ auth()->user()->role->name === 'superadmin' ? route('superadmin.requests.archive', $requestData->id) : route('admin.requests.archive', $requestData->id) }}"
                class="mt-6 flex justify-end gap-3">

                @csrf

                <button type="button" id="cancelArchiveRequest"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">
                    Cancel
                </button>

                <button type="submit"
                    class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
                    Archive request
                </button>
            </form>
        </div>
    </dialog>

    <script>
        (() => {
            const dialog = document.getElementById('archiveRequestDialog');
            const openButton = document.getElementById('openArchiveRequestDialog');
            const cancelButton = document.getElementById('cancelArchiveRequest');

            if (!dialog || !openButton || !cancelButton) return;

            openButton.addEventListener('click', () => dialog.showModal());
            cancelButton.addEventListener('click', () => dialog.close());

            dialog.addEventListener('click', event => {
                if (event.target === dialog) dialog.close();
            });
        })();
    </script>
@endif


    <!-- Summary -->
    <section class="print-card bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-5 border-b border-gray-100 dark:border-gray-700">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-xs font-bold px-2.5 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">
                            {{ $requestData->ticket_number }}
                        </span>
                        <span class="px-2.5 py-1.5 rounded-lg border text-[11px] font-bold {{ $statusStyles[$requestData->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                            {{ ucfirst(str_replace('_', ' ', $requestData->status)) }}
                        </span>
                    </div>
                    <h1 class="mt-3 text-xl font-bold text-gray-900 dark:text-white">{{ $requestData->title }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $requestData->category }} · {{ $requestData->location }}</p>
                </div>

                <div class="text-left md:text-right">
                    <div class="text-[11px] uppercase tracking-wider text-gray-400">Submitted</div>
                    <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $requestData->created_at?->format('M d, Y h:i A') }}</div>
                    @if($requestData->date_completed)
                        <div class="mt-2 text-[11px] uppercase tracking-wider text-gray-400">Completed</div>
                        <div class="text-sm font-semibold text-emerald-700">{{ $requestData->date_completed->format('M d, Y h:i A') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Requester</div>
                <div class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200">
                    {{ $requestData->user?->fname }} {{ $requestData->user?->lname }}
                </div>
                <div class="text-xs text-gray-500">{{ $requestData->user?->email ?? 'Deleted user' }}</div>
            </div>

            <div>
                <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Assigned personnel</div>
                <div class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200">
                    {{ $currentAssignment ? $currentAssignment->fname.' '.$currentAssignment->lname : 'Not assigned' }}
                </div>
                @if($requestData->assigned_at)
                    <div class="text-xs text-gray-500">Assigned {{ $requestData->assigned_at->format('M d, Y h:i A') }}</div>
                @endif
            </div>

            <div>
                <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Priority</div>
                <div class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ ucfirst($requestData->priority) }}</div>
            </div>

            <div>
                <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Location</div>
                <div class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $requestData->location }}</div>
            </div>

            <div class="text-sm leading-6 text-gray-700 dark:text-gray-300 whitespace-pre-line">
    {{ filled($requestData->description)
        ? $requestData->description
        : 'No description provided by the requester.' }}
</div>
            @if($requestData->images->isNotEmpty())
                <div class="md:col-span-2">
                    <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Attached photos</div>
                    <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-3 print:grid-cols-3">
                        @foreach($requestData->images as $image)
                            <a href="{{ route('maintenance-images.show', $image->id) }}" target="_blank" rel="noopener" class="block">
                                <img src="{{ route('maintenance-images.show', $image->id) }}" alt="Photo attached to {{ $requestData->ticket_number }}" class="w-full h-36 object-cover rounded-lg border border-gray-200 print:h-auto print:aspect-auto print:object-contain" loading="lazy">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Admin action panel -->
    <section class="no-print mt-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-sm font-bold text-gray-800 dark:text-white">Request Management</h2>
            <p class="mt-1 text-xs text-gray-500">
                You can assign multiple requests to the same personnel. They can hold several approved assignments, but can only work on one request at a time. Use Pull Back Assignment to withdraw this request and return it to Pending.
            </p>
        </div>

        <form id="requestManagementForm" method="POST" action="{{ route('updateRequest.admin', $requestData->id) }}" class="p-5">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="save" id="requestAction">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
        Priority
    </label>
    <select name="priority"
            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm text-gray-800 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        @foreach(['low','medium','high'] as $priority)
            <option value="{{ $priority }}" @selected($requestData->priority === $priority)>
                {{ ucfirst($priority) }}
            </option>
        @endforeach
    </select>
</div>

                <div class="md:col-span-2">
                    <label for="personnelSearch" class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                        Assign maintenance personnel
                    </label>

                    @php
                        $personnelAssignmentDisabled = in_array($requestData->status, ['rejected', 'completed'], true);
                    @endphp

                    {{-- Keep the original form field so the controller receives assigned_to as before. --}}
                    <select name="assigned_to" id="personnelSelect" class="hidden"
                            {{ $personnelAssignmentDisabled ? 'disabled' : '' }}>
                        <option value="">Select personnel</option>
                        @foreach($personnels as $person)
                            @php
                                $personnelUnavailable = $activePersonnelIds->contains($person->id);
                                $isCurrentAssignee = $currentAssignment?->id === $person->id;
                            @endphp
                            <option
                                value="{{ $person->id }}"
                                @selected($isCurrentAssignee)
                            >
                                {{ $person->fname }} {{ $person->lname }}{{ $personnelUnavailable && !$isCurrentAssignee ? ' — Currently in task (can receive another assignment)' : '' }}
                            </option>
                        @endforeach
                    </select>

                    <div class="relative" id="personnelPicker">
                        <div class="flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 dark:border-gray-600 dark:bg-gray-700 {{ $personnelAssignmentDisabled ? 'opacity-60' : '' }}">
                            <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="11" cy="11" r="7" stroke-width="2"/>
                                <path stroke-linecap="round" stroke-width="2" d="m16 16 4 4"/>
                            </svg>
                            <input
                                type="text"
                                id="personnelSearch"
                                autocomplete="off"
                                placeholder="Search by name or designation..."
                                class="min-w-0 w-full border-0 bg-transparent py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-0 dark:text-white"
                                {{ $personnelAssignmentDisabled ? 'disabled' : '' }}
                            >
                        </div>

                        <div id="personnelOptions"
                             class="absolute z-30 mt-1 hidden max-h-52 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-600 dark:bg-gray-800">
                            @foreach($personnels as $person)
                                @php
                                    $personnelUnavailable = $activePersonnelIds->contains($person->id);
                                    $isCurrentAssignee = $currentAssignment?->id === $person->id;
                                @endphp
                                <button
                                    type="button"
                                    class="personnel-option block w-full px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700"
                                    data-value="{{ $person->id }}"
                                    data-search="{{ strtolower($person->fname.' '.$person->lname) }}"
                                    data-label="{{ $person->fname }} {{ $person->lname }}"
                                    data-disabled="0"
                                >
                                    <span class="flex items-center justify-between gap-3">
                                        <span class="font-medium">{{ $person->fname }} {{ $person->lname }}</span>
                                        @if($personnelUnavailable && !$isCurrentAssignee)
                                            <span class="shrink-0 rounded-full bg-amber-50 px-2 py-1 text-[10px] font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Currently in task</span>
                                        @elseif($isCurrentAssignee)
                                            <span class="shrink-0 rounded-full bg-[#607844]/10 px-2 py-1 text-[10px] font-semibold text-[#607844] dark:text-[#a9c18e]">Current assignee</span>
                                        @endif
                                    </span>
                                </button>
                            @endforeach
                            <p id="noPersonnelResults" class="hidden px-4 py-3 text-sm text-gray-400">
                                No matching personnel found.
                            </p>
                        </div>
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400">Personnel marked “Currently in task” can still receive this assignment. They will start it after finishing their current In Progress request. Use Pull Back Assignment to remove the current assignee and return this request to Pending.</p>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                        Remarks / comments
                        <span class="font-normal text-gray-400">— optional for updates; required when pulling back an assignment</span>
                    </label>
                    <textarea name="remarks" rows="3"
                              placeholder="Add a note about this request, assignment, instruction, or decision..."
                              class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-2 mt-5">
    @if($requestData->status === 'pending')

<button
    type="submit"
    onclick="document.getElementById('requestAction').value = 'reject'"
    class="px-4 py-2.5 rounded-xl border border-red-200 bg-red-50 text-red-700 text-sm font-semibold hover:bg-red-100">
    Reject Request
</button>

        <button
            type="submit"
            onclick="document.getElementById('requestAction').value = 'approve'"
            class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
            Approve & Assign
        </button>

    @else

        @if(in_array($requestData->status, ['approved', 'in_progress'], true) && $currentAssignment)
            <button
                type="button"
                id="openPullBackModal"
                class="px-4 py-2.5 rounded-xl border border-amber-300 bg-amber-50 text-amber-800 text-sm font-semibold hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                Pull Back Assignment
            </button>
        @endif

        <button
            type="submit"
            onclick="document.getElementById('requestAction').value = 'save'"
            class="px-5 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-indigo-700">
            Save Update
        </button>

    @endif
</div>
        </form>

        {{-- Custom confirmation modal for pulling back an assignment. --}}
        <div id="pullBackConfirmationModal" class="no-print fixed inset-0 z-[100] hidden items-center justify-center bg-gray-950/60 px-4 py-6" aria-hidden="true">
            <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 dark:bg-gray-800" role="dialog" aria-modal="true" aria-labelledby="pullBackModalTitle" aria-describedby="pullBackModalDescription">
                <div class="flex items-start gap-4 border-b border-gray-100 px-5 py-5 dark:border-gray-700">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path d="M12 9v4m0 4h.01M10.3 3.9 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3l-7.5-13.1a2 2 0 0 0-3.4 0Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 id="pullBackModalTitle" class="text-base font-bold text-gray-900 dark:text-white">Pull back this assignment?</h3>
                        <p id="pullBackModalDescription" class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-300">This request will return to <strong>Pending</strong>, and the currently assigned personnel will be released. The assignment action and your reason will remain in the activity history.</p>
                    </div>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Reason provided</p>
                    <p id="pullBackReasonPreview" class="mt-1.5 whitespace-pre-wrap break-words rounded-xl bg-gray-50 px-3 py-3 text-sm text-gray-700 dark:bg-gray-700/70 dark:text-gray-200">No reason provided.</p>
                </div>
                <div class="flex flex-col-reverse gap-2 border-t border-gray-100 bg-gray-50 px-5 py-4 sm:flex-row sm:justify-end dark:border-gray-700 dark:bg-gray-800">
                    <button type="button" id="cancelPullBack" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Cancel</button>
                    <button type="button" id="confirmPullBack" class="rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">Yes, Pull Back Assignment</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Activity history -->
    <section class="print-card mt-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-gray-800 dark:text-white">Activity & Remarks</h2>
                    <p class="mt-1 text-xs text-gray-500">Every meaningful update is recorded here, including comments made without changing status.</p>
                </div>
                <span class="text-xs text-gray-400">{{ $requestData->logs->count() }} entries</span>
            </div>
        </div>

        <div class="p-5">
            
@forelse($requestData->logs->sortByDesc('created_at') as $log)
    @php
        $logData = json_decode($log->remarks, true);
        $isMaterialLog = in_array($log->action, [
            'materials_issued',
            'materials_returned'
        ], true) && is_array($logData);

        $actionLabel = match ($log->action) {
            'materials_issued' => 'Materials Issued',
            'materials_returned' => 'Materials Returned',
            'status_update' => 'Status Update',
            default => ucfirst(str_replace('_', ' ', $log->action ?? 'update')),
        };
    @endphp

    <div class="timeline-item relative pl-8 pb-6 last:pb-0">
        @if(!$loop->last)
            <span class="absolute left-[7px] top-4 bottom-0 w-px bg-gray-200"></span>
        @endif

        <span class="absolute left-0 top-1 w-4 h-4 rounded-full border-2 border-white shadow-sm bg-indigo-500"></span>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-semibold text-gray-800 dark:text-white">
                        {{ $log->updatedBy?->fname }} {{ $log->updatedBy?->lname }}
                    </span>

                    <span class="inline-flex px-2 py-1 rounded-md bg-gray-100 text-gray-600 text-[10px] font-semibold uppercase">
                        {{ $actionLabel }}
                    </span>

                    @if($log->status)
                        <span class="inline-flex px-2 py-1 rounded-md text-[10px] font-semibold {{ $statusStyles[$log->status] ?? 'bg-gray-50 text-gray-600 border border-gray-200' }}">
                            {{ ucfirst(str_replace('_', ' ', $log->status)) }}
                        </span>
                    @endif
                </div>

                @if($isMaterialLog)
                    <div class="mt-3 rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 mb-2">
                            {{ $log->action === 'materials_issued' ? 'Issued materials' : 'Returned materials' }}
                        </p>

                        @if($log->action === 'materials_issued')
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $logData['item_name'] ?? 'Inventory item' }}
                                — {{ $logData['quantity_issued'] ?? 0 }}
                                {{ $logData['unit'] ?? '' }}
                                <span class="text-xs text-gray-400">
                                    ({{ ucfirst($logData['handling_type'] ?? 'consumed') }})
                                </span>
                            </p>
                        @else
                            @foreach(($logData['items'] ?? []) as $item)
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ $item['item_name'] ?? 'Inventory item' }}
                                    — {{ $item['quantity'] ?? 0 }}
                                    {{ $item['unit'] ?? '' }}
                                </p>
                            @endforeach

                            @if(!empty($logData['notes']))
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <strong>Remarks:</strong> {{ $logData['notes'] }}
                                </p>
                            @endif
                        @endif
                    </div>
                @elseif($log->remarks)
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300 whitespace-pre-line">
                        {{ $log->remarks }}
                    </p>
                @endif
            </div>

            <time class="text-[11px] text-gray-400 whitespace-nowrap">
                {{ $log->created_at?->format('M d, Y h:i A') }}
            </time>
        </div>
    </div>
@empty
    <div class="py-8 text-center text-sm text-gray-400">
        No activity has been recorded yet.
    </div>
@endforelse

        </div>
    </section>

    <!-- Assignment history -->
    @if($requestData->assignments->isNotEmpty())
        <section class="print-card mt-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-bold text-gray-800 dark:text-white">Assignment History</h2>
                <p class="mt-1 text-xs text-gray-500">Previous assignments are retained for accountability.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/40 text-[11px] uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Personnel</th>
                            <th class="px-5 py-3">Assigned by</th>
                            <th class="px-5 py-3">Assignment date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($requestData->assignments->sortBy('assigned_at') as $assignment)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-700 dark:text-gray-200">
                                    {{ $assignment->personnel?->fname }} {{ $assignment->personnel?->lname }}
                                </td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $assignment->assignedBy?->fname }} {{ $assignment->assignedBy?->lname }}
                                </td>
                                <td class="px-5 py-3 text-gray-500">
                                    {{ \Illuminate\Support\Carbon::parse($assignment->assigned_at ?? $assignment->created_at)->format('M d, Y h:i A') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <!-- Inventory used -->
    @if($requestData->inventoryUsages->count())
        <section class="print-card mt-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-bold text-gray-800 dark:text-white">Materials / Inventory Used</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Item</th>
                            <th class="px-5 py-3">Quantity</th>
                            <th class="px-5 py-3">Used by</th>
                            <th class="px-5 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($requestData->inventoryUsages as $usage)
                            <tr>
                                <td class="px-5 py-3 font-medium">{{ $usage->item?->name ?? 'Deleted item' }}</td>
                                <td class="px-5 py-3">{{ $usage->quantity_used }} {{ $usage->item?->unit }}</td>
                                <td class="px-5 py-3">{{ $usage->user?->fname }} {{ $usage->user?->lname }}</td>
                                <td class="px-5 py-3">{{ $usage->used_at?->format('M d, Y h:i A') ?? $usage->created_at?->format('M d, Y h:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <!-- Print footer -->
    <div class="hidden print:flex mt-8 pt-4 border-t border-gray-300 justify-between text-[10px] text-gray-500">
        <span>CDSO Maintenance Management System</span>
        <span>Printed {{ now()->format('M d, Y h:i A') }}</span>
    </div>
</div>

{{-- Client-side PDF generation is used only by the mobile Download PDF button. --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
        referrerpolicy="no-referrer"></script>
<script>
    (() => {
        const downloadButton = document.getElementById('downloadRequestPdf');
        if (!downloadButton) return;

        downloadButton.addEventListener('click', async () => {
            const report = document.querySelector('.print-page');
            if (!report) return;

            if (typeof window.html2pdf !== 'function') {
                alert('The PDF tool could not be loaded. Please check your internet connection and try again.');
                return;
            }

            const originalLabel = downloadButton.innerHTML;
            downloadButton.disabled = true;
            downloadButton.classList.add('opacity-60', 'cursor-wait');
            downloadButton.textContent = 'Preparing PDF...';

            let temporaryWrapper;

            try {
                const clone = report.cloneNode(true);

                // Exclude controls and dialogs from the downloaded report.
                clone.querySelectorAll('.no-print, .print-hidden, dialog, script').forEach(element => element.remove());

                // Show the report header and footer that are normally visible only when printing.
                const printHeader = clone.querySelector('.print-header');
                if (printHeader) printHeader.style.display = 'block';

                const printFooter = clone.querySelector('.hidden.print\\:flex');
                if (printFooter) printFooter.style.display = 'flex';

                // Remove screen-only overflow and sizing constraints from report sections.
                clone.querySelectorAll('.overflow-x-auto').forEach(element => {
                    element.style.overflow = 'visible';
                });

                temporaryWrapper = document.createElement('div');
                temporaryWrapper.style.cssText = 'position:fixed;left:-10000px;top:0;width:186mm;background:#ffffff;z-index:-1;';
                clone.style.cssText += ';width:186mm;max-width:186mm;margin:0;padding:0;background:#ffffff;color:#111827;';
                temporaryWrapper.appendChild(clone);
                document.body.appendChild(temporaryWrapper);

                const ticketNumber = @json($requestData->ticket_number ?? ('CDSO-' . $requestData->id));
                await window.html2pdf().set({
                    margin: [10, 10, 12, 10],
                    filename: `${ticketNumber}-maintenance-request.pdf`,
                    image: { type: 'jpeg', quality: 0.96 },
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: '#ffffff',
                        scrollX: 0,
                        scrollY: 0
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait'
                    },
                    pagebreak: {
                        mode: ['css', 'legacy'],
                        avoid: ['.timeline-item', 'tr', 'img']
                    }
                }).from(clone).save();
            } catch (error) {
                console.error('PDF generation failed:', error);
                alert('Unable to generate the PDF. Please try again.');
            } finally {
                temporaryWrapper?.remove();
                downloadButton.disabled = false;
                downloadButton.classList.remove('opacity-60', 'cursor-wait');
                downloadButton.innerHTML = originalLabel;
            }
        });
    })();
</script>

<script>
    (() => {
        const picker = document.getElementById('personnelPicker');
        const searchInput = document.getElementById('personnelSearch');
        const select = document.getElementById('personnelSelect');
        const optionsPanel = document.getElementById('personnelOptions');
        const noResults = document.getElementById('noPersonnelResults');

        if (!picker || !searchInput || !select || !optionsPanel) return;

        const options = Array.from(optionsPanel.querySelectorAll('.personnel-option'));

        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.value) {
            searchInput.value = selectedOption.textContent.trim();
        }

        const closeOptions = () => optionsPanel.classList.add('hidden');
        const openOptions = () => optionsPanel.classList.remove('hidden');

        const filterOptions = () => {
            const query = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            options.forEach(option => {
                const matches = (option.dataset.search || '').includes(query);
                option.classList.toggle('hidden', !matches);
                if (matches) visibleCount++;
            });

            noResults.classList.toggle('hidden', visibleCount !== 0);
            openOptions();
        };

        searchInput.addEventListener('focus', filterOptions);
        searchInput.addEventListener('input', filterOptions);

        options.forEach(option => {
            option.addEventListener('click', () => {
                if (option.disabled || option.dataset.disabled === '1') return;

                select.value = option.dataset.value;
                searchInput.value = option.dataset.label;
                closeOptions();
                select.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });

        document.addEventListener('click', event => {
            if (!picker.contains(event.target)) closeOptions();
        });

        searchInput.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeOptions();
        });
    })();
</script>


<script>
    (() => {
        const openButton = document.getElementById('openPullBackModal');
        const modal = document.getElementById('pullBackConfirmationModal');
        const cancelButton = document.getElementById('cancelPullBack');
        const confirmButton = document.getElementById('confirmPullBack');
        const reasonField = document.querySelector('#requestManagementForm textarea[name="remarks"]');
        const reasonPreview = document.getElementById('pullBackReasonPreview');
        const actionField = document.getElementById('requestAction');
        const form = document.getElementById('requestManagementForm');

        if (!openButton || !modal || !cancelButton || !confirmButton || !reasonField || !reasonPreview || !actionField || !form) return;

        let previouslyFocusedElement = null;

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
            previouslyFocusedElement?.focus();
        };

        openButton.addEventListener('click', () => {
            const reason = reasonField.value.trim();
            if (!reason) {
                reasonField.focus();
                reasonField.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                reasonField.setCustomValidity('Please provide a reason for pulling back this assignment.');
                reasonField.reportValidity();
                return;
            }

            reasonField.setCustomValidity('');
            reasonField.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
            reasonPreview.textContent = reason;
            previouslyFocusedElement = document.activeElement;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            confirmButton.focus();
        });

        reasonField.addEventListener('input', () => {
            reasonField.setCustomValidity('');
            reasonField.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
        });

        cancelButton.addEventListener('click', closeModal);
        modal.addEventListener('click', event => {
            if (event.target === modal) closeModal();
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
        });

        confirmButton.addEventListener('click', () => {
            if (!reasonField.value.trim()) {
                closeModal();
                reasonField.focus();
                reasonField.setCustomValidity('Please provide a reason for pulling back this assignment.');
                reasonField.reportValidity();
                return;
            }

            actionField.value = 'pull_back';
            confirmButton.disabled = true;
            confirmButton.textContent = 'Processing...';
            form.requestSubmit();
        });
    })();
</script>

@endsection
