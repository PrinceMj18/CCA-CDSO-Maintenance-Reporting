
<aside id="cdso-role-sidebar" x-data="{ openLogoutModal: false }"
    class="cdso-sidebar fixed inset-y-0 left-0 z-40 flex w-[min(18rem,88vw)] flex-shrink-0 flex-col overflow-y-auto shadow-2xl transition-transform duration-200 ease-out md:sticky md:top-0 md:h-screen md:w-64 md:translate-x-0 md:shadow-none"
    :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    style="background-color: rgba(82, 105, 58, 1);">

    <div class="flex min-h-full flex-col py-5 text-white">

        <!-- LOGO / BRAND -->
        <a href="{{ route('superadminDashboard') }}"
            class="mx-5 flex items-center gap-3 rounded-xl px-2 py-2 transition-colors hover:bg-white/10">

<div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-white p-1">
    <img
        src="{{ asset('img/logo.png') }}"
        alt="CDSO Logo"
        class="h-full w-full object-contain">
</div>

            <div class="min-w-0">
                <h2 class="text-lg font-bold leading-tight tracking-wide">
                    CDSO
                </h2>
                <p class="text-xs leading-5 text-white/75">
                    Maintenance Reporting
                </p>
                <p class="mt-1 text-[10px] font-semibold uppercase tracking-widest text-white/60">
                    Administrator Portal
                </p>
            </div>
        </a>

        <div class="mx-5 my-5 border-t border-white/20"></div>

        <!-- MAIN MENU LABEL -->
        <div class="px-6">
            <p class="mb-3 text-[10px] font-bold uppercase tracking-[0.15em] text-white/50">
                Main Menu
            </p>
        </div>

        <nav class="flex-1">
            <ul class="space-y-1">

                <!-- DASHBOARD -->
                <li class="relative px-4">
                    @if(request()->routeIs('superadminDashboard'))
                        <span class="absolute inset-y-1 left-0 w-1 rounded-r-full bg-white"></span>
                    @endif

                    <a href="{{ route('superadminDashboard') }}"
                        class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition-colors duration-150
                        {{ request()->routeIs('superadminDashboard')
                            ? 'bg-white/15 text-white'
                            : 'text-white/75 hover:bg-white/10 hover:text-white' }}">

                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor"
                            stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m3 10 9-7 9 7M5 9v11h14V9M9 20v-6h6v6" />
                        </svg>

                        <span class="ml-4">Dashboard</span>
                    </a>
                </li>

                <!-- ACCOUNTS MANAGEMENT -->
                <li class="relative px-4">
                    @if(
                        request()->routeIs('acc_superadmin') ||
                        request()->routeIs('edit_account')
                    )
                        <span class="absolute inset-y-1 left-0 w-1 rounded-r-full bg-white"></span>
                    @endif

                    <a href="{{ route('acc_superadmin') }}"
                        class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition-colors duration-150
                        {{
                            request()->routeIs('acc_superadmin') ||
                            request()->routeIs('edit_account')
                                ? 'bg-white/15 text-white'
                                : 'text-white/75 hover:bg-white/10 hover:text-white'
                        }}">

                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor"
                            stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm10-5v6m-3-3h6" />
                        </svg>

                        <span class="ml-4">Accounts Management</span>
                    </a>
                </li>

                <!-- ARCHIVED ACCOUNTS -->
                @php
                    $archiveActive = request()->routeIs('superadmin.archives.accounts*');
                @endphp

                <li class="relative px-4">
                    @if($archiveActive)
                        <span class="absolute inset-y-1 left-0 w-1 rounded-r-full bg-white"></span>
                    @endif

                    <a href="{{ route('superadmin.archives.accounts') }}"
                        class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition-colors duration-150
                        {{ $archiveActive
                            ? 'bg-white/15 text-white'
                            : 'text-white/75 hover:bg-white/10 hover:text-white' }}">

                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor"
                            stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7h5l2 2h11v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 12v5m-2.5-2.5h5" />
                        </svg>

                        <span class="ml-4">Archived Accounts</span>
                    </a>
                </li>

                <!-- CREATE NEW ACCOUNT -->
                @php
                    $createAccountActive = request()->routeIs('superadmin.accounts.create');
                @endphp

                <li class="relative px-4">
                    @if($createAccountActive)
                        <span class="absolute inset-y-1 left-0 w-1 rounded-r-full bg-white"></span>
                    @endif

                    <a href="{{ route('superadmin.accounts.create') }}"
                        class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold transition-colors duration-150
                        {{ $createAccountActive
                            ? 'bg-white/15 text-white'
                            : 'text-white/75 hover:bg-white/10 hover:text-white' }}">

                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor"
                            stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm9-3v6m-3-3h6" />
                        </svg>

                        <span class="ml-4">Create New Account</span>
                    </a>
                </li>

            </ul>
        </nav>

        <!-- BOTTOM SECTION -->
        <div class="mt-auto px-4 pt-6">

            <div class="mb-4 border-t border-white/20"></div>

            <!-- LOGOUT -->
            <button
                type="button"
                @click="openLogoutModal = true"
                class="flex w-full items-center rounded-xl px-4 py-3 text-sm font-semibold text-white/75 transition-colors duration-150 hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/50">

                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor"
                    stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 16l4-4m0 0-4-4m4 4H7m6 4v1a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v1" />
                </svg>

                <span class="ml-4">Logout</span>
            </button>

            <!-- FOOTER -->
            <div class="mt-5 px-4 pb-2">
                <p class="text-[10px] font-medium leading-relaxed text-white/50">
                    CDSO Maintenance Reporting System
                </p>
                <p class="mt-1 text-[10px] text-white/40">
                    Super Admin Portal
                </p>
            </div>
        </div>
    </div>

    <!-- LOGOUT MODAL -->
    <template x-teleport="body">
        <div
            x-cloak
            x-show="openLogoutModal"
            x-transition.opacity
            @keydown.escape.window="openLogoutModal = false"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 px-4"
            style="display: none;">

            <div
                @click.away="openLogoutModal = false"
                x-transition
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800">

                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-red-50 text-red-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 16l4-4m0 0-4-4m4 4H7m6 4v1a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v1" />
                        </svg>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        Confirm Logout
                    </h3>
                </div>

                <p class="mt-4 text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                    Are you sure you want to logout?
                </p>

                <div class="mt-6 flex justify-end gap-3">

                    <!-- CANCEL -->
                    <button
                        type="button"
                        @click="openLogoutModal = false"
                        class="rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                        Cancel
                    </button>

                    <!-- LOGOUT -->
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf

                        <button
                            type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2">
                            Logout
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </template>

</aside>
