
@extends('layouts.apps')

@section('content')

@if (session('success') || session('error'))
    <div id="archive-alert"
         role="alert"
         class="fixed top-5 right-5 z-50 flex items-center gap-3
                rounded-lg px-5 py-4 shadow-lg
                {{ session('success')
                    ? 'bg-green-600 text-white'
                    : 'bg-red-600 text-white' }}">

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-6 h-6 flex-shrink-0"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor"
             stroke-width="2"
             aria-hidden="true">

            @if (session('success'))
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M5 13l4 4L19 7"/>
            @else
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M6 18L18 6M6 6l12 12"/>
            @endif

        </svg>

        <div>
            <p class="font-semibold">
                {{ session('success') ? 'Success' : 'Error' }}
            </p>

            <p class="text-sm">
                {{ session('success') ?? session('error') }}
            </p>
        </div>

        <button type="button"
                onclick="document.getElementById('archive-alert').remove()"
                class="ml-3 text-white hover:opacity-75"
                aria-label="Close notification">
            &times;
        </button>

    </div>
@endif

@php
    $tabs = [
        'pending' => ['label' => 'Pending Review', 'tone' => 'amber'],
        'approved' => ['label' => 'Assigned', 'tone' => 'blue'],
        'in_progress' => ['label' => 'In Progress', 'tone' => 'violet'],
        'completed' => ['label' => 'Completed', 'tone' => 'emerald'],
        'rejected' => ['label' => 'Rejected', 'tone' => 'rose'],
    ];

    // Preserve selected tab after refreshing.
    $activeTab = request('tab', $activeTab ?? 'pending');

    if (!array_key_exists($activeTab, $tabs)) {
        $activeTab = 'pending';
    }
@endphp

