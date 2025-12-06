@extends('recipient.layouts.recipient-layout')

@section('title', 'Map View')

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-hidden">

    <!-- Welcome & Key Actions -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 p-6 md:p-8 pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Map View</h1>
            <p class="text-sm text-zinc-500 mt-1">View all available food locations on an interactive map.</p>
        </div>
        <div class="flex gap-3">
            <button class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 rounded-lg text-sm font-medium hover:bg-zinc-50 transition-colors">
                <i data-lucide="layers" class="w-4 h-4 inline mr-2"></i>
                Layers
            </button>
        </div>
    </div>

    <!-- Map Container -->
    <div class="flex-1 relative overflow-hidden">
        <div id="map" class="w-full h-full"></div>

        <!-- Map Controls -->
        <div class="absolute top-4 left-4 bg-white rounded-lg shadow-lg border border-zinc-200 p-3 space-y-3 z-[1000]">
            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1">Distance Radius</label>
                <input type="range" min="1" max="20" value="5" class="w-24" onchange="updateRadius(this.value)">
                <span class="text-xs text-zinc-500">5km</span>
            </div>
            <div>
                <label class="block text-xs font-semibold text-zinc-700 mb-1">Food Type</label>
                <select class="text-xs border border-zinc-200 rounded px-2 py-1" onchange="filterByFoodType(this.value)">
                    <option>All Types</option>
                    <option>Italian</option>
                    <option>Chinese</option>
                    <option>Indian</option>
                    <option>Mexican</option>
                    <option>American</option>
                </select>
            </div>
        </div>

        <!-- Legend -->
        <div class="absolute bottom-4 left-4 bg-white rounded-lg shadow-lg border border-zinc-200 p-3 z-[1000]">
            <h4 class="text-xs font-semibold text-zinc-700 mb-2">Legend</h4>
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                    <span class="text-xs text-zinc-600">Available Food</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                    <span class="text-xs text-zinc-600">Expiring Soon</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <span class="text-xs text-zinc-600">Your Location</span>
                </div>
            </div>
        </div>

        <!-- Leaflet Map Attribution -->
        <div class="absolute bottom-4 right-4 bg-white/80 rounded shadow px-2 py-1 text-xs text-gray-500 z-[1000]">
            <i class="fas fa-map mr-1"></i>
            Leaflet OpenStreetMap
        </div>

        <!-- Food Info Panel -->
        <div id="food-info-panel" class="absolute top-4 right-4 bg-white rounded-lg shadow-lg border border-zinc-200 p-4 w-80 hidden z-[1000]">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-zinc-900">Food Details</h3>
                <button onclick="closeFoodInfo()" class="text-zinc-400 hover:text-zinc-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div id="food-info-content">
                <!-- Dynamic content will be inserted here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let map;
    let userLocation = null;
    let foodMarkers = [];
    let radiusCircle = null;

    // Initialize the map
    function initMap() {
        // Default location (will be overridden if user location is available)
        const defaultLocation = { lat: 40.7128, lng: -74.0060 };

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

            // Add radius circle
            radiusCircle = new google.maps.Circle({
                strokeColor: '#3B82F6',
                strokeOpacity: 0.3,
                strokeWeight: 2,
                fillColor: '#3B82F6',
                fillOpacity: 0.1,
                map: map,
                center: userLocation,
                radius: 5000 // 5km in meters
            });

            // Center map on user location
            map.setCenter(userLocation);
        }
    }

    function updateRadius(radius) {
        if (radiusCircle && userLocation) {
            map.removeLayer(radiusCircle);
            radiusCircle = L.circle(userLocation, {
                color: '#3B82F6',
                fillColor: '#3B82F6',
                fillOpacity: 0.1,
                radius: radius * 1000 // Convert km to meters
            }).addTo(map);
            // Update radius display
            const radiusSpan = document.querySelector('.absolute.top-4.left-4 span');
            if (radiusSpan) {
                radiusSpan.textContent = radius + 'km';
            }
        }
    }

    function filterByFoodType(type) {
        // Placeholder for food type filtering logic
        console.log('Filtering by food type:', type);

        // Update map view based on selection
        if (type === 'all') {
            // Show all markers
            foodMarkers.forEach(marker => marker.setOpacity(1));
        } else {
            // Filter markers based on type (this would be implemented with actual data)
            foodMarkers.forEach(marker => marker.setOpacity(0.3));
        }
    }

    function closeFoodInfo() {
        document.getElementById('food-info-panel').classList.add('hidden');
    }

    // Load Leaflet CSS and JS
    function loadLeaflet() {
        // Add Font Awesome for icons
        const fontAwesomeLink = document.createElement('link');
        fontAwesomeLink.rel = 'stylesheet';
        fontAwesomeLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
        document.head.appendChild(fontAwesomeLink);

        // Add Leaflet CSS
        const cssLink = document.createElement('link');
        cssLink.rel = 'stylesheet';
        cssLink.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(cssLink);

        // Add Leaflet JS
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.async = true;
        script.defer = true;
        script.onload = initLeafletMap;
        document.head.appendChild(script);
    }

    // Initialize Leaflet map
    function initLeafletMap() {
        const mapContainer = document.getElementById('map');

        // Default location (Kuala Lumpur)
        const defaultLocation = [3.1390, 101.6869];

        // Initialize the map
        map = L.map('map').setView(defaultLocation, 12);

        // Add tile layer (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Try to get user location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    userLocation = [position.coords.latitude, position.coords.longitude];
                    updateUserLocation();
                    loadFoodMarkers();
                },
                (error) => {
                    console.log('Geolocation error:', error);
                    userLocation = defaultLocation;
                    updateUserLocation();
                    loadFoodMarkers();
                }
            );
        } else {
            userLocation = defaultLocation;
            updateUserLocation();
            loadFoodMarkers();
        }
    }

    function updateUserLocation() {
        if (userLocation) {
            // Add user location marker
            const userMarker = L.marker(userLocation, {
                icon: L.divIcon({
                    className: 'user-location-marker',
                    html: '<div style="background-color: #3B82F6; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                })
            }).addTo(map);

            userMarker.bindPopup('Your Location').openPopup();

            // Add radius circle
            radiusCircle = L.circle(userLocation, {
                color: '#3B82F6',
                fillColor: '#3B82F6',
                fillOpacity: 0.1,
                radius: 5000 // 5km in meters
            }).addTo(map);
        }
    }

    function loadFoodMarkers() {
        // Sample food data - in a real app, this would come from your API
        const foodData = [
            {
                id: 1,
                name: 'Classic Pizza Restaurant',
                lat: 3.1343,
                lng: 101.6854,
                food: '2 Large Pepperoni Pizzas',
                distance: '1.2 km'
            },
            {
                id: 2,
                name: 'Burger Palace',
                lat: 3.1478,
                lng: 101.6958,
                food: '20 Cheeseburgers & Fries',
                distance: '2.5 km'
            },
            {
                id: 3,
                name: 'Thai Garden Restaurant',
                lat: 3.1209,
                lng: 101.6779,
                food: 'Pad Thai & Spring Rolls',
                distance: '3.1 km'
            },
            {
                id: 4,
                name: 'Italian Bistro',
                lat: 3.1567,
                lng: 101.7012,
                food: 'Spaghetti & Meatballs',
                distance: '4.2 km'
            },
            {
                id: 5,
                name: 'Chinese Kitchen',
                lat: 3.1289,
                lng: 101.6723,
                food: 'Fried Rice & Dumplings',
                distance: '4.8 km'
            }
        ];

        // Add markers for each food location
        foodData.forEach(food => {
            const distance = calculateDistance(userLocation[0], userLocation[1], food.lat, food.lng);
            const isExpiring = distance < 3; // Food within 3km considered expiring soon

            const markerColor = isExpiring ? '#f97316' : '#10b981'; // Orange for expiring, green for available

            const marker = L.marker([food.lat, food.lng], {
                icon: L.divIcon({
                    className: 'food-marker',
                    html: `<div style="background-color: ${markerColor}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                })
            }).addTo(map);

            marker.bindPopup(`
                <div style="min-width: 200px;">
                    <h3 style="margin: 0 0 8px 0; font-weight: 600; color: #1f2937;">${food.name}</h3>
                    <p style="margin: 0 0 8px 0; color: #6b7280;">${food.food}</p>
                    <p style="margin: 0 0 8px 0; color: #374151; font-size: 14px;">
                        <i class="fas fa-location-arrow" style="margin-right: 4px;"></i>
                        ${food.distance} away
                    </p>
                    <button onclick="requestFood(${food.id})" style="background-color: #3b82f6; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                        Request Food
                    </button>
                </div>
            `);

            foodMarkers.push(marker);
        });
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        // Haversine formula for distance calculation
        const R = 6371; // Earth's radius in km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function requestFood(foodId) {
        alert('Food request functionality would be implemented here for food ID: ' + foodId);
    }

    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        loadLeaflet();
    });

    // Initialize Lucide icons
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection