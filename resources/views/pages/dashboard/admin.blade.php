<div class="container px-6 mx-auto grid">
  <div class="flex items-center justify-between mb-4">

    <div>
      <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
        Maintenance Reports
      </h2>

      <p class="text-sm text-gray-500 dark:text-gray-400">
        Select a period to update all request-based dashboard cards and charts.
      </p>
    </div>

  </div>


  {{-- REPORT PERIOD SELECTOR --}}
  
<div class="p-4 mb-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">

    <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:gap-2">

        <!-- MOBILE: DATE RANGE DROPDOWN -->
        <div class="w-full sm:hidden">
            <label for="report-period-mobile"
                   class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Date Range
            </label>

            <select
                id="report-period-mobile"
                onchange="changeReportPeriod(this.value)"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-3
                       text-sm text-gray-800 focus:border-indigo-500 focus:ring-2
                       focus:ring-indigo-200 dark:border-gray-600 dark:bg-gray-700
                       dark:text-white">

                <option value="overall">Overall</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="yearly">Yearly</option>
                <option value="decade">10-Year</option>

            </select>
        </div>

        <!-- DESKTOP: ORIGINAL BUTTONS -->
        <div class="hidden flex-wrap gap-2 sm:flex">

            <button type="button" onclick="changeReportPeriod('overall')" id="btn-overall"
                class="report-period-btn px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white">
                Overall
            </button>

            <button type="button" onclick="changeReportPeriod('daily')" id="btn-daily"
                class="report-period-btn px-4 py-2 text-sm font-medium rounded-lg text-gray-600 bg-gray-100 hover:bg-gray-200 dark:text-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600">
                Daily
            </button>

            <button type="button" onclick="changeReportPeriod('weekly')" id="btn-weekly"
                class="report-period-btn px-4 py-2 text-sm font-medium rounded-lg text-gray-600 bg-gray-100 hover:bg-gray-200 dark:text-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600">
                Weekly
            </button>

            <button type="button" onclick="changeReportPeriod('monthly')" id="btn-monthly"
                class="report-period-btn px-4 py-2 text-sm font-medium rounded-lg text-gray-600 bg-gray-100 hover:bg-gray-200 dark:text-gray-300 dark:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600">
                Monthly
            </button>

            <button type="button" onclick="changeReportPeriod('quarterly')" id="btn-quarterly"
                class="report-period-btn px-4 py-2 text-sm font-medium rounded-lg text-gray-600 bg-gray-100 hover:bg-gray-200 dark:text-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600">
                Quarterly
            </button>

            <button type="button" onclick="changeReportPeriod('yearly')" id="btn-yearly"
                class="report-period-btn px-4 py-2 text-sm font-medium rounded-lg text-gray-600 bg-gray-100 hover:bg-gray-200 dark:text-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600">
                Yearly
            </button>

            <button type="button" onclick="changeReportPeriod('decade')" id="btn-decade"
                class="report-period-btn px-4 py-2 text-sm font-medium rounded-lg text-gray-600 bg-gray-100 hover:bg-gray-200 dark:text-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600">
                10-Year
            </button>

        </div>

        <!-- MOBILE: DOWNLOAD PDF -->
        <div class="w-full sm:hidden print:hidden">
            <button type="button" id="downloadReportBtn" onclick="printMaintenanceReport(true)"
                class="inline-flex w-full items-center justify-center rounded-lg bg-[#607844] px-4 py-3
                       text-sm font-semibold text-white hover:bg-[#475e2d]
                       focus:outline-none focus:ring-2 focus:ring-[#607844] focus:ring-offset-2">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3v12m0 0l-4-4m4 4l4-4M5 17v3h14v-3" />
                </svg>
                Download PDF
            </button>
        </div>

        <!-- DESKTOP: PRINT -->
        <div class="hidden justify-end print:hidden sm:ml-auto sm:flex">
            <button type="button" onclick="printMaintenanceReport(false)"
                class="inline-flex w-full sm:w-auto items-center justify-center rounded-lg bg-[#607844] px-4 py-2
                       text-sm font-medium text-white hover:bg-[#475e2d]
                       focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5h-2M6 14h12v8H6v-8z" />
                </svg>
                Print Report
            </button>
        </div>

    </div>
</div>



  {{-- REPORT SUMMARY --}}
  <div class="grid gap-6 mb-6 md:grid-cols-3">

    {{-- Selected Period --}}
    <div class="p-5 bg-white rounded-lg shadow-sm dark:bg-gray-800">

      <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
        Report Period
      </p>

      <p id="reportPeriodTitle" class="mt-2 text-xl font-bold text-gray-700 dark:text-gray-200">
        Overall
      </p>

    </div>


    {{-- Requests --}}
    <div class="p-5 bg-white rounded-lg shadow-sm dark:bg-gray-800">

      <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
        Requests in Period
      </p>

      <p id="reportTotal" class="mt-2 text-3xl font-bold text-indigo-600">
        {{ $total ?? 0 }}
      </p>

    </div>


    {{-- Completed --}}
    <div class="p-5 bg-white rounded-lg shadow-sm dark:bg-gray-800">

      <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
        Completed Requests
      </p>

      <p id="reportCompleted" class="mt-2 text-3xl font-bold text-green-600">
        {{ $completed ?? 0 }}
      </p>

    </div>

  </div>


  {{-- RECURRING PROBLEM LOCATIONS --}}
  <div class="min-w-0 p-5 sm:p-6 mb-8 bg-white rounded-lg shadow-sm dark:bg-gray-800">
    <div class="mb-4">
      <h3 class="font-semibold text-gray-800 dark:text-gray-200">Recurring Problem Locations</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">Top locations for the selected date range, paired with the categories reported at each location.</p>
    </div>
    <div class="relative h-72 min-w-0"><canvas id="locationProblemChart"></canvas></div>
    <p id="locationProblemEmpty" class="hidden py-8 text-center text-sm text-gray-500">No location data is available yet.</p>
  </div>

  {{-- DYNAMIC MAINTENANCE REQUEST TREND --}}
  <div class="min-w-0 mb-8 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="flex flex-col gap-5 border-b border-gray-100 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6 dark:border-gray-700">
      <div class="min-w-0">
        <div class="mb-2 flex items-center gap-2">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#607844]/10 text-[#607844] dark:bg-[#607844]/20 dark:text-green-300">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 14l4-4 4 4 6-7"/></svg>
          </span>
          <span class="text-xs font-semibold uppercase tracking-wider text-[#607844] dark:text-green-300">Maintenance Analytics</span>
        </div>
        <h4 id="reportChartTitle" class="text-lg font-semibold text-gray-800 dark:text-gray-100">
          Maintenance Request Trend
        </h4>
        <p id="reportChartDescription" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Number of maintenance requests received over the selected period.
        </p>
      </div>

      
    </div>

    <div class="p-4 sm:p-6">
      <div class="relative h-72 sm:h-80">
        <canvas id="requestLineChart"></canvas>
      </div>
    </div>
  </div>

{{-- MAINTENANCE REQUESTS --}}
{{-- ========================================================= --}}

<div class="mt-7 mb-5">
    <h2 class="text-xl font-semibold tracking-tight text-gray-800 dark:text-white">
        Maintenance Requests
    </h2>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        Overview of all maintenance requests and their current status.
    </p>
</div>

<div class="grid grid-cols-1 gap-4 mb-9 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

    {{-- Total Requests --}}
    <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Requests</p>
            <p id="dashboardTotal" class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $total ?? 0 }}
            </p>
        </div>
    </div>

    {{-- Pending --}}
    <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
            <p id="dashboardPending" class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $pending ?? 0 }}
            </p>
        </div>
    </div>

    {{-- Approved --}}
    <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600 dark:bg-violet-900/20 dark:text-violet-400">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 3c-2.236 0-4.33.614-6.118 1.984A11.955 11.955 0 003 12c0 2.236.614 4.33 1.984 6.118A11.955 11.955 0 0012 21c2.236 0 4.33-.614 6.118-1.984A11.955 11.955 0 0021 12c0-2.236-.614-4.33-1.382-4.016z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved</p>
            <p id="dashboardApproved" class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $approved ?? 0 }}
            </p>
        </div>
    </div>

    {{-- In Progress --}}
    <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">In Progress</p>
            <p id="dashboardInProgress" class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $inProgress ?? 0 }}
            </p>
        </div>
    </div>

    {{-- Completed --}}
    <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed</p>
            <p id="dashboardCompleted" class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $completed ?? 0 }}
            </p>
        </div>
    </div>

    {{-- Rejected --}}
    <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejected</p>
            <p id="dashboardRejected" class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $rejected ?? 0 }}
            </p>
        </div>
    </div>

    {{-- Top Request Category --}}
