@extends('admin.layouts.admin-layout')

@php
    use App\Models\FoodListing;
    use App\Models\User;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    use App\Models\PickupVerification;
@endphp

@section('title', 'Listing Approvals - MyFoodshare Admin')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Search and Filters -->
    <div class="bg-white rounded-xl border border-zinc-200 p-6 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-zinc-700 mb-2">Search Listings</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by food name or description..."
                       class="w-full px-4 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="w-full sm:w-48">
                <label class="block text-sm font-medium text-zinc-700 mb-2">Category</label>
                <select name="category" class="w-full px-4 py-2 border border-zinc-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">All Categories</option>
                    <option value="fresh" {{ request('category') == 'fresh' ? 'selected' : '' }}>Fresh</option>
                    <option value="cooked" {{ request('category') == 'cooked' ? 'selected' : '' }}>Cooked</option>
                    <option value="bakery" {{ request('category') == 'bakery' ? 'selected' : '' }}>Bakery</option>
                    <option value="beverages" {{ request('category') == 'beverages' ? 'selected' : '' }}>Beverages</option>
                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                    <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>
                    Search
                </button>
            </div>
        </form>
    </div>

    <!-- Listings Table -->
    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 border-b border-zinc-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Food Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Restaurant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Expiry</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200">
                    @forelse ($foodListings as $listing)
                    <tr class="hover:bg-zinc-50">
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                @if($listing->images && count($listing->images) > 0)
                                    <img src="{{ Storage::url($listing->images[0]) }}" alt="{{ $listing->food_name }}"
                                         class="w-12 h-12 rounded-lg object-cover border border-zinc-200">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-zinc-100 border border-zinc-200 flex items-center justify-center">
                                        <i data-lucide="package" class="w-6 h-6 text-zinc-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="text-sm font-medium text-zinc-900">{{ $listing->food_name }}</h3>
                                    <p class="text-sm text-zinc-500">{{ Str::limit($listing->description, 50) }}</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 mt-1">
                                        {{ $listing->category }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($listing->restaurantProfile)
                                <div class="text-sm font-medium text-zinc-900">{{ $listing->restaurantProfile->business_name }}</div>
                                <div class="text-sm text-zinc-500">{{ $listing->restaurantProfile->address }}</div>
                            @else
                                <div class="text-sm font-medium text-zinc-900">{{ $listing->creator->name }}</div>
                                <div class="text-sm text-zinc-500">No restaurant profile</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-zinc-900">{{ $listing->quantity }} {{ $listing->unit }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-zinc-900">{{ $listing->expiry_date }}</div>
                            <div class="text-sm text-zinc-500">{{ $listing->expiry_time }}</div>
                            @if($listing->expiry_date < now()->toDateString())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                    Expired
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-zinc-900">{{ $listing->created_at->format('M d, Y') }}</div>
                            <div class="text-sm text-zinc-500">{{ $listing->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.food-listings.show', $listing->id) }}"
                                   class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                    View
                                </a>
                                <form method="POST" action="{{ route('admin.food-listings.approve', $listing->id) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                                        <i data-lucide="check" class="w-4 h-4 mr-1"></i>
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.food-listings.reject', $listing->id) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                        <i data-lucide="x" class="w-4 h-4 mr-1"></i>
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i data-lucide="package-x" class="w-12 h-12 text-zinc-400 mx-auto mb-4"></i>
                            <h3 class="text-lg font-medium text-zinc-900 mb-2">No pending listings found</h3>
                            <p class="text-sm text-zinc-500">There are currently no food listings awaiting approval.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($foodListings->hasPages())
        <div class="px-6 py-4 border-t border-zinc-200">
            {{ $foodListings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide Icons
    lucide.createIcons();
</script>
@endpush