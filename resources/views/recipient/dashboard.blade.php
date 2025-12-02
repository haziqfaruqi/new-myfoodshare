<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodShare - NGO Recipient Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Custom Scrollbar */
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

        /* Pulse animation for active status */
        @keyframes pulse-ring {
            0% { transform: scale(0.33); }
            80%, 100% { opacity: 0; }
        }
        .pulse-ring::before {
            content: '';
            position: absolute;
            left: 0; top: 0;
            display: block;
            width: 100%; height: 100%;
            background-color: #10b981;
            border-radius: 50%;
            animation: pulse-ring 1.25s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }

        /* Map Placeholder Pattern */
        .map-pattern {
            background-color: #e5e7eb;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239ca3af' fill-opacity='0.2'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-zinc-50 text-zinc-900 flex h-screen overflow-hidden">

    <!-- Sidebar Navigation -->
    <aside class="w-64 border-r border-zinc-200 bg-white hidden md:flex flex-col z-20">
        <div class="h-16 flex items-center px-6 border-b border-zinc-100">
            <div class="flex items-center gap-2 text-blue-600">
                <i data-lucide="heart-handshake" class="w-6 h-6 fill-current"></i>
                <span class="font-bold tracking-tight text-zinc-900 text-lg">FoodShare</span>
                <span class="text-[10px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded ml-1 font-medium">NGO</span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto py-6 px-3 space-y-8">
            <!-- Main Nav -->
            <div>
                <h3 class="px-3 text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Collection</h3>
                <nav class="space-y-1">
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-900 bg-zinc-100 rounded-md">
                        <i data-lucide="layout-grid" class="w-4 h-4 text-zinc-500"></i>
                        Dashboard
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                        <i data-lucide="search" class="w-4 h-4 text-zinc-400"></i>
                        Browse Food
                        <span class="ml-auto bg-emerald-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">12 New</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                        <i data-lucide="map" class="w-4 h-4 text-zinc-400"></i>
                        Map View
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                        <i data-lucide="clock" class="w-4 h-4 text-zinc-400"></i>
                        My Pickups
                    </a>
                </nav>
            </div>

            <!-- Impact & Settings -->
            <div>
                <h3 class="px-3 text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2">Organization</h3>
                <nav class="space-y-1">
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                        <i data-lucide="pie-chart" class="w-4 h-4 text-zinc-400"></i>
                        Impact Report
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                        <i data-lucide="building-2" class="w-4 h-4 text-zinc-400"></i>
                        NGO Profile
                    </a>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50 rounded-md transition-colors">
                        <i data-lucide="settings" class="w-4 h-4 text-zinc-400"></i>
                        Settings
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
                <button class="text-zinc-400 hover:text-zinc-600">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        
        <!-- Header -->
        <header class="h-16 border-b border-zinc-200 bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-6">
            <div class="md:hidden flex items-center gap-2">
                <i data-lucide="menu" class="w-5 h-5 text-zinc-600"></i>
                <span class="font-semibold text-zinc-900">FoodShare</span>
            </div>
            
            <div class="hidden md:flex items-center text-sm breadcrumbs text-zinc-500">
                <span>NGO Portal</span>
                <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-zinc-300"></i>
                <span class="text-zinc-900 font-medium">Dashboard</span>
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
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="flex-1 overflow-y-auto p-6 md:p-8 scroll-smooth">
            <div class="max-w-6xl mx-auto space-y-8">
                
                <!-- Welcome & Key Actions -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Dashboard</h1>
                        <p class="text-sm text-zinc-500 mt-1">Manage your pickups and discover food nearby.</p>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="document.getElementById('verification-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-600/20 rounded-lg text-sm font-medium transition-all">
                            <i data-lucide="scan-line" class="w-4 h-4"></i>
                            Verify Pickup
                        </button>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Active Matches</span>
                            <div class="p-2 bg-emerald-50 rounded-lg">
                                <i data-lucide="link" class="w-4 h-4 text-emerald-600"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-zinc-900">3</span>
                            <span class="text-xs font-medium text-zinc-500">Pickups today</span>
                        </div>
                    </div>
                    
                    <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Meals Recovered</span>
                            <div class="p-2 bg-orange-50 rounded-lg">
                                <i data-lucide="utensils" class="w-4 h-4 text-orange-600"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-zinc-900">842</span>
                            <span class="text-xs font-medium text-emerald-600">+45 this week</span>
                        </div>
                    </div>

                    <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Est. Money Saved</span>
                            <div class="p-2 bg-green-50 rounded-lg">
                                <i data-lucide="dollar-sign" class="w-4 h-4 text-green-600"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-zinc-900">$4,250</span>
                            <span class="text-xs font-medium text-zinc-500">Total value</span>
                        </div>
                    </div>

                    <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Pending Approval</span>
                            <div class="p-2 bg-amber-50 rounded-lg">
                                <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-zinc-900">2</span>
                            <span class="text-xs font-medium text-zinc-500">Requests sent</span>
                        </div>
                    </div>
                </div>

                <!-- Main Section: Discover & Matches -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Left: Discovery Feed -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-zinc-200 shadow-sm">
                            <div class="flex items-center gap-4">
                                <h2 class="font-semibold text-zinc-900">Available Nearby</h2>
                                <span class="text-xs bg-zinc-100 text-zinc-600 px-2 py-1 rounded-md">Within 5km</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button class="p-2 text-zinc-400 hover:text-zinc-900 transition-colors">
                                    <i data-lucide="map" class="w-5 h-5"></i>
                                </button>
                                <button class="p-2 text-zinc-900 bg-zinc-100 rounded-lg">
                                    <i data-lucide="list" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Auto-Match Alert -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 p-4 rounded-xl flex items-start gap-3 relative overflow-hidden">
                            <div class="bg-white p-2 rounded-full shadow-sm z-10">
                                <i data-lucide="sparkles" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div class="z-10">
                                <h3 class="text-sm font-semibold text-blue-900">Smart Match Found!</h3>
                                <p class="text-xs text-blue-700 mt-0.5">We found 50kg of Produce at <span class="font-bold">Whole Foods Market</span> (0.8km away) that matches your preferences.</p>
                                <div class="mt-2 flex gap-2">
                                    <button class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors">Request Now</button>
                                    <button class="px-3 py-1.5 bg-white text-blue-600 border border-blue-200 text-xs font-medium rounded-lg hover:bg-blue-50">View Details</button>
                                </div>
                            </div>
                            <i data-lucide="zap" class="absolute right-[-10px] top-[-10px] w-32 h-32 text-blue-100 opacity-50 rotate-12"></i>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Food Card 1 -->
                            <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden shadow-sm group hover:border-blue-300 transition-all">
                                <div class="h-40 bg-zinc-100 relative">
                                    <img src="https://images.unsplash.com/photo-1584345604476-8ec5e12e42dd?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                    <div class="absolute bottom-3 left-3 text-white">
                                        <p class="font-semibold text-sm">Fresh Bakery Assortment</p>
                                        <p class="text-xs opacity-90">Bagels, Croissants, Bread</p>
                                    </div>
                                    <span class="absolute top-3 right-3 bg-white/90 backdrop-blur text-zinc-800 text-[10px] font-bold px-2 py-0.5 rounded shadow-sm">1.2 km</span>
                                </div>
                                <div class="p-4">
                                    <div class="flex justify-between items-center mb-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center text-[10px] font-bold text-orange-700">SB</div>
                                            <span class="text-xs font-medium text-zinc-600">Sunshine Bakery</span>
                                        </div>
                                        <span class="text-xs text-rose-600 font-medium flex items-center gap-1">
                                            <i data-lucide="clock" class="w-3 h-3"></i> Exp: 4h
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-zinc-100">
                                        <span class="text-xs text-zinc-500">Approx. 5 kg</span>
                                        <button class="text-xs bg-zinc-900 text-white px-3 py-1.5 rounded-lg hover:bg-zinc-700 transition-colors">Request</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Food Card 2 -->
                            <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden shadow-sm group hover:border-blue-300 transition-all">
                                <div class="h-40 bg-zinc-100 relative">
                                    <img src="https://images.unsplash.com/photo-1606758696803-162785d9539d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                    <div class="absolute bottom-3 left-3 text-white">
                                        <p class="font-semibold text-sm">Canned Soup & Beans</p>
                                        <p class="text-xs opacity-90">Non-perishables</p>
                                    </div>
                                    <span class="absolute top-3 right-3 bg-white/90 backdrop-blur text-zinc-800 text-[10px] font-bold px-2 py-0.5 rounded shadow-sm">3.5 km</span>
                                </div>
                                <div class="p-4">
                                    <div class="flex justify-between items-center mb-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-[10px] font-bold text-blue-700">MM</div>
                                            <span class="text-xs font-medium text-zinc-600">Metro Market</span>
                                        </div>
                                        <span class="text-xs text-emerald-600 font-medium flex items-center gap-1">
                                            <i data-lucide="calendar" class="w-3 h-3"></i> Exp: 12d
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between pt-3 border-t border-zinc-100">
                                        <span class="text-xs text-zinc-500">20 Cans</span>
                                        <button class="text-xs bg-zinc-900 text-white px-3 py-1.5 rounded-lg hover:bg-zinc-700 transition-colors">Request</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Active Matches & Schedule -->
                    <div class="space-y-6">
                        
                        <!-- My Pickups Panel -->
                        <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-5">
                            <h3 class="text-sm font-semibold text-zinc-900 mb-4">Upcoming Pickups</h3>
                            <div class="space-y-4">
                                <!-- Pickup 1: Ready -->
                                <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-100 relative overflow-hidden">
                                    <div class="absolute right-0 top-0 p-1">
                                        <span class="bg-white text-emerald-700 text-[9px] font-bold px-1.5 py-0.5 rounded shadow-sm uppercase">Ready</span>
                                    </div>
                                    <div class="flex gap-3">
                                        <div class="w-10 h-10 rounded bg-white flex items-center justify-center shrink-0 shadow-sm text-emerald-600">
                                            <i data-lucide="package-check" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-zinc-900">Italian Bistro Leftovers</p>
                                            <p class="text-xs text-zinc-500">Verify Code: <span class="font-mono font-bold text-zinc-900">8829</span></p>
                                            <div class="flex items-center gap-1 mt-1 text-[10px] text-zinc-500">
                                                <i data-lucide="clock" class="w-3 h-3"></i>
                                                <span>Pickup by 9:00 PM</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button onclick="document.getElementById('verification-modal').classList.remove('hidden')" class="w-full mt-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded hover:bg-emerald-700 transition-colors">
                                        Arrived at Location
                                    </button>
                                </div>

                                <!-- Pickup 2: Pending -->
                                <div class="p-3 rounded-lg bg-zinc-50 border border-zinc-100">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="bg-amber-100 text-amber-700 text-[9px] font-bold px-1.5 py-0.5 rounded uppercase">Pending Approval</span>
                                        <button class="text-zinc-400 hover:text-red-500"><i data-lucide="x" class="w-3 h-3"></i></button>
                                    </div>
                                    <div class="flex gap-3">
                                        <div class="w-10 h-10 rounded bg-white flex items-center justify-center shrink-0 border border-zinc-200 text-zinc-400">
                                            <i data-lucide="clock" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-zinc-900">Downtown Deli</p>
                                            <p class="text-xs text-zinc-500">15 Sandwiches</p>
                                            <p class="text-[10px] text-zinc-400 mt-0.5">Requested 10 mins ago</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Map Preview -->
                        <div class="bg-zinc-100 rounded-xl h-48 w-full relative overflow-hidden border border-zinc-200 map-pattern group">
                             <!-- Pins -->
                            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                                <span class="relative flex h-3 w-3">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500 border-2 border-white"></span>
                                </span>
                            </div>
                            <div class="absolute top-10 right-10">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full ring-2 ring-white"></div>
                            </div>
                            <div class="absolute bottom-10 left-12">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full ring-2 ring-white"></div>
                            </div>
                            
                            <div class="absolute bottom-2 left-2 bg-white/90 backdrop-blur px-2 py-1 rounded shadow-sm text-[10px] font-medium text-zinc-600">
                                You are here
                            </div>
                            <button class="absolute inset-0 w-full h-full flex items-center justify-center bg-black/0 hover:bg-black/5 transition-colors group-hover:opacity-100 opacity-0">
                                <span class="bg-white shadow-md text-zinc-900 px-3 py-1.5 rounded-lg text-xs font-semibold">Open Full Map</span>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Verification & Rating Modal -->
    <div id="verification-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('verification-modal').classList.add('hidden')"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-6 border-b border-zinc-100 text-center">
                <h3 class="text-lg font-bold text-zinc-900">Complete Pickup</h3>
                <p class="text-sm text-zinc-500">Italian Bistro • Order #8829</p>
            </div>
            
            <div class="p-6 overflow-y-auto space-y-6">
                <!-- Step 1: Scan -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider">1. Verification</label>
                    <div class="bg-zinc-900 rounded-xl p-4 text-center relative overflow-hidden group cursor-pointer">
                        <i data-lucide="camera" class="w-8 h-8 text-zinc-500 mx-auto mb-2"></i>
                        <p class="text-sm text-zinc-300">Tap to Scan Restaurant Code</p>
                        <p class="text-xs text-zinc-500 mt-1">or enter code manually</p>
                        <!-- Simulated Camera Overlay -->
                        <div class="absolute inset-0 bg-black/50 hidden group-hover:flex items-center justify-center">
                             <span class="text-white text-xs font-bold border border-white px-3 py-1 rounded-full">Activate Camera</span>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Quality Check -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider">2. Quality Rating</label>
                    <div class="flex justify-center gap-2">
                        <button class="w-10 h-10 rounded-full border border-zinc-200 text-zinc-300 hover:text-yellow-400 hover:border-yellow-400 flex items-center justify-center transition-colors"><i data-lucide="star" class="w-5 h-5 fill-current"></i></button>
                        <button class="w-10 h-10 rounded-full border border-zinc-200 text-zinc-300 hover:text-yellow-400 hover:border-yellow-400 flex items-center justify-center transition-colors"><i data-lucide="star" class="w-5 h-5 fill-current"></i></button>
                        <button class="w-10 h-10 rounded-full border border-zinc-200 text-zinc-300 hover:text-yellow-400 hover:border-yellow-400 flex items-center justify-center transition-colors"><i data-lucide="star" class="w-5 h-5 fill-current"></i></button>
                        <button class="w-10 h-10 rounded-full border border-zinc-200 text-zinc-300 hover:text-yellow-400 hover:border-yellow-400 flex items-center justify-center transition-colors"><i data-lucide="star" class="w-5 h-5 fill-current"></i></button>
                        <button class="w-10 h-10 rounded-full border border-zinc-200 text-zinc-300 hover:text-yellow-400 hover:border-yellow-400 flex items-center justify-center transition-colors"><i data-lucide="star" class="w-5 h-5 fill-current"></i></button>
                    </div>
                </div>

                <!-- Step 3: Evidence -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider">3. Photo Evidence (Optional)</label>
                    <div class="border-2 border-dashed border-zinc-200 rounded-lg p-4 text-center hover:bg-zinc-50 transition-colors cursor-pointer">
                        <i data-lucide="image-plus" class="w-5 h-5 text-zinc-400 mx-auto"></i>
                        <span class="text-xs text-zinc-500 mt-1 block">Upload photo of received food</span>
                    </div>
                </div>
                
                 <!-- Step 4: Feedback -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider">4. Notes</label>
                     <textarea rows="2" placeholder="Any issues with quantity or packaging?" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 resize-none"></textarea>
                </div>
            </div>

            <div class="p-6 pt-2 border-t border-zinc-100 bg-zinc-50">
                <button class="w-full py-2.5 bg-zinc-900 text-white rounded-lg font-medium shadow-lg shadow-zinc-900/10 hover:bg-zinc-800 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Confirm Pickup
                </button>
                <button onclick="document.getElementById('verification-modal').classList.add('hidden')" class="w-full mt-2 text-xs text-zinc-500 hover:text-zinc-900">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        // Initialize Icons
        lucide.createIcons();
    </script>
</body>
</html>