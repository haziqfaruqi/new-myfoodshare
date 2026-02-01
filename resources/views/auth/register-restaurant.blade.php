@extends('layouts.app')

@section('content')
<!-- Restaurant Registration Enhanced UI -->
<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-zinc-50">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md border-b border-emerald-100/50 sticky top-0 z-50">
        <div class="px-4 sm:px-6 lg:px-8 w-full">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center gap-3">
                    @php
                        $logo = \App\Models\Setting::get('site_logo');
                    @endphp
                    @if($logo)
                        <img src="{{ asset($logo) }}" alt="Logo" class="h-10 w-auto object-contain">
                    @else
                        <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="leaf" class="w-6 h-6 text-white"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">MyFoodshare</h1>
                        <p class="text-xs text-emerald-600 font-medium">Restaurant Partner Portal</p>
                    </div>
                </div>
                <nav class="hidden md:flex items-center gap-6 text-sm">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-emerald-600 transition-colors">Home</a>
                    <a href="#" class="text-emerald-600 font-medium">Register</a>
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-emerald-600 transition-colors">Login</a>
                </nav>
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/10 via-transparent to-emerald-500/5"></div>
        <div class="px-4 sm:px-6 lg:px-8 py-12 w-full">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-800 px-4 py-2 rounded-full text-sm font-medium mb-6">
                    <i data-lucide="store" class="w-4 h-4"></i>
                    Restaurant Partner Registration
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Join the Movement Against Food Waste
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Connect surplus food with those in need. Reduce waste, save costs, and make a positive impact on your community.
                </p>
                <div class="flex justify-center gap-8 mt-8 text-sm">
                    <div class="flex items-center gap-2 text-gray-600">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                        <span>Secure & Compliant</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <i data-lucide="users" class="w-4 h-4 text-emerald-600"></i>
                        <span>Community Impact</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <i data-lucide="trending-up" class="w-4 h-4 text-emerald-600"></i>
                        <span>Cost Savings</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Form -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Progress Steps -->
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center text-white text-sm font-bold">1</div>
                            <span class="text-white font-medium">Basic Info</span>
                        </div>
                        <div class="w-16 h-0.5 bg-emerald-400"></div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-emerald-400 rounded-full flex items-center justify-center text-white text-sm font-bold">2</div>
                            <span class="text-emerald-100 font-medium">Restaurant Details</span>
                        </div>
                        <div class="w-16 h-0.5 bg-emerald-400/50"></div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-emerald-400/30 rounded-full flex items-center justify-center text-white text-sm font-bold">3</div>
                            <span class="text-emerald-100/70 font-medium">Review & Submit</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <form class="p-6 sm:p-8" action="{{ route('register.restaurant.store') }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="space-y-8">
                    <!-- Basic Information Section -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="user" class="w-5 h-5 text-emerald-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                                <p class="text-sm text-gray-500">Your contact and account details</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Contact Person Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input id="name" name="name" type="text" required
                                           value="{{ old('name') }}"
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                           placeholder="John Doe">
                                </div>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input id="email" name="email" type="email" autocomplete="email" required
                                           value="{{ old('email') }}"
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                           placeholder="john@example.com">
                                </div>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input id="phone" name="phone" type="tel" required
                                           value="{{ old('phone') }}"
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                           placeholder="+60123456789">
                                </div>
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input id="password" name="password" type="password" autocomplete="new-password" required
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                           placeholder="Create a password">
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirm Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                           placeholder="Confirm your password">
                                </div>
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Restaurant Information Section -->
                    <div class="space-y-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="store" class="w-5 h-5 text-emerald-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Restaurant Information</h3>
                                <p class="text-sm text-gray-500">Your business details for verification</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label for="restaurant_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Restaurant/Business Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="building" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input id="restaurant_name" name="restaurant_name" type="text" required
                                           value="{{ old('restaurant_name') }}"
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                           placeholder="John's Italian Restaurant">
                                </div>
                                @error('restaurant_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Business Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="map-pin" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <textarea id="address" name="address" rows="3" required
                                              class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors resize-none"
                                              placeholder="123 Main Street, Shah Alam, Selangor">{{ old('address') }}</textarea>
                                </div>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Location Picker Map -->
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i data-lucide="map" class="w-4 h-4 inline mr-1"></i>
                                    Restaurant Location
                                </label>
                                <p class="text-xs text-gray-500 mb-2">Click on the map to set your restaurant's location</p>

                                <!-- Hidden inputs for coordinates -->
                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">

                                <!-- Map Container -->
                                <div id="location-map" class="w-full h-64 rounded-lg border border-gray-300 relative z-10">
                                    <div class="absolute inset-0 flex items-center justify-center bg-gray-100 text-gray-500">
                                        <div class="text-center">
                                            <i data-lucide="map-pin" class="w-8 h-8 mx-auto mb-2 text-gray-400"></i>
                                            <p class="text-sm">Click to enable location picker</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Location Info Display -->
                                <div id="location-info" class="mt-2 p-3 bg-emerald-50 border border-emerald-200 rounded-lg hidden">
                                    <div class="flex items-center gap-2 text-sm text-emerald-700">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                        <span>Location selected: <span id="coords-display" class="font-medium"></span></span>
                                    </div>
                                </div>

                                <!-- Use Current Location Button -->
                                <button type="button" id="use-current-location" class="mt-2 inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i data-lucide="navigation" class="w-4 h-4"></i>
                                    Use My Current Location
                                </button>
                            </div>

                            <div>
                                <label for="cuisine_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cuisine Type <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="utensils" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <select id="cuisine_type" name="cuisine_type" required
                                            class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors appearance-none bg-white">
                                        <option value="">Select cuisine type</option>
                                        <option value="Malaysian" {{ old('cuisine_type') == 'Malaysian' ? 'selected' : '' }}>Malaysian</option>
                                        <option value="Chinese" {{ old('cuisine_type') == 'Chinese' ? 'selected' : '' }}>Chinese</option>
                                        <option value="Indian" {{ old('cuisine_type') == 'Indian' ? 'selected' : '' }}>Indian</option>
                                        <option value="Western" {{ old('cuisine_type') == 'Western' ? 'selected' : '' }}>Western</option>
                                        <option value="Italian" {{ old('cuisine_type') == 'Italian' ? 'selected' : '' }}>Italian</option>
                                        <option value="Japanese" {{ old('cuisine_type') == 'Japanese' ? 'selected' : '' }}>Japanese</option>
                                        <option value="Thai" {{ old('cuisine_type') == 'Thai' ? 'selected' : '' }}>Thai</option>
                                        <option value="International" {{ old('cuisine_type') == 'International' ? 'selected' : '' }}>International</option>
                                        <option value="Other" {{ old('cuisine_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                                </div>
                                @error('cuisine_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="business_license" class="block text-sm font-medium text-gray-700 mb-2">
                                    Business License Number <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="file-text" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input id="business_license" name="business_license" type="text" required
                                           value="{{ old('business_license') }}"
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                           placeholder="e.g., BL2024001">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Required for compliance with Malaysian Food Act 1983</p>
                                @error('business_license')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="space-y-6 pt-6 border-t border-gray-200">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-1">
                                <input id="terms" name="terms" type="checkbox" required
                                       class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            </div>
                            <div class="flex-1">
                                <label for="terms" class="text-sm font-medium text-gray-700">
                                    <span class="text-red-500">*</span> Terms and Conditions
                                </label>
                                <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                                    I confirm that all information provided is accurate and my establishment complies with Malaysian Food Act 1983. I understand that MyFoodshare may verify my business license and that my account will be set to pending status until approval is granted.
                                </p>
                            </div>
                        </div>
                        @error('terms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6">
                        <button type="submit"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-semibold rounded-lg hover:from-emerald-700 hover:to-emerald-800 focus:ring-4 focus:ring-emerald-500/20 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <span class="flex items-center justify-center gap-2">
                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                                Register Restaurant Account
                            </span>
                        </button>

                        <button type="button"
                                onclick="window.location.href='{{ route('home') }}'"
                                class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-500/20 transition-all">
                            <span class="flex items-center justify-center gap-2">
                                <i data-lucide="home" class="w-5 h-5"></i>
                                Back to Home
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Additional Links -->
                <div class="mt-8 pt-8 border-t border-gray-200 text-center">
                    <div class="flex flex-col sm:flex-row justify-center items-center gap-4 text-sm">
                        <a href="{{ route('register.recipient') }}" class="flex items-center gap-2 text-emerald-600 hover:text-emerald-700 font-medium">
                            <i data-lucide="heart-handshake" class="w-4 h-4"></i>
                            Register as Recipient
                        </a>
                        <span class="text-gray-400">•</span>
                        <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-700">
                            ← Back to general registration
                        </a>
                        <span class="text-gray-400">•</span>
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-700">
                            Already have an account? Sign in
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Initialize Lucide Icons
    lucide.createIcons();

    // Mobile menu toggle
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        // Simple mobile menu functionality
        alert('Mobile menu would open here');
    });

    // Location Picker Map
    let map;
    let marker;
    const DEFAULT_LAT = 3.1390; // Kuala Lumpur
    const DEFAULT_LNG = 101.6869;

    function initLocationMap() {
        const mapContainer = document.getElementById('location-map');
        if (!mapContainer) return;

        // Clear the placeholder
        mapContainer.innerHTML = '';

        // Initialize map
        map = L.map('location-map').setView([DEFAULT_LAT, DEFAULT_LNG], 12);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Add click handler to map
        map.on('click', function(e) {
            setMarkerLocation(e.latlng.lat, e.latlng.lng);
        });

        // Load existing coordinates if available
        const existingLat = document.getElementById('latitude').value;
        const existingLng = document.getElementById('longitude').value;

        if (existingLat && existingLng) {
            setMarkerLocation(parseFloat(existingLat), parseFloat(existingLng));
        } else {
            // Add default marker
            setMarkerLocation(DEFAULT_LAT, DEFAULT_LNG);
        }
    }

    function setMarkerLocation(lat, lng) {
        // Remove existing marker if any
        if (marker) {
            map.removeLayer(marker);
        }

        // Add new marker
        marker = L.marker([lat, lng], {
            draggable: true
        }).addTo(map);

        // Update hidden inputs
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);

        // Update display
        document.getElementById('location-info').classList.remove('hidden');
        document.getElementById('coords-display').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

        // Center map on new location
        map.setView([lat, lng], 14);

        // Handle marker drag
        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            setMarkerLocation(position.lat, position.lng);
        });
    }

    // Use current location button
    document.getElementById('use-current-location')?.addEventListener('click', function() {
        if (navigator.geolocation) {
            this.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Getting location...';
            lucide.createIcons();

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    setMarkerLocation(lat, lng);

                    document.getElementById('use-current-location').innerHTML = '<i data-lucide="navigation" class="w-4 h-4"></i> Use My Current Location';
                    lucide.createIcons();
                },
                function(error) {
                    alert('Unable to get your location. Please click on the map to set your location manually.');
                    document.getElementById('use-current-location').innerHTML = '<i data-lucide="navigation" class="w-4 h-4"></i> Use My Current Location';
                    lucide.createIcons();
                }
            );
        } else {
            alert('Geolocation is not supported by your browser. Please click on the map to set your location manually.');
        }
    });

    // Initialize map when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initLocationMap();
    });
</script>

<style>
    #location-map {
        z-index: 1;
    }
    .leaflet-container {
        font-family: inherit;
    }
</style>
@endsection