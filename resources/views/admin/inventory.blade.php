@extends('layouts.apps')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6" x-data="{ showDeleteModal: false, deleteUrl: '' }">
        <div class="max-w-7xl mx-auto">
            <p class="text-xs tracking-[0.3em] uppercase text-teal-600 dark:text-teal-400 font-semibold">
                Resource Control
            </p>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mt-2">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white">
                        Inventory Management
                    </h1>

                    <p class="mt-2 max-w-2xl text-gray-600 dark:text-gray-400">
                        Monitor school maintenance materials, identify low-stock supplies, and keep repairs moving without
                        delay.
                    </p>
                </div>

                <button type="button" onclick="openInventoryModal()"
                    class="inline-flex min-h-11 w-full flex-shrink-0 items-center justify-center gap-2 rounded-xl bg-[#607844] text-xs font-semibold text-white
           transition hover:bg-[#52693a] sm:w-auto sm:min-h-10 sm:px-4 sm:py-2">
                    <span class="text-xl leading-none" aria-hidden="true">+</span><span>Add Material</span>
                </button>
            </div>

            
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mt-8">

    <!-- Inventory Items -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 flex items-center gap-4">
        <div class="w-11 h-11 shrink-0 rounded-lg bg-[#607844]/10 text-[#607844] flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 7.5 12 3.75l8.25 3.75M3.75 7.5V16.5L12 20.25l8.25-3.75V7.5M3.75 7.5 12 11.25m8.25-3.75L12 11.25M12 11.25v9" />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm text-gray-500 dark:text-gray-400">Inventory Items</p>
            <p class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $total_items ?? 'Error' }}
            </p>
        </div>
    </div>

    <!-- Low Stock -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 flex items-center gap-4">
        <div class="w-11 h-11 shrink-0 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m0 3h.008v.008H12v-.008z" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M10.29 3.86 2.82 17a1.5 1.5 0 0 0 1.3 2.25h15.76a1.5 1.5 0 0 0 1.3-2.25L13.71 3.86a1.97 1.97 0 0 0-3.42 0z" />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm text-gray-500 dark:text-gray-400">Low Stock</p>
            <p class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $low_stocks ?? 'Error' }}
            </p>
        </div>
    </div>

    <!-- Out of Stock -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 flex items-center gap-4">
        <div class="w-11 h-11 shrink-0 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 7.5 12 3.75l8.25 3.75M3.75 7.5v9L12 20.25l8.25-3.75v-9M3.75 7.5 12 11.25m8.25-3.75L12 11.25m0 0v9" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m8.5 14.5 7 3.5m0-3.5-7 3.5" />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm text-gray-500 dark:text-gray-400">Out of Stock</p>
            <p class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $out_of_stock ?? 'Error' }}
            </p>
        </div>
    </div>

    <!-- Estimated Value -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 flex items-center gap-4">
        <div class="w-11 h-11 shrink-0 rounded-lg bg-[#607844]/10 text-[#607844] flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <text x="12" y="17" text-anchor="middle" font-size="16" font-weight="600">₱</text>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm text-gray-500 dark:text-gray-400">Estimated Value</p>
            <p class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                ₱{{ number_format($estimated_value, 2) }}
            </p>
        </div>
    </div>

</div>


            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm dark:shadow-none mt-6 overflow-hidden">

                <div class="p-6 border-b border-gray-200 dark:border-gray-700
               flex flex-col lg:flex-row
               lg:items-center lg:justify-between
               gap-4">

                    <!-- Title -->
                    <div class="min-w-0">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Maintenance Supplies
                        </h2>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Review current quantities and storage locations.
                        </p>

                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="{{ route('inventory') }}" class="flex items-center gap-2 flex-shrink-0">

                        <!-- Search -->
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search item, category, or location" class="w-56 h-10 rounded-lg
                       border border-gray-300 dark:border-gray-700
                       bg-white dark:bg-gray-900
                       px-3 text-sm
                       text-gray-900 dark:text-white
                       placeholder-gray-400">

                        <!-- Status -->
                        <select name="status" class="w-36 h-10 rounded-lg
                       border border-gray-300 dark:border-gray-700
                       bg-white dark:bg-gray-900
                       px-3 text-sm
                       text-gray-900 dark:text-white">

                            <option value="">All Statuses</option>

                            <option value="In Stock" {{ request('status') == 'In Stock' ? 'selected' : '' }}>
                                In Stock
                            </option>

                            <option value="Low Stock" {{ request('status') == 'Low Stock' ? 'selected' : '' }}>
                                Low Stock
                            </option>

                            <option value="Out of Stock" {{ request('status') == 'Out of Stock' ? 'selected' : '' }}>
                                Out of Stock
                            </option>

                        </select>

                        <!-- Search -->
                        
