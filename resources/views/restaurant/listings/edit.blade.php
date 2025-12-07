@extends('restaurant.layouts.restaurant-layout')

@section('title', 'Edit Listing - Restaurant Portal')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Edit Food Listing</h1>
        <p class="text-sm text-zinc-500 mt-1">Update your food donation details and information.</p>
    </div>

    <!-- Listing Card -->
    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-emerald-500 to-emerald-600 relative">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -bottom-12 left-6">
                @if($listing->images && count($listing->images) > 0)
                    <img src="{{ Storage::url($listing->images[0]) }}" alt="Food" class="w-24 h-24 rounded-xl border-4 border-white shadow-lg object-cover">
                @else
                    <img src="https://images.unsplash.com/photo-1555244162-803834f70033?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80" alt="Food" class="w-24 h-24 rounded-xl border-4 border-white shadow-lg object-cover">
                @endif
            </div>
        </div>

        <div class="pt-14 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-zinc-900">{{ $listing->food_name }}</h2>
                    <div class="flex items-center gap-4 mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800">
                            {{ $listing->category }}
                        </span>
                        <div class="text-sm text-zinc-500">
                            Created {{ $listing->created_at->format('M j, Y') }}
                        </div>
                    </div>
                </div>
                <a href="{{ route('restaurant.listings') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-zinc-200 text-zinc-700 rounded-lg text-sm font-medium hover:bg-zinc-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to Listings
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-xl border border-zinc-200">
        <div class="p-6 border-b border-zinc-200">
            <h3 class="text-lg font-semibold text-zinc-900">Edit Food Details</h3>
            <p class="text-sm text-zinc-500 mt-1">Update your food donation information.</p>
        </div>
        <div class="p-6">
            <form action="{{ route('restaurant.listings.update', $listing->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-xs font-medium text-zinc-700">Food Name</label>
                    <input type="text" name="title" value="{{ $listing->food_name }}"
                           class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                           required>
                </div>

                <div>
                    <label class="text-xs font-medium text-zinc-700">Description</label>
                    <textarea name="description" rows="4" class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none"
                              required>{{ $listing->description }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-zinc-700">Food Category</label>
                        <select name="food_category" class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" required>
                            <option value="fresh" {{ $listing->category == 'fresh' ? 'selected' : '' }}>Fresh</option>
                            <option value="cooked" {{ $listing->category == 'cooked' ? 'selected' : '' }}>Cooked</option>
                            <option value="bakery" {{ $listing->category == 'bakery' ? 'selected' : '' }}>Bakery</option>
                            <option value="beverages" {{ $listing->category == 'beverages' ? 'selected' : '' }}>Beverages</option>
                            <option value="other" {{ $listing->category == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-zinc-700">Quantity</label>
                        <input type="text" name="quantity" value="{{ $listing->quantity }}"
                               class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                               placeholder="e.g., 5 kg, 10 servings"
                               required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-medium text-zinc-700">Expiry Date</label>
                        <input type="date" name="expiry_date" value="{{ $listing->expiry_date?->format('Y-m-d') }}"
                               class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                               required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-zinc-700">Expiry Time</label>
                        <input type="time" name="expiry_time" value="{{ $listing->expiry_time?->format('H:i') }}"
                               class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                               required>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-medium text-zinc-700">Pickup Instructions</label>
                    <textarea name="pickup_instructions" rows="3" class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none"
                              placeholder="Provide any special instructions for pickup...">{{ $listing->special_instructions }}</textarea>
                </div>

                <div>
                    <label class="text-xs font-medium text-zinc-700">Food Image</label>
                    <div class="mt-1">
                        @if($listing->images && count($listing->images) > 0)
                            <div class="flex items-center gap-4 mb-3">
                                <img src="{{ Storage::url($listing->images[0]) }}" alt="Current food image" class="w-20 h-20 rounded-lg object-cover border border-zinc-200">
                                <div class="text-sm text-zinc-500">
                                    <p class="font-medium text-zinc-900">Current Image</p>
                                    <p>Click below to replace</p>
                                </div>
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*"
                               class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <p class="text-xs text-zinc-500 mt-1">Replace the food image (optional)</p>
                    </div>
                </div>

                <!-- Status Section -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i data-lucide="info" class="w-4 h-4 text-amber-600"></i>
                        <h4 class="text-sm font-medium text-amber-900">Listing Status</h4>
                    </div>
                    <div class="text-sm text-amber-700">
                        <p><strong>Current Status:</strong>
                            @if($listing->status === 'expired')
                                <span class="text-red-600">Expired</span>
                            @elseif($listing->status === 'completed')
                                <span class="text-gray-600">Completed</span>
                            @elseif($listing->status === 'reserved')
                                <span class="text-amber-600">Reserved</span>
                            @else
                                <span class="text-emerald-600">Active</span>
                            @endif>
                        </p>
                        <p><strong>Approval Status:</strong>
                            @if($listing->approval_status === 'pending')
                                <span class="text-amber-600">Pending Approval</span>
                            @elseif($listing->approval_status === 'approved')
                                <span class="text-emerald-600">Approved</span>
                            @else
                                <span class="text-red-600">Rejected</span>
                            @endif>
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-zinc-100">
                    <a href="{{ route('restaurant.listings') }}" 
                    class="flex-1 py-2.5 border border-zinc-200 text-zinc-700 rounded-lg font-medium hover:bg-zinc-50 transition-all flex items-center justify-center">
                        Cancel
                    </a>

                    <button type="submit" class="flex-1 py-2.5 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-all flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Update Listing
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endsection