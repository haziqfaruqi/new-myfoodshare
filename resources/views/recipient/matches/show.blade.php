@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Match Details</h1>
                    <p class="text-sm text-gray-600">View your food match details</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('recipient.matches.index') }}"
                       class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition-colors">
                        Back to Matches
                    </a>
                    <span class="text-sm text-gray-600">Welcome, {{ Auth::user()->name }}</span>
                    <a href="{{ route('logout') }}" class="px-4 py-2 text-sm text-red-600 hover:text-red-800 transition-colors">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Match Details -->
            <div class="lg:col-span-2">
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Match Status</h2>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                            @if ($match->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif ($match->status == 'approved') bg-green-100 text-green-800
                            @elseif ($match->status == 'scheduled') bg-blue-100 text-blue-800
                            @elseif ($match->status == 'in_progress') bg-purple-100 text-purple-800
                            @elseif ($match->status == 'completed') bg-gray-100 text-gray-800
                            @elseif ($match->status == 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($match->status) }}
                        </span>
                    </div>

                    @if ($match->status == 'approved')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-green-800">
                                    Your match has been approved! Please contact the restaurant to schedule pickup.
                                </p>
                            </div>
                        </div>
                    @endif

                    @if ($match->status == 'scheduled')
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm text-blue-800">
                                    Your pickup has been scheduled! Please arrive at the scheduled time.
                                </p>
                            </div>
                            @if ($match->pickup_scheduled_at)
                                <div class="mt-2">
                                    <p class="text-sm font-medium text-blue-900">
                                        Scheduled Pickup: {{ $match->pickup_scheduled_at->format('F j, Y, g:i A') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($match->status == 'completed')
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-gray-800">
                                    Your pickup has been completed successfully! Thank you for using MyFoodshare.
                                </p>
                            </div>
                            @if ($match->rating)
                                <div class="mt-2">
                                    <p class="text-sm font-medium text-gray-900">
                                        Rating: {{ $match->rating }}/5 Stars
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($match->status == 'rejected')
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <p class="text-sm text-red-800">
                                    Your match has been rejected by the restaurant.
                                </p>
                            </div>
                            @if ($match->notes)
                                <div class="mt-2">
                                    <p class="text-sm font-medium text-red-900">
                                        Reason: {{ $match->notes }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Match Timeline -->
                    <div class="border-l-2 border-gray-200 pl-4 ml-4">
                        <div class="mb-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-3 h-3 bg-blue-600 rounded-full mt-1"></div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Match Created</p>
                                    <p class="text-sm text-gray-600">{{ $match->created_at->format('F j, Y, g:i A') }}</p>
                                </div>
                            </div>
                        </div>

                        @if ($match->approved_at)
                            <div class="mb-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-3 h-3 bg-green-600 rounded-full mt-1"></div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Match Approved</p>
                                        <p class="text-sm text-gray-600">{{ $match->approved_at->format('F j, Y, g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($match->pickup_scheduled_at)
                            <div class="mb-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-3 h-3 bg-blue-600 rounded-full mt-1"></div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Pickup Scheduled</p>
                                        <p class="text-sm text-gray-600">{{ $match->pickup_scheduled_at->format('F j, Y, g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($match->completed_at)
                            <div class="mb-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-3 h-3 bg-gray-600 rounded-full mt-1"></div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Pickup Completed</p>
                                        <p class="text-sm text-gray-600">{{ $match->completed_at->format('F j, Y, g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Food Details -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Food Details</h3>
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">Food Item</h4>
                            <p class="text-sm text-gray-900">{{ $match->foodListing->food_name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">Description</h4>
                            <p class="text-sm text-gray-600">{{ $match->foodListing->description }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-1">Category</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($match->foodListing->category) }}
                                </span>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-1">Quantity</h4>
                                <p class="text-sm text-gray-900">{{ $match->foodListing->quantity }} {{ $match->foodListing->unit }}</p>
                            </div>
                        </div>
                        @if ($match->distance)
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-1">Distance</h4>
                                <p class="text-sm text-gray-900">{{ number_format($match->distance, 1) }} km away</p>
                            </div>
                        @endif
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-1">Expiry Date</h4>
                                <p class="text-sm text-gray-900">{{ $match->foodListing->expiry_date->format('F j, Y') }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-1">Expiry Time</h4>
                                <p class="text-sm text-gray-900">
                                    @if ($match->foodListing->expiry_time)
                                        {{ $match->foodListing->expiry_time->format('g:i A') }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if ($match->foodListing->dietary_info && count(json_decode($match->foodListing->dietary_info, true)) > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Dietary Information</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach (json_decode($match->foodListing->dietary_info, true) as $info)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $info }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Special Instructions -->
                @if ($match->notes)
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Special Instructions</h3>
                        <p class="text-sm text-gray-600">{{ $match->notes }}</p>
                    </div>
                @endif

                <!-- Pickup Photos -->
                @if ($match->pickup_photos)
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pickup Photos</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach ($match->pickup_photos as $photo)
                                <img src="{{ Storage::url($photo) }}"
                                     alt="Pickup photo"
                                     class="w-full h-32 object-cover rounded-lg">
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Feedback -->
                @if ($match->status == 'completed' && $match->feedback)
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Feedback</h3>
                        <p class="text-sm text-gray-600">{{ $match->feedback }}</p>
                    </div>
                @endif

                <!-- Rating -->
                @if ($match->status == 'completed' && $match->rating)
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quality Rating</h3>
                        <div class="flex items-center">
                            <div class="flex">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $match->rating)
                                        <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="ml-2 text-sm text-gray-600">{{ $match->rating }}/5</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Actions & Restaurant Info -->
            <div class="lg:col-span-1">
                <!-- Restaurant Information -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Restaurant Information</h3>
                    <div class="space-y-3">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">Restaurant Name</h4>
                            <p class="text-sm text-gray-900">{{ $match->foodListing->restaurantProfile->restaurant_name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">Cuisine Type</h4>
                            <p class="text-sm text-gray-600">{{ $match->foodListing->restaurantProfile->cuisine_type }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">Pickup Location</h4>
                            <p class="text-sm text-gray-900">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $match->foodListing->pickup_location }}
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">Pickup Address</h4>
                            <p class="text-sm text-gray-600">{{ $match->foodListing->pickup_address }}</p>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                @if ($match->qr_code && in_array($match->status, ['approved', 'scheduled']))
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pickup QR Code</h3>
                        <div class="text-center">
                            <div id="qrcode" class="flex justify-center mb-4"></div>
                            <p class="text-sm text-gray-600 mb-2">Show this QR code to the restaurant staff for pickup verification</p>
                            <p class="text-xs text-gray-500">Verification code: <span class="font-mono font-bold">{{ $match->qr_code }}</span></p>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('food-listings.index') }}"
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-center text-sm font-medium">
                            Browse More Food
                        </a>
                        <a href="{{ route('recipient.matches.index') }}"
                           class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-200 transition-colors text-center text-sm font-medium">
                            View All Matches
                        </a>
                        @if (in_array($match->status, ['pending', 'approved']))
                            <form method="POST" action="{{ route('recipient.matches.cancel', $match->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 transition-colors text-sm font-medium"
                                        onclick="return confirm('Are you sure you want to cancel this match?');">
                                    Cancel Match
                                </button>
                            </form>
                        @endif
                        @if ($match->status == 'scheduled' && !$match->in_progress)
                            <form method="POST" action="{{ route('recipient.matches.confirmPickup', $match->id) }}">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors text-sm font-medium">
                                    Confirm Pickup
                                </button>
                            </form>
                        @endif
                        @if ($match->status == 'in_progress')
                            <a href="{{ route('recipient.matches.complete', $match->id) }}"
                               class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition-colors text-center text-sm font-medium">
                                Complete Pickup
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if ($match->qr_code)
        // Generate QR code
        const qrcodeDiv = document.getElementById('qrcode');
        qrcodeDiv.innerHTML = '';

        const qrData = window.location.origin + '/recipient/match/' + $match->id + '/verify';
        new QRCode(qrcodeDiv, {
            text: qrData,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    @endif
});
</script>
@endsection