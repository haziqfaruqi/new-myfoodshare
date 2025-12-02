@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Available Food</h1>
                    <p class="text-sm text-gray-600">Find and reserve food donations near you</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Welcome, {{ Auth::user()->name }}</span>
                    <a href="{{ route('logout') }}" class="px-4 py-2 text-sm text-red-600 hover:text-red-800 transition-colors">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Search and Filter Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('food-listings.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Food</label>
                    <input type="text" name="search" placeholder="Search by food name..."
                           value="{{ request('search') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        <option value="fresh" {{ request('category') == 'fresh' ? 'selected' : '' }}>Fresh Food</option>
                        <option value="cooked" {{ request('category') == 'cooked' ? 'selected' : '' }}>Cooked Meals</option>
                        <option value="bakery" {{ request('category') == 'bakery' ? 'selected' : '' }}>Bakery</option>
                        <option value="beverages" {{ request('category') == 'beverages' ? 'selected' : '' }}>Beverages</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Distance</label>
                    <select name="distance" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Any Distance</option>
                        <option value="5" {{ request('distance') == '5' ? 'selected' : '' }}>Within 5 km</option>
                        <option value="10" {{ request('distance') == '10' ? 'selected' : '' }}>Within 10 km</option>
                        <option value="20" {{ request('distance') == '20' ? 'selected' : '' }}>Within 20 km</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                        Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Summary -->
        <div class="flex justify-between items-center mb-6">
            <p class="text-sm text-gray-600">
                Showing {{ $foodListings->firstItem() }}-{{ $foodListings->lastItem() }} of {{ $foodListings->total() }} available food items
            </p>
            <div class="flex space-x-2">
                <select class="px-3 py-1 border border-gray-300 rounded-md text-sm">
                    <option>Sort by: Newest</option>
                    <option>Sort by: Expiry Date</option>
                    <option>Sort by: Distance</option>
                </select>
            </div>
        </div>

        <!-- Food Listings Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($foodListings as $foodListing)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <!-- Food Images -->
                        @if ($foodListing->images && count($foodListing->images) > 0)
                            <div class="aspect-w-16 aspect-h-12 mb-4">
                                <img src="{{ Storage::url($foodListing->images[0]) }}"
                                     alt="{{ $foodListing->food_name }}"
                                     class="w-full h-48 object-cover rounded-lg">
                            </div>
                        @else
                            <div class="w-full h-48 bg-gray-200 rounded-lg mb-4 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif

                        <!-- Food Details -->
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $foodListing->food_name }}</h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $foodListing->quantity }} {{ $foodListing->unit }}
                            </span>
                        </div>

                        <!-- Restaurant Info -->
                        <div class="mb-3">
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">{{ $foodListing->restaurantProfile->restaurant_name }}</span>
                            </p>
                        </div>

                        <!-- Description -->
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                            {{ $foodListing->description }}
                        </p>

                        <!-- Category and Expiry -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($foodListing->category) }}
                            </span>
                            <span class="text-xs text-gray-500">
                                Expires {{ $foodListing->expiry_date->format('M d, Y') }}
                                @if ($foodListing->expiry_time)
                                    at {{ $foodListing->expiry_time->format('H:i') }}
                                @endif
                            </span>
                        </div>

                        <!-- Dietary Info -->
                        @if ($foodListing->dietary_info && count($foodListing->dietary_info) > 0)
                            <div class="flex flex-wrap gap-1 mb-4">
                                @foreach ($foodListing->dietary_info as $info)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $info }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Pickup Location -->
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $foodListing->pickup_location }}
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-3">
                            <a href="{{ route('food-listings.show', $foodListing->id) }}"
                               class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                                View Details
                            </a>
                            @if (Auth::user()->isRecipient() && $foodListing->isVisibleToRecipients())
                                <a href="{{ route('food-listings.show', $foodListing->id) }}"
                                   class="flex-1 bg-emerald-600 text-white text-center py-2 px-4 rounded-md hover:bg-emerald-700 transition-colors text-sm font-medium">
                                    Request Match
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $foodListings->links() }}
        </div>
    </div>
</div>
@endsection