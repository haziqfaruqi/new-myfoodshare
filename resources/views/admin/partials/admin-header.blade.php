@php
    $currentPage = request()->path();

    // Generate breadcrumbs based on current page
    $breadcrumbs = [];
    if (strpos($currentPage, 'user-approvals') !== false) {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Approvals', 'url' => route('admin.user-approvals')],
            ['name' => 'User Approvals', 'url' => null]
        ];
    } elseif (strpos($currentPage, 'food-listings') !== false) {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Approvals', 'url' => route('admin.food-listings')],
            ['name' => 'Listing Approvals', 'url' => null]
        ];
    } elseif (strpos($currentPage, 'user-management') !== false) {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Users', 'url' => route('admin.user-management')],
            ['name' => 'User Management', 'url' => null]
        ];
    } elseif (strpos($currentPage, 'active-listings') !== false) {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Listings', 'url' => route('admin.active-listings')],
            ['name' => 'Active Listings', 'url' => null]
        ];
    } elseif (strpos($currentPage, 'pickup-monitoring') !== false) {
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Operations', 'url' => route('admin.pickup-monitoring')],
            ['name' => 'Pickup Verification', 'url' => null]
        ];
    } else {
        // Default dashboard breadcrumb
        $breadcrumbs = [
            ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['name' => 'Overview', 'url' => null]
        ];
    }
@endphp

<!-- Admin Header -->
<header class="bg-white border-b border-zinc-200 h-16 flex items-center px-6">
    <div class="flex items-center justify-between w-full">
        <div class="flex items-center gap-4">
            <button class="md:hidden p-2 rounded-lg hover:bg-zinc-100" onclick="toggleSidebar()">
                <i data-lucide="menu" class="w-5 h-5 text-zinc-600"></i>
            </button>
            <div class="hidden md:flex items-center text-sm breadcrumbs text-zinc-500">
                @foreach($breadcrumbs as $index => $breadcrumb)
                    @if($breadcrumb['url'])
                        <a href="{{ $breadcrumb['url'] }}" class="hover:text-zinc-900 transition-colors">{{ $breadcrumb['name'] }}</a>
                    @else
                        <span class="text-zinc-900 font-medium">{{ $breadcrumb['name'] }}</span>
                    @endif
                    @if($index < count($breadcrumbs) - 1)
                        <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-zinc-300"></i>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="relative hidden sm:block">
                <i data-lucide="search" class="w-4 h-4 text-zinc-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" placeholder="Search..." class="pl-9 pr-4 py-1.5 text-sm bg-zinc-100 border-none rounded-md focus:ring-2 focus:ring-emerald-500/20 focus:bg-white transition-all w-64 placeholder:text-zinc-400">
            </div>
            <button class="relative text-zinc-500 hover:text-zinc-900" title="Notifications">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <span class="absolute top-0 right-0 w-2 h-2 bg-rose-500 rounded-full border-2 border-white"></span>
            </button>
            <form action="{{ route('logout') }}" method="POST" class="ml-2">
                @csrf
                    <button type="submit" class="text-zinc-500 hover:text-red-600 transition-colors" title="Logout">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>
</header>