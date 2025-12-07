<!-- Sidebar Navigation -->
<aside id="restaurant-sidebar" class="w-64 border-r border-zinc-200 bg-white fixed md:static inset-y-0 left-0 transform z-30 transition-transform duration-300 ease-in-out md:transform-none -translate-x-full md:translate-x-0">
    <div class="h-16 flex items-center px-6 border-b border-zinc-100">
        <div class="flex items-center gap-2 text-emerald-600">
            <i data-lucide="leaf" class="w-6 h-6 fill-current"></i>
            <span class="font-bold tracking-tight text-zinc-900 text-lg">MyFoodshare</span>
            <span class="text-[10px] bg-zinc-100 text-zinc-500 px-1.5 py-0.5 rounded ml-1">PARTNER</span>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto py-6 px-3 space-y-8">
        <!-- Main Navigation -->
        <div>
            <h3 class="px-3 text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Main</h3>
            <nav class="space-y-1">
                <a href="{{ route('restaurant.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors
                   {{ request()->is('restaurant/dashboard') || request()->is('restaurant*') && !request()->is('restaurant/listings*') && !request()->is('restaurant/requests*') && !request()->is('restaurant/schedule*') && !request()->is('restaurant/profile*') ? 'text-zinc-900 bg-zinc-100' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 {{ request()->is('restaurant/dashboard') || request()->is('restaurant*') && !request()->is('restaurant/listings*') && !request()->is('restaurant/requests*') && !request()->is('restaurant/schedule*') && !request()->is('restaurant/profile*') ? 'text-zinc-500' : 'text-zinc-400' }}"></i>
                    Dashboard
                </a>
                <a href="{{ route('restaurant.listings') }}"
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors
                   {{ request()->is('restaurant/listings*') ? 'text-zinc-900 bg-zinc-100' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    <i data-lucide="package" class="w-4 h-4 {{ request()->is('restaurant/listings*') ? 'text-zinc-500' : 'text-zinc-400' }}"></i>
                    Manage Listings
                </a>
                <a href="{{ route('restaurant.requests') }}"
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors
                   {{ request()->is('restaurant/requests*') ? 'text-zinc-900 bg-zinc-100' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    <i data-lucide="clipboard-list" class="w-4 h-4 {{ request()->is('restaurant/requests*') ? 'text-zinc-500' : 'text-zinc-400' }}"></i>
                    Manage Requests
                </a>
                <a href="{{ route('restaurant.schedule') }}"
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors
                   {{ request()->is('restaurant/schedule*') ? 'text-zinc-900 bg-zinc-100' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    <i data-lucide="calendar" class="w-4 h-4 {{ request()->is('restaurant/schedule*') ? 'text-zinc-500' : 'text-zinc-400' }}"></i>
                    Manage Schedule
                </a>
                <a href="{{ route('restaurant.profile') }}"
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors
                   {{ request()->is('restaurant/profile*') ? 'text-zinc-900 bg-zinc-100' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    <i data-lucide="user" class="w-4 h-4 {{ request()->is('restaurant/profile*') ? 'text-zinc-500' : 'text-zinc-400' }}"></i>
                    Profile
                </a>
            </nav>
        </div>
    </div>

    <!-- User Profile -->
    <div class="p-4 border-t border-zinc-100">
        <div class="flex items-center gap-3">
            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ auth()->user()->name }}" alt="User" class="w-9 h-9 rounded-full bg-emerald-50 border border-emerald-100">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-zinc-900 truncate">{{ auth()->user()->restaurantProfile->restaurant_name ?? auth()->user()->name }}</p>
                <div class="flex items-center text-xs text-yellow-500">
                    <i data-lucide="star" class="w-3 h-3 fill-current mr-1"></i>
                    <span class="font-medium text-zinc-700">4.9</span>
                    <span class="text-zinc-400 ml-1">(128)</span>
                </div>
            </div>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('restaurant-logout-form').submit();" class="text-zinc-400 hover:text-red-600 transition-colors">
                <i data-lucide="log-out" class="w-4 h-4"></i>
            </a>
            <form id="restaurant-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</aside>