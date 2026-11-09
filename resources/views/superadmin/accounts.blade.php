@extends('layouts.apps')

@section('content')

@php
    $roleCounts = [
        'user' => $users->filter(fn ($user) => strtolower($user->role->name ?? 'user') === 'user')->count(),
        'personnel' => $users->filter(fn ($user) => strtolower($user->role->name ?? '') === 'personnel')->count(),
        'admin' => $users->filter(fn ($user) => strtolower($user->role->name ?? '') === 'admin')->count(),
    ];

    $roleIndexes = [
        'user' => 0,
        'personnel' => 0,
        'admin' => 0,
    ];
@endphp

<div x-data="{
    tab: 'user',
    showDeleteModal: false,
    deleteUrl: '',
    page: 1,
    perPage: 5,
    roleCounts: @js($roleCounts),

    get totalPages() {
        return Math.max(1, Math.ceil(this.roleCounts[this.tab] / this.perPage));
    },

    changeTab(role) {
        this.tab = role;
        this.page = 1;
    }
}" class="w-full px-4 mt-6 mb-8">

    <!-- PAGE HEADER -->
    <div class="mb-6">

        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">

            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                    Administrator
                </p>

                <h1 class="mt-1 text-2xl font-semibold text-gray-800 dark:text-gray-100">
                    Accounts Management
                </h1>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Manage system accounts, roles, and user access.
                </p>
            </div>

            <!-- ACCOUNT COUNT -->
            <div class="flex items-center gap-2 px-3 py-2
                        bg-white border border-gray-200 rounded-lg shadow-sm
                        dark:bg-gray-800 dark:border-gray-700">

                <div class="flex items-center justify-center w-8 h-8 rounded-full"
                     style="background-color: rgba(82, 105, 58, 0.12);">

                    <svg class="w-4 h-4"
                         style="color: rgba(82, 105, 58, 1);"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 100-8 4 4 0 000 8zm4 2a4 4 0 10-8 0">
                        </path>

                    </svg>

                </div>

                <div>
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        Total Accounts
                    </p>

                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                        {{ $users->count() }}
                    </p>
                </div>

            </div>

        </div>

    </div>


    <!-- ROLE TABS -->
    <div class="mb-4 bg-white border border-gray-200 rounded-lg shadow-xs
                dark:bg-gray-800 dark:border-gray-700">

        <div class="flex items-center px-2 overflow-x-auto">

            @foreach(['user', 'personnel', 'admin'] as $role)

                <button
                    @click="changeTab('{{ $role }}')"
                    class="relative flex items-center gap-2 px-4 py-3
                           text-sm font-medium whitespace-nowrap
                           transition-colors duration-150
                           focus:outline-none"
                    :class="tab === '{{ $role }}'
                        ? 'text-gray-800 dark:text-white'
                        : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'">

                    <!-- ROLE DOT -->
                    <span
                        class="w-2 h-2 rounded-full transition-colors duration-150"
                        :class="tab === '{{ $role }}'
                            ? 'bg-green-600'
                            : 'bg-gray-300 dark:bg-gray-600'">
                    </span>

                    {{ ucfirst($role) }}

                    <!-- ACTIVE INDICATOR -->
                    <span
                        x-show="tab === '{{ $role }}'"
                        x-transition
                        class="absolute bottom-0 left-3 right-3 h-0.5 rounded-full"
                        style="background-color: rgba(82, 105, 58, 1);">
                    </span>

                </button>

            @endforeach

        </div>

    </div>


    <!-- TABLE CARD -->
    <div class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-sm
                dark:bg-gray-800 dark:border-gray-700">

        <!-- TABLE HEADER -->
        <div class="flex flex-col gap-3 px-5 py-4 border-b border-gray-100
                    sm:flex-row sm:items-center sm:justify-between
                    dark:border-gray-700">

            <div>
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                    System Accounts
                </h2>

                <p class="text-xs text-gray-400 dark:text-gray-500">
                    View and manage accounts by role.
                </p>
            </div>

            <div class="text-xs text-gray-400 dark:text-gray-500">
                Select a role above to filter accounts
            </div>

        </div>


        <!-- TABLE -->
        <div class="w-full overflow-x-auto">

            <table class="w-full whitespace-nowrap">

                <thead>

                    <tr class="text-xs font-semibold tracking-wide text-left
                               text-gray-500 uppercase
                               bg-gray-50 border-b border-gray-200
                               dark:bg-gray-800 dark:text-gray-400
                               dark:border-gray-700">

                        <th class="px-5 py-3">
                            User
                        </th>

                        <th class="px-5 py-3">
                            Role
                        </th>

                        <th class="px-5 py-3">
                            Last Updated
                        </th>

                        <th class="px-5 py-3 text-right">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">

                    @forelse($users as $user)
                        @php
                            $paginationRole = strtolower($user->role->name ?? 'user');
                            $rowIndex = $roleIndexes[$paginationRole] ?? 0;

                            if (array_key_exists($paginationRole, $roleIndexes)) {
                                $roleIndexes[$paginationRole]++;
                            }
                        @endphp

                        <tr
                            x-show="tab === '{{ $paginationRole }}' && Math.floor({{ $rowIndex }} / perPage) + 1 === page"
                            x-transition
                            class="text-gray-700 transition-colors duration-150
                                   hover:bg-gray-50
                                   dark:text-gray-200 dark:hover:bg-gray-700/40">

                            <!-- USER -->
                            <td class="px-5 py-4">

                                <div class="flex items-center">

                                    <!-- INITIAL -->
                                    <div class="flex items-center justify-center
                                                flex-shrink-0 w-10 h-10
                                                font-semibold text-sm
                                                rounded-full"
                                         style="background-color: rgba(82, 105, 58, 0.12);
                                                color: rgba(82, 105, 58, 1);">

                                        {{ strtoupper(substr($user->fname, 0, 1)) }}

                                    </div>


                                    <!-- DETAILS -->
                                    <div class="ml-3">

                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">

                                            {{ $user->fname }} {{ $user->lname }}

                                        </p>

                                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">

                                            {{ $user->email }}

                                        </p>

                                    </div>

                                </div>

                            </td>


                            <!-- ROLE -->
                            <td class="px-5 py-4">

                                @php
                                    $roleName = strtolower($user->role->name ?? 'user');

                                    $roleClasses = match($roleName) {
                                      
                                        'admin' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800',
                                        'personnel' => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-800',
                                        default => 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600',
                                    };
                                @endphp

                                <span class="inline-flex items-center !px-2 !py-0.5
             text-xs font-semibold capitalize
             border rounded-full
             {{ $roleClasses }}">

    {{ $roleName }}

