<!-- Header -->
<header class="h-16 border-b border-zinc-200 bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-6">
    <div class="md:hidden flex items-center gap-2">
        <i data-lucide="menu" class="w-5 h-5 text-zinc-600"></i>
        <span class="font-semibold text-zinc-900">MyFoodshare</span>
    </div>

    <div class="hidden md:flex items-center text-sm breadcrumbs text-zinc-500">
        <span>NGO Portal</span>
        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-zinc-300"></i>
        <span class="text-zinc-900 font-medium">@yield('title', 'Dashboard')</span>
    </div>

    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full text-xs font-medium border border-blue-100">
            <i data-lucide="truck" class="w-3 h-3"></i>
            Vehicle Active
        </div>
        <button class="relative text-zinc-500 hover:text-zinc-900 transition-colors">
            <i data-lucide="bell" class="w-5 h-5"></i>
            <span class="absolute top-0 right-0 w-2 h-2 bg-rose-500 rounded-full border-2 border-white"></span>
        </button>
        <a href="{{ route('logout') }}"
           class="px-3 py-1.5 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i data-lucide="log-out" class="w-4 h-4 inline mr-1"></i>
            Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</header>