<div class="flex min-w-0 items-start gap-4 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 xl:col-span-2">
    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 00.95.69h4.17c.969 0 1.371 1.24.588 1.81l-3.373 2.45a1 1 0 00-.364 1.118l1.288 3.966c.3.922-.755 1.688-1.54 1.118l-3.373-2.45a1 1 0 00-1.176 0l-3.373 2.45c-.784.57-1.838-.196-1.539-1.118l1.288-3.966a1 1 0 00-.364-1.118L6.04 9.393c-.784-.57-.38-1.81.588-1.81h4.17a1 1 0 00.95-.69l1.3-3.966z"/>
            </svg>
    </div>

    <div class="min-w-0 flex-1">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
            Top Request
        </p>

        <p id="dashboardTopCategory"
           class="mt-1 text-base font-semibold leading-snug text-gray-900 dark:text-white break-words">
            {{ $topCategory ?? 'No data' }}
        </p>

        <p id="dashboardTopCategoryCount"
           class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ $topCategoryCount ?? 0 }} requests each
        </p>
    </div>
</div>

</div>


{{-- ========================================================= --}}
{{-- INVENTORY OVERVIEW --}}
{{-- ========================================================= --}}

<div class="mb-5 border-t border-gray-200 pt-7 dark:border-gray-700">
    <h2 class="text-xl font-semibold tracking-tight text-gray-800 dark:text-white">
        Inventory Overview
    </h2>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        Monitor available materials and current stock levels.
    </p>
