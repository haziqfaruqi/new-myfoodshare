@extends('recipient.layouts.recipient-layout')

@section('title', 'NGO Profile')

@section('content')
<form action="{{ route('recipient.ngo-profile.update') }}" method="POST" class="flex-1 flex flex-col h-screen overflow-hidden">
    @csrf
    <input type="hidden" name="_method" value="PUT">

    <!-- Welcome & Key Actions -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 p-6 md:p-8 pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">NGO Profile</h1>
            <p class="text-sm text-zinc-500 mt-1">Manage your organization's information and settings.</p>
        </div>
        <div class="flex gap-3">
            <button type="button" class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 rounded-lg text-sm font-medium hover:bg-zinc-50 transition-colors">
                <i data-lucide="eye" class="w-4 h-4 inline mr-2"></i>
                Preview
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                Save Changes
            </button>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="flex-1 overflow-y-auto px-6 md:p-8 pb-6">
        <div class="space-y-6">
            <!-- Organization Header -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <div class="flex items-start gap-6">
                    <div class="w-20 h-20 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i data-lucide="building-2" class="w-10 h-10 text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h2 class="text-xl font-bold text-zinc-900">City Shelter</h2>
                            <span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2 py-1 rounded-full">Verified</span>
                        </div>
                        <p class="text-sm text-zinc-600 mb-4">Providing shelter, meals, and support to homeless individuals and families since 2010.</p>
                        <div class="flex gap-4 text-sm">
                            <span class="text-zinc-500">Member since: January 2023</span>
                            <span class="text-zinc-500">Status: Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-zinc-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Organization Name</label>
                        <input type="text" value="City Shelter" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Organization Type</label>
                        <select class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Shelter</option>
                            <option>Food Bank</option>
                            <option>Community Kitchen</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Registration Number</label>
                        <input type="text" value="CS-2023-001" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Tax Exempt Status</label>
                        <select class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Yes</option>
                            <option>No</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-zinc-900 mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Primary Email</label>
                        <input type="email" value="info@cityshelter.org" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Alternative Email</label>
                        <input type="email" value="admin@cityshelter.org" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Primary Phone</label>
                        <input type="tel" value="+60 12-345 6789" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Alternative Phone</label>
                        <input type="tel" value="+60 17-987 6543" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Address</label>
                        <input type="text" value="123 Jalan Shelter, Kuala Lumpur, Malaysia" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Website</label>
                        <input type="url" value="https://cityshelter.org" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Organization Details -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-zinc-900 mb-4">Organization Details</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Mission Statement</label>
                        <textarea rows="3" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">To provide shelter, food, and support services to individuals and families experiencing homelessness, helping them rebuild their lives with dignity and hope.</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Description</label>
                        <textarea rows="4" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">City Shelter has been serving the community since 2010, providing not just accommodation but comprehensive support services including meals, counseling, job placement assistance, and educational programs. We currently house up to 150 individuals nightly and serve over 500 meals daily.</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Service Area</label>
                        <input type="text" value="Kuala Lumpur and surrounding areas" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-2">Annual Capacity</label>
                            <input type="number" value="150" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-2">Staff Members</label>
                            <input type="number" value="25" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-2">Volunteers</label>
                            <input type="number" value="120" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Settings -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-zinc-900 mb-4">Location Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Organization Location</label>
                        <p class="text-sm text-zinc-600 mb-3">Pin your organization's location on the map below. This location will be used for food matching and will be displayed on your dashboard and map view.</p>

                        <!-- Location Map -->
                        <div class="relative">
                            <div id="map" class="w-full rounded-lg border border-zinc-200" style="height: 500px;"></div>
                            <div class="absolute top-4 left-4 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-zinc-200 z-10">
                                <p class="text-xs font-medium text-zinc-700">
                                    <i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i>
                                    Click anywhere to pin your location
                                </p>
                            </div>
                        </div>

                            <!-- Current Location Display -->
                            <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-blue-900">Current Pinned Location</p>
                                        <p class="text-xs text-blue-700" id="current-location-text">No location set</p>
                                    </div>
                                    <button type="button" onclick="clearLocation()" class="text-xs text-red-600 hover:text-red-700">
                                        <i data-lucide="x" class="w-4 h-4 inline mr-1"></i>
                                        Clear
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Location Input Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 mb-2">Latitude</label>
                                <input type="text" name="latitude" id="latitude-input" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 3.1390">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 mb-2">Longitude</label>
                                <input type="text" name="longitude" id="longitude-input" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 101.6869">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 mb-2">Location Name</label>
                                <input type="text" name="location_name" id="location-name-input" class="w-full px-3 py-2 border border-zinc-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Main Office">
                            </div>
                        </div>

                      </div>
                </div>
            </div>
        </div>

        <!-- Hidden form fields for profile data -->
        <input type="hidden" name="organization_name" value="{{ auth()->user()->organization_name }}">
        <input type="hidden" name="contact_person" value="{{ auth()->user()->name }}">
        <input type="hidden" name="phone" value="{{ auth()->user()->phone }}">
        <input type="hidden" name="address" value="{{ auth()->user()->address }}">
        <input type="hidden" name="description" value="{{ auth()->user()->description }}">
        <input type="hidden" name="ngo_registration" value="{{ auth()->user()->ngo_registration }}">

        <!-- Show flash message if available -->
        @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
        @endif
                    </div>
                </div>
            </div>

            {{-- <!-- Documents -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-zinc-900 mb-4">Required Documents</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 border border-zinc-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i data-lucide="file-text" class="w-5 h-5 text-zinc-500"></i>
                            <div>
                                <p class="text-sm font-medium text-zinc-900">Registration Certificate</p>
                                <p class="text-xs text-zinc-500">city_shelter_cert.pdf</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-emerald-600 font-medium">Uploaded</span>
                            <button class="text-xs text-blue-600 hover:text-blue-700">Replace</button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 border border-zinc-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i data-lucide="file-text" class="w-5 h-5 text-zinc-500"></i>
                            <div>
                                <p class="text-sm font-medium text-zinc-900">Tax Exempt Certificate</p>
                                <p class="text-xs text-zinc-500">tax_exempt_cert.pdf</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-emerald-600 font-medium">Uploaded</span>
                            <button class="text-xs text-blue-600 hover:text-blue-700">Replace</button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 border border-zinc-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i data-lucide="file-text" class="w-5 h-5 text-zinc-500"></i>
                            <div>
                                <p class="text-sm font-medium text-zinc-900">Bank Statement</p>
                                <p class="text-xs text-zinc-500">Not uploaded</p>
                            </div>
                        </div>
                        <button class="text-xs text-blue-600 hover:text-blue-700">Upload</button>
                    </div>
                </div>
            </div> --}}
        <!-- Hidden form fields for profile data -->
    <input type="hidden" name="organization_name" value="{{ auth()->user()->organization_name }}">
    <input type="hidden" name="contact_person" value="{{ auth()->user()->name }}">
    <input type="hidden" name="phone" value="{{ auth()->user()->phone }}">
    <input type="hidden" name="address" value="{{ auth()->user()->address }}">
    <input type="hidden" name="description" value="{{ auth()->user()->description }}">
  <input type="hidden" name="ngo_registration" value="{{ auth()->user()->ngo_registration }}">
