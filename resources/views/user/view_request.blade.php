@extends('layouts.apps')

@section('content')
    @php
        $statusStyles = [
            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
            'approved' => 'bg-blue-50 text-blue-700 border-blue-200',
            'in_progress' => 'bg-violet-50 text-violet-700 border-violet-200',
            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
        ];
     @endphp

    <div class="py-5">
        <div class="flex items-center justify-between gap-3 mb-5">
            <a href="{{ route('trackReq') }}" class="text-sm font-medium text-gray-500 hover:text-indigo-600">← Back to My
                Requests</a>
        </div>
    </div>

    <section
        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
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
        class="mt-5 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-sm font-bold">Updates & Remarks</h2>
            <p class="mt-1 text-xs text-gray-500">The complete history of updates made to your request.</p>
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

    <div class="relative pl-8 pb-6 last:pb-0">
        @if(!$loop->last)
            <span class="absolute left-[7px] top-4 bottom-0 w-px bg-gray-200"></span>
        @endif

        <span class="absolute left-0 top-1 w-4 h-4 rounded-full border-2 border-white bg-indigo-500"></span>

        <div class="flex flex-col gap-2 sm:flex-row sm:justify-between">
            <div class="min-w-0">
                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ trim(($log->updatedBy?->fname ?? '') . ' ' . ($log->updatedBy?->lname ?? '')) ?: 'System' }}
                </div>

                <div class="mt-1 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-[10px] uppercase tracking-wide text-gray-600 dark:text-gray-300 font-semibold">
                        {{ $actionLabel }}
                    </span>

                    @if($log->status)
                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-emerald-50 text-emerald-700 text-[10px] uppercase tracking-wide font-semibold">
                            {{ ucfirst(str_replace('_', ' ', $log->status)) }}
                        </span>
                    @endif
                </div>

                @if($isMaterialLog)
                    <div class="mt-3 space-y-2">
                        @if($log->action === 'materials_issued')
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                <span class="font-semibold text-gray-800 dark:text-gray-100">
                                    {{ $logData['item_name'] ?? 'Material' }}
                                </span>
                                — {{ $logData['quantity_issued'] ?? 0 }}
                                {{ $logData['unit'] ?? '' }}

                                @if(!empty($logData['handling_type']))
                                    <span class="text-xs text-gray-400">
                                        ({{ ucfirst($logData['handling_type']) }})
                                    </span>
                                @endif
                            </div>

                        @elseif($log->action === 'materials_returned')
                            @foreach(($logData['items'] ?? []) as $item)
                                @php
                                    $returnedQuantity = $item['quantity']
                                        ?? (
                                            ($item['good'] ?? 0)
                                            + ($item['damaged'] ?? 0)
                                            + ($item['missing'] ?? 0)
                                        );
                                @endphp

                                <div class="text-sm text-gray-600 dark:text-gray-300">
                                    <span class="font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $item['item_name'] ?? 'Material' }}
                                    </span>
                                    — {{ $returnedQuantity }}
                                    {{ $item['unit'] ?? '' }} returned
                                </div>
                            @endforeach

                            @if(!empty($logData['notes']))
                                <p class="text-sm text-gray-500 dark:text-gray-400 whitespace-pre-line">
                                    Remarks: {{ $logData['notes'] }}
                                </p>
                            @endif
                        @endif
                    </div>

                @elseif($log->remarks)
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">
                        {{ $log->remarks }}
                    </p>
                @endif
            </div>

            <time class="shrink-0 text-[11px] text-gray-400">
                {{ $log->created_at?->format('M d, Y h:i A') }}
            </time>
        </div>
    </div>
@empty
    <div class="py-8 text-center text-sm text-gray-400">
        No updates recorded yet.
    </div>
@endforelse
        </div>
    </section>
    </div>
@endsection