</span>
                            </td>


                            <!-- DATE -->
                            <td class="px-5 py-4">

                                <div class="flex items-center gap-2">

                                    <svg class="w-4 h-4 text-gray-400"
                                         fill="none"
                                         stroke="currentColor"
                                         stroke-width="2"
                                         viewBox="0 0 24 24">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>

                                    </svg>

                                    <span class="text-sm text-gray-600 dark:text-gray-300">
                                        {{ $user->updated_at->format('M d, Y') }}
                                    </span>

                                </div>

                            </td>


                            <!-- ACTIONS -->
                            <td class="px-5 py-4">

                                <div class="flex items-center justify-end gap-2">

                                    <!-- UPDATE -->
                                    <a href="{{ route('edit_account', $user->id) }}"
                                       class="inline-flex items-center gap-1.5
                                              px-3 py-1.5
                                              text-xs font-semibold
                                              text-gray-600
                                              bg-gray-50
                                              border border-gray-200
                                              rounded-md
                                              hover:bg-gray-100
                                              hover:text-gray-800
                                              transition-colors
                                              dark:bg-gray-700
                                              dark:border-gray-600
                                              dark:text-gray-200
                                              dark:hover:bg-gray-600">

                                        <svg class="w-3.5 h-3.5"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2"
                                             viewBox="0 0 24 24">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-7.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 7.5-7.5z">
                                            </path>

                                        </svg>

                                        Update

                                    </a>


                                    <!-- DELETE -->
                                    <button
                                        @click="
                                            showDeleteModal = true;
                                            deleteUrl = '{{ route('delete_account', $user->id) }}';
                                        "
                                        class="inline-flex items-center gap-1.5
                                               px-3 py-1.5
                                               text-xs font-semibold
                                               text-red-600
                                               bg-red-50
                                               border border-red-100
                                               rounded-md
                                               hover:bg-red-100
                                               transition-colors
                                               dark:bg-red-900/20
                                               dark:text-red-300
                                               dark:border-red-800
                                               dark:hover:bg-red-900/30">

                                        <svg class="w-3.5 h-3.5"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2"
                                             viewBox="0 0 24 24">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M6 7h12m-9 0V5a1 1 0 011-1h4a1 1 0 011 1v2m2 0v12a2 2 0 01-2 2H8a2 2 0 01-2-2V7h12z">
                                            </path>

                                        </svg>

                                        Archive

                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="px-5 py-12 text-center">

                                <div class="flex flex-col items-center">

                                    <div class="flex items-center justify-center
                                                w-12 h-12 mb-3
                                                text-gray-400
                                                bg-gray-100 rounded-full
                                                dark:bg-gray-700">

                                        <svg class="w-6 h-6"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2"
                                             viewBox="0 0 24 24">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m8-10a4 4 0 100-8 4 4 0 000 8zm8 10v-2a4 4 0 00-3-3.87m-1-10a4 4 0 010 7.75">
                                            </path>

                                        </svg>

                                    </div>

                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                        No accounts found
                                    </p>

                                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                        There are currently no accounts to display.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <!-- PAGINATION -->
        <div class="flex flex-col sm:flex-row items-center justify-between
                    gap-3 px-5 py-4 border-t border-gray-100
                    dark:border-gray-700">

            <p class="text-xs text-gray-500 dark:text-gray-400">
                Page <span class="font-semibold text-gray-700 dark:text-gray-200" x-text="page"></span>
                of <span class="font-semibold text-gray-700 dark:text-gray-200" x-text="totalPages"></span>
            </p>

            <div class="flex items-center gap-1">

                <!-- PREVIOUS PAGE -->
                <button
                    type="button"
                    @click="page = Math.max(1, page - 1)"
                    :disabled="page === 1"
                    aria-label="Previous page"
                    class="flex items-center justify-center w-8 h-8
                           text-gray-500 border border-gray-200 rounded-md
                           hover:bg-gray-100 disabled:opacity-40
                           disabled:cursor-not-allowed
                           dark:border-gray-600 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                         stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <!-- PAGE NUMBERS -->
                <template x-for="number in totalPages" :key="number">
                    <button
                        type="button"
                        @click="page = number"
                        x-text="number"
                        :aria-current="page === number ? 'page' : null"
                        :class="page === number
                            ? 'text-white border-transparent'
                            : 'text-gray-600 bg-white border-gray-200 hover:bg-gray-100 dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700'"
                        :style="page === number ? 'background-color: rgba(82, 105, 58, 1);' : ''"
                        class="flex items-center justify-center w-8 h-8
                               text-xs font-semibold border rounded-md transition-colors">
                    </button>
                </template>

                <!-- NEXT PAGE -->
                <button
                    type="button"
                    @click="page = Math.min(totalPages, page + 1)"
                    :disabled="page === totalPages"
                    aria-label="Next page"
                    class="flex items-center justify-center w-8 h-8
                           text-gray-500 border border-gray-200 rounded-md
                           hover:bg-gray-100 disabled:opacity-40
                           disabled:cursor-not-allowed
                           dark:border-gray-600 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                         stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

            </div>
        </div>

    </div>


    <!-- SUCCESS / ERROR MODAL -->
    @if(session('success') || session('error'))

        <div
            x-data="{ open: true }"
            x-show="open"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center
                   p-4 bg-black/50"
            style="display: none;">

            <div
                @click.away="open = false"
                class="w-full max-w-md overflow-hidden
                       bg-white rounded-xl shadow-2xl
                       dark:bg-gray-800">

                <!-- MODAL HEADER -->
                <div class="px-6 py-5 border-b border-gray-100
                            dark:border-gray-700">

                    <div class="flex items-center gap-3">

                        <div class="flex items-center justify-center
                                    w-10 h-10 rounded-full
                                    {{ session('success')
                                        ? 'bg-green-100 text-green-600'
                                        : 'bg-red-100 text-red-600' }}">

                            @if(session('success'))

                                <svg class="w-5 h-5"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="2"
                                     viewBox="0 0 24 24">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M5 13l4 4L19 7">
                                    </path>

                                </svg>

                            @else

                                <svg class="w-5 h-5"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="2"
                                     viewBox="0 0 24 24">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          d="M12 9v4m0 4h.01M10.29 3.86l-8.18 14A2 2 0 003.84 21h16.32a2 2 0 001.73-3.14l-8.18-14a2 2 0 00-3.42 0z">
                                    </path>

                                </svg>

                            @endif

                        </div>


                        <div>

                            <h2 class="text-base font-semibold text-gray-800 dark:text-white">

                                {{ session('success') ? 'Success' : 'Notice' }}

                            </h2>

                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                Account Management
                            </p>

                        </div>

                    </div>

                </div>


                <!-- MESSAGE -->
                <div class="px-6 py-5">

                    <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                        {{ session('success') ?? session('error') }}
                    </p>

                </div>


                <!-- FOOTER -->
                <div class="flex justify-end px-6 py-4 bg-gray-50
                            dark:bg-gray-700/40">

                    <button
                        @click="open = false"
                        class="px-4 py-2 text-sm font-semibold
                               text-white rounded-lg
                               transition-colors"
                        style="background-color: rgba(82, 105, 58, 1);"
                        onmouseover="this.style.backgroundColor='rgba(68, 88, 48, 1)'"
                        onmouseout="this.style.backgroundColor='rgba(82, 105, 58, 1)'">

                        OK

                    </button>

                </div>

            </div>

        </div>

    @endif


    <!-- DELETE CONFIRMATION MODAL -->
    <div
        x-show="showDeleteModal"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center
               p-4 bg-black/50"
        style="display: none;">

        <div
            @click.away="showDeleteModal = false"
            class="w-full max-w-md overflow-hidden
                   bg-white rounded-xl shadow-2xl
                   dark:bg-gray-800">

            <!-- MODAL HEADER -->
            <div class="px-6 py-5 border-b border-gray-100
                        dark:border-gray-700">

                <div class="flex items-center gap-3">

                    <div class="flex items-center justify-center
                                w-10 h-10 text-red-600
                                bg-red-100 rounded-full
                                dark:bg-red-900/30 dark:text-red-300">

                        <svg class="w-5 h-5"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M12 9v4m0 4h.01M10.29 3.86l-8.18 14A2 2 0 003.84 21h16.32a2 2 0 001.73-3.14l-8.18-14a2 2 0 00-3.42 0z">
                            </path>

                        </svg>

                    </div>

                    <div>

                        <h2 class="text-base font-semibold text-gray-800 dark:text-white">
                            Archive Account
                        </h2>

                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Permanent account removal
                        </p>

                    </div>

                </div>

            </div>


            <!-- MESSAGE -->
            <div class="px-6 py-5">

                <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-300">

                    Are you sure you want to archive this account?

                </p>

                <div class="p-3 mt-4 text-xs leading-relaxed
                            text-red-700 bg-red-50
                            border border-red-100 rounded-lg
                            dark:bg-red-900/20
                            dark:text-red-300
                            dark:border-red-800">

                    The account can be restored later from Archived Accounts. The account will be permanently removed from the system.

                </div>

            </div>


            <!-- ACTIONS -->
            <div class="flex justify-end gap-3 px-6 py-4
                        bg-gray-50 dark:bg-gray-700/40">

                <button
                    @click="showDeleteModal = false"
                    class="px-4 py-2 text-sm font-medium
                           text-gray-700 bg-white
                           border border-gray-200
                           rounded-lg
                           hover:bg-gray-50
                           transition-colors
                           dark:bg-gray-700
                           dark:text-gray-200
                           dark:border-gray-600
                           dark:hover:bg-gray-600">

                    Cancel

                </button>


                <form :action="deleteUrl" method="POST">

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2
                               px-4 py-2
                               text-sm font-semibold
                               text-white
                               bg-red-600
                               rounded-lg
                               hover:bg-red-700
                               transition-colors">

                        <svg class="w-4 h-4"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M6 7h12m-9 0V5a1 1 0 011-1h4a1 1 0 011 1v2m2 0v12a2 2 0 01-2 2H8a2 2 0 01-2-2V7h12z">
                            </path>

                        </svg>

                        Archive Account

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection