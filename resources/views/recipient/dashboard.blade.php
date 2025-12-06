@extends('recipient.layouts.recipient-layout')

@section('title', 'Dashboard')

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-hidden">

    <!-- Welcome & Key Actions -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 p-6 md:p-8 pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Dashboard</h1>
            <p class="text-sm text-zinc-500 mt-1">Manage your pickups and discover food nearby.</p>
        </div>
        <div class="flex gap-3">
            <button
                onclick="{{ $upcomingPickups->whereIn('status', ['approved', 'scheduled'])->count() > 0 ? 'document.getElementById(\'verification-modal\').classList.remove(\'hidden\')' : 'return false' }}"
                class="inline-flex items-center gap-2 px-4 py-2 {{ $upcomingPickups->whereIn('status', ['approved', 'scheduled'])->count() > 0 ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-600/20' : 'bg-zinc-400 text-zinc-100 cursor-not-allowed' }} rounded-lg text-sm font-medium transition-all"
                {{ $upcomingPickups->whereIn('status', ['approved', 'scheduled'])->count() > 0 ? '' : 'disabled' }}>
                <i data-lucide="scan-line" class="w-4 h-4"></i>
                Verify Pickup
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 px-6 md:px-8 pb-4">
        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Active Matches</span>
                <div class="p-1.5 bg-emerald-50 rounded-lg">
                    <i data-lucide="link" class="w-4 h-4 text-emerald-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $stats['active_matches'] }}</span>
                <span class="text-xs font-medium text-zinc-500">Pickups today</span>
            </div>
        </div>

        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Meals Recovered</span>
                <div class="p-1.5 bg-orange-50 rounded-lg">
                    <i data-lucide="utensils" class="w-4 h-4 text-orange-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $stats['completed_pickups'] }}</span>
                <span class="text-xs font-medium text-emerald-600">+5 this week</span>
            </div>
        </div>

        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Est. Money Saved</span>
                <div class="p-1.5 bg-green-50 rounded-lg">
                    <i data-lucide="dollar-sign" class="w-4 h-4 text-green-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">RM{{ $stats['total_food_received'] * 50 }}</span>
                <span class="text-xs font-medium text-zinc-500">Total value</span>
            </div>
        </div>

        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Pending Approval</span>
                <div class="p-1.5 bg-amber-50 rounded-lg">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $stats['pending_requests'] }}</span>
                <span class="text-xs font-medium text-zinc-500">Requests sent</span>
            </div>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-4 px-6 md:px-8 pb-6 overflow-hidden">
        <!-- Left Column: Food Listings (Scrollable) -->
        <div class="lg:col-span-2 space-y-4 flex flex-col h-full">
            <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-zinc-200 shadow-sm">
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
            @if ($nearbyFoodListings->count() > 0 && $nearbyFoodListings->first())
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 p-3 rounded-xl flex items-start gap-3 relative overflow-hidden">
                <div class="bg-white p-1.5 rounded-full shadow-sm z-10">
                    <i data-lucide="sparkles" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div class="z-10">
                    <h3 class="text-sm font-semibold text-blue-900">Smart Match Found!</h3>
                    <p class="text-xs text-blue-700 mt-0.5">We found {{$nearbyFoodListings->first()->quantity}} of {{$nearbyFoodListings->first()->food_type}} at <span class="font-bold">{{$nearbyFoodListings->first()->restaurantProfile->restaurant_name ?? $nearbyFoodListings->first()->creator->name}}</span> ({{$nearbyFoodListings->first()->distance}}km away) that matches your preferences.</p>
                    <div class="mt-2 flex gap-2">
                        <form action="{{ route('recipient.matches.store', $nearbyFoodListings->first()->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors">Request Now</button>
                        </form>
                        <button class="px-3 py-1.5 bg-white text-blue-600 border border-blue-200 text-xs font-medium rounded-lg hover:bg-blue-50">View Details</button>
                    </div>
                </div>
                <i data-lucide="zap" class="absolute right-[-10px] top-[-10px] w-32 h-32 text-blue-100 opacity-50 rotate-12"></i>
            </div>
            @endif

            <!-- Food Listings (Scrollable) -->
            <div class="flex-1 overflow-y-auto space-y-3 pr-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($nearbyFoodListings as $listing)
                    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden shadow-sm group hover:border-blue-300 transition-all">
                        <div class="h-32 bg-zinc-100 relative">
                            <img src="https://images.unsplash.com/photo-1584345604476-8ec5e12e42dd?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-3 left-3 text-white">
                                <p class="font-semibold text-sm">{{ $listing->food_type ?? 'Food' }}</p>
                                <p class="text-xs opacity-90">{{ $listing->quantity ?? 'N/A' }}</p>
                            </div>
                            <span class="absolute top-3 right-3 bg-white/90 backdrop-blur text-zinc-800 text-[10px] font-bold px-2 py-0.5 rounded shadow-sm">{{ $listing->distance }} km</span>
                        </div>
                        <div class="p-3">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 rounded-full bg-orange-100 flex items-center justify-center text-[9px] font-bold text-orange-700">
                                        {{ strtoupper(substr($listing->restaurantProfile->restaurant_name ?? $listing->creator->name, 0, 2)) }}
                                    </div>
                                    <span class="text-xs font-medium text-zinc-600">{{ $listing->restaurantProfile->restaurant_name ?? $listing->creator->name }}</span>
                                </div>
                                @if ($listing->expiry_date == now()->toDateString() && $listing->expiry_time <= now()->format('H:i'))
                                <span class="text-xs text-rose-600 font-medium flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3 h-3"></i> Expired
                                </span>
                                @else
                                <span class="text-xs text-emerald-600 font-medium flex items-center gap-1">
                                    <i data-lucide="calendar" class="w-3 h-3"></i> {{ $listing->expiry_date }}
                                </span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-zinc-100">
                                <span class="text-xs text-zinc-500">{{ $listing->quantity }} {{ $listing->unit ?? 'units' }}</span>
                                <form action="{{ route('recipient.matches.store', $listing->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs bg-zinc-900 text-white px-3 py-1 rounded-lg hover:bg-zinc-700 transition-colors">Request</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @if ($nearbyFoodListings->isEmpty())
                    <div class="col-span-2 text-center py-8">
                        <i data-lucide="map-pin" class="w-12 h-12 text-zinc-300 mx-auto mb-3"></i>
                        <p class="text-sm text-zinc-500">No food available within 5km radius</p>
                        <p class="text-xs text-zinc-400 mt-1">Check back later for new listings</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Pickups & Map -->
        <div class="space-y-4 flex flex-col h-full">
            <!-- Upcoming Pickups -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-4 flex-shrink-0">
                <h3 class="text-sm font-semibold text-zinc-900 mb-3">Upcoming Pickups</h3>
                <div class="space-y-3">
                    @foreach ($upcomingPickups as $pickup)
                    <div class="p-3 rounded-lg
                        @if($pickup->status == 'approved' && $pickup->pickup_scheduled_at <= now())
                            bg-emerald-50 border border-emerald-100
                        @elseif($pickup->status == 'scheduled')
                            bg-blue-50 border border-blue-100
                        @else
                            bg-zinc-50 border border-zinc-100
                        @endif
                        relative overflow-hidden">
                        @if($pickup->status == 'approved' && $pickup->pickup_scheduled_at <= now())
                        <div class="absolute right-0 top-0 p-1">
                            <span class="bg-white text-emerald-700 text-[8px] font-bold px-1.5 py-0.5 rounded shadow-sm uppercase">Ready</span>
                        </div>
                        @endif
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded bg-white flex items-center justify-center shrink-0
                                @if($pickup->status == 'approved' && $pickup->pickup_scheduled_at <= now())
                                    shadow-sm text-emerald-600
                                @elseif($pickup->status == 'scheduled')
                                    border border-blue-200 text-blue-600
                                @else
                                    border border-zinc-200 text-zinc-400
                                @endif">
                                @if($pickup->status == 'approved' && $pickup->pickup_scheduled_at <= now())
                                    <i data-lucide="package-check" class="w-4 h-4"></i>
                                @elseif($pickup->status == 'scheduled')
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                @else
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-zinc-900 truncate">{{ $pickup->foodListing->restaurantProfile->restaurant_name ?? $pickup->foodListing->creator->name }}</p>
                                <p class="text-xs text-zinc-500">Code: <span class="font-mono font-bold text-zinc-900">{{ str_pad($pickup->id, 4, '0', STR_PAD_LEFT) }}</span></p>
                                @if($pickup->pickup_scheduled_at)
                                <div class="flex items-center gap-1 mt-1 text-[10px] text-zinc-500">
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    <span>{{ \Carbon\Carbon::parse($pickup->pickup_scheduled_at)->format('M j, Y g:i A') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @if($pickup->status == 'approved' && $pickup->pickup_scheduled_at <= now())
                        <button onclick="openVerificationModal({{ $pickup->foodListing->restaurantProfile->restaurant_name ?? $pickup->foodListing->creator->name }}, {{ $pickup->id }})" class="w-full mt-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded hover:bg-emerald-700 transition-colors">
                            Arrived
                        </button>
                        @endif
                    </div>
                    @endforeach
                    @if ($upcomingPickups->isEmpty())
                    <div class="text-center py-6">
                        <i data-lucide="calendar-x" class="w-8 h-8 text-zinc-300 mx-auto mb-2"></i>
                        <p class="text-sm text-zinc-500">No upcoming pickups</p>
                        <p class="text-xs text-zinc-400 mt-1">Your scheduled pickups will appear here</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Map (Fills remaining height) -->
            <div class="bg-zinc-100 rounded-xl h-full w-full relative overflow-hidden border border-zinc-200 flex-1">
                <div id="map" class="w-full h-full"></div>

                <div class="absolute bottom-2 left-2 bg-white/90 backdrop-blur px-2 py-1 rounded shadow-sm text-[10px] font-medium text-zinc-600">
                    <i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i>
                    {{ $nearbyFoodListings->count() }} food locations within 5km
                </div>
                <button onclick="openFullMap()" class="absolute top-2 right-2 bg-white shadow-md text-zinc-900 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-zinc-100 transition-colors">
                    <i data-lucide="maximize-2" class="w-3 h-3 inline mr-1"></i>
                    Full Map
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- Verification & Rating Modal -->
<div id="verification-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('verification-modal').classList.add('hidden')"></div>
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-zinc-100 text-center">
            <h3 class="lg font-bold text-zinc-900">Complete Pickup</h3>
            <p class="text-sm text-zinc-500" id="verification-restaurant-name">Italian Bistro • Order #<span id="verification-order-id">8829</span></p>
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
@endsection

@section('scripts')
<script>
    let map;
    let userLocation = null;
    let foodMarkers = [];

    // Initialize the map
    function initMap() {
        // Default location (will be overridden if user location is available)
        const defaultLocation = { lat: 40.7128, lng: -74.0060 }; // New York

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: defaultLocation,
            styles: [
                {
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{ visibility: 'off' }]
                }
            ]
        });

        // Try to get user location
            if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    updateUserLocation();
                },
                (error) => {
                    console.log('Geolocation error:', error);
                    useDefaultLocation();
                }
            );
        } else {
            useDefaultLocation();
        }

        // Add food listings to map if they exist
        @json($nearbyFoodListings)
        if (foodListings && foodListings.length > 0) {
            addFoodMarkersToMap(foodListings);
        }
    }

    function useDefaultLocation() {
        userLocation = { lat: 40.7128, lng: -74.0060 };
        updateUserLocation();
    }

    function updateUserLocation() {
        if (userLocation) {
            // Add user location marker
            new google.maps.Marker({
                position: userLocation,
                map: map,
                title: 'Your Location',
                icon: {
                    url: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAiIGhlaWdodD0iMzAiIHZpZXdCb3g9IjAgMCAzIDMwIiBmaWxsPSIjZTVlN2ViIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMyIgaGVpZ2h0PSIyIiBmaWxsPSIjNWFhN2RiIi8+Cjx0ZXh0IHg9IjUiIHk9IjUiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iI2MxYzNjMyI+PC90ZXh0Pgo8L3N2Zz4=',
                    scaledSize: new google.maps.Size(24, 24)
                }
            });

            // Center map on user location
            map.setCenter(userLocation);

            // Add sample food locations if none from database
            if (foodMarkers.length === 0) {
                addSampleFoodLocations();
            }
        }
    }

    function addFoodMarkersToMap(foodListings) {
        foodListings.forEach(listing => {
            if (listing.latitude && listing.longitude) {
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(listing.latitude), lng: parseFloat(listing.longitude) },
                    map: map,
                    title: listing.restaurantProfile?.restaurant_name || listing.creator?.name,
                    icon: {
                        url: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAiIGhlaWdodD0iMzAiIHZpZXdCb3g9IjAgMCAzIDMwIiBmaWxsPSIjOTdmZGE4IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMyIgaGVpZ2h0PSIyIiBmaWxsPSIjM2MxODNlIi8+Cjx0ZXh0IHg9IjUiIHk9IjUiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iI2ZmZiI+PC90ZXh0Pgo8L3N2Zz4=',
                        scaledSize: new google.maps.Size(24, 24)
                    }
                });

                // Add info window
                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div style="padding: 10px;">
                            <h4 style="margin: 0 0 5px 0; font-weight: 600;">${listing.restaurantProfile?.restaurant_name || listing.creator?.name}</h4>
                            <p style="margin: 0 0 5px 0; font-size: 14px;">${listing.food_type || 'Food'} • ${listing.quantity}</p>
                            <p style="margin: 0; font-size: 12px; color: #666;">${listing.distance} km away</p>
                        </div>
                    `
                });

                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });

                foodMarkers.push(marker);
            }
        });
    }

    function addSampleFoodLocations() {
        const sampleLocations = [
            { lat: userLocation.lat + 0.01, lng: userLocation.lng + 0.01, name: 'Sample Restaurant 1' },
            { lat: userLocation.lat - 0.01, lng: userLocation.lng + 0.01, name: 'Sample Restaurant 2' },
            { lat: userLocation.lat + 0.01, lng: userLocation.lng - 0.01, name: 'Sample Restaurant 3' }
        ];

        sampleLocations.forEach(location => {
            const marker = new google.maps.Marker({
                position: location,
                map: map,
                title: location.name,
                icon: {
                    url: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAiIGhlaWdodD0iMzAiIHZpZXdCb3g9IjAgMCAzIDMwIiBmaWxsPSIjOTdmZGE4IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMyIgaGVpZ2h0PSIyIiBmaWxsPSIjM2MxODNlIi8+Cjx0ZXh0IHg9IjUiIHk9IjUiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iI2ZmZiI+PC90ZXh0Pgo8L3N2Zz4=',
                    scaledSize: new google.maps.Size(24, 24)
                }
            });

            foodMarkers.push(marker);
        });
    }

    function openVerificationModal(restaurantName, orderId) {
        document.getElementById('verification-restaurant-name').textContent = restaurantName + ' • Order #<span id="verification-order-id">' + orderId + '</span>';
        document.getElementById('verification-modal').classList.remove('hidden');
    }

    function openFullMap() {
        // Placeholder for full map functionality
        alert('Full map view would open here');
    }

    // Initialize Lucide icons with enhanced retry logic
    function initializeIcons() {
        const maxRetries = 10;
        let retryCount = 0;

        function tryInitializeIcons() {
            console.log(`Lucide initialization attempt ${retryCount + 1}/${maxRetries}`);
            console.log('Lucide type:', typeof lucide);

            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                try {
                    lucide.createIcons();
                    console.log('Lucide icons initialized successfully');

                    // Force refresh icons
                    setTimeout(() => {
                        const icons = document.querySelectorAll('[data-lucide]');
                        console.log('Found icons:', icons.length);
                        if (icons.length > 0) {
                            lucide.createIcons();
                            console.log('Icons refreshed successfully');
                        }
                    }, 200);

                    return;
                } catch (error) {
                    console.error('Error initializing Lucide icons:', error);
                }
            } else if (retryCount < maxRetries) {
                retryCount++;
                console.log(`Lucide not loaded, retry ${retryCount}/${maxRetries}`);
                setTimeout(tryInitializeIcons, 200);
            } else {
                console.error('Lucide icons failed to load after multiple attempts');

                // Fallback: Try to load Lucide from CDN if not available
                if (typeof lucide === 'undefined') {
                    console.log('Attempting to load Lucide from CDN...');
                    const script = document.createElement('script');
                    script.src = 'https://unpkg.com/lucide@latest';
                    script.onload = () => {
                        console.log('Lucide loaded from CDN');
                        lucide.createIcons();
                    };
                    script.onerror = () => {
                        console.error('Failed to load Lucide from CDN');
                    };
                    document.head.appendChild(script);
                }
            }
        }

        tryInitializeIcons();
    }

    // Load Google Maps script
    function loadGoogleMaps() {
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap&libraries=places';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }

    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing...');

        // Initialize icons immediately and with multiple delays
        initializeIcons();

        // Also initialize after different delays
        setTimeout(() => initializeIcons(), 500);
        setTimeout(() => initializeIcons(), 1000);
        setTimeout(() => initializeIcons(), 2000);

        loadGoogleMaps();
    });

    // Also initialize when the page is fully loaded
    window.addEventListener('load', function() {
        setTimeout(() => initializeIcons(), 300);
    });

    </script>
@endsection