</div>

<div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-3">

    {{-- Total Inventory --}}
    <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Inventory Items</p>
            <p class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $totalInventory ?? 0 }}
            </p>
        </div>
    </div>

    {{-- Low Stock --}}
    <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Low Stock</p>
            <p class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $low_stocks ?? 0 }}
            </p>
        </div>
    </div>

    {{-- Out of Stock --}}
    <div class="flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Out of Stock</p>
            <p class="mt-1 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $outOfStock ?? 0 }}
            </p>
        </div>
    </div>

</div>

  {{-- ========================================================= --}}
  {{-- DAILY REPORT --}}
  {{-- ========================================================= --}}





  {{-- ========================================================= --}}
  {{-- ANALYTICS --}}
  {{-- ========================================================= --}}

  <div class="flex items-center justify-between mb-4">
    <div>
      <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
        Maintenance Analytics
      </h2>

      <p class="text-sm text-gray-500 dark:text-gray-400">
        Visual overview of maintenance activity and request trends.
      </p>
    </div>
  </div>


  <div class="grid gap-6 mb-8 lg:grid-cols-2">

    {{-- CATEGORY --}}
    <div class="min-w-0 p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">

      <h4 class="mb-1 font-semibold text-gray-800 dark:text-gray-200">
        Requests by Category
      </h4>

      <p class="mb-5 text-sm text-gray-500 dark:text-gray-400">
        Distribution of maintenance requests by category.
      </p>

      <div class="relative h-72">
        <canvas id="pie"></canvas>
      </div>

    </div>


    {{-- STATUS --}}
    <div class="min-w-0 p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">

      <h4 class="mb-1 font-semibold text-gray-800 dark:text-gray-200">
        Request Status Overview
      </h4>

      <p class="mb-5 text-sm text-gray-500 dark:text-gray-400">
        Current status distribution of all requests.
      </p>

      <div class="relative h-72">
        <canvas id="statusChart"></canvas>
      </div>

    </div>



  </div>

</div>

{{-- ========================================================= --}}
{{-- PRINTABLE MAINTENANCE REPORT --}}
{{-- ========================================================= --}}

<div id="printReport">

  {{-- REPORT HEADER --}}
  <div class="print-report-header">

    <div class="print-company">
      MAINTENANCE MANAGEMENT SYSTEM
    </div>

    <div class="print-report-title">
      MAINTENANCE REPORT
    </div>

    <div class="print-report-meta">
      <div>
        <strong>Report Period:</strong>
        <span id="printPeriod"></span>
      </div>

      <div>
        <strong>Date Range:</strong>
        <span id="printDateRange"></span>
      </div>

      <div>
        <strong>Generated:</strong>
        <span id="printGenerated"></span>
      </div>
    </div>

  </div>


  {{-- SUMMARY --}}
  <div class="print-section">

    <div class="print-section-title">
      REQUEST SUMMARY
    </div>

    <div class="print-summary-grid">

      <div class="print-summary-card">
        <span>Total Requests</span>
        <strong id="printTotal">0</strong>
      </div>

      <div class="print-summary-card">
        <span>Pending</span>
        <strong id="printPending">0</strong>
      </div>

      <div class="print-summary-card">
        <span>Approved</span>
        <strong id="printApproved">0</strong>
      </div>

      <div class="print-summary-card">
        <span>In Progress</span>
        <strong id="printInProgress">0</strong>
      </div>

      <div class="print-summary-card">
        <span>Completed</span>
        <strong id="printCompleted">0</strong>
      </div>

      <div class="print-summary-card">
        <span>Rejected</span>
        <strong id="printRejected">0</strong>
      </div>

    </div>

  </div>

