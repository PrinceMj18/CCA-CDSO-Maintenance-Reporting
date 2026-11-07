{{-- resources/views/pages/dashboard/user.blade.php --}}

@extends('layouts.apps')

@section('content')

    @if (session('success'))
        <div id="success-popup"
            class="fixed right-5 top-5 z-[99999] w-[calc(100%-2rem)] max-w-md rounded-xl border border-green-200 bg-white p-4 shadow-lg dark:border-green-800 dark:bg-gray-800"
            role="alert" aria-live="polite">
            <div class="flex items-start gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-50 text-[#607844] dark:bg-green-900/30 dark:text-green-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Request submitted</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="document.getElementById('success-popup')?.remove()"
                    class="text-xl leading-none text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                    aria-label="Close notification">&times;</button>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const popup = document.getElementById('success-popup');
                if (popup) {
                    setTimeout(() => {
                        popup.style.transition = 'opacity 0.3s ease';
                        popup.style.opacity = '0';
                        setTimeout(() => popup.remove(), 300);
                    }, 5000);
                }
            });
        </script>
    @endif

    <div class="mx-auto max-w-7xl px-4 py-7 sm:px-6 lg:px-8">

        {{-- Heading --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="mb-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#607844] dark:text-green-400">
                    CDSO Maintenance Portal
                </p>
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                    Dashboard
                </h1>
                <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                    Manage your maintenance requests in one place.
                </p>
            </div>

            <a href="{{ route('showForm') }}"
                class="inline-flex items-center justify-center gap-2 self-start rounded-lg bg-[#607844] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#506638] focus:outline-none focus:ring-2 focus:ring-[#607844] focus:ring-offset-2 sm:self-auto">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5" />
                </svg>
                Submit a Request
            </a>
        </div>

        {{-- Welcome panel --}}
        <section
            class="relative mb-8 overflow-hidden rounded-2xl border border-[#dce5d3] bg-[#f2f5ee] dark:border-gray-700 dark:bg-gray-800">
            <div class="absolute -right-10 -top-16 h-56 w-56 rounded-full border-[28px] border-[#607844]/[0.07]"></div>
            <div class="absolute -bottom-24 right-28 h-48 w-48 rounded-full bg-[#607844]/[0.05]"></div>

            <div class="relative grid gap-6 p-6 sm:p-8 lg:grid-cols-[1fr_auto] lg:items-center">
                <div class="max-w-2xl">
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-[#d5dfcb] bg-white/70 px-3 py-1 text-xs font-medium text-[#536b3c] dark:border-gray-600 dark:bg-gray-700 dark:text-green-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#718b54]"></span>
                        Maintenance request center
                    </span>
                    <h2 class="mt-4 text-2xl font-semibold tracking-tight text-[#263321] dark:text-white sm:text-3xl">
                        Welcome back!
                    </h2>
                    <p class="mt-2 max-w-xl text-sm leading-6 text-[#64705d] dark:text-gray-300">
                        Stay informed about your maintenance concerns. Check request statuses and follow each step until the
                        work is completed.
                    </p>
                    <a href="{{ route('trackReq', ['status' => 'all']) }}"
                        class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-[#536b3c] transition hover:text-[#354927] dark:text-green-300 dark:hover:text-green-200">
                        Track your requests
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14m-6-6l6 6-6 6" />
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-3 lg:w-72">
                    <div
                        class="rounded-xl border border-white/80 bg-white/80 p-4 shadow-sm dark:border-gray-600 dark:bg-gray-700">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#edf2e8] text-[#607844] dark:bg-gray-600 dark:text-green-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                            </svg>
                        </div>
                        <p class="mt-3 text-xs font-medium text-gray-500 dark:text-gray-300">Total requests</p>
                        <p class="mt-0.5 text-2xl font-semibold text-gray-900 dark:text-white">{{ $total ?? 0 }}</p>
                    </div>

                    <div
                        class="rounded-xl border border-white/80 bg-white/80 p-4 shadow-sm dark:border-gray-600 dark:bg-gray-700">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#f5f0df] text-[#987b32] dark:bg-gray-600 dark:text-yellow-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M12 8v4l2.5 2.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="mt-3 text-xs font-medium text-gray-500 dark:text-gray-300">Active requests</p>
                        <p class="mt-0.5 text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ ($pending ?? 0) + ($approved ?? 0) + ($inProgress ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Request status cards --}}
        <section class="mb-8">
            <div class="mb-4 flex items-end justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Request Overview</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">A quick look at your request statuses.</p>
                </div>
                <a href="{{ route('trackReq', ['status' => 'all']) }}"
                    class="hidden text-sm font-medium text-[#607844] hover:underline dark:text-green-400 sm:inline">
                    View all
                </a>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">

                

                {{-- Pending --}}
                <a href="{{ route('trackReq', ['status' => 'pending']) }}"
                    class="group rounded-xl border border-gray-200 bg-white p-4 transition duration-200 hover:-translate-y-0.5 hover:border-amber-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[#607844] dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Pending</span>
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M12 8v4l2.5 2.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold tracking-tight text-amber-600 dark:text-amber-400">
                        {{ $pending ?? 0 }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Awaiting review</p>
                </a>

                {{-- Approved --}}
                <a href="{{ route('trackReq', ['status' => 'approved']) }}"
                    class="group rounded-xl border border-gray-200 bg-white p-4 transition duration-200 hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[#607844] dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Approved</span>
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600 dark:bg-sky-900/30 dark:text-sky-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M5 12l4 4L19 6" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold tracking-tight text-sky-700 dark:text-sky-400">
                        {{ $approved ?? 0 }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Accepted requests</p>
                </a>

                {{-- In progress --}}
                <a href="{{ route('trackReq', ['status' => 'in_progress']) }}"
                    class="group rounded-xl border border-gray-200 bg-white p-4 transition duration-200 hover:-translate-y-0.5 hover:border-violet-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[#607844] dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">In Progress</span>
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-50 text-violet-600 dark:bg-violet-900/30 dark:text-violet-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M12 3v3m0 12v3M3 12h3m12 0h3M5.64 5.64l2.12 2.12m8.48 8.48 2.12 2.12m0-12.72-2.12 2.12m-8.48 8.48-2.12 2.12" />
                                <circle cx="12" cy="12" r="4" stroke-width="1.8" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold tracking-tight text-violet-700 dark:text-violet-400">
                        {{ $inProgress ?? 0 }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Work underway</p>
                </a>

                {{-- Completed --}}
                <a href="{{ route('trackReq', ['status' => 'completed']) }}"
                    class="group rounded-xl border border-gray-200 bg-white p-4 transition duration-200 hover:-translate-y-0.5 hover:border-green-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[#607844] dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Completed</span>
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M5 12l4 4L19 6" />
                                <circle cx="12" cy="12" r="9" stroke-width="1.8" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold tracking-tight text-green-700 dark:text-green-400">
                        {{ $completed ?? 0 }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Finished requests</p>
                </a>
{{-- rejected --}}
                <a href="{{ route('trackReq', ['status' => 'rejected']) }}"
                    class="group rounded-xl border border-gray-200 bg-white p-4 transition duration-200 hover:-translate-y-0.5 hover:border-[#aab99b] hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[#607844] dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Rejected Requests</span>
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#edf2e8] text-[#607844] dark:bg-gray-700 dark:text-green-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M6 6l12 12M18 6L6 18" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $rejected ?? 0 }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">View rejected list</p>
                </a>
            </div>
        </section>

        {{-- Workflow --}}
        <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 sm:p-6">
            <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#607844] dark:text-green-400">How it works
                </p>
                <h2 class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Your request journey</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Each request moves through these four stages.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="relative flex gap-3">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-amber-200 bg-amber-50 text-sm font-semibold text-amber-700 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                        01</div>
                    <div class="pt-1">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Pending</h3>
                        <p class="mt-1 text-sm leading-5 text-gray-500 dark:text-gray-400">Your request is received and
                            waiting for review.</p>
                    </div>
                </div>

                <div class="relative flex gap-3">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-sky-200 bg-sky-50 text-sm font-semibold text-sky-700 dark:border-sky-800 dark:bg-sky-900/30 dark:text-sky-300">
                        02</div>
                    <div class="pt-1">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Approved</h3>
                        <p class="mt-1 text-sm leading-5 text-gray-500 dark:text-gray-400">The request is accepted for
                            action.</p>
                    </div>
                </div>

                <div class="relative flex gap-3">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-violet-200 bg-violet-50 text-sm font-semibold text-violet-700 dark:border-violet-800 dark:bg-violet-900/30 dark:text-violet-300">
                        03</div>
                    <div class="pt-1">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">In Progress</h3>
                        <p class="mt-1 text-sm leading-5 text-gray-500 dark:text-gray-400">Assigned personnel are working on
                            the concern.</p>
                    </div>
                </div>

                <div class="relative flex gap-3">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-green-200 bg-green-50 text-sm font-semibold text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">
                        04</div>
                    <div class="pt-1">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Completed</h3>
                        <p class="mt-1 text-sm leading-5 text-gray-500 dark:text-gray-400">The maintenance work is finished.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div
            class="mt-6 flex flex-col items-center justify-between gap-2 border-t border-gray-200 pt-4 text-xs text-gray-400 dark:border-gray-700 dark:text-gray-500 sm:flex-row">
            <span>CDSO Maintenance Reporting System</span>
            <span>Need assistance? Contact the system administrator.</span>
        </div>

    </div>

@endsection