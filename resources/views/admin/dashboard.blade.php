<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MyFoodshare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Custom Scrollbar for sleek look */
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

        /* Toggle Switch */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #10b981;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #10b981;
        }

        /* Hide number input arrows */
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
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                        <i data-lucide="check-square" class="w-4 h-4 text-zinc-400"></i>
                        Verifications
                        <span class="ml-auto bg-emerald-100 text-emerald-700 py-0.5 px-2 rounded-full text-[10px] font-medium">3 New</span>
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
                <span>Dashboard</span>
                <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-zinc-300"></i>
                <span class="text-zinc-900 font-medium">Overview</span>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative hidden sm:block">
                    <i data-lucide="search" class="w-4 h-4 text-zinc-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" placeholder="Search for food..." class="pl-9 pr-4 py-1.5 text-sm bg-zinc-100 border-none rounded-md focus:ring-2 focus:ring-emerald-500/20 focus:bg-white transition-all w-64 placeholder:text-zinc-400">
                </div>
                <button class="relative text-zinc-500 hover:text-zinc-900">
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

        <!-- Scrollable Dashboard Content -->
        <div class="flex-1 overflow-y-auto p-6 md:p-8 scroll-smooth">
            
            <div class="max-w-6xl mx-auto space-y-8">
                
                <!-- Welcome & Stats -->
                <div class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Dashboard</h1>
                            <p class="text-sm text-zinc-500 mt-1">Manage listings, track impact, and help the community.</p>
                        </div>
                        <div class="flex gap-3">
                            <button class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-zinc-200 shadow-sm rounded-lg text-sm font-medium text-zinc-700 hover:bg-zinc-50 transition-all">
                                <i data-lucide="filter" class="w-4 h-4"></i>
                                Filters
                            </button>
                            <button class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white shadow-sm shadow-zinc-500/20 rounded-lg text-sm font-medium transition-all">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                New Listing
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Stat Card 1 -->
                        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-medium text-zinc-500 uppercase">Active Listings</span>
                                <i data-lucide="package" class="w-4 h-4 text-emerald-600"></i>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-semibold tracking-tight text-zinc-900">24</span>
                                <span class="text-xs font-medium text-emerald-600">+4 today</span>
                            </div>
                        </div>
                        
                        <!-- Stat Card 2 -->
                        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-medium text-zinc-500 uppercase">Matches Found</span>
                                <i data-lucide="users" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-semibold tracking-tight text-zinc-900">1,204</span>
                                <span class="text-xs font-medium text-zinc-500">Lifetime</span>
                            </div>
                        </div>

                        <!-- Stat Card 3 -->
                        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-medium text-zinc-500 uppercase">Meals Saved</span>
                                <i data-lucide="utensils" class="w-4 h-4 text-orange-600"></i>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-semibold tracking-tight text-zinc-900">856</span>
                                <span class="text-xs font-medium text-emerald-600">↑ 12%</span>
                            </div>
                        </div>

                        <!-- Stat Card 4 -->
                        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-medium text-zinc-500 uppercase">Verification Rate</span>
                                <i data-lucide="shield-check" class="w-4 h-4 text-purple-600"></i>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-semibold tracking-tight text-zinc-900">98.2%</span>
                                <span class="text-xs font-medium text-zinc-500">Last 30 days</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Left: Food Listings (Broad View) -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-medium text-zinc-900">Available Near You</h2>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-medium text-zinc-500">Sort by:</span>
                                <select class="bg-transparent text-xs font-medium text-zinc-900 border-none focus:ring-0 cursor-pointer pr-6">
                                    <option>Proximity</option>
                                    <option>Expiry (Soonest)</option>
                                    <option>Quantity</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Food Card 1 -->
                            <div class="group bg-white rounded-xl border border-zinc-200 overflow-hidden hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300">
                                <div class="h-32 bg-zinc-100 relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent z-10"></div>
                                    <!-- Placeholder for Image -->
                                    <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80');"></div>
                                    <div class="absolute bottom-3 left-3 z-20 flex flex-col">
                                        <span class="text-white font-medium text-sm">Artisan Sourdough</span>
                                        <span class="text-zinc-200 text-xs">Bakery Items</span>
                                    </div>
                                    <span class="absolute top-3 right-3 z-20 bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full tracking-wide">VERIFIED</span>
                                </div>
                                <div class="p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="text-xs text-zinc-500 flex items-center gap-1">
                                                <i data-lucide="map-pin" class="w-3 h-3"></i> 0.4 miles away
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-1 text-xs font-medium text-orange-600 bg-orange-50 px-2 py-0.5 rounded">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            Exp: 2h
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 mb-4">
                                        <div class="w-5 h-5 rounded-full bg-zinc-200 flex items-center justify-center text-[10px] font-bold text-zinc-600">LB</div>
                                        <span class="text-xs text-zinc-600">Le Boulangerie • <span class="text-emerald-600">4.9 ★</span></span>
                                    </div>
                                    <button class="w-full py-2 bg-white border border-zinc-200 text-zinc-900 text-sm font-medium rounded-lg hover:border-emerald-500 hover:text-emerald-600 transition-colors">
                                        View Details
                                    </button>
                                </div>
                            </div>

                            <!-- Food Card 2 -->
                            <div class="group bg-white rounded-xl border border-zinc-200 overflow-hidden hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300">
                                <div class="h-32 bg-zinc-100 relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent z-10"></div>
                                    <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1547592180-85f173990554?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80');"></div>
                                    <div class="absolute bottom-3 left-3 z-20 flex flex-col">
                                        <span class="text-white font-medium text-sm">Fresh Organic Veggies</span>
                                        <span class="text-zinc-200 text-xs">Produce</span>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="text-xs text-zinc-500 flex items-center gap-1">
                                                <i data-lucide="map-pin" class="w-3 h-3"></i> 1.2 miles away
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-1 text-xs font-medium text-zinc-600 bg-zinc-100 px-2 py-0.5 rounded">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            Exp: 1d
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 mb-4">
                                        <div class="w-5 h-5 rounded-full bg-zinc-200 flex items-center justify-center text-[10px] font-bold text-zinc-600">GM</div>
                                        <span class="text-xs text-zinc-600">Green Market • <span class="text-emerald-600">4.7 ★</span></span>
                                    </div>
                                    <button class="w-full py-2 bg-white border border-zinc-200 text-zinc-900 text-sm font-medium rounded-lg hover:border-emerald-500 hover:text-emerald-600 transition-colors">
                                        View Details
                                    </button>
                                </div>
                            </div>

                             <!-- Food Card 3 -->
                             <div class="group bg-white rounded-xl border border-zinc-200 overflow-hidden hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300">
                                <div class="h-32 bg-zinc-100 relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent z-10"></div>
                                    <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1555244162-803834f70033?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80');"></div>
                                    <div class="absolute bottom-3 left-3 z-20 flex flex-col">
                                        <span class="text-white font-medium text-sm">Catering Trays</span>
                                        <span class="text-zinc-200 text-xs">Prepared Meals</span>
                                    </div>
                                    <span class="absolute top-3 right-3 z-20 bg-zinc-900/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded-full tracking-wide">BULK</span>
                                </div>
                                <div class="p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="text-xs text-zinc-500 flex items-center gap-1">
                                                <i data-lucide="map-pin" class="w-3 h-3"></i> 2.5 miles away
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-1 text-xs font-medium text-zinc-600 bg-zinc-100 px-2 py-0.5 rounded">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            Exp: 5h
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 mb-4">
                                        <div class="w-5 h-5 rounded-full bg-zinc-200 flex items-center justify-center text-[10px] font-bold text-zinc-600">EC</div>
                                        <span class="text-xs text-zinc-600">Events Co. • <span class="text-emerald-600">5.0 ★</span></span>
                                    </div>
                                    <button class="w-full py-2 bg-white border border-zinc-200 text-zinc-900 text-sm font-medium rounded-lg hover:border-emerald-500 hover:text-emerald-600 transition-colors">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Activity & Verification -->
                    <div class="space-y-6">
                        
                        <!-- Active Pickup Panel -->
                        <div class="bg-gradient-to-br from-zinc-900 to-zinc-800 rounded-xl p-5 text-white shadow-lg relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-32 bg-emerald-500/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
                            
                            <div class="relative z-10">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-sm font-medium text-zinc-100">Active Pickup</h3>
                                        <p class="text-xs text-zinc-400 mt-1">Order #4922-A • 15 mins ago</p>
                                    </div>
                                    <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-bold px-2 py-0.5 rounded-full">READY</span>
                                </div>

                                <div class="flex items-center gap-3 bg-white/5 p-3 rounded-lg border border-white/10 mb-4">
                                    <div class="w-10 h-10 rounded bg-white/10 flex items-center justify-center">
                                        <i data-lucide="coffee" class="w-5 h-5 text-zinc-300"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">Starbucks Leftovers</p>
                                        <p class="text-xs text-zinc-400">12 items • Bagged</p>
                                    </div>
                                </div>

                                <button class="w-full py-2 bg-emerald-500 hover:bg-emerald-400 text-zinc-900 text-sm font-semibold rounded-lg shadow-md transition-colors flex items-center justify-center gap-2">
                                    <i data-lucide="qr-code" class="w-4 h-4"></i>
                                    Show Pickup QR
                                </button>
                            </div>
                        </div>

                        <!-- Recent Activity List -->
                        <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-5">
                            <h3 class="text-sm font-medium text-zinc-900 mb-4">Recent Activity</h3>
                            <div class="space-y-4">
                                <!-- Item 1 -->
                                <div class="flex gap-3 relative">
                                    <div class="absolute left-[11px] top-8 bottom-[-16px] w-[1px] bg-zinc-100"></div>
                                    <div class="w-6 h-6 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center shrink-0 z-10">
                                        <i data-lucide="check" class="w-3 h-3 text-emerald-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-zinc-900 font-medium">Donation Approved</p>
                                        <p class="text-[10px] text-zinc-500">Admin verified "Sushi Platter"</p>
                                        <p class="text-[10px] text-zinc-400 mt-0.5">2m ago</p>
                                    </div>
                                </div>
                                <!-- Item 2 -->
                                <div class="flex gap-3 relative">
                                    <div class="absolute left-[11px] top-8 bottom-[-16px] w-[1px] bg-zinc-100"></div>
                                    <div class="w-6 h-6 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0 z-10">
                                        <i data-lucide="user-check" class="w-3 h-3 text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-zinc-900 font-medium">Match Found</p>
                                        <p class="text-[10px] text-zinc-500">Linked with local shelter</p>
                                        <p class="text-[10px] text-zinc-400 mt-0.5">1h ago</p>
                                    </div>
                                </div>
                                <!-- Item 3 -->
                                <div class="flex gap-3 relative">
                                    <div class="w-6 h-6 rounded-full bg-zinc-50 border border-zinc-100 flex items-center justify-center shrink-0 z-10">
                                        <i data-lucide="upload-cloud" class="w-3 h-3 text-zinc-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-zinc-900 font-medium">Listing Created</p>
                                        <p class="text-[10px] text-zinc-500">By Joe's Pizza</p>
                                        <p class="text-[10px] text-zinc-400 mt-0.5">3h ago</p>
                                    </div>
                                </div>
                            </div>
                            <button class="w-full mt-4 text-xs font-medium text-zinc-500 hover:text-zinc-900 border-t border-zinc-100 pt-3">
                                View Full History
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Admin Table Section (Simulated Data) -->
                <div class="pt-2">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-medium text-zinc-900">Pending Approvals (Admin)</h3>
                        <button class="text-xs text-emerald-600 font-medium hover:underline">Manage All</button>
                    </div>
                    <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-zinc-50 text-zinc-500 border-b border-zinc-200">
                                    <tr>
                                        <th class="px-6 py-3 font-medium">Donor</th>
                                        <th class="px-6 py-3 font-medium">Food Type</th>
                                        <th class="px-6 py-3 font-medium">Qty</th>
                                        <th class="px-6 py-3 font-medium">Submitted</th>
                                        <th class="px-6 py-3 font-medium">Status</th>
                                        <th class="px-6 py-3 font-medium text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100">
                                    <tr class="hover:bg-zinc-50/50 transition-colors">
                                        <td class="px-6 py-3 font-medium text-zinc-900">Downtown Bistro</td>
                                        <td class="px-6 py-3 text-zinc-600">Soup & Salads</td>
                                        <td class="px-6 py-3 text-zinc-600">5kg</td>
                                        <td class="px-6 py-3 text-zinc-500">10 mins ago</td>
                                        <td class="px-6 py-3">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                                <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                                Pending
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button class="text-zinc-400 hover:text-red-600"><i data-lucide="x" class="w-4 h-4"></i></button>
                                                <button class="text-zinc-400 hover:text-emerald-600"><i data-lucide="check" class="w-4 h-4"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-zinc-50/50 transition-colors">
                                        <td class="px-6 py-3 font-medium text-zinc-900">City Market</td>
                                        <td class="px-6 py-3 text-zinc-600">Dairy Products</td>
                                        <td class="px-6 py-3 text-zinc-600">12 units</td>
                                        <td class="px-6 py-3 text-zinc-500">45 mins ago</td>
                                        <td class="px-6 py-3">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                                <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                                Pending
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button class="text-zinc-400 hover:text-red-600"><i data-lucide="x" class="w-4 h-4"></i></button>
                                                <button class="text-zinc-400 hover:text-emerald-600"><i data-lucide="check" class="w-4 h-4"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            
            <footer class="mt-12 mb-4 text-center">
                <p class="text-xs text-zinc-400">© 2024 FoodShare Platform. Reducing waste, feeding communities.</p>
            </footer>

        </div>

        <!-- Floating Action Button for Mobile -->
        <button class="md:hidden absolute bottom-6 right-6 w-12 h-12 bg-zinc-900 text-white rounded-full shadow-lg flex items-center justify-center z-30">
            <i data-lucide="plus" class="w-6 h-6"></i>
        </button>

    </main>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
    </script>
</body>
</html>