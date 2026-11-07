<header
    class="z-10 py-4 shadow-lg"
    style="background-color: rgba(60, 80, 42, 1);">

    <div class="cdso-header-inner container flex min-h-12 items-center justify-between gap-3 px-6 mx-auto text-white">

        <!-- Left: Mobile menu + welcome -->
        <div class="flex min-w-0 items-center gap-3 leading-tight">
            @auth
                <button type="button"
                    class="cdso-mobile-menu-button inline-flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-white/10 text-white transition hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/70 md:hidden"
                    @click="mobileSidebarOpen = !mobileSidebarOpen"
                    :aria-expanded="mobileSidebarOpen.toString()"
                    aria-controls="cdso-role-sidebar"
                    aria-label="Toggle navigation menu">
                    <svg x-show="!mobileSidebarOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileSidebarOpen" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            @endauth

            @auth
                <span class="text-sm opacity-80">
                    Welcome back,
                </span>

                <span class="font-semibold text-base">
                    {{ auth()->user()->fname }}
                </span>
            @else
                <span class="text-sm">
                    Welcome, Guest
                </span>
            @endauth

        </div>

        <!-- Right: User Info -->
        <ul class="cdso-header-actions flex flex-shrink-0 items-center space-x-6">
      
<!-- Notifications -->
      @auth
      <li class="relative" x-data="{ open: false }">
        <button
          type="button"
          class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 transition focus:outline-none"
          id="notification-toggle"
          @click="open = !open"
          @keydown.escape.window="open = false"
          aria-label="Notifications"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
              d="M15 17H9m10-2H5l1.5-2V9a3.5 3.5 0 017-0v4L15 15h4zM10 20h4" />
          </svg>

          @if(auth()->user()->unreadNotifications()->count())
            <span id="notifications-unread-count" class="absolute -top-1 -right-1 min-w-[19px] h-[19px] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center ring-2 ring-[#3c502a]">
              {{ min(auth()->user()->unreadNotifications()->count(), 99) }}
            </span>
          @endif
        </button>

        <div
          x-show="open"
          x-transition
          @click.outside="open = false"
          class="absolute right-0 mt-3 w-[360px] max-w-[calc(100vw-2rem)] bg-white text-gray-800 rounded-2xl shadow-2xl border border-gray-200 overflow-hidden z-50"
          style="display:none;"
        >
          <!-- <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <div>
              <p class="text-sm font-semibold">Notifications</p>
              <p id="notifications-unread-label" class="text-[11px] text-gray-500">{{ auth()->user()->unreadNotifications()->count() }} unread</p>
            </div>
            @if(auth()->user()->unreadNotifications()->count())
              <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                  Mark all read
                </button>
              </form>
            @endif
          </div> -->

          <div class="max-h-[380px] overflow-y-auto divide-y divide-gray-100">
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
                 class="block px-4 py-3 hover:bg-gray-50 transition {{ $notification->read_at ? '' : 'bg-indigo-50/50' }}">
                <div class="flex gap-3">
                  <span class="flex-shrink-0 w-8 h-8 rounded-lg {{ $typeClass }} flex items-center justify-center text-xs font-bold">
                    {{ strtoupper(substr($data['type'] ?? 'N', 0, 1)) }}
                  </span>
                  <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                      <p class="text-xs font-semibold truncate">{{ $data['title'] ?? 'Notification' }}</p>
                      @if(!$notification->read_at)
                        <span data-notification-unread-dot class="w-2 h-2 mt-1 rounded-full bg-indigo-600 flex-shrink-0"></span>
                      @endif
                    </div>
                    <p class="mt-0.5 text-xs text-gray-600 line-clamp-2">{{ $data['message'] ?? '' }}</p>
                    <p class="mt-1 text-[10px] text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                  </div>
                </div>
              </a>
            @empty
              <div class="px-4 py-10 text-center">
                <p class="text-sm font-medium text-gray-500">You're all caught up.</p>
                <p class="mt-1 text-xs text-gray-400">New request and maintenance updates will appear here.</p>
              </div>
            @endforelse
          </div>
        </div>
      </li>
      @endauth

      <!-- Email / Profile Info -->
      <li class="hidden sm:block text-right">
        @auth
          <div class="text-xs opacity-80">Signed in as</div>
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

  @auth
  <script>
  document.addEventListener('DOMContentLoaded', function () {
      const toggle = document.getElementById('notification-toggle');
      const unreadBadge = document.getElementById('notifications-unread-count');

      if (!toggle || !unreadBadge) return;

      toggle.addEventListener('click', function () {
          // Opening the notification panel marks all currently unread notifications as read.
          fetch(@json(route('notifications.readAll')), {
              method: 'POST',
              headers: {
                  'X-CSRF-TOKEN': @json(csrf_token()),
                  'X-Requested-With': 'XMLHttpRequest',
                  'Accept': 'application/json'
              }
          }).then(function (response) {
              if (!response.ok) throw new Error('Could not mark notifications as read.');

              unreadBadge.remove();

              const unreadLabel = document.getElementById('notifications-unread-label');
              if (unreadLabel) unreadLabel.textContent = '0 unread';

              document.querySelectorAll('[data-notification-row]').forEach(function (row) {
                  row.classList.remove('bg-indigo-50/50');
              });

              document.querySelectorAll('[data-notification-unread-dot]').forEach(function (dot) {
                  dot.remove();
              });
          }).catch(function (error) {
              console.error(error);
          });
      });
  });
  </script>
  @endauth
</header>