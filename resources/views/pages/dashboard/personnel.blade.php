<div class="space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">Your Work Dashboard</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your current task and the assigned requests waiting in your queue.</p>
        </div>
        <a href="{{ route('maintenanceRequest.personnel') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">View assigned tasks</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Assigned tasks</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $activeCount ?? 0 }}</p>
            <p class="mt-1 text-xs text-gray-500">Your current task and upcoming assignments</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ready to start</p>
            <p class="mt-2 text-3xl font-bold text-blue-600">{{ $approved ?? 0 }}</p>
            <p class="mt-1 text-xs text-gray-500">Approved assignments waiting for work to begin</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Currently in progress</p>
            <p class="mt-2 text-3xl font-bold text-violet-600">{{ $inProgress ?? 0 }}</p>
            <p class="mt-1 text-xs text-gray-500">Jobs already being worked on</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-900 dark:bg-amber-950/30">
            <p class="text-sm font-medium text-amber-800 dark:text-amber-300">High-priority assigned tasks</p>
            <p class="mt-2 text-3xl font-bold text-amber-700 dark:text-amber-300">{{ $highPriorityCount ?? 0 }}</p>
            <p class="mt-1 text-xs text-amber-700/80 dark:text-amber-300/80">Prioritize these when starting your next task</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 xl:col-span-2">
            <div class="flex items-start justify-between gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-700">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Current task & upcoming assignments</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">You may have several assigned requests, but only one can be In Progress at a time.</p>
                </div>
                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">{{ $workQueue->count() }} assigned</span>
            </div>
            <div class="p-5 space-y-4">
                @if($currentWork)
                    <div class="rounded-xl border border-[#607844]/30 bg-[#607844]/5 p-4 dark:bg-[#607844]/10">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-xs font-bold uppercase tracking-wide text-[#607844] dark:text-[#a9c18e]">Currently working on</span>
                            <span class="rounded-full bg-violet-100 px-2.5 py-1 text-[11px] font-semibold text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">In Progress</span>
                        </div>
                        <a href="{{ route('viewRequest.personnel', $currentWork->id) }}" class="mt-2 block">
                            <span class="font-mono text-xs font-semibold text-indigo-600 dark:text-indigo-300">{{ $currentWork->ticket_number }}</span>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $currentWork->title }}</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $currentWork->location }} · {{ $currentWork->category }}</p>
                            <span class="mt-3 inline-flex text-sm font-semibold text-[#607844] dark:text-[#a9c18e]">Open current task →</span>
                        </a>
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-5 text-center dark:border-gray-600 dark:bg-gray-700/30">
                        <p class="font-semibold text-gray-800 dark:text-white">No task is currently in progress</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if(($approved ?? 0) > 0)
                                You have approved assignments ready to start.
                            @else
                                You are currently available. No active assignment has been assigned to you.
                            @endif
                        </p>
                    </div>
                @endif

                <div>
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Upcoming assigned tasks</h4>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $approved ?? 0 }} waiting</span>
                    </div>
                    <div class="divide-y divide-gray-100 rounded-lg border border-gray-100 dark:divide-gray-700 dark:border-gray-700">
                        @forelse($workQueue->where('status', 'approved') as $work)
                            <a href="{{ route('viewRequest.personnel', $work->id) }}" class="block px-4 py-3 transition hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-mono text-xs font-semibold text-indigo-600 dark:text-indigo-300">{{ $work->ticket_number }}</span>
                                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[11px] font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Waiting to start</span>
                                            @if($work->priority === 'high')
                                                <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[11px] font-semibold text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">High priority</span>
                                            @endif
                                        </div>
                                        <p class="mt-1 truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $work->title }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $work->location }} · {{ $work->category }} · Assigned {{ $work->assigned_at?->format('M d, Y') ?? $work->updated_at?->format('M d, Y') }}</p>
                                    </div>
                                    <span class="shrink-0 text-sm font-semibold text-indigo-600 dark:text-indigo-300">View details →</span>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-5 text-center text-sm text-gray-500 dark:text-gray-400">
                                No other assigned tasks are waiting.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-100 px-5 py-3 dark:border-gray-700">
                <a href="{{ route('maintenanceRequest.personnel') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300">View all assigned tasks and history →</a>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-700">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Recent request updates</h3>
                    @if($recentActivity->isNotEmpty())
                        <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            {{ $recentActivity->count() }} {{ $recentActivity->count() === 1 ? 'update' : 'updates' }}
                        </span>
                    @endif
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Latest updates for your current task. Scroll to view older activity.</p>
            </div>
            <div class="max-h-[520px] overflow-y-auto overscroll-contain divide-y divide-gray-100 dark:divide-gray-700"
                 tabindex="0"
                 role="region"
                 aria-label="Recent request updates">
                @forelse($recentActivity as $log)
                    <div class="px-5 py-4">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $log->request?->title ?? 'Maintenance request' }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ $log->action ? ucfirst(str_replace('_', ' ', $log->action)) : ucfirst(str_replace('_', ' ', $log->status)) }}
                            @if($log->updatedBy) · by {{ trim($log->updatedBy->fname.' '.$log->updatedBy->lname) }} @endif
                        </p>
                        
@if($log->remarks)
    @php
        $logData = json_decode($log->remarks, true);
    @endphp

    <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
        @if($log->action === 'materials_returned'
            && is_array($logData)
            && isset($logData['items'])
            && is_array($logData['items']))

            <p class="font-medium">Materials returned:</p>

            <ul class="mt-1 space-y-1">
                @foreach($logData['items'] as $item)
                    <li>
                        {{ $item['item_name'] ?? 'Unknown item' }}
                        — {{ $item['quantity'] ?? 0 }}
                        {{ $item['unit'] ?? 'units' }}
                    </li>
                @endforeach
            </ul>

            @if(!empty($logData['notes']))
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Remarks: {{ $logData['notes'] }}
                </p>
            @endif

        @else
            <p>{{ \Illuminate\Support\Str::limit($log->remarks, 110) }}</p>
        @endif
    </div>
@endif

                        <p class="mt-2 text-[11px] text-gray-400">{{ $log->created_at?->format('M d, Y · h:i A') }}</p>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No updates for your current task yet.</div>
                @endforelse
            </div>
        </section>
    </div>

    <div class="rounded-xl border border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-800/60">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">End-of-day reminder</h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Record work performed and add remarks as you go. When completing a job, record the materials used or confirm that no materials were needed.</p>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Completed today: <span class="font-semibold">{{ $completedToday ?? 0 }}</span></p>
    </div>
</div>
