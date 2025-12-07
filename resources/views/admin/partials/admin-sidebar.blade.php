<!-- Sidebar Navigation -->
<aside id="admin-sidebar" class="w-64 border-r border-zinc-200 bg-white fixed md:static inset-y-0 left-0 transform z-30 transition-transform duration-300 ease-in-out md:transform-none -translate-x-full md:translate-x-0 flex flex-col">
    <div class="h-16 flex items-center px-6 border-b border-zinc-100">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 text-emerald-600 hover:text-emerald-700 transition-colors">
            <i data-lucide="leaf" class="w-5 h-5 fill-current"></i>
            <span class="font-semibold tracking-tight text-zinc-900">MyFoodshare</span>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto py-6 px-3 space-y-8">
        <!-- Dashboard -->
        <div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('admin/dashboard') || request()->is('admin') ? 'text-emerald-600 bg-emerald-50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }} rounded-md transition-colors">
                <i data-lucide="layout-dashboard" class="w-4 h-4 text-emerald-600"></i>
                Dashboard
            </a>
        </div>

        <!-- Approvals -->
        <div>
            <h3 class="px-3 text-xs font-medium text-zinc-400 uppercase tracking-wider mb-2">Approvals</h3>
            <nav class="space-y-0.5">
                <a href="{{ route('admin.user-approvals') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('admin/user-approvals*') ? 'text-emerald-600 bg-emerald-50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }} rounded-md transition-colors">
                    <i data-lucide="user-check" class="w-4 h-4 text-zinc-400"></i>
                    User Approvals
                    <span class="ml-auto bg-amber-100 text-amber-700 py-0.5 px-2 rounded-full text-[10px] font-medium">{{ App\Models\User::where('status', 'pending')->count() }}</span>
                </a>
                <a href="{{ route('admin.food-listings') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('admin/food-listings*') ? 'text-emerald-600 bg-emerald-50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }} rounded-md transition-colors">
                    <i data-lucide="package" class="w-4 h-4 text-zinc-400"></i>
                    Listing Approvals
                    <span class="ml-auto bg-amber-100 text-amber-700 py-0.5 px-2 rounded-full text-[10px] font-medium">{{ App\Models\FoodListing::where('approval_status', 'pending')->count() }}</span>
                </a>
            </nav>
        </div>

        <!-- Management -->
        <div>
            <h3 class="px-3 text-xs font-medium text-zinc-400 uppercase tracking-wider mb-2">Management</h3>
            <nav class="space-y-0.5">
                <a href="{{ route('admin.user-management') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('admin/user-management*') ? 'text-emerald-600 bg-emerald-50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }} rounded-md transition-colors">
                    <i data-lucide="users" class="w-4 h-4 text-zinc-400"></i>
                    User Management
                </a>
                <a href="{{ route('admin.active-listings') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('admin/active-listings*') ? 'text-emerald-600 bg-emerald-50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }} rounded-md transition-colors">
                    <i data-lucide="package-2" class="w-4 h-4 text-zinc-400"></i>
                    Active Listings
                </a>
            </nav>
        </div>

        <!-- Monitoring -->
        <div>
            <h3 class="px-3 text-xs font-medium text-zinc-400 uppercase tracking-wider mb-2">Monitoring</h3>
            <nav class="space-y-0.5">
                <a href="{{ route('admin.pickup-monitoring') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('admin/pickup-monitoring') && !request()->is('admin/pickup-monitoring/report*') ? 'text-emerald-600 bg-emerald-50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }} rounded-md transition-colors">
                    <i data-lucide="qr-code" class="w-4 h-4 text-zinc-400"></i>
                    Pickup Verification
                    <span class="ml-auto bg-emerald-100 text-emerald-700 py-0.5 px-2 rounded-full text-[10px] font-medium">
                        {{ App\Models\PickupVerification::where('verification_status', 'pending')->whereNull('qr_code_scanned')->count() }}
                    </span>
                </a>
                <a href="{{ route('admin.pickup-monitoring.report') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium {{ request()->is('admin/pickup-monitoring/report') ? 'text-emerald-600 bg-emerald-50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }} rounded-md transition-colors">
                    <i data-lucide="file-text" class="w-4 h-4 text-zinc-400"></i>
                    Pickup Report
                </a>
            </nav>
        </div>
    </div>

    <!-- User Profile Stub -->
    <div class="p-4 border-t border-zinc-100">
        <div class="flex items-center gap-3">
            <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ Auth::user()->name }}" alt="User" class="w-8 h-8 rounded-full bg-zinc-100 border border-zinc-200">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-zinc-900 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-zinc-500 truncate">{{ Auth::user()->role === 'admin' ? 'System Administrator' : Auth::user()->role }}</p>
            </div>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();" class="text-zinc-400 hover:text-zinc-600 transition-colors">
                <i data-lucide="log-out" class="w-4 h-4"></i>
            </a>
            <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</aside>