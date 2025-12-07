@extends('restaurant.layouts.restaurant-layout')

@section('title', 'Manage Listings - Restaurant Portal')

@section('content')
<div class="space-y-6 w-full">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Manage Listings</h1>
            <p class="text-sm text-zinc-500 mt-1">Manage your food donation listings and track their status.</p>
        </div>
        <button onclick="document.getElementById('post-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white shadow-lg shadow-zinc-900/20 rounded-lg text-sm font-medium transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add New Listing
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-zinc-200 p-4">
        <form method="GET" class="flex flex-col lg:flex-row lg:items-center gap-4">
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-medium text-zinc-700">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or description..."
                           class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>
                <div>
                    <label class="text-xs font-medium text-zinc-700">Status</label>
                    <select name="status" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-zinc-700">Category</label>
                    <select name="category" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <option value="">All Categories</option>
                        <option value="fresh" {{ request('category') == 'fresh' ? 'selected' : '' }}>Fresh</option>
                        <option value="cooked" {{ request('category') == 'cooked' ? 'selected' : '' }}>Cooked</option>
                        <option value="bakery" {{ request('category') == 'bakery' ? 'selected' : '' }}>Bakery</option>
                        <option value="beverages" {{ request('category') == 'beverages' ? 'selected' : '' }}>Beverages</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route('restaurant.listings') }}" class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 rounded-lg text-sm font-medium hover:bg-zinc-50 transition-colors">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Listings Table -->
    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 border-b border-zinc-200">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Food Name</span>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Category</span>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Quantity</span>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Expiry</span>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Status</span>
                        </th>
                        <th class="px-6 py-3 text-left">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200">
                    @foreach ($foodListings as $listing)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                @if($listing->images && count($listing->images) > 0)
                                    <img src="{{ Storage::url($listing->images[0]) }}" class="w-10 h-10 rounded-lg object-cover" alt="Food image">
                                @else
                                    <img src="https://images.unsplash.com/photo-1555244162-803834f70033?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80" class="w-10 h-10 rounded-lg object-cover" alt="Food placeholder">
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-zinc-900">{{ $listing->food_name }}</div>
                                    <div class="text-xs text-zinc-500">{{ Str::limit($listing->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800">
                                {{ $listing->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-900">
                            {{ $listing->quantity }} {{ $listing->unit }}
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-900">
                            <div>{{ $listing->expiry_date?->format('M j, Y') }}</div>
                            <div class="text-xs text-zinc-500">{{ $listing->expiry_time?->format('g:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($listing->approval_status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                    Pending Approval
                                </span>
                            @elseif($listing->approval_status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i data-lucide="x" class="w-3 h-3 mr-1"></i>
                                    Rejected
                                </span>
                            @elseif($listing->status === 'expired')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Expired
                                </span>
                            @elseif($listing->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Completed
                                </span>
                            @elseif($listing->status === 'reserved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    Reserved
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    Active
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('restaurant.listings.edit', $listing->id) }}" class="text-zinc-500 hover:text-emerald-600 transition-colors">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('restaurant.listings.destroy', $listing->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this listing?')" class="text-zinc-500 hover:text-red-600 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @if($listing->status === 'reserved' && $listing->matches->first())
                                    <a href="{{ route('restaurant.requests.show', $listing->matches->first()->id) }}" class="text-zinc-500 hover:text-blue-600 transition-colors">
                                        <i data-lucide="users" class="w-4 h-4"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        @if($foodListings->isEmpty())
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-zinc-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="package" class="w-8 h-8 text-zinc-400"></i>
            </div>
            <h3 class="font-medium text-zinc-900 mb-1">No listings found</h3>
            <p class="text-sm text-zinc-500 mb-4">No food listings match your current filters.</p>
            <a href="{{ route('restaurant.listings.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-900 text-white rounded-lg text-sm font-medium hover:bg-zinc-800 transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add Your First Listing
            </a>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($foodListings->hasPages())
    <div class="flex items-center justify-between">
        <div class="text-sm text-zinc-500">
            Showing {{ $foodListings->firstItem() }} to {{ $foodListings->lastItem() }} of {{ $foodListings->total() }} listings
        </div>
        <div class="flex gap-1">
            {{ $foodListings->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>

    <!-- Post Donation Modal -->
    <div id="post-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('post-modal').classList.add('hidden')"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50">
                <h3 class="font-semibold text-zinc-900">Post New Donation</h3>
                <button onclick="document.getElementById('post-modal').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('restaurant.listings.store') }}" method="POST" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
                @csrf
                <!-- Form Fields -->
                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Food Image</label>
                    <div class="border-2 border-dashed border-zinc-200 rounded-lg p-6 text-center hover:border-zinc-300 transition-colors cursor-pointer" onclick="document.getElementById('food-image').click()">
                        <input type="file" id="food-image" name="image" class="hidden" accept="image/*" onchange="previewImage(event)">
                        <div id="image-preview" class="hidden">
                            <img id="preview-img" class="w-20 h-20 object-cover rounded-lg mx-auto mb-2">
                            <p class="text-xs text-emerald-600 font-medium">Click to change image</p>
                        </div>
                        <div id="image-upload-placeholder">
                            <i data-lucide="image-plus" class="w-8 h-8 text-zinc-400 mx-auto mb-2"></i>
                            <p class="text-xs text-zinc-500 font-medium">Click to upload food image</p>
                            <p class="text-[10px] text-zinc-400 mt-1">PNG, JPG up to 5MB</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Listing Title</label>
                    <input type="text" name="title" placeholder="e.g. 5kg of Assorted Vegetables" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Description</label>
                    <textarea name="description" rows="2" placeholder="Describe the food items, condition, and any other relevant details..." class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-zinc-700">Food Category</label>
                        <select name="food_category" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            <option>Fresh</option>
                            <option>Cooked</option>
                            <option>Bakery</option>
                            <option>Beverages</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-zinc-700">Quantity</label>
                        <input type="text" name="quantity" placeholder="e.g., 5 kg, 10 servings" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Expiry Date & Time</label>
                    <div class="flex gap-2">
                        <input type="date" name="expiry_date" class="flex-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <input type="time" name="expiry_time" class="flex-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Pickup Instructions</label>
                    <textarea name="pickup_instructions" rows="3" placeholder="Enter rear entrance code, ask for Manager Mike..." class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none"></textarea>
                </div>

                <!-- Location Preview (Dynamic from Restaurant Profile) -->
                <div class="p-3 bg-zinc-50 rounded-lg border border-zinc-200 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-zinc-200 flex items-center justify-center">
                        <i data-lucide="map-pin" class="w-4 h-4 text-zinc-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-zinc-900">Pickup Location</p>
                        <p class="text-[10px] text-zinc-500" id="restaurant-location">
                            {{ auth()->user()->restaurantProfile?->address ?? 'Set restaurant location in profile' }}
                        </p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3 pt-4 border-t border-zinc-100">
                    <button type="button" onclick="document.getElementById('post-modal').classList.add('hidden')" class="flex-1 py-2.5 border border-zinc-200 text-zinc-700 rounded-lg font-medium hover:bg-zinc-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-2.5 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-all flex items-center justify-center gap-2">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        Post Donation
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        // Image Preview Function
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('image-preview');
                    const placeholder = document.getElementById('image-upload-placeholder');
                    const img = document.getElementById('preview-img');

                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
    });
</script>
@endsection