</form>

<!-- Show flash message if available -->
@if(session('success'))
<div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50">
    {{ session('success') }}
</div>
@endif
@endsection

@section('scripts')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    /* Map container styles */
    #map {
        border-radius: 0.5rem;
        z-index: 1;
    }

    /* Marker styling */
    .leaflet-container a.leaflet-popup-close-button {
        color: #1f2937;
    }

    /* Loading state */
    .map-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #6b7280;
        font-size: 14px;
    }
</style>

<script>
    let map;
    let marker = null;

    // Initialize Lucide icons and map
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded');

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Initialize map
        initMap();
    });

    function initMap() {
        try {
            // Check if map container exists
            const mapContainer = document.getElementById('map');
            if (!mapContainer) {
                console.error('Map container not found');
                return;
            }

            // Default location (Kuala Lumpur city center)
            let defaultLocation = [3.1390, 101.6869];
            let initialLocation = defaultLocation;

            // Check if recipient has saved coordinates
            const recipientProfile = @json($recipientProfile);
            if (recipientProfile && recipientProfile.latitude && recipientProfile.longitude) {
                initialLocation = [parseFloat(recipientProfile.latitude), parseFloat(recipientProfile.longitude)];
            }

            // Initialize the map
            map = L.map('map').setView(initialLocation, 13);

            // Add tile layer (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Add click event to map
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                placeMarker(lat, lng);
                updateInputs(lat, lng);
                updateLocationDisplay(lat, lng);
            });

            // If saved location exists, place marker there
            if (recipientProfile && recipientProfile.latitude && recipientProfile.longitude) {
                placeMarker(parseFloat(recipientProfile.latitude), parseFloat(recipientProfile.longitude));
                updateLocationDisplay(parseFloat(recipientProfile.latitude), parseFloat(recipientProfile.longitude));

                // Fill location name if available
                if (recipientProfile.location_name) {
                    document.getElementById('location-name-input').value = recipientProfile.location_name;
                }
            }

            console.log('Map initialized successfully');

        } catch (error) {
            console.error('Error initializing map:', error);
            document.getElementById('map').innerHTML = `
                <div class="map-error">
                    <div style="text-align: center; padding: 20px; color: #ef4444;">
                        <div style="font-size: 48px; margin-bottom: 10px;">❌</div>
                        <p style="margin-bottom: 10px;">Map Error</p>
                        <p style="font-size: 12px; color: #6b7280;">Unable to load the interactive map. Please use the coordinates below.</p>
                    </div>
                </div>
            `;
        }
    }

    function placeMarker(lat, lng) {
        // Remove existing marker
        if (marker) {
            map.removeLayer(marker);
        }

        // Create custom icon
        const customIcon = L.divIcon({
            className: 'custom-marker',
            html: '<div style="background-color: #ef4444; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });

        // Add new marker
        marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);

        // Add popup
        marker.bindPopup(`
            <div style="min-width: 200px; text-align: center;">
                <h3 style="margin: 0 0 5px 0; font-weight: 600; color: #1f2937;">Pinned Location</h3>
                <p style="margin: 0; color: #6b7280;">Lat: ${lat.toFixed(6)}</p>
                <p style="margin: 0; color: #6b7280;">Lng: ${lng.toFixed(6)}</p>
            </div>
        `).openPopup();
    }

    function updateInputs(lat, lng) {
        // Update latitude and longitude input fields
        document.getElementById('latitude-input').value = lat.toFixed(6);
        document.getElementById('longitude-input').value = lng.toFixed(6);
    }

    function updateLocationDisplay(lat, lng) {
        const locationText = document.getElementById('current-location-text');
        const locationName = document.getElementById('location-name-input').value || 'Pinned Location';

        if (lat && lng) {
            locationText.textContent = `${locationName} (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
        } else {
            locationText.textContent = 'No location set';
        }
    }

    // Sync inputs with location display
    function setupInputListeners() {
        const latitudeInput = document.getElementById('latitude-input');
        const longitudeInput = document.getElementById('longitude-input');
        const locationNameInput = document.getElementById('location-name-input');

        if (latitudeInput && longitudeInput) {
            // Update marker when coordinates change manually
            function updateFromInputs() {
                const lat = parseFloat(latitudeInput.value);
                const lng = parseFloat(longitudeInput.value);

                if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                    placeMarker(lat, lng);
                    map.setView([lat, lng], 13);
                    updateLocationDisplay(lat, lng);
                }
            }

            latitudeInput.addEventListener('input', updateFromInputs);
            longitudeInput.addEventListener('input', updateFromInputs);
        }

        if (locationNameInput) {
            locationNameInput.addEventListener('input', function() {
                const lat = parseFloat(latitudeInput?.value || '0');
                const lng = parseFloat(longitudeInput?.value || '0');
                if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                    updateLocationDisplay(lat, lng);
                }
            });
        }
    }

    // Setup input listeners when map is initialized
    setTimeout(setupInputListeners, 100);
</script>
@endsection