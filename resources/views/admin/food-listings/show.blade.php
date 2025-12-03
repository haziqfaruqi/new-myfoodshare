<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Listing Details - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    @php
        use Illuminate\Support\Facades\Storage;
    @endphp
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #e4e4e7;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #d4d4d8;
        }

        .toggle-checkbox:checked {
            right: 0;
            border-color: #10b981;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #10b981;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="bg-zinc-50 text-zinc-900 flex h-screen overflow-hidden selection:bg-emerald-100 selection:text-emerald-900">

    <!-- Sidebar Navigation -->
    <aside class="w-64 border-r border-zinc-200 bg-white hidden md:flex flex-col z-20">
        <div class="h-16 flex items-center px-6 border-b border-zinc-100">
            <div class="flex items-center gap-2 text-emerald-600">
                <i data-lucide="leaf" class="w-5 h-5 fill-current"></i>
                <span class="font-semibold tracking-tight text-zinc-900">FoodShare</span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto py-6 px-3 space-y-8">
            <!-- Main Nav -->
            <div>
                <h3 class="px-3 text-xs font-medium text-zinc-400 uppercase tracking-wider mb-2">Platform</h3>
                <nav class="space-y-0.5">
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-900 bg-zinc-100 rounded-md">
                        <i data-lucide="layout-grid" class="w-4 h-4 text-zinc-500"></i>
                        Overview
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                        <i data-lucide="search" class="w-4 h-4 text-zinc-400"></i>
                        Browse Food
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                        <i data-lucide="map-pin" class="w-4 h-4 text-zinc-400"></i>
                        Map View
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                        <i data-lucide="plus-circle" class="w-4 h-4 text-zinc-400"></i>
                        Post Donation
                    </a>
                </nav>
            </div>

            <!-- Management -->
            <div>
                <h3 class="px-3 text-xs font-medium text-zinc-400 uppercase tracking-wider mb-2">Management</h3>
                <nav class="space-y-0.5">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                        <i data-lucide="check-square" class="w-4 h-4 text-zinc-400"></i>
                        Pending Approvals
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                        <i data-lucide="bar-chart-3" class="w-4 h-4 text-zinc-400"></i>
                        Analytics
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                        <i data-lucide="users" class="w-4 h-4 text-zinc-400"></i>
                        Users
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
                <div class="flex items-center gap-1">
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-zinc-400 hover:text-red-600 transition-colors" title="Logout">
                            <i data-lucide="log-out" class="w-3 h-3"></i>
                        </button>
                    </form>
                    <button class="text-zinc-400 hover:text-zinc-600 ml-1" title="Settings">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">

        <!-- Mobile Header -->
        <header class="h-16 border-b border-zinc-200 bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-6">
            <div class="md:hidden flex items-center gap-2">
                <i data-lucide="menu" class="w-5 h-5 text-zinc-600"></i>
                <span class="font-semibold text-zinc-900">FoodShare</span>
            </div>

            <div class="hidden md:flex items-center text-sm breadcrumbs text-zinc-500">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-zinc-300"></i>
                <a href="{{ route('admin.food-listings.index') }}">Pending Approvals</a>
                <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-zinc-300"></i>
                <span class="text-zinc-900 font-medium">Food Listing Details</span>
            </div>

            <div class="flex items-center gap-4">
                <form action="{{ route('logout') }}" method="POST" class="ml-2">
                    @csrf
                    <button type="submit" class="text-zinc-500 hover:text-red-600 transition-colors" title="Logout">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-6 md:p-8 scroll-smooth">

            <div class="max-w-4xl mx-auto space-y-8">

                <!-- Page Header -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Food Listing Details</h1>
                            <p class="text-sm text-zinc-500 mt-1">Review and approve food donation listing</p>
                        </div>
                        <a href="{{ route('admin.food-listings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-zinc-200 text-zinc-700 rounded-lg hover:bg-zinc-50 transition-colors">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            Back to Pending
                        </a>
                    </div>
                </div>

                <!-- Food Listing Details -->
                <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <!-- Left: Images and Basic Info -->
                        <div class="space-y-6 p-6">
                            <!-- Image Gallery -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-medium text-zinc-900">Images</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @if($foodListing->images && count($foodListing->images) > 0)
                                        @foreach($foodListing->images as $image)
                                            <div class="relative group">
                                                <img src="{{ Storage::url($image) }}" alt="Food image" class="w-full h-32 object-cover rounded-lg border border-zinc-200">
                                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                                    <button class="text-white p-2 rounded-full bg-black/20 hover:bg-black/40">
                                                        <i data-lucide="zoom-in" class="w-4 h-4"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="w-full h-32 bg-zinc-100 rounded-lg border-2 border-dashed border-zinc-200 flex items-center justify-center">
                                            <div class="text-center">
                                                <i data-lucide="image" class="w-8 h-8 text-zinc-400 mx-auto mb-2"></i>
                                                <p class="text-sm text-zinc-500">No images uploaded</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-medium text-zinc-900">Basic Information</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-medium text-zinc-500">Food Name</label>
                                        <p class="text-sm text-zinc-900 font-medium">{{ $foodListing->food_name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-zinc-500">Category</label>
                                        <p class="text-sm text-zinc-900 font-medium">{{ ucfirst($foodListing->category) }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-zinc-500">Quantity</label>
                                        <p class="text-sm text-zinc-900 font-medium">{{ $foodListing->quantity }} {{ $foodListing->unit }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-zinc-500">Status</label>
                                        <p class="text-sm text-zinc-900 font-medium">
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                                <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                                PENDING APPROVAL
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Details and Actions -->
                        <div class="space-y-6 p-6 border-t lg:border-t-0 lg:border-l border-zinc-200">
                            <!-- Donor Information -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-medium text-zinc-900">Donor Information</h3>
                                <div class="bg-zinc-50 rounded-lg p-4">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-10 h-10 rounded-full bg-zinc-200 flex items-center justify-center text-sm font-bold text-zinc-600">
                                            {{ substr($foodListing->restaurantProfile?->restaurant_name ?? $foodListing->creator->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-zinc-900">
                                                {{ $foodListing->restaurantProfile?->restaurant_name ?? $foodListing->creator->name }}
                                            </p>
                                            <p class="text-xs text-zinc-500">
                                                {{ $foodListing->creator->email }} • {{ $foodListing->creator->phone ?? 'No phone' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2 text-xs">
                                            <i data-lucide="map-pin" class="w-3 h-3 text-zinc-500"></i>
                                            <span class="text-zinc-600">{{ $foodListing->pickup_location }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs">
                                            <i data-lucide="phone" class="w-3 h-3 text-zinc-500"></i>
                                            <span class="text-zinc-600">{{ $foodListing->creator->phone ?? 'Not provided' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Expiry Information -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-medium text-zinc-900">Expiry Information</h3>
                                <div class="bg-orange-50 rounded-lg p-4 border border-orange-100">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-orange-900">Expiry Date & Time</p>
                                            <p class="text-xs text-orange-700 mt-1">
                                                {{ $foodListing->expiry_date?->format('F j, Y') }} at {{ $foodListing->expiry_time?->format('g:i A') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs font-medium text-orange-600">Time Remaining</p>
                                            <p class="text-sm font-bold text-orange-900">{{ $foodListing->expiry_date?->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-medium text-zinc-900">Description</h3>
                                <div class="bg-zinc-50 rounded-lg p-4">
                                    <p class="text-sm text-zinc-700 leading-relaxed">{{ $foodListing->description }}</p>
                                </div>
                            </div>

                            <!-- Special Instructions -->
                            @if($foodListing->special_instructions)
                                <div class="space-y-4">
                                    <h3 class="text-sm font-medium text-zinc-900">Special Instructions</h3>
                                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                                        <p class="text-sm text-blue-800 leading-relaxed">{{ $foodListing->special_instructions }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Dietary Info -->
                            @if($foodListing->dietary_info)
                                <div class="space-y-4">
                                    <h3 class="text-sm font-medium text-zinc-900">Dietary Information</h3>
                                    <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($foodListing->dietary_info as $info)
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                    <i data-lucide="check" class="w-3 h-3"></i>
                                                    {{ $info }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="space-y-4 pt-4 border-t border-zinc-200">
                                <h3 class="text-sm font-medium text-zinc-900">Admin Actions</h3>
                                <form action="{{ route('admin.food-listings.approve', $foodListing->id) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                        Approve Listing
                                    </button>
                                </form>
                                <form action="{{ route('admin.food-listings.reject', $foodListing->id) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <button type="button" onclick="showRejectModal()" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                        Reject Listing
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6">
                    <h3 class="text-sm font-medium text-zinc-900 mb-4">Listing Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 border border-emerald-200 flex items-center justify-center">
                                    <i data-lucide="plus" class="w-4 h-4 text-emerald-600"></i>
                                </div>
                                <div class="w-px h-16 bg-zinc-200 mt-2"></div>
                            </div>
                            <div class="flex-1 pb-4">
                                <p class="text-sm font-medium text-zinc-900">Listing Created</p>
                                <p class="text-xs text-zinc-500">{{ $foodListing->created_at->format('F j, Y g:i A') }}</p>
                                <p class="text-xs text-zinc-600 mt-1">By {{ $foodListing->restaurantProfile?->restaurant_name ?? $foodListing->creator->name }}</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full bg-amber-100 border border-amber-200 flex items-center justify-center">
                                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                                </div>
                                <div class="w-px h-16 bg-zinc-200 mt-2"></div>
                            </div>
                            <div class="flex-1 pb-4">
                                <p class="text-sm font-medium text-zinc-900">Pending Approval</p>
                                <p class="text-xs text-zinc-500">{{ $foodListing->updated_at->format('F j, Y g:i A') }}</p>
                                <p class="text-xs text-zinc-600 mt-1">Awaiting admin review</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <footer class="mt-12 mb-4 text-center">
                <p class="text-xs text-zinc-400">© 2024 FoodShare Platform. Reducing waste, feeding communities.</p>
            </footer>

        </div>

    </main>

    <!-- Reject Modal -->
    <div id="reject-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 animate-in zoom-in-90">
            <div class="p-6 border-b border-zinc-100">
                <h3 class="text-lg font-semibold text-zinc-900">Reject Listing</h3>
                <p class="text-sm text-zinc-500 mt-1">Please provide a reason for rejecting this food listing.</p>
            </div>
            <form action="{{ route('admin.food-listings.reject', $foodListing->id) }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Rejection Reason</label>
                        <textarea name="admin_notes" rows="4" required placeholder="Please explain why this listing is being rejected..." class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 resize-none"></textarea>
                    </div>
                    <div class="flex gap-3 pt-4 border-t border-zinc-100">
                        <button type="button" onclick="hideRejectModal()" class="flex-1 py-2 border border-zinc-200 text-zinc-700 rounded-lg font-medium hover:bg-zinc-50 transition-all">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-all">
                            Reject Listing
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Modal functions
        function showRejectModal() {
            document.getElementById('reject-modal').classList.remove('hidden');
        }

        function hideRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
        }

        // Close modal on backdrop click
        document.getElementById('reject-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideRejectModal();
            }
        });
    </script>
</body>
</html>