{{-- RECURRING PROBLEM LOCATIONS --}}
  <div class="print-section print-chart-section">

    <div class="print-section-title">
      RECURRING PROBLEM LOCATIONS
    </div>

    <p class="print-chart-description">
      Top locations for the selected date range, paired with the categories reported at each location.
    </p>

    <div class="print-chart">
      <img id="printLocationProblemImage" alt="Recurring Problem Locations">
    </div>

  </div>
  {{-- TOP CATEGORY --}}
  <div class="print-section">

    <div class="print-section-title">
      TOP REQUEST CATEGORY
    </div>

    <div class="print-highlight">

      <div>
        <span>Most Requested Category</span>
        <strong id="printTopCategory">
          No data
        </strong>
      </div>

      <div>
        <span>Number of Requests</span>
        <strong id="printTopCategoryCount">
          0
        </strong>
      </div>

    </div>

  </div>


  {{-- TREND --}}
  <div class="print-section print-chart-section">

    <div class="print-section-title">
      MAINTENANCE REQUEST TREND
    </div>

    <div class="print-chart-description" id="printChartDescription">
      Number of maintenance requests received during the selected period.
    </div>

    <div class="print-chart">
      <img id="printTrendImage" alt="Maintenance Request Trend">
    </div>

  </div>


  {{-- CATEGORY --}}
  <div class="print-section print-chart-section">

    <div class="print-section-title">
      REQUESTS BY CATEGORY
    </div>

    <div class="print-chart">
      <img id="printCategoryImage" alt="Requests by Category">
    </div>

  </div>


  {{-- STATUS --}}
  <div class="print-section print-chart-section">

    <div class="print-section-title">
      REQUEST STATUS OVERVIEW
    </div>

    <div class="print-chart">
      <img id="printStatusImage" alt="Request Status Overview">
    </div>

  </div>


  


  {{-- FOOTER --}}
  <div class="print-report-footer">

    Maintenance Management System

    <span>•</span>

    <span id="printFooterPeriod"></span>

    <span>•</span>

    Generated <span id="printFooterDate"></span>

  </div>

