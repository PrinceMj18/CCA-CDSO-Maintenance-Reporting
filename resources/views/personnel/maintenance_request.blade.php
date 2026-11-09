@extends('layouts.apps')

@section('content')

  @php
    $statusLabels = [
        'approved' => 'Waiting to Start',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
    ];

    // Default to Waiting to Start.
    $activeTab = request()->query('tab', $activeTab ?? 'approved');

    // Validate selected tab.
    if (!array_key_exists($activeTab, $statusLabels)) {
        $activeTab = 'approved';
    }

    $statusCounts = $statusCounts ?? [
        'approved' => 0,
        'in_progress' => 0,
        'completed' => 0,
    ];

    // Preserve filters and selected tab during pagination.
    $requests->appends(request()->except('page'));
@endphp

  <div class="mt-6 mb-6">

    <!-- PAGE HEADER -->
<div class="mb-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">


        <a href="{{ route('personnelDashboard') }}"
           class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[#607844]/30 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">

            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

    </div>
</div>

    <div class="mb-5 flex items-start gap-3 rounded-xl border border-[#607844]/20 bg-[#607844]/5 px-4 py-3 dark:bg-[#607844]/10">
      <i class="fas fa-info-circle mt-0.5 text-[#607844] dark:text-[#a9c18e]"></i>
      <div>
        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">One task at a time</p>
        <p class="mt-1 text-xs leading-5 text-gray-600 dark:text-gray-300">
          You can have multiple assigned requests. Start one task at a time; after you complete the current task, you can begin another approved assignment.
        </p>
      </div>
    </div>

    <!-- ===================================================== -->
    <!-- STATUS TABS -->
    <!-- ===================================================== -->

    <div class="bg-white dark:bg-gray-800
                  rounded-lg shadow-xs
                  border border-gray-200 dark:border-gray-700">

      <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700">

        <!-- DESKTOP HORIZONTAL TABS -->
        <div class="hidden md:flex items-center gap-3">

          @foreach($statusLabels as $status => $label)

                    <a href="{{ request()->fullUrlWithQuery([
                'tab' => $status,
                'page' => 1
              ]) }}" @if($activeTab === $status) aria-current="page" @endif
                      class="inline-flex items-center justify-center
                                           whitespace-nowrap
                                           rounded-xl border
                                           px-3 py-2
            text-sm font-medium
            rounded-lg
            gap-2
                                           transition-all duration-200
                                           focus:outline-none
                                           focus:ring-2 focus:ring-[#607844]/30
                                           {{ $activeTab === $status
                ? 'bg-[#607844] border-[#607844] text-white shadow-sm'
                : 'bg-white border-[#dce3ed] text-[#344054] hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700' }}">

                      {{ $label }}

                    </a>

          @endforeach

        </div>


        <!-- MOBILE DROPDOWN -->
        <form method="GET" action="{{ url()->current() }}"
          class="md:hidden flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 max-w-xl">

          <label for="personnel-status-tab"
            class="text-sm font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">

            Filter by status

          </label>

          {{-- Preserve other active filters --}}
          @foreach(request()->except(['tab', 'page']) as $key => $value)

            @if(!is_array($value) && !is_object($value))

              <input type="hidden" name="{{ $key }}" value="{{ $value }}">

            @endif

          @endforeach

          <select id="personnel-status-tab" name="tab" onchange="this.form.submit()" class="w-full sm:w-auto sm:min-w-[240px]
                             rounded-lg border border-gray-300
                             dark:border-gray-600
                             bg-white dark:bg-gray-700
                             text-gray-800 dark:text-white
                             text-sm px-3 py-2.5
                             focus:border-[#607844]
                             focus:ring-2 focus:ring-[#607844]">

            @foreach($statusLabels as $status => $label)

              <option value="{{ $status }}" {{ $activeTab === $status ? 'selected' : '' }}>

                {{ $label }} ({{ $statusCounts[$status] ?? 0 }})

              </option>

            @endforeach

          </select>

          <noscript>
            <button type="submit" class="px-4 py-2 rounded-lg bg-[#607844] text-white text-sm font-medium">
              Apply
            </button>
          </noscript>

        </form>

      </div>


      <!-- ================================================= -->
      <!-- FILTERS -->
      <!-- ================================================= -->

      <div class="p-4 border-b border-gray-200 dark:border-gray-700">

        <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3">

          {{-- Preserve selected tab --}}
          <input type="hidden" name="tab" value="{{ $activeTab }}">

          <!-- SEARCH -->
          <div class="relative">

            <input type="text" name="search" value="{{ request('search') }}"
              placeholder="Search ticket number, requester, title, location..." class="w-full rounded-lg
                                 border-gray-300
                                 dark:border-gray-600
                                 dark:bg-gray-700
                                 dark:text-white
                                 text-sm
                                 focus:border-indigo-500
                                 focus:ring-indigo-500">

          </div>


          <!-- CATEGORY -->
          <select name="category" class="rounded-lg
                             border-gray-300
                             dark:border-gray-600
                             dark:bg-gray-700
                             dark:text-white
                             text-sm
                             focus:border-indigo-500
                             focus:ring-indigo-500">

            <option value="">All Categories</option>

            <option value="Electrical" {{ request('category') == 'Electrical' ? 'selected' : '' }}>
              Electrical
            </option>

            <option value="Plumbing" {{ request('category') == 'Plumbing' ? 'selected' : '' }}>
              Plumbing
            </option>

            <option value="Furniture" {{ request('category') == 'Furniture' ? 'selected' : '' }}>
              Furniture
            </option>

            <option value="IT Equipment" {{ request('category') == 'IT Equipment' ? 'selected' : '' }}>
              IT Equipment
            </option>

            <option value="Facility" {{ request('category') == 'Facility' ? 'selected' : '' }}>
              Facility
            </option>
            <option value="General Services" {{ request('category') == 'General Services' ? 'selected' : '' }}>
              General Services
            </option>

          </select>


          <!-- FILTER BUTTON -->
          <button type="submit" class="px-4 py-2
                             bg-[#607844]
                             text-white
                             rounded-lg
                             text-sm
                             font-medium
                             hover:bg-[#52693a]
                             transition">

            Filter

          </button>


          <!-- RESET -->
          <a href="{{ url()->current() . '?' . http_build_query(['tab' => $activeTab]) }}" class="px-4 py-2
                             bg-gray-500
                             text-white
                             rounded-lg
                             text-sm
                             font-medium
                             hover:bg-gray-600
                             text-center
                             transition">

            Reset

          </a>

        </form>

      </div>


      <!-- ================================================= -->
      <!-- TABLE -->
      <!-- ================================================= -->

      <div class="w-full overflow-x-auto">

        <table class="w-full whitespace-no-wrap">

          <thead>

            <tr class="text-xs font-semibold
                                 tracking-wide text-left
                                 text-gray-500 uppercase
                                 border-b
                                 dark:border-gray-700
                                 bg-gray-50
                                 dark:bg-gray-800
                                 dark:text-gray-400">

              <th class="px-5 py-3">
                Ticket Number
              </th>

              <th class="px-5 py-3">
                Requester
              </th>

              <th class="px-5 py-3">
                Title
              </th>

              <th class="px-5 py-3">
                Category
              </th>

              <th class="px-5 py-3">
                Status
              </th>

              <th class="px-5 py-3">
                Date
              </th>

              <th class="px-5 py-3">
                Action
              </th>

            </tr>

          </thead>


          <tbody class="bg-white divide-y
                                 dark:divide-gray-700
                                 dark:bg-gray-800">

            @forelse($requests as $request)

              <tr class="text-gray-700
                                       dark:text-gray-300
                                       hover:bg-gray-50
                                       dark:hover:bg-gray-700/40
                                       transition">

                <!-- TICKET NUMBER -->
                <td class="px-5 py-4">

                  <span class="inline-flex items-center
                                                 w-fit max-w-full
                                                 px-2 py-1
                                                 text-xs font-semibold
                                                 text-indigo-700
                                                 bg-indigo-100
                                                 rounded-md
                                                 dark:bg-indigo-900/40
                                                 dark:text-indigo-300">

                    {{ $request->ticket_number ?? 'Not assigned' }}

                  </span>

                </td>


                <!-- REQUESTER -->
                <td class="px-5 py-4">

                  <div>

                    <p class="text-sm font-semibold
                                                  text-gray-700
                                                  dark:text-gray-200">

                      {{ $request->user->fname ?? '' }}
                      {{ $request->user->lname ?? '' }}

                    </p>

                    <p class="text-xs
                                                  text-gray-500
                                                  dark:text-gray-400">

                      {{ $request->user->email ?? 'N/A' }}

                    </p>

                  </div>

                </td>


                <!-- TITLE -->
                <td class="px-5 py-4">

                  <p class="text-sm font-medium
                                              text-gray-700
                                              dark:text-gray-200">

                    {{ $request->title }}

                  </p>

                </td>


                <!-- CATEGORY -->
                <td class="px-5 py-4">

                  <span class="text-sm
                                                 text-gray-600
                                                 dark:text-gray-300">

                    {{ $request->category }}

                  </span>

                </td>


                <!-- STATUS -->
                <td class="px-5 py-4">

                  @if($request->status === 'pending')

                    <span class="inline-flex w-fit max-w-full px-2.5 py-1
                                                       text-xs font-semibold rounded-full
                                                       bg-[#607844]/10 text-[#607844]
                                                       dark:bg-[#607844]/20 dark:text-[#a9c18e]">

                      Pending

                    </span>

                  @elseif($request->status === 'approved')

                    <span class="inline-flex w-fit max-w-full px-2.5 py-1
                                                       text-xs font-semibold rounded-full
                                                       bg-[#607844]/10 text-[#607844]
                                                       dark:bg-[#607844]/20 dark:text-[#a9c18e]">

                      Waiting to Start

                    </span>

                  @elseif($request->status === 'in_progress')

                    <span class="inline-flex w-fit max-w-full px-2.5 py-1
                                                       text-xs font-semibold rounded-full
                                                       bg-[#607844]/10 text-[#607844]
                                                       dark:bg-[#607844]/20 dark:text-[#a9c18e]">

                      In Progress

                    </span>

                  @elseif($request->status === 'completed')

                    <span class="inline-flex w-fit max-w-full px-2.5 py-1
                                                       text-xs font-semibold rounded-full
                                                       bg-[#607844]/10 text-[#607844]
                                                       dark:bg-[#607844]/20 dark:text-[#a9c18e]">

                      Completed

                    </span>

                  @endif

                </td>


                <!-- DATE -->
                <td class="px-5 py-4 text-sm
                                           text-gray-500
                                           dark:text-gray-400">

                  {{ $request->updated_at->format('M d, Y') }}

                </td>


                <!-- ACTION -->
                <td class="px-5 py-4">

                  <a href="{{ route('viewRequest.personnel', $request->id) }}" class="inline-flex min-h-9 w-fit max-w-full
                                               items-center rounded-lg
                                               bg-[#607844] px-3 py-1.5
                                               text-xs font-semibold text-white
                                               transition hover:bg-[#52693a]
                                               focus:outline-none
                                               focus:ring-2 focus:ring-[#607844]/30">

                    View

                  </a>

                </td>

              </tr>

            @empty

                    <!-- EMPTY STATE -->
                    <tr>

                      <td colspan="7" class="px-5 py-10 text-center">

                        <p class="text-sm font-medium
                                                    text-gray-500
                                                    dark:text-gray-400">

                          {{ $activeTab === 'all'
              ? 'No assigned tasks found.'
              : 'No ' . strtolower($statusLabels[$activeTab]) . ' requests found.' }}

                        </p>

                      </td>

                    </tr>

            @endforelse

          </tbody>

        </table>

      </div>


      <!-- ================================================= -->
      <!-- CUSTOM PAGINATION -->
      <!-- ================================================= -->

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

            <a href="{{ $paginator->previousPageUrl() }}" aria-label="Previous page" class="rounded-lg border border-gray-200
                                   px-3 py-2 text-sm
                                   text-gray-700 hover:bg-gray-100
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

              <span aria-current="page" class="flex h-9 min-w-9 items-center justify-center
                                         rounded-full bg-gray-800 px-3
                                         text-sm font-semibold text-white
                                         dark:bg-indigo-600">

                {{ $page }}

              </span>

            @else

              <a href="{{ $paginator->url($page) }}" class="flex h-9 min-w-9 items-center justify-center
                                         rounded-full px-3
                                         text-sm font-medium text-gray-700
                                         hover:bg-gray-100
                                         dark:text-gray-200
                                         dark:hover:bg-gray-700">

                {{ $page }}

              </a>

            @endif

            @php $previousPage = $page; @endphp

          @endforeach


          <!-- NEXT -->
          @if($paginator->hasMorePages())

            <a href="{{ $paginator->nextPageUrl() }}" aria-label="Next page" class="rounded-lg border border-gray-200
                                   px-3 py-2 text-sm
                                   text-gray-700 hover:bg-gray-100
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


          <!-- GO TO PAGE -->
          @if($lastPage > 1)

            <form method="GET" action="{{ url()->current() }}" class="ml-2 flex items-center gap-2">

              @foreach(request()->except('page') as $key => $value)

                @if(is_scalar($value))

                  <input type="hidden" name="{{ $key }}" value="{{ $value }}">

                @endif

              @endforeach

              <label for="personnel-jump" class="text-xs text-gray-500 dark:text-gray-400">

                Go to

              </label>

              <select id="personnel-jump" name="page" onchange="this.form.submit()" class="rounded-lg border border-gray-300
                                       bg-white px-2 py-2
                                       text-sm dark:border-gray-600
                                       dark:bg-gray-700 dark:text-white">

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

  </div>

@endsection