<button
    type="submit"
    class="h-10 px-4 shrink-0 whitespace-nowrap rounded-lg
           bg-[#607844] text-xs font-semibold text-white
           transition hover:bg-[#52693a]
           focus:outline-none focus:ring-2 focus:ring-[#607844]/30">
    Search
</button>


                        <!-- Reset -->
                        <a href="{{ route('inventory') }}" class="h-10 px-3 rounded-lg
                       bg-gray-500 text-white text-sm font-medium
                       hover:bg-gray-600 transition
                       inline-flex items-center justify-center
                       whitespace-nowrap">
                            Reset
                        </a>

                    </form>

<!-- Pagination Settings -->


                </div>


                <table class="w-full text-left">

                    <thead class="bg-gray-100 dark:bg-gray-900/40 text-gray-600 dark:text-gray-400 uppercase text-xs">
                        <tr>
                            <th class="p-4">Item</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Quantity</th>
                            <th>Min. Stock</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-700 dark:text-gray-200">

                        @foreach ($inventoryItems as $item)

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">

                                <td class="p-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $item->name }}
                                    </div>
                                </td>

                                <td>{{ $item->category }}</td>

                                <td>{{ $item->location }}</td>

                                <td class="font-semibold">
                                    {{ $item->quantity }}
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $item->unit }}
                                    </span>
                                </td>

                                <td>
                                    {{ $item->minimum_stock }}
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $item->unit }}
                                    </span>
                                </td>

                                <td>
    @if ($item->quantity == 0)
        <span class="inline-flex w-fit max-w-full items-center rounded-full
       px-2.5 py-1 text-xs font-medium text-white bg-red-600">
            Out of Stock
        </span>
    @elseif ($item->quantity <= $item->minimum_stock)
        <span class="inline-flex w-fit max-w-full items-center rounded-full
       px-2.5 py-1 text-xs font-medium text-white bg-amber-600">
            Low Stock
        </span>
    @else
        <span class="inline-flex w-fit max-w-full items-center rounded-full
       px-2.5 py-1 text-xs font-medium text-white bg-emerald-600">
            In Stock
        </span>
    @endif
</td>
                                
<td data-mobile-label="Action" class="px-4 py-3">
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('edit_inventory', $item->id) }}"
           class="inline-flex min-h-9 w-fit max-w-full items-center rounded-lg bg-[#607844] px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-[#52693a] focus:outline-none focus:ring-2 focus:ring-[#607844]/30">
            Update
        </a>

        <button
            type="button"
            @click="
                showDeleteModal = true;
                deleteUrl = '{{ route('delete_inventory', $item->id) }}';
            "
            class="inline-flex min-h-9 items-center justify-center rounded-lg
                   border border-gray-300 bg-white px-3 py-2 text-xs
                   font-medium text-gray-700 transition hover:bg-gray-100
                   dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200
                   dark:hover:bg-gray-700">
            Delete
        </button>
    </div>
</td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>
                <!-- Pagination -->

                
<!-- Custom Pagination -->
@php
    $paginator = $inventoryItems;
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();

    $visiblePages = collect(range(1, max(1, $lastPage)))
        ->filter(fn ($page) =>
            $page === 1 ||
            $page === $lastPage ||
            abs($page - $currentPage) <= 2
        )
        ->values();
@endphp

<div class="flex flex-col gap-4 border-t border-gray-100 px-5 py-4
            dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">

    <!-- Record Count -->
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

        <!-- Previous Page -->
        @if($paginator->onFirstPage())
            <span class="rounded-lg border border-gray-200 px-3 py-2
                         text-sm text-gray-300 dark:border-gray-700">
                &lsaquo;
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               aria-label="Previous page"
               class="rounded-lg border border-gray-200 px-3 py-2 text-sm
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
                <span aria-current="page"
                      class="flex h-9 min-w-9 items-center justify-center
                             rounded-full bg-gray-800 px-3 text-sm font-semibold
                             text-white dark:bg-teal-600">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $paginator->url($page) }}"
                   class="flex h-9 min-w-9 items-center justify-center
                          rounded-full px-3 text-sm font-medium text-gray-700
                          hover:bg-gray-100 dark:text-gray-200
                          dark:hover:bg-gray-700">
                    {{ $page }}
                </a>
            @endif

            @php $previousPage = $page; @endphp
        @endforeach

        <!-- Next Page -->
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               aria-label="Next page"
               class="rounded-lg border border-gray-200 px-3 py-2 text-sm
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
            <form method="GET"
                  action="{{ route('inventory') }}"
                  class="ml-2 flex items-center gap-2">

                @foreach(request()->except('page') as $key => $value)
                    @if(is_scalar($value))
                        <input type="hidden"
                               name="{{ $key }}"
                               value="{{ $value }}">
                    @endif
                @endforeach

                <label for="inventory-jump"
                       class="text-xs text-gray-500 dark:text-gray-400">
                    Go to
                </label>

                <select id="inventory-jump"
                        name="page"
                        onchange="this.form.submit()"
                        class="rounded-lg border border-gray-300 bg-white px-2 py-2
                               text-sm dark:border-gray-600 dark:bg-gray-900
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

                @if(session('success') || session('error'))
                    <div x-data="{ open: true }" x-show="open" x-transition
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                        style="display:none;">

                        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl dark:bg-gray-800">

                            <h2
                                class="text-lg font-bold
                                                                                        {{ session('success') ? 'text-green-600' : 'text-red-600' }}">

                                {{ session('success') ? 'Success' : 'Notice' }}
                            </h2>

                            <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ session('success') ?? session('error') }}
                            </p>

                            <div class="flex justify-end mt-6">
                                <button @click="open = false"
                                    class="px-4 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700">
                                    OK
                                </button>
                            </div>

                        </div>

                    </div>
                @endif
                <!-- Delete Modal -->
                <div x-show="showDeleteModal" x-transition
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                    style="display:none;">

                    <div @click.away="showDeleteModal = false"
                        class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl dark:bg-gray-800">

                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">
                            Delete Item
                        </h2>

                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                            Are you sure you want to delete this item?
                            This action cannot be undone.
                        </p>

                        <div class="flex justify-end gap-3 mt-6">

                            <button @click="showDeleteModal = false"
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                                Cancel
                            </button>

                            <form :action="deleteUrl" method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">
                                    Delete
                                </button>
                            </form>

                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
    @include('admin.partials.add-inventory-modal')

    @include('admin.partials.inventory-script')


    <!-- Add Inventory Modal -->


@endsection