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
                background: #fff !important;
                color: #111827 !important;
                font-family: Arial, Helvetica, sans-serif !important;
            }

            aside,
            header,
            .no-print {
                display: none !important;
            }

            main {
                overflow: visible !important;
                height: auto !important;
            }

            main>div {
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .print-card {
                border: 1px solid #d1d5db !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                break-inside: avoid;
            }
        }

        @media print {

            /* Hide the shared application layout */
            body * {
                visibility: hidden !important;
            }

            /* Show only the request record and its history */
            .print-page,
            .print-page * {
                visibility: visible !important;
            }

            /* Start the record at the top of the printed page */
            .print-page {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                max-width: none !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            /* Hide navigation buttons and other screen-only elements */
            .no-print {
                display: none !important;
            }

            /* Keep the record readable on paper */
            .print-card {
                background: #fff !important;
                color: #111827 !important;
                border: 1px solid #d1d5db !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                overflow: visible !important;
                break-inside: auto !important;
                page-break-inside: auto !important;
            }

            /* Show the report heading only when printing */
            .print-header {
                display: block !important;
            }

            /* Do not print an image viewer overlay */
            #imageFullscreen {
                display: none !important;
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
    @endphp

    <div class="py-5 print-page">
        <div class="flex items-center justify-between gap-3 mb-5 no-print">
            <a href="{{ route('trackReq') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">← Back to My
                Requests</a>
            <button type="button" onclick="window.print()"
                class="px-4 py-2.5 rounded-xl bg-[#607844] hover:bg-[#476129] text-white text-sm font-semibold">
                Print Request
            </button>
        </div>

        <div class="hidden print:block mb-6">
            <div class="border-b-2 border-gray-900 pb-4">
                <div class="text-xs font-bold tracking-wider text-gray-500">CDSO MAINTENANCE</div>
                <div class="text-2xl font-bold">MAINTENANCE REQUEST RECORD</div>
            </div>
        </div>

        <section
            class="print-card bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-5 border-b border-gray-100 dark:border-gray-700">
                <div class="flex flex-wrap items-center gap-2">
                    <span
                        class="font-mono text-xs font-bold px-2.5 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">
                        {{ $requestData->ticket_number }}
                    </span>
                    <span
                        class="px-2.5 py-1.5 rounded-lg border text-[11px] font-bold {{ $statusStyles[$requestData->status] ?? '' }}">
                        {{ ucfirst(str_replace('_', ' ', $requestData->status)) }}
                    </span>
                </div>
                <h1 class="mt-3 text-xl font-bold text-gray-900 dark:text-white">{{ $requestData->title }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $requestData->category }} · {{ $requestData->location }}</p>
            </div>

            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Submitted</div>
                    <div class="mt-1 text-sm font-semibold">{{ $requestData->created_at?->format('M d, Y h:i A') }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Assigned personnel</div>
                    <div class="mt-1 text-sm font-semibold">
                        {{ $requestData->assignedPersonnel ? $requestData->assignedPersonnel->fname . ' ' . $requestData->assignedPersonnel->lname : 'Not assigned yet' }}
                    </div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Priority</div>
                    <div class="mt-1 text-sm font-semibold">{{ ucfirst($requestData->priority) }}</div>
                </div>
                <div>
                    <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Location</div>
                    <div class="mt-1 text-sm font-semibold">{{ $requestData->location }}</div>
                </div>
                <div class="md:col-span-2">
                    <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Description</div>
                    <div class="mt-2 text-sm leading-6 whitespace-pre-line">{{ $requestData->description }}</div>
                </div>
                @if($requestData->images->isNotEmpty())
                    <div class="md:col-span-2">
                        <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Attached photos</div>
                        <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($requestData->images as $image)
                                <a href="{{ route('maintenance-images.show', $image->id) }}" target="_blank" rel="noopener"
                                    class="block">
                                    <img src="{{ route('maintenance-images.show', $image->id) }}"
                                        alt="Photo attached to {{ $requestData->ticket_number }}"
                                        class="w-full h-36 object-cover rounded-lg border border-gray-200" loading="lazy">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section
            class="print-card mt-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-bold">Updates & Remarks</h2>
                <p class="mt-1 text-xs text-gray-500">The complete history of updates made to your request.</p>
            </div>

            <div class="p-5">
                @forelse($requestData->logs->sortByDesc('created_at') as $log)
                    <div class="relative pl-8 pb-6 last:pb-0">
                        @if(!$loop->last)<span class="absolute left-[7px] top-4 bottom-0 w-px bg-gray-200"></span>@endif
                        <span class="absolute left-0 top-1 w-4 h-4 rounded-full border-2 border-white bg-indigo-500"></span>
                        <div class="flex flex-col gap-1 sm:flex-row sm:justify-between">
                            <div>
                                <div class="text-sm font-semibold">{{ $log->updatedBy?->fname }} {{ $log->updatedBy?->lname }}
                                </div>
                                <div class="mt-1 text-[10px] uppercase text-gray-400 font-semibold">
                                    {{ str_replace('_', ' ', $log->action ?? 'update') }}
                                    @if($log->status) · {{ ucfirst(str_replace('_', ' ', $log->status)) }} @endif
                                </div>
                                @if($log->remarks)
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $log->remarks }}
                                    </p>
                                @endif
                            </div>
                            <time class="text-[11px] text-gray-400">{{ $log->created_at?->format('M d, Y h:i A') }}</time>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-sm text-gray-400">No updates recorded yet.</div>
                @endforelse
            </div>
        </section>

        <div class="hidden print:flex mt-8 pt-4 border-t border-gray-300 justify-between text-[10px] text-gray-500">
            <span>CDSO Maintenance Management System</span>
            <span>Printed {{ now()->format('M d, Y h:i A') }}</span>
        </div>
    </div>
@endsection