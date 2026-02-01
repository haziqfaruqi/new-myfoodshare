<!-- Restaurant Header -->
<header class="h-16 border-b border-zinc-200 bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-6">
    <button onclick="toggleSidebar()" class="md:hidden flex items-center gap-2 text-zinc-600 hover:text-zinc-900 transition-colors">
        <i data-lucide="menu" class="w-5 h-5"></i>
        <span class="font-semibold text-zinc-900">MyFoodshare</span>
    </button>

    <div class="hidden md:flex items-center text-sm breadcrumbs text-zinc-500">
        <span>Restaurant Portal</span>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-zinc-300"></i>
        <span class="text-zinc-900 font-medium">
            @if(request()->is('restaurant/dashboard'))
                Dashboard Overview
            @elseif(request()->is('restaurant/listings*'))
                Manage Listings
            @elseif(request()->is('restaurant/requests*'))
                Manage Requests
            @elseif(request()->is('restaurant/schedule*'))
                Manage Schedule
            @elseif(request()->is('restaurant/profile*'))
                Profile
            @else
                Dashboard
            @endif
        </span>
    </div>

    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2 bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-full text-xs font-medium border border-emerald-100">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            Store Open
        </div>

        <!-- Notification Bell with Dropdown -->
        <div class="relative" id="notification-dropdown">
            <button onclick="toggleNotificationDropdown()" class="relative text-zinc-500 hover:text-zinc-900 transition-colors">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <span id="pending-count" class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 rounded-full border-2 border-white text-[10px] font-bold text-white flex items-center justify-center">0</span>
            </button>

            <!-- Notification Dropdown Menu -->
            <div id="notification-menu" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-zinc-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-zinc-100 bg-zinc-50">
                    <h3 class="text-sm font-semibold text-zinc-900">Notifications</h3>
                </div>
                <div id="notification-list" class="max-h-64 overflow-y-auto">
                    <div class="px-4 py-6 text-center text-sm text-zinc-500">
                        <i data-lucide="bell-off" class="w-8 h-8 mx-auto text-zinc-300 mb-2"></i>
                        <p>No notifications yet</p>
                    </div>
                </div>
                <div class="px-4 py-2 border-t border-zinc-100 bg-zinc-50 flex items-center justify-between">
                    <a href="{{ route('restaurant.requests') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all requests</a>
                    <div class="flex items-center gap-2">
                        <button onclick="markAllAsRead()" class="text-xs text-zinc-500 hover:text-zinc-700 font-medium">Mark all read</button>
                        <button onclick="clearAllNotifications()" class="text-xs text-red-500 hover:text-red-700 font-medium">Clear</button>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="ml-2">
            @csrf
            <button type="submit" class="text-zinc-500 hover:text-red-600 transition-colors" title="Logout">
                <i data-lucide="log-out" class="w-5 h-5"></i>
            </button>
        </form>
    </div>
</header>