
<aside id="cdso-role-sidebar" x-data="{ openLogoutModal: false }"
    class="cdso-sidebar fixed inset-y-0 left-0 z-40 flex w-[min(18rem,88vw)] flex-shrink-0 flex-col overflow-y-auto shadow-2xl transition-transform duration-200 ease-out md:sticky md:top-0 md:h-screen md:w-64 md:translate-x-0 md:shadow-none"
    :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    style="background-color: rgba(82, 105, 58, 1);">

    <div class="flex min-h-full flex-col py-4 text-white">

        <!-- ================================= -->
        <!-- LOGO / BRAND HEADER -->
        <!-- ================================= -->

        <a href="{{ route('userDashboard') }}"
            class="flex items-center gap-3 px-5 py-4 transition-opacity hover:opacity-90">

<div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-white p-1">
    <img
        src="{{ asset('img/logo.png') }}"
        alt="CDSO Logo"
        class="h-full w-full object-contain">
</div>

            <div class="min-w-0">
                <h1 class="text-xl font-bold tracking-wide text-white">
                    CDSO
                </h1>

                <p class="mt-0.5 text-xs leading-relaxed text-white/70">
                    Maintenance Reporting
                </p>

                <span class="mt-1 inline-block text-[10px] font-semibold uppercase tracking-wider text-white/50">
                    User Portal
                </span>
            </div>

        </a>

        <!-- HEADER DIVIDER -->
        <div class="mx-5 border-t border-white/20"></div>


        <!-- ================================= -->
        <!-- MAIN NAVIGATION -->
        <!-- ================================= -->

        <div class="px-6 pb-2 pt-5">
            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-white/50">
                Main Menu
            </p>
        </div>

        <ul class="space-y-1">

            <!-- DASHBOARD -->
            <li class="relative px-4 py-1">

                @if(request()->routeIs('userDashboard'))
                    <span class="absolute inset-y-0 left-0 w-1 rounded-r-lg bg-white"></span>
                @endif

                <a href="{{ route('userDashboard') }}"
                    class="inline-flex min-h-11 w-full items-center rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
                    {{ request()->routeIs('userDashboard')
                        ? 'bg-white/15 text-white'
                        : 'text-white/75 hover:bg-white/10 hover:text-white' }}">

                    <svg class="h-5 w-5 shrink-0" fill="none"
                        stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24"
                        stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <path d="m3 10 9-7 9 7"/>
                        <path d="M5 9v11h14V9"/>
                        <path d="M9 20v-6h6v6"/>
                    </svg>

                    <span class="ml-3">
                        Dashboard
                    </span>

                </a>
            </li>


            <!-- REQUEST REPORT -->
            <li class="relative px-4 py-1">

                @if(request()->routeIs('showForm'))
                    <span class="absolute inset-y-0 left-0 w-1 rounded-r-lg bg-white"></span>
                @endif

                <a href="{{ route('showForm') }}"
                    class="inline-flex min-h-11 w-full items-center rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
                    {{ request()->routeIs('showForm')
                        ? 'bg-white/15 text-white'
                        : 'text-white/75 hover:bg-white/10 hover:text-white' }}">

                    <svg class="h-5 w-5 shrink-0" fill="none"
                        stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24"
                        stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <path d="M14 2v6h6"/>
                        <path d="M12 11v6"/>
                        <path d="M9 14h6"/>
                    </svg>

                    <span class="ml-3">
                        Request Report
                    </span>

                </a>
            </li>


            <!-- TRACK REQUEST -->
            <li class="relative px-4 py-1">

                @if(request()->routeIs('trackReq') || request()->routeIs('viewRequest.user'))
                    <span class="absolute inset-y-0 left-0 w-1 rounded-r-lg bg-white"></span>
                @endif

                <a href="{{ route('trackReq') }}"
                    class="inline-flex min-h-11 w-full items-center rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-150
                    {{ request()->routeIs('trackReq') || request()->routeIs('viewRequest.user')
                        ? 'bg-white/15 text-white'
                        : 'text-white/75 hover:bg-white/10 hover:text-white' }}">

                    <svg class="h-5 w-5 shrink-0" fill="none"
                        stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24"
                        stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <rect x="5" y="4" width="14" height="18" rx="2"/>
                        <path d="M9 4.5V3h6v1.5"/>
                        <path d="M9 10h6"/>
                        <path d="M9 14h6"/>
                        <path d="M9 18h3"/>
                    </svg>

                    <span class="ml-3">
                        Track Request Report
                    </span>

                </a>
            </li>

        </ul>


        <!-- ================================= -->
        <!-- SPACER -->
        <!-- ================================= -->

        <div class="flex-1"></div>


        <!-- ================================= -->
        <!-- LOGOUT SECTION -->
        <!-- ================================= -->

        <div class="mx-5 mb-3 border-t border-white/20"></div>

        <div class="px-4 pb-3">

            <button
                type="button"
                @click="openLogoutModal = true"
                class="inline-flex min-h-11 w-full items-center rounded-lg px-3 py-2.5 text-sm font-semibold text-white/80 transition-colors duration-150 hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/70">

                <svg class="h-5 w-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    viewBox="0 0 24 24"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true">
                    <path d="M10 17l5-5-5-5"/>
                    <path d="M15 12H3"/>
                    <path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/>
                </svg>

                <span class="ml-3">
                    Logout
                </span>

            </button>

        </div>


        <!-- ================================= -->
        <!-- SIDEBAR FOOTER -->
        <!-- ================================= -->

        <div class="px-5 pb-4 text-center">
            <p class="text-[10px] font-medium tracking-wide text-white/60">
                CDSO Maintenance Reporting System
            </p>
            <p class="mt-1 text-[10px] text-white/40">
                User Portal
            </p>
        </div>


        <!-- ================================= -->
        <!-- LOGOUT CONFIRMATION MODAL -->
        <!-- ================================= -->

        <template x-teleport="body">
            <div
                x-show="openLogoutModal"
                x-transition
                x-cloak
                @keydown.escape.window="openLogoutModal = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

                <div
                    @click.away="openLogoutModal = false"
                    class="w-full max-w-md rounded-xl bg-white px-6 py-5 shadow-xl dark:bg-gray-800">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            <svg class="h-5 w-5" fill="none"
                                stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 17l5-5-5-5"/>
                                <path d="M15 12H3"/>
                                <path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/>
                            </svg>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            Confirm Logout
                        </h3>

                    </div>

                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                        Are you sure you want to logout?
                    </p>

                    <div class="mt-6 flex justify-end gap-3">

                        <!-- CANCEL -->
                        <button
                            type="button"
                            @click="openLogoutModal = false"
                            class="rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>

                        <!-- LOGOUT -->
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf

                            <button
                                type="submit"
                                class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition bg-red-700">
                                Logout
                            </button>
                        </form>

                    </div>

                </div>
            </div>
        </template>

    </div>

</aside>