</div>
<style>
  #printReport {
    display: none;
  }

  /* Styling used by the mobile PDF export. The report is rendered off-screen
     so it does not replace or disturb the visible dashboard. */
  #printReport.pdf-export-mode {
    display: block !important;
    position: absolute !important;
    left: -10000px !important;
    top: 0 !important;
    width: 794px !important;
    max-width: none !important;
    margin: 0 !important;
    padding: 0 !important;
    box-sizing: border-box !important;
    background: #ffffff !important;
    color: #111827 !important;
    font-family: Arial, Helvetica, sans-serif !important;
  }

  #printReport.pdf-export-mode .print-report-header {
    border-bottom: 2px solid #111827;
    padding-bottom: 10px;
    margin-bottom: 14px;
  }

  #printReport.pdf-export-mode .print-company {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0.4px;
  }

  #printReport.pdf-export-mode .print-report-title {
    margin-top: 3px;
    font-size: 20px;
    font-weight: 700;
  }

  #printReport.pdf-export-mode .print-report-meta {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 8px;
    margin-top: 12px;
    font-size: 12px;
  }

  #printReport.pdf-export-mode .print-section {
    margin-bottom: 14px;
    break-inside: avoid;
  }

  #printReport.pdf-export-mode .print-section-title {
    font-size: 14px;
    font-weight: 700;
    padding-bottom: 5px;
    margin-bottom: 8px;
    border-bottom: 1px solid #9ca3af;
    color: #111827;
  }

  #printReport.pdf-export-mode .print-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 7px;
  }

  #printReport.pdf-export-mode .print-summary-card {
    border: 1px solid #d1d5db;
    padding: 8px;
    min-height: 0;
    break-inside: avoid;
  }

  #printReport.pdf-export-mode .print-summary-card span,
  #printReport.pdf-export-mode .print-highlight span {
    display: block;
    font-size: 11px;
    color: #4b5563;
    margin-bottom: 4px;
  }

  #printReport.pdf-export-mode .print-summary-card strong {
    display: block;
    font-size: 18px;
    color: #111827;
  }

  #printReport.pdf-export-mode .print-highlight {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border: 1px solid #d1d5db;
    break-inside: avoid;
  }

  #printReport.pdf-export-mode .print-highlight > div {
    padding: 10px;
  }

  #printReport.pdf-export-mode .print-highlight > div + div {
    border-left: 1px solid #d1d5db;
  }

  #printReport.pdf-export-mode .print-highlight strong {
    display: block;
    font-size: 17px;
    color: #111827;
  }

  #printReport.pdf-export-mode .print-chart-description {
    font-size: 11px;
    color: #4b5563;
    margin-bottom: 6px;
  }

  #printReport.pdf-export-mode .print-chart {
    width: 100%;
    text-align: center;
  }

  #printReport.pdf-export-mode .print-chart img {
    display: block;
    width: 100%;
    max-height: 235px;
    height: auto;
    margin: 0 auto;
    object-fit: contain;
  }

  #printReport.pdf-export-mode .print-report-footer {
    margin-top: 12px;
    padding-top: 7px;
    border-top: 1px solid #9ca3af;
    text-align: center;
    font-size: 11px;
    color: #6b7280;
    break-inside: avoid;
  }

  #printReport.pdf-export-mode .print-section-title,
  #printReport.pdf-export-mode .print-summary-card,
  #printReport.pdf-export-mode .print-highlight,
  #printReport.pdf-export-mode .print-chart {
    page-break-inside: avoid;
  }

  @media print {
    @page {
      size: A4 portrait;
      margin: 10mm;
    }

    html,
    body {
      margin: 0 !important;
      padding: 0 !important;
      min-height: 0 !important;
      height: auto !important;
      background: #ffffff !important;
      color: #111827 !important;
      font-family: Arial, Helvetica, sans-serif !important;
      overflow: visible !important;
    }

    body * {
      visibility: hidden !important;
    }

    #printReport,
    #printReport * {
      visibility: visible !important;
    }

    #printReport {
      position: absolute !important;
      left: 0 !important;
      top: 0 !important;
      display: block !important;
      width: 100% !important;
      max-width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      background: #ffffff !important;
      color: #111827 !important;
      box-sizing: border-box !important;
    }

    #printReport img {
      display: block !important;
      max-width: 100% !important;
      height: auto !important;
      object-fit: contain !important;
    }

    .print-report-header {
      border-bottom: 2px solid #111827;
      padding-bottom: 10px;
      margin-bottom: 14px;
    }

    .print-company {
      font-size: 16px;
      font-weight: 700;
      letter-spacing: 0.4px;
    }

    .print-report-title {
      margin-top: 3px;
      font-size: 20px;
      font-weight: 700;
    }

    .print-report-meta {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 8px;
      margin-top: 12px;
      font-size: 9pt;
    }

    .print-section {
      margin-bottom: 14px;
      page-break-inside: auto;
      break-inside: auto;
    }

    .print-section-title {
      font-size: 11pt;
      font-weight: 700;
      padding-bottom: 5px;
      margin-bottom: 8px;
      border-bottom: 1px solid #9ca3af;
      color: #111827;
      page-break-after: avoid;
      break-after: avoid;
    }

    .print-summary-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 7px;
    }

    .print-summary-card {
      border: 1px solid #d1d5db;
      padding: 8px;
      min-height: 0;
      break-inside: avoid;
    }

    .print-summary-card span {
      display: block;
      font-size: 8pt;
      color: #4b5563;
      margin-bottom: 3px;
    }

    .print-summary-card strong {
      display: block;
      font-size: 14pt;
      color: #111827;
    }

    .print-highlight {
      display: grid;
      grid-template-columns: 1fr 1fr;
      border: 1px solid #d1d5db;
      break-inside: avoid;
    }

    .print-highlight > div {
      padding: 10px;
    }

    .print-highlight > div + div {
      border-left: 1px solid #d1d5db;
    }

    .print-highlight span {
      display: block;
      font-size: 8pt;
      color: #4b5563;
      margin-bottom: 4px;
    }

    .print-highlight strong {
      display: block;
      font-size: 13pt;
      color: #111827;
    }

    .print-chart-description {
      font-size: 8pt;
      color: #4b5563;
      margin-bottom: 6px;
    }

    .print-chart {
      width: 100%;
      text-align: center;
    }

    .print-chart img {
      width: 100%;
      max-height: 62mm;
      margin: 0 auto;
      object-fit: contain;
    }

    .print-report-footer {
      margin-top: 12px;
      padding-top: 7px;
      border-top: 1px solid #9ca3af;
      text-align: center;
      font-size: 8pt;
      color: #6b7280;
      break-inside: avoid;
    }

    .print-temporarily-hidden {
      display: none !important;
    }
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const analyticsUrl = @json(route('adminDashboard.analytics'));
    const initialAnalytics = {
      period: @json($period ?? 'overall'),
      title: @json($title ?? 'Overall'),
      chartTitle: @json($chartTitle ?? 'Overall Maintenance Requests'),
      description: @json($description ?? 'All recorded maintenance requests, grouped by year.'),
      dateRange: @json($dateRange ?? 'All recorded maintenance requests'),

      total: @json($total ?? 0),
      pending: @json($pending ?? 0),
      approved: @json($approved ?? 0),
      inProgress: @json($inProgress ?? 0),
      completed: @json($completed ?? 0),
      rejected: @json($rejected ?? 0),

      categoryData: @json($categoryData ?? []),
      topCategory: @json($topCategory ?? 'No data'),
      topCategoryCount: @json($topCategoryCount ?? 0),

      labels: @json($labels ?? []),
      data: @json($data ?? []),
      locationData: @json($locationData ?? [])
    };

    let categoryChart = null;
    let statusChart = null;
    let trendChart = null;
    let reportChart = null;
    let locationProblemChart = null;
    const analyticsCache = new Map();

    const setText = (id, value) => {
      const element = document.getElementById(id);
      if (element) element.textContent = value;
    };
    function printMaintenanceReport(downloadPdf = false) {
      const data = window.currentDashboardAnalytics;

      if (!data) {
        console.error('No dashboard analytics data available for printing.');
        alert('Please wait for the dashboard to finish loading.');
        return;
      }

      const generatedDate = new Date().toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });

      setText('printPeriod', data.title || 'Overall');
      setText('printDateRange', data.dateRange || 'All recorded maintenance requests');
      setText('printGenerated', generatedDate);

      setText('printTotal', data.total ?? 0);
      setText('printPending', data.pending ?? 0);
      setText('printApproved', data.approved ?? 0);
      setText('printInProgress', data.inProgress ?? 0);
      setText('printCompleted', data.completed ?? 0);
      setText('printRejected', data.rejected ?? 0);

      setText('printTopCategory', data.topCategory || 'No data');
      setText('printTopCategoryCount', data.topCategoryCount ?? 0);
      setText(
        'printChartDescription',
        data.description ||
          'Number of maintenance requests received during the selected period.'
      );
      setText('printFooterPeriod', data.title || 'Overall');
      setText('printFooterDate', generatedDate);

      const chartsToPrint = [
        ['requestLineChart', 'printTrendImage'],
        ['pie', 'printCategoryImage'],
        ['statusChart', 'printStatusImage'],
        ['locationProblemChart', 'printLocationProblemImage']
      ];

      chartsToPrint.forEach(([canvasId, imageId]) => {
        const canvas = document.getElementById(canvasId);
        const image = document.getElementById(imageId);

        if (canvas && image && canvas.width > 0 && canvas.height > 0) {
          image.src = canvas.toDataURL('image/png', 1.0);
        } else if (image) {
          image.removeAttribute('src');
        }
      });

      // Hide unrelated dashboard branches while printing. This prevents the
      // hidden dashboard's full page layout from creating extra blank pages.
      const printReport = document.getElementById('printReport');
      if (printReport) {
        let current = printReport;
        while (current && current !== document.body) {
          const parent = current.parentElement;
          if (!parent) break;

          Array.from(parent.children).forEach(sibling => {
            if (
              sibling !== current &&
              !sibling.matches('script, style, link') &&
              !sibling.classList.contains('print-temporarily-hidden')
            ) {
              sibling.classList.add('print-temporarily-hidden');
            }
          });

          current = parent;
        }
      }

      const printableImages = Array.from(
        document.querySelectorAll(
          '#printTrendImage, #printCategoryImage, #printStatusImage, #printLocationProblemImage'
        )
      ).filter(image => image.getAttribute('src'));

      Promise.all(
        printableImages.map(image => {
          if (image.complete && image.naturalWidth > 0) {
            return Promise.resolve();
          }

          return new Promise(resolve => {
            image.onload = resolve;
            image.onerror = resolve;
          });
        })
      ).then(async () => {
        if (!downloadPdf) {
          window.print();
          return;
        }

        const reportElement = document.getElementById('printReport');
        const downloadButton = document.getElementById('downloadReportBtn');

        if (!reportElement || typeof html2pdf === 'undefined') {
          alert('PDF download is unavailable. Please check your internet connection and try again.');
          return;
        }

        if (downloadButton) {
          downloadButton.disabled = true;
          downloadButton.textContent = 'Preparing PDF…';
        }

        reportElement.classList.add('pdf-export-mode');

        try {
          const safePeriod = String(data.period || 'overall')
            .replace(/[^a-z0-9_-]/gi, '-');
          const fileName = `CDSO-Maintenance-Report-${safePeriod}.pdf`;

          await html2pdf()
            .set({
              margin: [10, 10, 10, 10],
              filename: fileName,
              image: { type: 'jpeg', quality: 0.98 },
              html2canvas: {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff',
                scrollX: 0,
                scrollY: 0
              },
              jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
              },
              pagebreak: { mode: ['css', 'legacy'] }
            })
            .from(reportElement)
            .save();
        } catch (error) {
          console.error('PDF download failed:', error);
          alert('The PDF could not be generated. Please try again.');
        } finally {
          reportElement.classList.remove('pdf-export-mode');
          document.querySelectorAll('.print-temporarily-hidden').forEach(element => {
            element.classList.remove('print-temporarily-hidden');
          });

          if (downloadButton) {
            downloadButton.disabled = false;
            downloadButton.innerHTML = `
              <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor"
                   viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 3v12m0 0l-4-4m4 4l4-4M5 17v3h14v-3" />
              </svg>
              Download PDF
            `;
          }
        }
      }).catch(error => {
        console.error('Unable to prepare maintenance report:', error);
        alert('The report could not be prepared. Please try again.');
      });
    }

    window.addEventListener('afterprint', function () {
      document.querySelectorAll('.print-temporarily-hidden').forEach(element => {
        element.classList.remove('print-temporarily-hidden');
      });
    });

    window.printMaintenanceReport = printMaintenanceReport;
    function updateButtons(period) {
      document.querySelectorAll('.report-period-btn').forEach(button => {
        button.classList.remove('bg-indigo-600', 'text-white');
        button.classList.add(
          'text-gray-600',
          'bg-gray-100',
          'dark:text-gray-300',
          'dark:bg-gray-700'
        );
      });

      const activeButton = document.getElementById('btn-' + period);

      if (activeButton) {
        activeButton.classList.remove(
          'text-gray-600',
          'bg-gray-100',
          'dark:text-gray-300',
          'dark:bg-gray-700'
        );
        activeButton.classList.add('bg-indigo-600', 'text-white');
      }
    }

    function destroyCharts() {
      [categoryChart, statusChart, trendChart, reportChart, locationProblemChart].forEach(chart => {
        if (chart) chart.destroy();
      });

      categoryChart = null;
      statusChart = null;
      trendChart = null;
      reportChart = null;
      locationProblemChart = null;
    }

    function renderCharts(data) {
      destroyCharts();

      const categoryCanvas = document.getElementById('pie');
      if (categoryCanvas) {
        const categoryData = data.categoryData || {};
        categoryChart = new Chart(categoryCanvas, {
          type: 'doughnut',
          data: {
            labels: Object.keys(categoryData),
            datasets: [{
              data: Object.values(categoryData),
              backgroundColor: [
                '#6366F1', '#10B981', '#F59E0B',
                '#EF4444', '#3B82F6', '#8B5CF6'
              ],
              borderWidth: 2,
              borderColor: '#ffffff'
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
              legend: {
                position: 'bottom',
                labels: { padding: 18, usePointStyle: true }
              }
            }
          }
        });
      }

      const statusCanvas = document.getElementById('statusChart');
      if (statusCanvas) {
        statusChart = new Chart(statusCanvas, {
          type: 'doughnut',
          data: {
            labels: ['Pending', 'Approved', 'In Progress', 'Completed', 'Rejected'],
            datasets: [{
              data: [
                data.pending,
                data.approved,
                data.inProgress,
                data.completed,
                data.rejected
              ],
              backgroundColor: [
                '#F59E0B', '#8B5CF6', '#3B82F6',
                '#10B981', '#EF4444'
              ],
              borderWidth: 2,
              borderColor: '#ffffff'
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
              legend: {
                position: 'bottom',
                labels: { padding: 18, usePointStyle: true }
              }
            }
          }
        });
      }

      const lineCanvas = document.getElementById('requestLineChart');

      if (lineCanvas) {
        trendChart = new Chart(lineCanvas, {
          type: 'line',

          data: {
            labels: data.labels || [],

            datasets: [{
              label: 'Maintenance Requests',
              data: data.data || [],

              borderColor: '#607844',
              backgroundColor: 'rgba(96, 120, 68, 0.13)',

              borderWidth: 3,

              fill: true,

              tension: 0.4,

              pointRadius: (data.data || []).length === 1 ? 6 : 3,
              pointHoverRadius: 7,

              pointBackgroundColor: '#607844',
              pointBorderColor: '#ffffff',
              pointBorderWidth: 2,
              pointHitRadius: 12
            }]
          },

          options: {
            responsive: true,
            maintainAspectRatio: false,

            interaction: {
              intersect: false,
              mode: 'index'
            },

            scales: {
              y: {
                beginAtZero: true,

                ticks: {
                  precision: 0
                },

                grid: {
                  color: 'rgba(156, 163, 175, 0.15)'
                },

                title: {
                  display: true,
                  text: 'Number of Requests'
                }
              },

              x: {
                offset: true,
                grid: {
                  display: false
                },

                title: {
                  display: true,
                  text: 'Period'
                }
              }
            },

            plugins: {
              legend: {
                display: false
              },

              tooltip: {
                callbacks: {
                  label: function (context) {
                    return context.parsed.y + ' request(s)';
                  }
                }
              }
            }
          }
        });
      }

      const locationCanvas = document.getElementById('locationProblemChart');
      const locationData = data.locationData || {};
      const locationEmpty = document.getElementById('locationProblemEmpty');
      if (locationCanvas && Object.keys(locationData).length) {
        if (locationEmpty) locationEmpty.classList.add('hidden');
        const locationEntries = Object.entries(locationData);
        locationProblemChart = new Chart(locationCanvas, {
          type: 'bar',
          data: {
            labels: locationEntries.map(([location, item]) => {
              const categories = Array.isArray(item.categories) && item.categories.length ? item.categories.join(', ') : 'Uncategorized';
              return `${location} — ${categories}`;
            }),
            datasets: [{ label: 'Requests', data: locationEntries.map(([, item]) => Number(item.total || 0)), backgroundColor: '#607844', borderRadius: 5 }]
          },
          options: {
            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              tooltip: { callbacks: { label: context => `${context.parsed.x} request(s)` } }
            },
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } }, y: { ticks: { autoSkip: false } } }
          }
        });
      } else if (locationEmpty) {
        locationEmpty.classList.remove('hidden');
      }
    }

    function renderDashboard(data) {

      // Store the currently selected analytics
      // so the Print Report button uses the same period.
      window.currentDashboardAnalytics = data;

      updateButtons(data.period);

      // Main dashboard cards
      setText('dashboardTotal', data.total);
      setText('dashboardPending', data.pending);
      setText('dashboardApproved', data.approved);
      setText('dashboardInProgress', data.inProgress);
      setText('dashboardCompleted', data.completed);
      setText('dashboardRejected', data.rejected);
      setText('dashboardTopCategory', data.topCategory);
      setText(
        'dashboardTopCategoryCount',
        data.topCategoryCount + ' requests each'
      );

      // Report summary
      setText('reportPeriodTitle', data.title);
      setText('reportTotal', data.total);
      setText('reportCompleted', data.completed);
      setText('reportChartTitle', data.chartTitle);
      setText('reportChartDescription', data.description);
      setText('reportTrendTotal', (data.data || []).reduce((sum, value) => sum + (Number(value) || 0), 0));

      renderCharts(data);
    }

    async function changeReportPeriod(period) {
      const mobileSelect = document.getElementById('report-period-mobile');
      if (mobileSelect && mobileSelect.value !== period) mobileSelect.value = period;
      updateButtons(period);

      if (analyticsCache.has(period)) {
        renderDashboard(analyticsCache.get(period));
        return;
      }

      const controls = [...document.querySelectorAll('.report-period-btn'), mobileSelect].filter(Boolean);
      controls.forEach(control => control.disabled = true);
      const periodTitle = document.getElementById('reportPeriodTitle');
      if (periodTitle) periodTitle.textContent = 'Loading…';

      try {
        const response = await fetch(
          analyticsUrl + '?period=' + encodeURIComponent(period),
          { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' }
        );
        if (!response.ok) throw new Error('Unable to load dashboard analytics.');
        const data = await response.json();
        analyticsCache.set(period, data);
        renderDashboard(data);
      } catch (error) {
        console.error(error);
        if (periodTitle) periodTitle.textContent = 'Unable to load report';
        alert('The report could not be loaded. Please try again.');
      } finally {
        controls.forEach(control => control.disabled = false);
      }
    }

    // Keep this function globally accessible because the existing buttons
    // call changeReportPeriod(...) directly from their onclick handlers.
    window.changeReportPeriod = changeReportPeriod;

    analyticsCache.set(initialAnalytics.period || 'overall', initialAnalytics);
    renderDashboard(initialAnalytics);
    const initialMobilePeriod = document.getElementById('report-period-mobile');
    if (initialMobilePeriod) initialMobilePeriod.value = initialAnalytics.period || 'overall';
  });
</script>