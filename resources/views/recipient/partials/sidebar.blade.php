<!-- Sidebar Navigation -->
<aside id="recipient-sidebar" class="w-64 border-r border-zinc-200 bg-white fixed md:static inset-y-0 left-0 transform z-30 transition-transform duration-300 ease-in-out md:transform-none -translate-x-full md:translate-x-0">
    <div class="h-16 flex items-center px-6 border-b border-zinc-100">
        <a href="{{ route('recipient.dashboard') }}" class="flex items-center gap-2 text-blue-600 hover:text-blue-700 transition-colors">
            <i data-lucide="heart-handshake" class="w-6 h-6 fill-current"></i>
            <span class="font-bold tracking-tight text-zinc-900 text-lg">MyFoodshare</span>
            <span class="text-[10px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded ml-1 font-medium">NGO</span>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto py-6 px-3 space-y-8">
        <!-- Main Nav -->
        <div>
            <h3 class="px-3 text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Collection</h3>
            <nav class="space-y-1">
                <a href="{{ route('recipient.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('recipient/dashboard*') ? 'text-zinc-900 bg-zinc-100' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }} rounded-md transition-colors">
                    <i data-lucide="layout-grid" class="w-4 h-4 {{ request()->is('recipient/dashboard*') ? 'text-zinc-500' : 'text-zinc-400' }}"></i>
                    Dashboard
                </a>
                <a href="{{ route('recipient.available-food') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('recipient/available-food*') ? 'text-zinc-900 bg-zinc-100' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }} rounded-md transition-colors">
                    <i data-lucide="search" class="w-4 h-4 {{ request()->is('recipient/available-food*') ? 'text-zinc-500' : 'text-zinc-400' }}"></i>
                    Browse Food
                    <span class="ml-auto bg-emerald-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $availableFoodCount ?? 12 }} New</span>
                </a>
                <a href="{{ route('recipient.map-view') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('recipient/map-view*') ? 'text-zinc-900 bg-zinc-100' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }} rounded-md transition-colors">
                    <i data-lucide="map" class="w-4 h-4 {{ request()->is('recipient/map-view*') ? 'text-zinc-500' : 'text-zinc-400' }}"></i>
                    Map View
                </a>
                <a href="{{ route('recipient.my-matches') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('recipient/my-matches*') ? 'text-zinc-900 bg-zinc-100' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }} rounded-md transition-colors">
                    <i data-lucide="clock" class="w-4 h-4 {{ request()->is('recipient/my-matches*') ? 'text-zinc-500' : 'text-zinc-400' }}"></i>
                    My Matches
                </a>
            </nav>
        </div>

        <!-- Impact & Settings -->
        <div>
            <h3 class="px-3 text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Organization</h3>
            <nav class="space-y-1">
                <a href="{{ route('recipient.impact-report') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('recipient/impact-report*') ? 'text-zinc-900 bg-zinc-100' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }} rounded-md transition-colors">
                    <i data-lucide="pie-chart" class="w-4 h-4 {{ request()->is('recipient/impact-report*') ? 'text-zinc-500' : 'text-zinc-400' }}"></i>
                    Impact Report
                </a>
                <a href="{{ route('recipient.ngo-profile') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('recipient/ngo-profile*') ? 'text-zinc-900 bg-zinc-100' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }} rounded-md transition-colors">
                    <i data-lucide="building-2" class="w-4 h-4 {{ request()->is('recipient/ngo-profile*') ? 'text-zinc-500' : 'text-zinc-400' }}"></i>
                    NGO Profile
                </a>
            </nav>
        </div>
    </div>

    <!-- User Profile -->
    <div class="p-4 border-t border-zinc-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold border border-blue-200">
                CS
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-zinc-900 truncate">City Shelter</p>
                <div class="flex items-center text-xs text-zinc-500">
                    <span class="truncate">Sarah (Coordinator)</span>
                </div>
            </div>
            <a href="{{ route('logout') }}" method="POST" class="text-zinc-400 hover:text-zinc-600">
                <i data-lucide="log-out" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</aside>