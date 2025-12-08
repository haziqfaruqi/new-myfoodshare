@extends('recipient.layouts.recipient-layout')

@section('title', 'Available Food')

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-hidden">

    <!-- Welcome & Key Actions -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 p-6 md:p-8 pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Available Food</h1>
            <p class="text-sm text-zinc-500 mt-1">Browse all available food listings near you.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="openFiltersModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-all">
                <i data-lucide="filter" class="w-4 h-4"></i>
                Filters
            </button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="px-6 md:px-8 pb-4">
        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-zinc-400 w-4 h-4"></i>
                        <input type="text" placeholder="Search food types, restaurants..."
                               class="w-full pl-10 pr-4 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex gap-2">
                    <select class="px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option>All Categories</option>
                        <option>Italian</option>
                        <option>Chinese</option>
                        <option>Indian</option>
                        <option>Mexican</option>
                        <option>American</option>
                    </select>
                    <select class="px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option>Distance</option>
                        <option>Within 1km</option>
                        <option>Within 5km</option>
                        <option>Within 10km</option>
                        <option>Within 20km</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Food Listings Grid -->
    <div class="flex-1 overflow-y-auto px-6 md:px-8 pb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($paginatedListings as $listing)
            <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden shadow-sm group hover:border-blue-300 transition-all">
                <div class="h-48 bg-zinc-100 relative">
                    @if($listing->images && isset($listing->images[0]))
                        <img src="{{ asset('storage/' . $listing->images[0]) }}"
                             class="w-full h-full object-cover"
                             alt="{{ $listing->food_name }}">
                    @else
                        <img src="https://images.unsplash.com/photo-1584345604476-8ec5e12e42dd?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                             class="w-full h-full object-cover"
                             alt="{{ $listing->food_name }}">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-3 left-3 text-white">
                        <p class="font-semibold text-lg">{{ $listing->food_name ?? $listing->food_type ?? 'Food' }}</p>
                        <p class="text-sm opacity-90">{{ $listing->quantity }} {{ $listing->unit ?? 'units' }}</p>
                    </div>
                    <div class="absolute top-3 right-3 flex gap-2">
                        <span class="bg-white/90 backdrop-blur text-zinc-800 text-xs font-bold px-2 py-1 rounded shadow-sm">
                            {{ $listing->expiry_date }}
                        </span>
                        @if($listing->expiry_time)
                        <span class="bg-white/90 backdrop-blur text-zinc-800 text-xs font-bold px-2 py-1 rounded shadow-sm">
                            {{ $listing->expiry_time }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-700 font-bold border border-orange-200">
                                {{ strtoupper(substr($listing->restaurantProfile->restaurant_name ?? $listing->creator->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900">{{ $listing->restaurantProfile->restaurant_name ?? $listing->creator->name }}</p>
                                <p class="text-xs text-zinc-500">{{ $listing->pickup_address }}</p>
                            </div>
                        </div>
                        @if($listing->distance)
                        <span class="text-xs text-zinc-500 bg-zinc-50 px-2 py-1 rounded">{{ $listing->distance }} km</span>
                        @endif
                    </div>

                    <p class="text-sm text-zinc-600 mb-4 line-clamp-2">{{ $listing->description }}</p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            @if($listing->expiry_date == now()->toDateString() && $listing->expiry_time <= now()->format('H:i'))
                                <span class="text-xs text-rose-600 font-medium">Expired</span>
                            @else
                                <span class="text-xs text-emerald-600 font-medium">Available</span>
                            @endif
                            @if($listing->dietary_info)
                                <span class="text-xs text-amber-600 font-medium">{{ $listing->dietary_info['type'] ?? 'Dietary' }}</span>
                            @endif
                        </div>
                        @if ($userMatches->has($listing->id))
                            <span class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg cursor-not-allowed">
                                Requested
                            </span>
                        @else
                            <form action="{{ route('recipient.matches.store', $listing->id) }}" method="POST" class="inline" onsubmit="handleRequestFood(this)">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    Request Food
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Empty State -->
        @if ($paginatedListings->isEmpty())
        <div class="text-center py-12">
            <i data-lucide="search-x" class="w-16 h-16 text-zinc-300 mx-auto mb-4"></i>
            <h3 class="text-lg font-medium text-zinc-900 mb-2">No food available</h3>
            <p class="text-zinc-500 mb-6">We couldn't find any food listings matching your criteria.</p>
            <div class="flex justify-center gap-3">
                <button onclick="window.location.href='{{ request()->fullUrl() }}'" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i data-lucide="refresh-cw" class="w-4 h-4 inline mr-2"></i>
                    Refresh Search
                </button>
            </div>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if ($paginatedListings->hasPages())
    <div class="px-6 md:px-8 py-4 border-t border-zinc-200">
        <div class="flex items-center justify-between">
            <p class="text-sm text-zinc-500">
                Showing {{ $paginatedListings->firstItem() }}-{{ $paginatedListings->lastItem() }} of {{ $paginatedListings->total() }} results
            </p>
            <div class="flex gap-2">
                {{ $paginatedListings->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
</div>

<!-- Filters Modal -->
<div id="filters-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-hidden flex flex-col">
        <div class="p-6 border-b border-zinc-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-zinc-900">Filters</h3>
                <button onclick="closeFiltersModal()" class="text-zinc-400 hover:text-zinc-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <div class="p-6 overflow-y-auto space-y-6">
            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-2">Food Category</label>
                <select id="category-filter" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    <option value="italian">Italian</option>
                    <option value="chinese">Chinese</option>
                    <option value="indian">Indian</option>
                    <option value="mexican">Mexican</option>
                    <option value="american">American</option>
                </select>
            </div>

            <!-- Distance Filter -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-2">Distance</label>
                <select id="distance-filter" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Any Distance</option>
                    <option value="1">Within 1km</option>
                    <option value="5">Within 5km</option>
                    <option value="10">Within 10km</option>
                    <option value="20">Within 20km</option>
                </select>
            </div>

            <!-- Dietary Info Filter -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-2">Dietary Preferences</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" id="vegetarian" class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-zinc-700">Vegetarian</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="vegan" class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-zinc-700">Vegan</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="gluten-free" class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-zinc-700">Gluten Free</span>
                    </label>
                </div>
            </div>

            <!-- Expiry Status Filter -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-2">Expiry Status</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" id="show-available" class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500" checked>
                        <span class="ml-2 text-sm text-zinc-700">Show Available</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="show-expiring" class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-zinc-700">Show Expiring Soon</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-zinc-100">
            <div class="flex gap-3">
                <button onclick="resetFilters()" class="flex-1 px-4 py-2 border border-zinc-300 text-zinc-700 rounded-lg hover:bg-zinc-50 transition-colors">
                    Reset
                </button>
                <button onclick="applyFilters()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize Lucide icons
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Global error handler
        document.addEventListener('DOMContentLoaded', function() {
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch.apply(this, args).catch(function(error) {
                    document.body.classList.add('error-shown');
                    throw error;
                });
            };
        });
    });

    // Handle food request button state
    function handleRequestFood(form) {
        const button = form.querySelector('button[type="submit"]');
        const originalText = button.textContent;

        // Change button state
        button.disabled = true;
        button.textContent = 'Requesting...';
        button.classList.add('opacity-75', 'cursor-not-allowed');

        // Listen for form submission response
        form.addEventListener('submit', function(e) {
            // If server returns an error (like validation error), restore button
            setTimeout(function() {
                if (!document.body.classList.contains('error-shown')) {
                    button.disabled = false;
                    button.textContent = originalText;
                    button.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            }, 100);
        });

        // If form submission fails (network error), restore original state
        form.addEventListener('error', function() {
            button.disabled = false;
            button.textContent = originalText;
            button.classList.remove('opacity-75', 'cursor-not-allowed');
        });
    }

    // Filters Modal Functions
    function openFiltersModal() {
        document.getElementById('filters-modal').classList.remove('hidden');
    }

    function closeFiltersModal() {
        document.getElementById('filters-modal').classList.add('hidden');
    }

    function applyFilters() {
        const category = document.getElementById('category-filter').value;
        const distance = document.getElementById('distance-filter').value;
        const vegetarian = document.getElementById('vegetarian').checked;
        const vegan = document.getElementById('vegan').checked;
        const glutenFree = document.getElementById('gluten-free').checked;
        const showAvailable = document.getElementById('show-available').checked;
        const showExpiring = document.getElementById('show-expiring').checked;

        let url = '{{ route("recipient.available-food") }}';

        // Build query parameters
        const params = [];
        if (category) params.push(`category=${category}`);
        if (distance) params.push(`distance=${distance}`);
        if (vegetarian) params.push('vegetarian=1');
        if (vegan) params.push('vegan=1');
        if (glutenFree) params.push('gluten-free=1');
        if (showAvailable) params.push('available=1');
        if (showExpiring) params.push('expiring=1');

        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        window.location.href = url;
    }

    function resetFilters() {
        document.getElementById('category-filter').value = '';
        document.getElementById('distance-filter').value = '';
        document.getElementById('vegetarian').checked = false;
        document.getElementById('vegan').checked = false;
        document.getElementById('gluten-free').checked = false;
        document.getElementById('show-available').checked = true;
        document.getElementById('show-expiring').checked = false;
    }

    // Close modal when clicking outside
    document.getElementById('filters-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeFiltersModal();
        }
    });
</script>
@endsection