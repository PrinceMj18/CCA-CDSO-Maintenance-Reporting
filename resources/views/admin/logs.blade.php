@extends('layouts.apps')

@section('content')

<div class="w-full overflow-hidden rounded-lg shadow-xs mt-6 mb-6 bg-white dark:bg-gray-800">


<!-- Header -->
<div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700">
    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
        Maintenance Request Logs
    </h3>

    <p class="text-sm text-gray-500 dark:text-gray-400">
        View maintenance requests and their complete activity history.
    </p>
</div>

<!-- Tabs -->
<div class="border-b border-gray-200 dark:border-gray-700">
    <nav class="flex flex-wrap px-4">

        <!-- Pending -->
        <button
            onclick="showTab('pending')"
            id="tab-pending"
            class="tab-button px-5 py-4 text-sm font-medium border-b-2 border-indigo-600 text-indigo-600">
            Pending
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-700">
                {{ $requests['pending']->total() }}
            </span>
        </button>

        <!-- Approved -->
        <button
            onclick="showTab('approved')"
            id="tab-approved"
            class="tab-button px-5 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400">
            Approved
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">
                {{ $requests['approved']->total() }}
            </span>
        </button>

        <!-- In Progress -->
        <button
            onclick="showTab('in_progress')"
            id="tab-in_progress"
            class="tab-button px-5 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400">
            In Progress
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-700">
                {{ $requests['in_progress']->total() }}
            </span>
        </button>

        <!-- Completed -->
        <button
            onclick="showTab('completed')"
            id="tab-completed"
            class="tab-button px-5 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400">
            Completed
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">
                {{ $requests['completed']->total() }}
            </span>
        </button>

<button
            onclick="showTab('rejected')"
            id="tab-rejected"
            class="tab-button px-5 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400">
            Rejected
            <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">
                {{ $requests['rejected']->total() }}
            </span>
        </button>

    </nav>
</div>

<!-- Filters -->
<div class="p-4 border-b border-gray-200 dark:border-gray-700">

    <form method="GET"
          action="{{ route('logsRequest') }}"
          class="grid grid-cols-1 md:grid-cols-4 gap-3">

        <!-- Search -->
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search title, email, location..."
            class="rounded-lg border-gray-300 dark:border-gray-600
                   dark:bg-gray-700 dark:text-white text-sm">

        <!-- Category -->
        <select
            name="category"
            class="rounded-lg border-gray-300 dark:border-gray-600
                   dark:bg-gray-700 dark:text-white text-sm">

            <option value="">All Categories</option>

            <option value="Electrical"
                {{ request('category') == 'Electrical' ? 'selected' : '' }}>
                Electrical
            </option>

            <option value="Plumbing"
                {{ request('category') == 'Plumbing' ? 'selected' : '' }}>
                Plumbing
            </option>

            <option value="Furniture"
                {{ request('category') == 'Furniture' ? 'selected' : '' }}>
                Furniture
            </option>

            <option value="c"
                {{ request('category') == 'IT Equipment' ? 'selected' : '' }}>
                IT Equipment
            </option>

            <option value="Facility"
                {{ request('category') == 'Facility' ? 'selected' : '' }}>
                Facility
            </option>
<option value="Facility"
                {{ request('category') == 'General Services' ? 'selected' : '' }}>
                General Services
            </option>
        </select>

        <!-- From Date -->
        <input
            type="date"
            name="from_date"
            value="{{ request('from_date') }}"
            class="rounded-lg border-gray-300 dark:border-gray-600
                   dark:bg-gray-700 dark:text-white text-sm">

        <!-- Buttons -->
        <div class="flex gap-2">

            <button
                type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                Filter
            </button>

            <a
                href="{{ route('logsRequest') }}"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600">
                Reset
            </a>

        </div>

    </form>

</div>


<!-- ===================================================== -->
<!-- PENDING -->
<!-- ===================================================== -->

<div id="content-pending" class="tab-content">

    @include('admin.partials.request-log-table', [
        'requestList' => $requests['pending'],
        'status' => 'pending',
        'statusLabel' => 'Pending'
    ])

</div>


<!-- ===================================================== -->
<!-- APPROVED -->
<!-- ===================================================== -->

<div id="content-approved" class="tab-content hidden">

    @include('admin.partials.request-log-table', [
        'requestList' => $requests['approved'],
        'status' => 'approved',
        'statusLabel' => 'Approved'
    ])

</div>


<!-- ===================================================== -->
<!-- IN PROGRESS -->
<!-- ===================================================== -->

<div id="content-in_progress" class="tab-content hidden">

    @include('admin.partials.request-log-table', [
        'requestList' => $requests['in_progress'],
        'status' => 'in_progress',
        'statusLabel' => 'In Progress'
    ])

</div>


<!-- ===================================================== -->
<!-- COMPLETED -->
<!-- ===================================================== -->

<div id="content-completed" class="tab-content hidden">

    @include('admin.partials.request-log-table', [
        'requestList' => $requests['completed'],
        'status' => 'completed',
        'statusLabel' => 'Completed'
    ])

</div>


<div id="content-rejected" class="tab-content hidden">

    @include('admin.partials.request-log-table', [
        'requestList' => $requests['rejected'],
        'status' => 'rejected',
        'statusLabel' => 'Rejected'
    ])

</div>


</div>

<script>

function showTab(tab) {

    // Hide all content
    document.querySelectorAll('.tab-content').forEach(function(content) {
        content.classList.add('hidden');
    });

    // Reset all tab buttons
    document.querySelectorAll('.tab-button').forEach(function(button) {

        button.classList.remove(
            'border-indigo-600',
            'text-indigo-600'
        );

        button.classList.add(
            'border-transparent',
            'text-gray-500'
        );
    });

    // Show selected content
    document.getElementById('content-' + tab)
        .classList.remove('hidden');

    // Activate selected tab
    const selectedTab = document.getElementById('tab-' + tab);

    selectedTab.classList.remove(
        'border-transparent',
        'text-gray-500'
    );

    selectedTab.classList.add(
        'border-indigo-600',
        'text-indigo-600'
    );
}

</script>
<style>
    html {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    html::-webkit-scrollbar {
        display: none;
        width: 0;
    }

    body {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    body::-webkit-scrollbar {
        display: none;
        width: 0;
    }
</style>
@endsection