<div class="py-6"
     x-data="{ tab: '{{ $activeTab }}' }"
     x-init="
        window.addEventListener('popstate', () => {
            const url = new URL(window.location.href);
            const selected = url.searchParams.get('tab') || 'pending';
            tab = ['pending', 'approved', 'in_progress', 'completed', 'rejected'].includes(selected)
                ? selected
                : 'pending';
        });
     ">

    <!-- ===================================================== -->
    <!-- HEADER -->
    <!-- ===================================================== -->

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between mb-6">

        <div>

            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-indigo-600">

                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>

                CDSO Maintenance

            </div>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-800 dark:text-white">
                Maintenance Requests
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Review requests, approve and assign personnel, then follow the complete activity history from one place.
            </p>

        </div>

        <div class="flex items-center gap-2">

            <div class="px-3 py-2 rounded-xl bg-white border border-gray-200 shadow-sm text-xs text-gray-600">

                <span class="font-semibold text-gray-900">
                    {{ collect($requests)->sum(fn($items) => $items->total()) }}
                </span>

                total records

            </div>

        </div>

    </div>


    <!-- ===================================================== -->
    <!-- FILTERS -->
    <!-- ===================================================== -->

    <div class="bg-white dark:bg-gray-800
                border border-gray-200 dark:border-gray-700
                rounded-2xl shadow-sm p-4 mb-5">

        <form method="GET"
              action="{{ route('maintenanceRequest.admin') }}"
              class="grid grid-cols-1 md:grid-cols-[1fr_190px_auto] gap-3">

            <!-- PRESERVE ACTIVE TAB -->
            <input type="hidden" name="tab" :value="tab">

            <div class="relative">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search ticket, requester, title, or location..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl
                           border-gray-200 bg-gray-50 text-sm
                           focus:border-indigo-500 focus:ring-indigo-500
                           dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0z" />

                </svg>

            </div>


            <!-- CATEGORY -->
            <select
                name="category"
                class="rounded-xl border-gray-200 bg-gray-50 text-sm
                       focus:border-indigo-500 focus:ring-indigo-500
                       dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                <option value="">All categories</option>

                @foreach(['Electrical', 'Plumbing', 'Furniture', 'Equipment / Technology', 'Facility', 'Air Conditioning'] as $category)

                    <option value="{{ $category }}"
                        @selected(request('category') === $category)>

                        {{ $category }}

                    </option>

                @endforeach

            </select>


            <div class="flex gap-2">

                <button
                    type="submit"
                    class="flex-1 md:flex-none px-5 py-2.5 rounded-xl
                           bg-indigo-600 hover:bg-indigo-700
                           text-white text-sm font-semibold transition">

                    Search

                </button>

                <a
                    href="{{ route('maintenanceRequest.admin') . '?' . http_build_query(['tab' => $activeTab]) }}"
                    class="px-4 py-2.5 rounded-xl border border-gray-200
                           text-gray-600 hover:bg-gray-50
                           text-sm font-medium">

                    Reset

                </a>

            </div>

        </form>

    </div>


    <!-- ===================================================== -->
    <!-- STATUS TABS AND REQUEST TABLES -->
    <!-- ===================================================== -->

    <div class="bg-white dark:bg-gray-800
                border border-gray-200 dark:border-gray-700
                rounded-2xl shadow-sm overflow-hidden">

        <!-- DESKTOP TABS -->
        <div class="hidden md:flex items-center gap-2 px-4 py-4
                    border-b border-gray-200 dark:border-gray-700">

            @foreach($tabs as $status => $tabData)

                <button
                    type="button"

                    @click="
                        tab = '{{ $status }}';

                        const url = new URL(window.location.href);
                        url.searchParams.set('tab', '{{ $status }}');
                        url.searchParams.delete('pending_page');
                        url.searchParams.delete('approved_page');
                        url.searchParams.delete('in_progress_page');
                        url.searchParams.delete('completed_page');
                        url.searchParams.delete('rejected_page');

                        window.history.replaceState({}, '', url.toString());
                    "

                    :class="tab === '{{ $status }}'
                        ? 'bg-[#607844] border-[#607844] text-white shadow-sm'
                        : 'bg-white border-[#dce3ed] text-[#344054] hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700'"

                    class="inline-flex items-center justify-center
                           whitespace-nowrap rounded-lg
                           border px-3 py-2
                           text-sm font-medium
                           transition-all duration-200
                           focus:outline-none
                           focus:ring-2 focus:ring-[#607844]/30">

                    {{ $tabData['label'] }}

                </button>

            @endforeach

        </div>


        <!-- MOBILE STATUS DROPDOWN -->
        <div class="md:hidden px-4 py-4 border-b border-gray-200 dark:border-gray-700">

            <label for="mobile-status-filter"
                   class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">

                Filter by status

            </label>

            <select
                id="mobile-status-filter"
                x-model="tab"

                @change="
                    const url = new URL(window.location.href);
                    url.searchParams.set('tab', tab);

                    url.searchParams.delete('pending_page');
                    url.searchParams.delete('approved_page');
                    url.searchParams.delete('in_progress_page');
                    url.searchParams.delete('completed_page');
                    url.searchParams.delete('rejected_page');

                    window.history.replaceState({}, '', url.toString());
                "

                class="w-full min-h-[56px] rounded-2xl
                       border-2 border-[#607844]
                       bg-white px-4 py-3
                       text-base text-gray-800
                       focus:outline-none
                       focus:ring-2 focus:ring-[#607844]/20
                       dark:bg-gray-800
                       dark:text-gray-100
                       dark:border-[#607844]">

                @foreach($tabs as $status => $tabData)

                    <option value="{{ $status }}">
                        {{ $tabData['label'] }} ({{ $requests[$status]->total() }})
                    </option>

                @endforeach

            </select>

        </div>


        <!-- ================================================= -->
        <!-- TAB CONTENT -->
        <!-- ================================================= -->

        @foreach($tabs as $status => $tabData)

            <section x-show="tab === '{{ $status }}'" x-cloak>

                @if($requests[$status]->count())

                    <!-- REQUEST TABLE -->
                    <div class="overflow-x-auto">

                        <table class="w-full text-left">

                            <thead class="bg-gray-50 dark:bg-gray-900/40
                                          text-[11px] uppercase tracking-wider
                                          text-gray-500">

                                <tr>

                                    <th class="px-5 py-3 font-semibold">Ticket</th>

                                    <th class="px-5 py-3 font-semibold">Request</th>

                                    <th class="px-5 py-3 font-semibold">Requester</th>

                                    <th class="px-5 py-3 font-semibold">Personnel</th>

                                    <th class="px-5 py-3 font-semibold">Priority</th>

                                    <th class="px-5 py-3 font-semibold">Updated</th>

                                    <th class="px-5 py-3"></th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">

                                @foreach($requests[$status] as $item)

                                    @php

                                        $priorityClass = match ($item->priority) {
                                            'high' => 'bg-rose-50 text-rose-700 border-rose-100',
                                            'low' => 'bg-slate-50 text-slate-600 border-slate-200',
                                            default => 'bg-amber-50 text-amber-700 border-amber-100',
                                        };

                                    @endphp

                                    <tr class="hover:bg-gray-50/80
                                               dark:hover:bg-gray-700/30 transition">

                                        <!-- TICKET -->
                                        <td class="px-5 py-4 whitespace-nowrap">

                                            <span class="inline-flex w-fit max-w-full
                                                         font-mono text-xs font-bold
                                                         text-indigo-700 bg-indigo-50
                                                         border border-indigo-100
                                                         px-2.5 py-1.5 rounded-lg">

                                                {{ $item->ticket_number ?? 'CDSO-' . $item->id }}

                                            </span>

                                            <div class="mt-1 text-[11px] text-gray-400">

                                                {{ $item->created_at?->format('M d, Y h:i A') }}

                                            </div>

                                        </td>


                                        <!-- REQUEST -->
                                        <td class="px-5 py-4 min-w-[220px]">

                                            <div class="text-sm font-semibold
                                                        text-gray-800 dark:text-white">

                                                {{ $item->title }}

                                            </div>

                                            <div class="mt-1 text-xs text-gray-500">

                                                {{ $item->category }} · {{ $item->location }}

                                            </div>

                                        </td>


                                        <!-- REQUESTER -->
                                        <td class="px-5 py-4 whitespace-nowrap">

                                            <div class="text-sm font-medium
                                                        text-gray-700 dark:text-gray-200">

                                                {{ $item->user?->fname }}
                                                {{ $item->user?->lname }}

                                            </div>

                                            <div class="text-xs text-gray-400">

                                                {{ $item->user?->email ?? 'Deleted user' }}

                                            </div>

                                        </td>


                                        <!-- PERSONNEL -->
                                        <td class="px-5 py-4 whitespace-nowrap">

                                            @if($item->assignedPersonnel)

                                                <div class="text-sm font-medium
                                                            text-gray-700 dark:text-gray-200">

                                                    {{ $item->assignedPersonnel->fname }}
                                                    {{ $item->assignedPersonnel->lname }}

                                                </div>

                                                <div class="text-xs text-gray-400">

                                                    {{ $item->assigned_at?->format('M d, Y') }}

                                                </div>

                                            @else

                                                <span class="text-xs text-gray-400">
                                                    Unassigned
                                                </span>

                                            @endif

                                        </td>


                                        <!-- PRIORITY -->
                                        <td class="px-5 py-4 whitespace-nowrap">

                                            <span class="inline-flex w-fit max-w-full
                                                         px-2.5 py-1 rounded-lg
                                                         border text-[11px] font-semibold
                                                         {{ $priorityClass }}">

                                                {{ ucfirst($item->priority) }}

                                            </span>

                                        </td>


                                        <!-- UPDATED -->
                                        <td class="px-5 py-4 whitespace-nowrap
                                                   text-xs text-gray-500">

                                            {{ $item->updated_at?->diffForHumans() }}

                                        </td>


                                        <!-- ACTION -->
                                        <td class="px-5 py-4 text-right whitespace-nowrap">

                                            <a
                                                href="{{ route('viewRequest.admin', $item->id) }}"
                                                class="inline-flex min-h-9 w-fit max-w-full
                                                       items-center gap-1
                                                       rounded-lg bg-[#607844]
                                                       px-3 py-1.5 text-xs
                                                       font-semibold text-white
                                                       transition hover:bg-[#52693a]
                                                       focus:outline-none
                                                       focus:ring-2
                                                       focus:ring-[#607844]/30">

                                                View request

                                                <svg class="w-3.5 h-3.5"
                                                     fill="none"
                                                     stroke="currentColor"
                                                     viewBox="0 0 24 24">

                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="m9 5 7 7-7 7" />

                                                </svg>

                                            </a>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>


                    <!-- ================================================= -->
                    <!-- PAGINATION -->
                    <!-- ================================================= -->

                    @php

                        $paginator = $requests[$status];

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


                    <div class="flex flex-col gap-4
                                border-t border-gray-100
                                px-5 py-4
                                dark:border-gray-700
                                sm:flex-row sm:items-center
                                sm:justify-between">

                        <!-- RECORD COUNT -->
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

                            <!-- PREVIOUS -->
                            @if($paginator->onFirstPage())

                                <span class="rounded-lg border border-gray-200
                                             px-3 py-2 text-sm text-gray-300
                                             dark:border-gray-700">

                                    &lsaquo;

                                </span>

                            @else

                                <a
                                    href="{{ $paginator->previousPageUrl() }}"
                                    aria-label="Previous page"
                                    class="rounded-lg border border-gray-200
                                           px-3 py-2 text-sm text-gray-700
                                           hover:bg-gray-100
                                           dark:border-gray-700
                                           dark:text-gray-200
                                           dark:hover:bg-gray-700">

                                    &lsaquo;

                                </a>

                            @endif


                            <!-- NUMBERED PAGES -->
                            @php $previousPage = 0; @endphp

                            @foreach($visiblePages as $page)

                                @if($previousPage && $page > $previousPage + 1)

                                    <span class="px-1 text-gray-500">
                                        ...
                                    </span>

                                @endif


                                @if($page === $currentPage)

                                    <span
                                        aria-current="page"
                                        class="flex h-9 min-w-9 items-center justify-center
                                               rounded-full bg-gray-800 px-3
                                               text-sm font-semibold text-white
                                               dark:bg-teal-600">

                                        {{ $page }}

                                    </span>

                                @else

                                    <a
                                        href="{{ $paginator->url($page) }}"
                                        class="flex h-9 min-w-9 items-center justify-center
                                               rounded-full px-3 text-sm font-medium
                                               text-gray-700 hover:bg-gray-100
                                               dark:text-gray-200
                                               dark:hover:bg-gray-700">

                                        {{ $page }}

                                    </a>

                                @endif

                                @php $previousPage = $page; @endphp

                            @endforeach


                            <!-- NEXT -->
                            @if($paginator->hasMorePages())

                                <a
                                    href="{{ $paginator->nextPageUrl() }}"
                                    aria-label="Next page"
                                    class="rounded-lg border border-gray-200
                                           px-3 py-2 text-sm text-gray-700
                                           hover:bg-gray-100
                                           dark:border-gray-700
                                           dark:text-gray-200
                                           dark:hover:bg-gray-700">

                                    &rsaquo;

                                </a>

                            @else

                                <span class="rounded-lg border border-gray-200
                                             px-3 py-2 text-sm text-gray-300
                                             dark:border-gray-700">

                                    &rsaquo;

                                </span>

                            @endif


                            <!-- JUMP TO PAGE -->
                            @if($lastPage > 1)

                                <form
                                    method="GET"
                                    action="{{ route('maintenanceRequest.admin') }}"
                                    class="ml-2 flex items-center gap-2">

                                    @foreach(request()->except([
                                        $status . '_page',
                                        'tab',
                                        'pending_page',
                                        'approved_page',
                                        'in_progress_page',
                                        'completed_page',
                                        'rejected_page'
                                    ]) as $key => $value)

                                        @if(is_scalar($value))

                                            <input type="hidden"
                                                   name="{{ $key }}"
                                                   value="{{ $value }}">

                                        @endif

                                    @endforeach

                                    <input type="hidden"
                                           name="tab"
                                           value="{{ $status }}">

                                    <label
                                        for="jump-{{ $status }}"
                                        class="text-xs text-gray-500 dark:text-gray-400">

                                        Go to

                                    </label>

                                    <select
                                        id="jump-{{ $status }}"
                                        name="{{ $status }}_page"
                                        onchange="this.form.submit()"
                                        class="rounded-lg border border-gray-300
                                               bg-white px-2 py-2 text-sm
                                               dark:border-gray-600
                                               dark:bg-gray-800
                                               dark:text-white">

                                        @for($page = 1; $page <= $lastPage; $page++)

                                            <option value="{{ $page }}"
                                                @selected($page === $currentPage)>

                                                {{ $page }}

                                            </option>

                                        @endfor

                                    </select>

                                </form>

                            @endif

                        </div>

                    </div>


                @else

                    <!-- EMPTY STATE -->
                    <div class="py-16 text-center">

                        <div class="mx-auto w-12 h-12 rounded-2xl
                                    bg-gray-100 flex items-center justify-center">

                            <svg class="w-6 h-6 text-gray-400"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="1.8"
                                      d="M9 12h6m-6 4h4M7 3h7l3 3v15H7z" />

                            </svg>

                        </div>

                        <h3 class="mt-4 text-sm font-semibold text-gray-700">

                            No {{ strtolower($tabData['label']) }} requests

                        </h3>

                        <p class="mt-1 text-xs text-gray-400">

                            Requests matching this stage will appear here.

                        </p>

                    </div>

                @endif

            </section>

        @endforeach

    </div>


    <!-- FOOTER NOTE -->
    <p class="mt-4 text-xs text-gray-400">

        Admin responsibility: review, approve/reject, assign personnel, and add administrative remarks. Personnel control work progress and completion.

    </p>

</div>

@endsection
