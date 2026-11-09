
<div class="container px-4 mx-auto">

    <!-- ACCOUNT SUMMARY BANNER -->
    <div class="relative mb-5 overflow-hidden rounded-xl shadow-sm"
         style="background-image: url('{{ asset('img/cca_school.png') }}');
                background-size: cover;
                background-position: center 45%;">

        <!-- Overlay -->
        <div class="absolute inset-0"
             style="background: linear-gradient(90deg,
                    rgba(25, 39, 24, 0.90) 0%,
                    rgba(40, 58, 35, 0.78) 55%,
                    rgba(40, 58, 35, 0.45) 100%);">
        </div>

        <div class="relative z-10 flex flex-col justify-center
                    min-h-[150px] px-6 py-6 sm:px-8">

            <div class="flex items-center gap-2 mb-2">
                <span class="w-7 h-0.5 rounded-full bg-green-300"></span>

                <p class="text-xs font-semibold tracking-[0.18em]
                          text-green-100 uppercase">
                    Administrator Dashboard
                </p>
            </div>

            <h2 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                Account Summary
            </h2>

            <p class="mt-1 text-sm text-gray-200">
                Monitor registered accounts and system access by role.
            </p>

            <div class="flex items-center gap-2 mt-4">
                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                <span class="text-xs font-medium text-green-100">
                    Account Overview
                </span>
            </div>

        </div>
    </div>


    <!-- ACCOUNT STATISTICS -->
    <div class="grid grid-cols-1 gap-3 mb-6 sm:grid-cols-2 xl:grid-cols-4">

        <!-- TOTAL ACCOUNTS -->
        <div class="relative flex items-center gap-4 p-4 overflow-hidden
                    bg-white border border-gray-200 rounded-xl
                    shadow-sm transition-all duration-200
                    hover:shadow-md hover:-translate-y-0.5
                    dark:bg-gray-800 dark:border-gray-700">

            <div class="absolute left-0 top-0 bottom-0 w-1"
                 style="background-color: #52693A;"></div>

            <div class="flex items-center justify-center flex-shrink-0
                        w-12 h-12 rounded-xl"
                 style="background-color: rgba(82, 105, 58, 0.12);">

                <svg class="w-6 h-6"
                     style="color: #52693A;"
                     fill="currentColor"
                     viewBox="0 0 20 20">

                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                </svg>

            </div>

            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                    Total Accounts
                </p>

                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{ $total ?? 0 }}
                    </h3>

                    <span class="text-[10px] font-semibold uppercase tracking-wide"
                          style="color: #52693A;">
                        All
                    </span>
                </div>

                <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                    Registered accounts
                </p>
            </div>
        </div>


        <!-- ADMIN -->
        <div class="relative flex items-center gap-4 p-4 overflow-hidden
                    bg-white border border-gray-200 rounded-xl
                    shadow-sm transition-all duration-200
                    hover:shadow-md hover:-translate-y-0.5
                    dark:bg-gray-800 dark:border-gray-700">

            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>

            <div class="flex items-center justify-center flex-shrink-0
                        w-12 h-12 text-blue-600 bg-blue-50 rounded-xl
                        dark:bg-blue-900/20 dark:text-blue-300">

                <svg class="w-6 h-6"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 3l7 4v5c0 4.42-3.13 8.36-7 9-3.87-.64-7-4.58-7-9V7l7-4z"/>

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M9 12l2 2 4-4"/>
                </svg>

            </div>

            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                    Administrative
                </p>

                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{ $admin ?? 0 }}
                    </h3>

                    <span class="text-[10px] font-semibold text-blue-600 uppercase tracking-wide">
                        Admin
                    </span>
                </div>

                <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                    System administrators
                </p>
            </div>
        </div>


        <!-- PERSONNEL -->
        <div class="relative flex items-center gap-4 p-4 overflow-hidden
                    bg-white border border-gray-200 rounded-xl
                    shadow-sm transition-all duration-200
                    hover:shadow-md hover:-translate-y-0.5
                    dark:bg-gray-800 dark:border-gray-700">

            <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500"></div>

            <div class="flex items-center justify-center flex-shrink-0
                        w-12 h-12 text-amber-600 bg-amber-50 rounded-xl
                        dark:bg-amber-900/20 dark:text-amber-300">

                <svg class="w-6 h-6"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M14.7 6.3a4 4 0 01-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 005.4-5.4l-2.2 2.2-2.8-2.8 2-2.4z"/>
                </svg>

            </div>

            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                    Maintenance Team
                </p>

                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{ $personnel ?? 0 }}
                    </h3>

                    <span class="text-[10px] font-semibold text-amber-600 uppercase tracking-wide">
                        Staff
                    </span>
                </div>

                <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                    Assigned personnel
                </p>
            </div>
        </div>


        <!-- USERS -->
        <div class="relative flex items-center gap-4 p-4 overflow-hidden
                    bg-white border border-gray-200 rounded-xl
                    shadow-sm transition-all duration-200
                    hover:shadow-md hover:-translate-y-0.5
                    dark:bg-gray-800 dark:border-gray-700">

            <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-400"></div>

            <div class="flex items-center justify-center flex-shrink-0
                        w-12 h-12 text-gray-600 bg-gray-100 rounded-xl
                        dark:bg-gray-700 dark:text-gray-300">

                <svg class="w-6 h-6"
                     fill="currentColor"
                     viewBox="0 0 20 20">

                    <path d="M10 2a4 4 0 100 8 4 4 0 000-8zM4 16a6 6 0 1112 0v1H4v-1z"/>
                </svg>

            </div>

            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                    Registered Users
                </p>

                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{ $user ?? 0 }}
                    </h3>

                    <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">
                        Users
                    </span>
                </div>

                <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                    Standard accounts
                </p>
            </div>
        </div>

    </div>


    <!-- CCA INSTITUTIONAL BRANDING PANEL -->
    <div class="relative flex flex-col items-center justify-center
                gap-4 px-6 py-8 mb-6 overflow-hidden
                bg-white border border-gray-200 rounded-xl shadow-sm
                sm:flex-row sm:gap-6
                dark:bg-gray-800 dark:border-gray-700">

        <!-- Decorative accent -->
        <div class="absolute top-0 left-0 w-full h-1"
             style="background-color: #52693A;">
        </div>

        <!-- Solid CCA Logo -->
        <img
            src="{{ asset('img/logo.png') }}"
            alt="City College of Angeles Logo"
            class="object-contain flex-shrink-0 w-24 h-24 sm:w-28 sm:h-28"
        >

        <!-- Institutional Text -->
        <div class="text-center sm:text-left">
            <p class="text-xs font-semibold tracking-[0.18em] uppercase"
               style="color: #52693A;">
                City College of Angeles
            </p>

            <h3 class="mt-1 text-xl font-bold text-gray-800 dark:text-white">
                CDSO Maintenance Reporting System
            </h3>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Administrator Portal
            </p>
        </div>

    </div>

</div>
