<!-- Restaurant Header -->
<header class="h-16 border-b border-zinc-200 bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-6">
    <button onclick="toggleSidebar()" class="md:hidden flex items-center gap-2 text-zinc-600 hover:text-zinc-900 transition-colors">
        <i data-lucide="menu" class="w-5 h-5"></i>
        <span class="font-semibold text-zinc-900">MyFoodshare</span>
    </button>

    <div class="hidden md:flex items-center text-sm breadcrumbs text-zinc-500">
        <span>Partner Portal</span>
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
        <button class="relative text-zinc-500 hover:text-zinc-900 transition-colors">
            <i data-lucide="bell" class="w-5 h-5"></i>
            <span class="absolute top-0 right-0 w-2 h-2 bg-rose-500 rounded-full border-2 border-white"></span>
        </button>
        <form action="{{ route('logout') }}" method="POST" class="ml-2">
            @csrf
            <button type="submit" class="text-zinc-500 hover:text-red-600 transition-colors" title="Logout">
                <i data-lucide="log-out" class="w-5 h-5"></i>
            </button>
        </form>
    </div>
</header>