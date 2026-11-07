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
        width: 100% !important;
        height: auto !important;
        overflow: visible !important;
        background: #fff !important;
        color: #111827 !important;
        font-family: Arial, Helvetica, sans-serif !important;
    }

    /* Hide application navigation and screen-only controls */
    aside,
    header,
    nav,
    .no-print,
    .print-hidden,
    button,
    .sidebar {
        display: none !important;
    }

    /* Remove layout constraints that can create blank pages */
    main,
    main > div,
    #app,
    body > div {
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
        overflow: visible !important;
        transform: none !important;
    }

    main > div {
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .print-page {
        display: block !important;
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        break-before: auto !important;
        page-break-before: auto !important;
    }

    /* Override Tailwind's print visibility utilities explicitly */
    .print\:block {
        display: block !important;
    }

    .print\:flex {
        display: flex !important;
    }

    .print\:hidden {
        display: none !important;
    }

    .print-card {
        background: #fff !important;
        color: #111827 !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        overflow: visible !important;
        break-inside: auto;
        page-break-inside: auto;
    }

    .timeline-item {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .print-break-before {
        break-before: page;
        page-break-before: always;
    }

    img {
        max-width: 100% !important;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }

    a {
        color: #111827 !important;
        text-decoration: none !important;
    }
}.print-header {
    display: none !important;
}

@media print {
    .print-header {
        display: block !important;
    }
}@media print {
    /* Hide everything from the shared application layout */
    body * {
        visibility: hidden !important;
    }

    /* Show only the maintenance request record */
    .print-page,
    .print-page * {
        visibility: visible !important;
    }

    /* Position the record at the top of the printed page */
    .print-page {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
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
<div class="flex items-center justify-between gap-3 mb-5 no-print">
    <a href="{{ route('maintenanceRequest.admin') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-indigo-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Maintenance Requests
    </a>

    <div class="flex items-center gap-2">
        @if(in_array($requestData->status, ['completed', 'rejected'], true) && !$requestData->trashed())
            <button type="button" id="openArchiveRequestDialog"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-700">
                Archive Request
            </button>
        @endif

        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#607844] hover:bg-[#476129] text-white text-sm font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M6 9V3h12v6M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5h-2m-2-5H8v8h8v-8z"/>
            </svg>
            Print Request
        </button>
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

            <div class="md:col-span-2">
                <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Request description</div>
                <div class="mt-2 text-sm leading-6 text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $requestData->description }}</div>
            </div>
            @if($requestData->images->isNotEmpty())
                <div class="md:col-span-2">
                    <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Attached photos</div>
                    <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($requestData->images as $image)
                            <a href="{{ route('maintenance-images.show', $image->id) }}" target="_blank" rel="noopener" class="block">
                                <img src="{{ route('maintenance-images.show', $image->id) }}" alt="Photo attached to {{ $requestData->ticket_number }}" class="w-full h-36 object-cover rounded-lg border border-gray-200" loading="lazy">
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
                Admins approve/reject and manage assignments. Active requests may be reassigned; work progress and completion are handled by the current assignee.
            </p>
        </div>

        <form method="POST" action="{{ route('updateRequest.admin', $requestData->id) }}" class="p-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">Priority</label>
                    <select name="priority"
                            class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @foreach(['low','medium','high'] as $priority)
                            <option value="{{ $priority }}" @selected($requestData->priority === $priority)>{{ ucfirst($priority) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">Assign maintenance personnel</label>
                    <select name="assigned_to"
                            {{ in_array($requestData->status, ['rejected','completed']) ? 'disabled' : '' }}
                            class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white disabled:opacity-60">
                        <option value="">Select personnel</option>
                        @foreach($personnels as $person)
                            <option value="{{ $person->id }}" @selected($currentAssignment?->id === $person->id)>
                                {{ $person->fname }} {{ $person->lname }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                        Remarks / comments
                        <span class="font-normal text-gray-400">— optional; can be added without changing status</span>
                    </label>
                    <textarea name="remarks" rows="3"
                              placeholder="Add a note about this request, assignment, instruction, or decision..."
                              class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-2 mt-5">
                @if($requestData->status === 'pending')
                    <button name="action" value="reject" type="submit"
                            class="px-4 py-2.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 text-sm font-semibold hover:bg-rose-100">
                        Reject Request
                    </button>
                    <button name="action" value="approve" type="submit"
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                        Approve & Assign
                    </button>
                @else
                    <button name="action" value="save" type="submit"
                            class="px-5 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-indigo-700">
                        Save Update
                    </button>
                @endif
            </div>
        </form>
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
                <div class="timeline-item relative pl-8 pb-6 last:pb-0">
                    @if(!$loop->last)
                        <span class="absolute left-[7px] top-4 bottom-0 w-px bg-gray-200"></span>
                    @endif
                    <span class="absolute left-0 top-1 w-4 h-4 rounded-full border-2 border-white shadow-sm bg-indigo-500"></span>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">
                                    {{ $log->updatedBy?->fname }} {{ $log->updatedBy?->lname }}
                                </span>
                                <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-500 text-[10px] font-semibold uppercase">
                                    {{ str_replace('_', ' ', $log->action ?? 'update') }}
                                </span>
                                @if($log->status)
                                    <span class="px-2 py-0.5 rounded-md {{ $statusStyles[$log->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }} text-[10px] font-semibold">
                                        {{ ucfirst(str_replace('_', ' ', $log->status)) }}
                                    </span>
                                @endif
                            </div>
                            @if($log->remarks)
                                <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $log->remarks }}</p>
                            @endif
                        </div>
                        <time class="text-[11px] text-gray-400 whitespace-nowrap">{{ $log->created_at?->format('M d, Y h:i A') }}</time>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-sm text-gray-400">No activity has been recorded yet.</div>
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
@endsection
