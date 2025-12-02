@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Food Listing</h1>
                    <p class="text-sm text-gray-600">List your available food for donation</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('restaurant.food-listings.index') }}"
                       class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition-colors">
                        Back to Listings
                    </a>
                    <span class="text-sm text-gray-600">Welcome, {{ Auth::user()->name }}</span>
                    <a href="{{ route('logout') }}" class="px-4 py-2 text-sm text-red-600 hover:text-red-800 transition-colors">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Food Information</h2>
                <p class="text-sm text-gray-600 mt-1">Fill in the details about the food you want to donate</p>
            </div>

            <form method="POST" action="{{ route('restaurant.food-listings.store') }}" enctype="multipart/form-data" class="p-6">
                @csrf

                <!-- Basic Information -->
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="food_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Food Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="food_name" name="food_name" required
                                   value="{{ old('food_name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., Fresh Garden Salad">
                            @error('food_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select id="category" name="category" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select a category</option>
                                <option value="fresh" {{ old('category') == 'fresh' ? 'selected' : '' }}>Fresh Food</option>
                                <option value="cooked" {{ old('category') == 'cooked' ? 'selected' : '' }}>Cooked Meals</option>
                                <option value="bakery" {{ old('category') == 'bakery' ? 'selected' : '' }}>Bakery</option>
                                <option value="beverages" {{ old('category') == 'beverages' ? 'selected' : '' }}>Beverages</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Describe the food, including ingredients, condition, and any relevant details...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantity Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quantity Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Quantity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="quantity" name="quantity" min="1" required
                                       value="{{ old('quantity') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., 10">
                                @error('quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">
                                    Unit <span class="text-red-500">*</span>
                                </label>
                                <select id="unit" name="unit" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select unit</option>
                                    <option value="pieces" {{ old('unit') == 'pieces' ? 'selected' : '' }}>Pieces</option>
                                    <option value="servings" {{ old('unit') == 'servings' ? 'selected' : '' }}>Servings</option>
                                    <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilograms</option>
                                    <option value="g" {{ old('unit') == 'g' ? 'selected' : '' }}>Grams</option>
                                    <option value="liters" {{ old('unit') == 'liters' ? 'selected' : '' }}>Liters</option>
                                    <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>Milliliters</option>
                                    <option value="boxes" {{ old('unit') == 'boxes' ? 'selected' : '' }}>Boxes</option>
                                    <option value="containers" {{ old('unit') == 'containers' ? 'selected' : '' }}>Containers</option>
                                </select>
                                @error('unit')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Expiry Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Expiry Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Expiry Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="expiry_date" name="expiry_date" required
                                       value="{{ old('expiry_date') }}"
                                       min="{{ now()->addDay()->format('Y-m-d') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('expiry_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="expiry_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Expiry Time <span class="text-red-500">*</span>
                                </label>
                                <input type="time" id="expiry_time" name="expiry_time" required
                                       value="{{ old('expiry_time') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('expiry_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pickup Location -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pickup Location</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2">
                                <label for="pickup_address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pickup Address <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="pickup_address" name="pickup_address" required
                                       value="{{ old('pickup_address') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., 123 Main Street, City, State">
                                @error('pickup_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="pickup_location" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pickup Location <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="pickup_location" name="pickup_location" required
                                       value="{{ old('pickup_location') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., Main Entrance">
                                @error('pickup_location')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Coordinates (Optional) -->
                        <div class="mt-4 text-sm text-gray-600">
                            <p class="mb-2">Location Coordinates (Optional - helps with proximity search):</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="latitude" class="block text-xs text-gray-600 mb-1">Latitude</label>
                                    <input type="number" id="latitude" name="latitude" step="0.000001"
                                           value="{{ old('latitude') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="e.g., 40.7128">
                                </div>
                                <div>
                                    <label for="longitude" class="block text-xs text-gray-600 mb-1">Longitude</label>
                                    <input type="number" id="longitude" name="longitude" step="0.000001"
                                           value="{{ old('longitude') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="e.g., -74.0060">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dietary Information -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Dietary Information</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="dietary_info[]" value="Vegetarian"
                                       class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">Vegetarian</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="dietary_info[]" value="Vegan"
                                       class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">Vegan</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="dietary_info[]" value="Gluten-Free"
                                       class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">Gluten-Free</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="dietary_info[]" value="Dairy-Free"
                                       class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">Dairy-Free</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="dietary_info[]" value="Nut-Free"
                                       class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">Nut-Free</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="dietary_info[]" value="Halal"
                                       class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">Halal</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="dietary_info[]" value="Kosher"
                                       class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">Kosher</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="dietary_info[]" value="Organic"
                                       class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="text-sm text-gray-700">Organic</span>
                            </label>
                        </div>
                        @error('dietary_info')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Special Instructions -->
                    <div>
                        <label for="special_instructions" class="block text-sm font-medium text-gray-700 mb-2">
                            Special Instructions
                        </label>
                        <textarea id="special_instructions" name="special_instructions" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Any special handling instructions, pickup requirements, or notes...">{{ old('special_instructions') }}</textarea>
                        @error('special_instructions')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Food Images -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Food Images</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Images (Max 5 files, 2MB each)
                                </label>
                                <input type="file" id="images" name="images[]" multiple accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Add photos to help recipients understand what food is available</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('restaurant.food-listings.index') }}"
                           class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Create Listing
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection