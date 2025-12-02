@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Food Details</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Welcome, {{ Auth::user()->name }}</span>
                    <a href="{{ route('logout') }}" class="px-4 py-2 text-sm text-red-600 hover:text-red-800 transition-colors">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Food Details -->
            <div class="lg:col-span-2">
                <!-- Food Images -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    @if ($foodListing->images && count($foodListing->images) > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach ($foodListing->images as $image)
                                <img src="{{ Storage::url($image) }}"
                                     alt="{{ $foodListing->food_name }}"
                                     class="w-full h-48 object-cover rounded-lg hover:opacity-90 transition-opacity">
                            @endforeach
                        </div>
                    @else
                        <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex flex-wrap items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $foodListing->food_name }}</h2>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $foodListing->quantity }} {{ $foodListing->unit }}
                        </span>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if ($foodListing->isVisibleToRecipients()) bg-green-100 text-green-800
                            @elseif ($foodListing->approval_status == 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            @if ($foodListing->isVisibleToRecipients())
                                Available
                            @elseif ($foodListing->approval_status == 'pending')
                                Pending Approval
                            @else
                                Unavailable
                            @endif
                        </span>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                        <p class="text-gray-600">{{ $foodListing->description }}</p>
                    </div>

                    <!-- Category and Expiry -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">Category</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($foodListing->category) }}
                            </span>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-1">Expiry</h4>
                            <p class="text-sm text-gray-900">
                                {{ $foodListing->expiry_date->format('F j, Y') }}
                                @if ($foodListing->expiry_time)
                                    at {{ $foodListing->expiry_time->format('g:i A') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Dietary Information -->
                    @if ($foodListing->dietary_info && count($foodListing->dietary_info) > 0)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Dietary Information</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($foodListing->dietary_info as $info)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $info }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Special Instructions -->
                    @if ($foodListing->special_instructions)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Special Instructions</h4>
                            <p class="text-sm text-gray-600">{{ $foodListing->special_instructions }}</p>
                        </div>
                    @endif
                </div>

                <!-- Restaurant Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Restaurant Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $foodListing->restaurantProfile->restaurant_name }}</p>
                                <p class="text-sm text-gray-600">{{ $foodListing->restaurantProfile->description }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $foodListing->pickup_location }}</p>
                                <p class="text-sm text-gray-600">{{ $foodListing->pickup_address }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Posted on</p>
                                <p class="text-sm text-gray-600">{{ $foodListing->created_at->format('F j, Y, g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Action -->
            <div class="lg:col-span-1">
                <!-- Match Request Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6 sticky top-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Food Match</h3>

                    @if ($foodListing->isVisibleToRecipients())
                        <form method="POST" action="{{ route('recipient.matches.store', $foodListing->id) }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Requested Quantity</label>
                                <input type="number" name="quantity" min="1" max="{{ $foodListing->quantity }}"
                                       value="1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                       required>
                                <p class="text-xs text-gray-500 mt-1">Available: {{ $foodListing->quantity }} {{ $foodListing->unit }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Organization Name</label>
                                <input type="text" name="organization_name"
                                       value="{{ Auth::user()->recipient->organization_name ?? Auth::user()->name }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                       required>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Special Requirements (Optional)</label>
                                <textarea name="special_requirements" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                          placeholder="Any special dietary requirements or notes..."></textarea>
                            </div>

                            <button type="submit"
                                    class="w-full bg-emerald-600 text-white py-2 px-4 rounded-md hover:bg-emerald-700 transition-colors font-medium">
                                Request Match
                            </button>
                        </form>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Not Available</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if ($foodListing->approval_status == 'pending')
                                    This food listing is pending approval.
                                @else
                                    This food listing is no longer available.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Quick Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Quick Information</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Category:</span>
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst($foodListing->category) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Quantity:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $foodListing->quantity }} {{ $foodListing->unit }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Status:</span>
                            <span class="text-sm font-medium text-gray-900">
                                @if ($foodListing->isVisibleToRecipients()) Available
                                @else Unavailable @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection