
<header
    class="relative z-20 py-4 shadow-lg"
    style="background-color: rgba(60, 80, 42, 1);">

    <div class="cdso-header-inner container flex min-h-12 items-center justify-between gap-3 px-4 sm:px-6 mx-auto text-white">

        <!-- Left: Mobile menu + welcome -->
        <div class="flex min-w-0 items-center gap-3 leading-tight">

            @auth
                <button type="button"
                    class="cdso-mobile-menu-button inline-flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-white/10 text-white transition hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/70 md:hidden"
                    @click="mobileSidebarOpen = !mobileSidebarOpen"
                    :aria-expanded="mobileSidebarOpen.toString()"
                    aria-controls="cdso-role-sidebar"
                    aria-label="Toggle navigation menu">

                    <svg x-show="!mobileSidebarOpen"
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>

                    <svg x-show="mobileSidebarOpen"
                        x-cloak
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 6l12 12M18 6L6 18"/>
                    </svg>
                </button>
            @endauth

            @auth
                <span class="text-sm opacity-80">
                    Welcome back,
                </span>

                <span class="font-semibold text-base truncate">
                    {{ auth()->user()->fname }}
                </span>
            @else
                <span class="text-sm">
                    Welcome, Guest
                </span>
            @endauth

        </div>

        <!-- Right: User Info -->
        <ul class="cdso-header-actions flex flex-shrink-0 items-center gap-3 sm:gap-6">

            <!-- Notifications: Hidden for Super Admin -->
            @auth
                @if(auth()->user()->role && auth()->user()->role->name !== 'superadmin')

                    <li class="relative" x-data="{ open: false }">

                        <!-- Notification Button -->
                        <button
                            type="button"
                            class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 transition focus:outline-none focus:ring-2 focus:ring-white/50"
                            id="notification-toggle"
                            @click="open = !open"
                            @keydown.escape.window="open = false"
                            :aria-expanded="open.toString()"
                            aria-label="Notifications">

                            <svg class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.7"
                                    d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>
                            </svg>

                            @if(auth()->user()->unreadNotifications()->count())
                                <span
                                    id="notifications-unread-count"
                                    class="absolute -top-1 -right-1 min-w-[19px] h-[19px] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center ring-2 ring-[#3c502a]">

                                    {{ min(auth()->user()->unreadNotifications()->count(), 99) }}
                                </span>
                            @endif

                        </button>

                        <!-- Notification Dropdown -->
                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            @click.outside="open = false"
                            @keydown.escape.window="open = false"

                            class="
                                fixed left-3 right-3 top-16
                                w-auto max-w-none
                                max-h-[calc(100dvh-5rem)]
                                bg-white text-gray-800
                                rounded-2xl shadow-2xl
                                border border-gray-200
                                overflow-hidden z-[100]

                                md:absolute md:left-auto md:right-0
                                md:top-full md:mt-3
                                md:w-[360px]
                                md:max-h-none
                            "
                            style="display: none;"
                            role="dialog"
                            aria-label="Notifications">

                            <!-- Notification Header -->
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-white">

                                <div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        Notifications
                                    </p>

                                    <p id="notifications-unread-label"
                                        class="text-[11px] text-gray-500">
                                        {{ auth()->user()->unreadNotifications()->count() }} unread
                                    </p>
                                </div>

                                @if(auth()->user()->unreadNotifications()->count())
                                    <form method="POST"
                                        action="{{ route('notifications.readAll') }}">
                                        @csrf

                                        <button type="submit"
                                            class="text-xs font-medium text-[#607844] hover:text-[#3c502a] transition">
                                            Mark all read
                                        </button>
                                    </form>
                                @endif

                            </div>

                            <!-- Notification List -->
                            <div class="
                                max-h-[calc(100dvh-10rem)]
                                overflow-y-auto overscroll-contain
                                divide-y divide-gray-100
                                md:max-h-[380px]
                            ">

                                @forelse(auth()->user()->notifications()->latest()->limit(8)->get() as $notification)

                                    @php
                                        $data = $notification->data;

                                        $typeClass = match($data['type'] ?? 'info') {
                                            'inventory' => 'bg-amber-100 text-amber-700',
                                            'assignment' => 'bg-blue-100 text-blue-700',
                                            'request' => 'bg-indigo-100 text-indigo-700',
                                            default => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp

                                    <a href="{{ route('notifications.read', $notification->id) }}"
                                        data-notification-row
                                        class="block px-3 py-3 sm:px-4 hover:bg-gray-50 transition {{ $notification->read_at ? '' : 'bg-indigo-50/50' }}">

                                        <div class="flex items-start gap-3">

                                            <!-- Notification Type Icon -->
                                            <span class="flex-shrink-0 w-9 h-9 rounded-xl {{ $typeClass }} flex items-center justify-center text-xs font-bold">
                                                {{ strtoupper(substr($data['type'] ?? 'N', 0, 1)) }}
                                            </span>

                                            <!-- Notification Details -->
                                            <div class="min-w-0 flex-1">

                                                <div class="flex items-start justify-between gap-2">

                                                    <p class="text-xs font-semibold text-gray-800 break-words">
                                                        {{ $data['title'] ?? 'Notification' }}
                                                    </p>

                                                    @if(!$notification->read_at)
                                                        <span
                                                            data-notification-unread-dot
                                                            class="w-2 h-2 mt-1 rounded-full bg-indigo-600 flex-shrink-0">
                                                        </span>
                                                    @endif

                                                </div>

                                                <p class="mt-1 text-xs text-gray-600 leading-relaxed break-words">
                                                    {{ $data['message'] ?? '' }}
                                                </p>

                                                <p class="mt-1.5 text-[10px] text-gray-400">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>

                                            </div>

                                        </div>

                                    </a>

                                @empty

                                    <div class="px-4 py-10 text-center">

                                        <div class="flex justify-center mb-3">
                                            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.7"
                                                        d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <p class="text-sm font-medium text-gray-500">
                                            You're all caught up.
                                        </p>

                                        <p class="mt-1 text-xs text-gray-400">
                                            New request and maintenance updates will appear here.
                                        </p>

                                    </div>

                                @endforelse

                            </div>

                        </div>

                    </li>

                @endif
            @endauth

            <!-- Email / Profile Info -->
            <li class="hidden sm:block text-right">

                @auth
                    <div class="text-xs opacity-80">
                        Signed in as
                    </div>

                    <div class="text-sm font-medium">
                        {{ auth()->user()->email }}
                    </div>
                @endauth

            </li>

            <!-- Avatar Circle -->
            @auth
                <li>
                    <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center font-semibold text-sm shadow-inner">
                        {{ strtoupper(substr(auth()->user()->fname, 0, 1)) }}
                    </div>
                </li>
            @endauth

        </ul>

    </div>

    <!-- Notification Read Functionality -->
    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const toggle = document.getElementById('notification-toggle');

                if (!toggle) return;

                toggle.addEventListener('click', function () {

                    const unreadBadge = document.getElementById('notifications-unread-count');

                    if (!unreadBadge) return;

                    // Mark notifications as read when opening the panel.
                    // The current state is toggled by Alpine before this event runs.
                    const expanded = toggle.getAttribute('aria-expanded') === 'true';

                    if (!expanded) return;

                    fetch(@json(route('notifications.readAll')), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': @json(csrf_token()),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Could not mark notifications as read.');
                        }

                        unreadBadge.remove();

                        const unreadLabel = document.getElementById('notifications-unread-label');

                        if (unreadLabel) {
                            unreadLabel.textContent = '0 unread';
                        }

                        document.querySelectorAll('[data-notification-row]').forEach(function (row) {
                            row.classList.remove('bg-indigo-50/50');
                        });

                        document.querySelectorAll('[data-notification-unread-dot]').forEach(function (dot) {
                            dot.remove();
                        });
                    })
                    .catch(function (error) {
                        console.error(error);
                    });

                });

            });
        </script>
    @endauth

</header>
