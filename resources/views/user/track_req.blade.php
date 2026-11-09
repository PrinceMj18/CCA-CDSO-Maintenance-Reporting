@extends('layouts.apps')

@section('content')

    <div class="container mx-auto w-full min-w-0 max-w-full overflow-x-hidden px-3 py-5 sm:px-5 lg:px-8 lg:py-8">

        <!-- HEADER -->
        <div class="flex flex-col gap-3 mb-6 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl lg:text-3xl dark:text-white">
                    {{ ucfirst(str_replace('_', ' ', $status ?? 'all')) }} Requests
                </h1>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Track and monitor your submitted maintenance requests.
                </p>
            </div>

            <a href="{{ route('userDashboard') }}"
                class="inline-flex min-h-11 w-full items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[#607844]/30 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">

                Back to Dashboard
            </a>
        </div>

        <!-- FILTERS -->
        
<!-- REQUEST STATUS FILTERS -->
@php
    $filters = [
        'all' => 'All Requests',
        'pending' => 'Pending',
        'approved' => 'Approved',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'rejected' => 'Rejected',
    ];

    $currentStatus = $status ?? request('status', 'all');

    if (!array_key_exists($currentStatus, $filters)) {
        $currentStatus = 'all';
    }
@endphp

<div class="mb-6 rounded-xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4 dark:border-gray-700 dark:bg-gray-800">
    <label for="request-status-mobile"
        class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500 sm:hidden dark:text-gray-400">
        Filter requests
    </label>

    <!-- MOBILE: STATUS DROPDOWN -->
    <form method="GET" action="{{ route('trackReq') }}"
        class="sm:hidden">
        @foreach(request()->except(['status', 'page']) as $key => $value)
            @if(is_scalar($value))
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach

        <select id="request-status-mobile"
            name="status"
            onchange="this.form.submit()"
            class="block min-h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-800 shadow-sm focus:border-[#607844] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#607844]/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white">

            @foreach($filters as $key => $label)
                <option value="{{ $key }}"
    {{ $currentStatus === $key ? 'selected' : '' }}>
    {{ $label }} ({{ $statusCounts[$key] ?? 0 }})
</option>
            @endforeach
        </select>

        <noscript>
            <button type="submit"
                class="mt-2 rounded-lg border border-gray-300 px-4 py-2 text-sm">
                Apply filter
            </button>
        </noscript>
    </form>

    <!-- DESKTOP: MINIMALIST STATUS BUTTONS -->
    <nav aria-label="Filter requests by status"
        class="hidden flex-wrap gap-2 sm:flex">

        @foreach($filters as $key => $label)
            <a href="{{ route('trackReq', array_merge(request()->query(), ['status' => $key, 'page' => 1])) }}"
                @if($currentStatus === $key) aria-current="page" @endif
                class="inline-flex items-center rounded-lg border px-3 py-2
                       text-sm font-medium transition
                       {{ $currentStatus === $key
                            ? 'border-[#607844] bg-[#607844] text-white shadow-sm dark:border-[#607844] dark:bg-[#607844] dark:text-white'
                            : 'border-slate-200 bg-white text-slate-600 hover:border-[#607844]/40 hover:bg-[#607844]/5 hover:text-[#52693a] dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                {{ $label }}
            </a>
        @endforeach

    </nav>
</div>

        <!-- SEARCH & FILTERS -->
        <form method="GET" action="{{ route('trackReq') }}" class="mb-6 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5 dark:border-gray-700 dark:bg-gray-800">

            <!-- Keep current status -->
            <input type="hidden" name="status" value="{{ $status ?? 'all' }}">

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_13rem_auto_auto] lg:items-end">

                <!-- Search -->
                <div class="flex-1">
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-gray-400">
                        Search
                    </label>

                    <div class="relative">

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search ticket number, title, description, or location..." class="w-full px-4 py-2.5 pl-10 text-sm bg-white border border-gray-200 rounded-xl
                                       focus:border-[#607844] focus:ring-2 focus:ring-[#607844]/20
                                       dark:bg-gray-800 dark:border-gray-700 dark:text-white
                                       dark:placeholder-gray-500">

                        <svg class="absolute w-4 h-4 text-gray-400 left-3 top-3.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0z">
                            </path>

                        </svg>

                    </div>
                </div>


                <!-- Category -->
                <div class="min-w-0">

                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-gray-400">
                        Category
                    </label>

                    <select name="category" class="min-h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 focus:border-[#607844] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#607844]/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white">

                        <option value="">All Categories</option>

                        @foreach($categories as $category)

                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>

                                {{ ucfirst($category) }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <!-- Search Button -->
                <div class="flex items-end">

                    <button type="submit" class="min-h-11 w-full rounded-lg bg-[#607844] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#52693a] focus:outline-none focus:ring-2 focus:ring-[#607844]/30 sm:w-auto">

                        Search

                    </button>

                </div>


                <!-- Reset -->
                <div class="flex items-end">

                    <a href="{{ route('trackReq', ['status' => $status ?? 'all']) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-lg border border-slate-200 bg-slate-50 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 sm:w-auto dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">

                        Reset

                    </a>

                </div>

            </div>

        </form>

        <!-- MOBILE REQUEST CARDS -->
        <div class="space-y-3 sm:hidden">
            @forelse($requests as $request)
                @php
                    $mobilePriorityColors = [
                        'low' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                        'medium' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                        'high' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
                    ];
                    $mobileStatusColors = [
                        'pending' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
                        'approved' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                        'in_progress' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                        'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                        'rejected' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
                    ];
                @endphp
                <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-slate-100 px-4 py-3 dark:border-gray-700">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-gray-400">Ticket number</p>
                                <p class="mt-1 break-words text-sm font-bold text-[#52693a] dark:text-emerald-300">{{ $request->ticket_number ?? 'Not assigned' }}</p>
                            </div>
                            <span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $mobileStatusColors[$request->status] ?? 'bg-slate-100 text-slate-700 ring-slate-500/20' }}">
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                        </div>
                        <h2 class="mt-3 break-words text-base font-semibold leading-snug text-slate-900 dark:text-white">{{ $request->title }}</h2>
                        <p class="mt-1 break-words text-sm leading-relaxed text-slate-500 dark:text-gray-400">{{ $request->description }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-3 px-4 py-3">
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-gray-400">Category</p>
                            <p class="mt-1 break-words text-sm font-medium text-slate-800 dark:text-gray-200">{{ ucfirst($request->category) }}</p>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-gray-400">Priority</p>
                            <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $mobilePriorityColors[$request->priority] ?? 'bg-slate-100 text-slate-700 ring-slate-500/20' }}">{{ ucfirst($request->priority) }}</span>
                        </div>
                        <div class="col-span-2 min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-gray-400">Location</p>
                            <p class="mt-1 break-words text-sm text-slate-700 dark:text-gray-300">{{ $request->location }}</p>
                        </div>
                        <div class="col-span-2 min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-gray-400">Submitted</p>
                            <p class="mt-1 text-sm text-slate-700 dark:text-gray-300">{{ $request->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="border-t border-slate-100 bg-slate-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/40">
                        <a href="{{ route('viewRequest.user', $request->id) }}" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg bg-[#607844] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#52693a] focus:outline-none focus:ring-2 focus:ring-[#607844]/30 focus:ring-offset-2">
                            View request updates
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-white px-5 py-10 text-center dark:border-gray-700 dark:bg-gray-800">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#607844]/10 text-[#607844]">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2Z"/></svg>
                    </div>
                    <h3 class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">No requests found</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">Try another status or adjust your search filters.</p>
                </div>
            @endforelse
        </div>

        <!-- DESKTOP REQUEST TABLE -->
        <div class="hidden w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:block">

            <div class="w-full overflow-x-auto">
                <table class="w-full min-w-[900px]">

                    <thead class="bg-slate-50 dark:bg-gray-700">

                        <tr
                            class="text-left text-[11px] font-semibold tracking-wider text-slate-500 uppercase dark:text-gray-400">
                            <th class="px-4 py-3">Ticket No.</th>
                            <th class="px-4 py-3">Request</th>
                            <th class="px-4 py-3">Category</th>
                            <!-- <th class="px-4 py-3">Priority</th> -->
                            <!-- <th class="px-4 py-3">Location</th> -->
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 mb-4">

                        @forelse($requests as $request)

                                            @php
                                                $priorityColors = [
                                                    'low' => 'bg-green-100 text-green-700',
                                                    'medium' => 'bg-yellow-100 text-yellow-700',
                                                    'high' => 'bg-red-100 text-red-700',
                                                ];

                                                $statusColors = [
                                                    'pending' => 'bg-orange-100 text-orange-700',
                                                    'approved' => 'bg-yellow-100 text-yellow-700',
                                                    'in_progress' => 'bg-blue-100 text-blue-700',
                                                    'completed' => 'bg-green-100 text-green-700',
                                                    'rejected' => 'bg-red-100 text-red-700',
                                                ];
                                            @endphp

                                            <tr class="transition-colors hover:bg-[#607844]/5 dark:hover:bg-gray-700/40">
                                                <!-- TICKET NUMBER -->
                                                <td class="px-3 py-4 whitespace-nowrap">

                                                    <span class="inline-flex items-center w-fit max-w-full px-2 py-1 text-xs font-semibold
                               text-[#52693a] bg-[#607844]/10 rounded-md
                               dark:bg-indigo-900/40 dark:text-indigo-300">

                                                        {{ $request->ticket_number ?? 'Not assigned' }}

                                                    </span>

                                                </td>
                                                <!-- REQUEST -->
                                                <td class="px-4 py-4">

                                                    <div class="max-w-[220px]">

                                                        <h3 class="text-sm font-semibold text-gray-800 truncate dark:text-white">
                                                            {{ $request->title }}
                                                        </h3>

                                                        <p class="mt-1 text-xs text-gray-500 truncate dark:text-gray-400">
                                                            {{ $request->description }}
                                                        </p>
                                                    </div>
                                                </td>

                                                <!-- CATEGORY -->
                                                <td class="px-4 py-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                                    {{ ucfirst($request->category) }}
                                                </td>

                                                <!-- PRIORITY -->
                                                <!-- <td class="px-4 py-4 whitespace-nowrap">

                                                    <span
                                                        class="inline-flex w-fit max-w-full px-2.5 py-1 text-[11px] font-semibold rounded-full {{ $priorityColors[$request->priority] ?? 'bg-gray-100 text-gray-700' }}">

                                                        {{ ucfirst($request->priority) }}
                                                    </span>
                                                </td> -->

                                                <!-- LOCATION -->
                                                <!-- <td
                                                    class="px-4 py-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 max-w-[160px] truncate">
                                                    {{ $request->location }}
                                                </td> -->

                                                <!-- STATUS -->
                                                <td class="px-4 py-4 whitespace-nowrap">

                                                    <span
                                                        class="inline-flex w-fit max-w-full px-2.5 py-1 text-[11px] font-semibold rounded-full {{ $statusColors[$request->status] ?? 'bg-gray-100 text-gray-700' }}">

                                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                    </span>
                                                </td>

                                                <!-- DATE -->
                                                <td class="px-4 py-4 text-xs sm:text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                                    {{ $request->created_at->format('M d, Y') }}
                                                </td>

                                                <!-- VIEW -->
                                                <td class="px-4 py-4 text-right whitespace-nowrap">
                                                    <a href="{{ route('viewRequest.user', $request->id) }}"
                                                        class="inline-flex min-h-9 w-fit max-w-full items-center rounded-lg bg-[#607844] px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-[#52693a] focus:outline-none focus:ring-2 focus:ring-[#607844]/30">
                                                        View updates
                                                    </a>
                                                </td>
                                            </tr>

                        @empty

                            <tr>

                                <td colspan="8" class="px-6 py-12 text-center">

                                    <div class="flex flex-col items-center">

                                        <svg class="w-14 h-14 mb-3 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 17v-2a4 4 0 018 0v2m-4-6a4 4 0 100-8 4 4 0 000 8z" />
                                        </svg>

                                        <h3 class="text-base font-semibold text-gray-700 dark:text-white">
                                            No requests found
                                        </h3>

                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            No maintenance requests available for this status.
                                        </p>
                                    </div>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
        <!-- PAGINATION -->

        <!-- CUSTOM PAGINATION -->
        @php
            $paginator = $requests;
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();

            $visiblePages = collect(range(1, max(1, $lastPage)))
                ->filter(
                    fn($page) =>
                        $page === 1 ||
                        $page === $lastPage ||
                        abs($page - $currentPage) <= 2
                )
                ->values();
        @endphp

        <div class="mt-4 flex flex-col gap-4 rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:flex-row sm:items-center sm:justify-between">

            <!-- Showing X to Y of Z -->
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Showing
                <span class="font-semibold text-gray-800 dark:text-white">
                    {{ $paginator->firstItem() ?? 0 }}
                </span>
                to
                <span class="font-semibold text-gray-800 dark:text-white">
                    {{ $paginator->lastItem() ?? 0 }}
                </span>
                of
                <span class="font-semibold text-gray-800 dark:text-white">
                    {{ $paginator->total() }}
                </span>
                requests
            </p>

            <div class="flex flex-wrap items-center gap-2">

                <!-- Previous -->
                @if($paginator->onFirstPage())
                    <span class="rounded-lg border border-gray-200 px-3 py-2
                                 text-sm text-gray-300 dark:border-gray-700">
                        &lsaquo;
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" aria-label="Previous page" class="rounded-lg border border-gray-200 px-3 py-2 text-sm
                              text-gray-700 hover:bg-gray-100 dark:border-gray-700
                              dark:text-gray-200 dark:hover:bg-gray-700">
                        &lsaquo;
                    </a>
                @endif

                <!-- Page Numbers -->
                @php $previousPage = 0; @endphp

                @foreach($visiblePages as $page)
                    @if($previousPage && $page > $previousPage + 1)
                        <span class="px-1 text-gray-500">...</span>
                    @endif

                    @if($page === $currentPage)
                        <span aria-current="page" class="flex h-9 min-w-9 items-center justify-center
                                         rounded-full bg-gray-800 px-3 text-sm font-semibold
                                         text-white dark:bg-indigo-600">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($page) }}" class="flex h-9 min-w-9 items-center justify-center
                                      rounded-full px-3 text-sm font-medium text-gray-700
                                      hover:bg-gray-100 dark:text-gray-200
                                      dark:hover:bg-gray-700">
                            {{ $page }}
                        </a>
                    @endif

                    @php $previousPage = $page; @endphp
                @endforeach

                <!-- Next -->
                @if($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" aria-label="Next page" class="rounded-lg border border-gray-200 px-3 py-2 text-sm
                              text-gray-700 hover:bg-gray-100 dark:border-gray-700
                              dark:text-gray-200 dark:hover:bg-gray-700">
                        &rsaquo;
                    </a>
                @else
                    <span class="rounded-lg border border-gray-200 px-3 py-2
                                 text-sm text-gray-300 dark:border-gray-700">
                        &rsaquo;
                    </span>
                @endif

                <!-- Go to Page -->
                @if($lastPage > 1)
                    <form method="GET" action="{{ route('trackReq') }}" class="ml-2 flex items-center gap-2">

                        @foreach(request()->except('page') as $key => $value)
                            @if(is_scalar($value))
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <input type="hidden" name="status" value="{{ $status }}">

                        <label for="track-jump" class="text-xs text-gray-500 dark:text-gray-400">
                            Go to
                        </label>

                        <select id="track-jump" name="page" onchange="this.form.submit()" class="rounded-lg border border-gray-300 bg-white px-2 py-2
                                       text-sm dark:border-gray-700 dark:bg-gray-900
                                       dark:text-white">

                            @for($page = 1; $page <= $lastPage; $page++)
                                <option value="{{ $page }}" @selected($page === $currentPage)>
                                    {{ $page }}
                                </option>
                            @endfor
                        </select>
                    </form>
                @endif

            </div>
        </div>

    </div>

@endsection