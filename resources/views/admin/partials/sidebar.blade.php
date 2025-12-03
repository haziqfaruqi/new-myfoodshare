<!-- Sidebar Navigation -->
<aside class="w-64 border-r border-zinc-200 bg-white hidden md:flex flex-col z-20">
    <div class="h-16 flex items-center px-6 border-b border-zinc-100">
        <div class="flex items-center gap-2 text-emerald-600">
            <i data-lucide="leaf" class="w-5 h-5 fill-current"></i>
            <span class="font-semibold tracking-tight text-zinc-900">FoodShare</span>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto py-6 px-3 space-y-8">
        <!-- Approvals -->
        <div>
            <h3 class="px-3 text-xs font-medium text-zinc-400 uppercase tracking-wider mb-2">Approvals</h3>
            <nav class="space-y-0.5">
                <a href="{{ route('admin.user-approvals') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                    <i data-lucide="user-check" class="w-4 h-4 text-zinc-400"></i>
                    User Approvals
                    <span class="ml-auto bg-amber-100 text-amber-700 py-0.5 px-2 rounded-full text-[10px] font-medium">{{ App\Models\User::where('status', 'pending')->count() }}</span>
                </a>
                <a href="{{ route('admin.food-listings') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                    <i data-lucide="package" class="w-4 h-4 text-zinc-400"></i>
                    Listing Approvals
                    <span class="ml-auto bg-amber-100 text-amber-700 py-0.5 px-2 rounded-full text-[10px] font-medium">
                        {{ App\Models\FoodListing::where('approval_status', 'pending')->count() }}
                    </span>
                </a>
            </nav>
        </div>

        <!-- Management -->
        <div>
            <h3 class="px-3 text-xs font-medium text-zinc-400 uppercase tracking-wider mb-2">Management</h3>
            <nav class="space-y-0.5">
                <a href="{{ route('admin.user-management') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                    <i data-lucide="users" class="w-4 h-4 text-zinc-400"></i>
                    User Management
                </a>
                <a href="{{ route('admin.active-listings') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                    <i data-lucide="package-2" class="w-4 h-4 text-zinc-400"></i>
                    Active Listings
                </a>
            </nav>
        </div>

        <!-- Monitoring -->
        <div>
            <h3 class="px-3 text-xs font-medium text-zinc-400 uppercase tracking-wider mb-2">Monitoring</h3>
            <nav class="space-y-0.5">
                <a href="{{ route('admin.pickup-monitoring') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                    <i data-lucide="qr-code" class="w-4 h-4 text-zinc-400"></i>
                    Pickup Verification
                    <span class="ml-auto bg-emerald-100 text-emerald-700 py-0.5 px-2 rounded-full text-[10px] font-medium">
                        {{ App\Models\PickupVerification::where('verification_status', 'pending')->whereNull('qr_code_scanned')->count() }}
                    </span>
                </a>
            </nav>
        </div>
    </div>

    <!-- User Profile Stub -->
    <div class="p-4 border-t border-zinc-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                <i data-lucide="user" class="w-5 h-5 text-emerald-600"></i>
            </div>
            <div class="flex-1">
                <div class="text-sm font-medium text-zinc-900">Admin User</div>
                <div class="text-xs text-zinc-500">admin@myfoodshare.com</div>
            </div>
            <i data-lucide="log-out" class="w-4 h-4 text-zinc-400 hover:text-zinc-600 cursor-pointer"></i>
        </div>
    </div>
</aside>