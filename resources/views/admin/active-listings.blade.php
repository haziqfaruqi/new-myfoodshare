@extends('admin.layouts.admin-layout')

@php
    use App\Models\User;
    use App\Models\FoodListing;
@endphp

@section('title', 'Active Listings - Admin Panel')

@section('content')

<div class="space-y-8 p-6 md:p-8 w-full">


            <div class="space-y-8 w-full">

                <!-- Header -->
                <div class="space-y-2">
                    <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Active Listings</h1>
                    <p class="text-sm text-zinc-500">Manage all active food donations and monitor their status</p>
                </div>

                <!-- Listing Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-medium text-zinc-500 uppercase">Total Listings</span>
                            <i data-lucide="package" class="w-4 h-4 text-zinc-600"></i>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ $stats['total_listings'] }}</span>
                            <span class="text-xs font-medium text-zinc-500">All</span>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-medium text-zinc-500 uppercase">Active</span>
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ $stats['active_listings'] }}</span>
                            <span class="text-xs font-medium text-emerald-600">Available</span>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-medium text-zinc-500 uppercase">Reserved</span>
                            <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ $stats['reserved_listings'] }}</span>
                            <span class="text-xs font-medium text-amber-600">Pending</span>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-medium text-zinc-500 uppercase">Expired</span>
                            <i data-lucide="alert-circle" class="w-4 h-4 text-red-600"></i>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ $stats['expired_listings'] }}</span>
                            <span class="text-xs font-medium text-red-600">Needs attention</span>
                        </div>
                    </div>

                    <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-medium text-zinc-500 uppercase">Pending Approval</span>
                            <i data-lucide="file-question" class="w-4 h-4 text-amber-600"></i>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ $stats['pending_approvals'] }}</span>
                            <span class="text-xs font-medium text-amber-600">Review needed</span>
                        </div>
                    </div>
                </div>

                <!-- Filters and Actions -->
                <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <select class="text-sm border border-zinc-200 rounded-md px-3 py-2 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                <option>All Status</option>
                                <option>Active</option>
                                <option>Reserved</option>
                                <option>Expired</option>
                            </select>
                            <select class="text-sm border border-zinc-200 rounded-md px-3 py-2 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                <option>All Categories</option>
                                <option>Fresh</option>
                                <option>Cooked</option>
                                <option>Bakery</option>
                                <option>Beverages</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium rounded-md transition-colors flex items-center gap-2">
                                <i data-lucide="download" class="w-4 h-4"></i>
                                Export Report
                            </button>
                            <button class="px-4 py-2 border border-zinc-200 text-zinc-700 text-sm font-medium rounded-md hover:bg-zinc-50 transition-colors">
                                Bulk Actions
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Listings Table -->
                <div class="bg-white border border-zinc-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-zinc-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-medium text-zinc-900">Food Listings</h2>
                            <span class="text-sm text-zinc-500">{{ $activeListings->total() }} listings</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-zinc-50 text-zinc-500 border-b border-zinc-200">
                                <tr>
                                    <th class="px-6 py-3 font-medium">Food Item</th>
                                    <th class="px-6 py-3 font-medium">Donor</th>
                                    <th class="px-6 py-3 font-medium">Category</th>
                                    <th class="px-6 py-3 font-medium">Quantity</th>
                                    <th class="px-6 py-3 font-medium">Status</th>
                                    <th class="px-6 py-3 font-medium">Expiry</th>
                                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @forelse($activeListings as $listing)
                                <tr class="hover:bg-zinc-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start gap-3">
                                            @if($listing->images && count($listing->images) > 0)
                                                <img src="{{ Storage::url($listing->images[0]) }}" alt="{{ $listing->food_name }}" class="w-10 h-10 rounded-lg object-cover">
                                            @else
                                                <div class="w-10 h-10 rounded-lg bg-zinc-100 flex items-center justify-center">
                                                    <i data-lucide="package" class="w-5 h-5 text-zinc-400"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-zinc-900 text-sm">{{ $listing->food_name }}</p>
                                                <p class="text-xs text-zinc-500 mt-0.5 line-clamp-2">{{ $listing->description }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-zinc-900">{{ $listing->restaurantProfile->restaurant_name ?? $listing->creator->name }}</p>
                                        <p class="text-xs text-zinc-500">{{ $listing->pickup_location }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700">
                                            {{ ucfirst($listing->category) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-zinc-900">{{ $listing->quantity }}</p>
                                        <p class="text-xs text-zinc-500">{{ $listing->unit }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($listing->status === 'active')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Active
                                            </span>
                                        @elseif($listing->status === 'reserved')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Reserved
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-zinc-900">{{ $listing->expiry_date?->format('M j, Y') }}</p>
                                        <p class="text-xs text-zinc-500">{{ $listing->expiry_time?->format('g:i A') }}</p>
                                        @if($listing->expiry_date && $listing->expiry_date->lt(now()))
                                            <p class="text-xs text-red-600 font-medium">Expired</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.food-listings.show', $listing->id) }}" class="text-zinc-400 hover:text-emerald-600 transition-colors" title="View Details">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            @if($listing->status === 'active')
                                                <button class="text-zinc-400 hover:text-blue-600 transition-colors" title="Manage Matches">
                                                    <i data-lucide="users" class="w-4 h-4"></i>
                                                </button>
                                            @endif
                                            <button class="text-zinc-400 hover:text-amber-600 transition-colors" title="Edit">
                                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                            </button>
                                            <form action="#" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to deactivate this listing?')">
                                                @csrf
                                                <button type="submit" class="text-zinc-400 hover:text-red-600 transition-colors" title="Deactivate">
                                                    <i data-lucide="x" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <i data-lucide="package-2" class="w-12 h-12 text-zinc-300 mx-auto mb-4"></i>
                                        <h3 class="text-lg font-medium text-zinc-900 mb-2">No Active Listings</h3>
                                        <p class="text-sm text-zinc-500">There are no active food listings at the moment.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-zinc-200">
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-zinc-500">Showing {{ $activeListings->firstItem() }}-{{ $activeListings->lastItem() }} of {{ $activeListings->total() }}</p>
                            <div class="flex items-center gap-2">
                                @if($activeListings->hasPages())
                                    @if($activeListings->onFirstPage())
                                        <button class="px-3 py-1 text-xs text-zinc-400 cursor-not-allowed">Previous</button>
                                    @else
                                        <a href="{{ $activeListings->previousPageUrl() }}" class="px-3 py-1 text-xs text-zinc-600 hover:text-zinc-900 bg-white border border-zinc-200 rounded-md">Previous</a>
                                    @endif

                                    <span class="px-3 py-1 text-xs text-zinc-900 bg-zinc-100 rounded-md">{{ $activeListings->currentPage() }} / {{ $activeListings->lastPage() }}</span>

                                    @if($activeListings->hasMorePages())
                                        <a href="{{ $activeListings->nextPageUrl() }}" class="px-3 py-1 text-xs text-zinc-600 hover:text-zinc-900 bg-white border border-zinc-200 rounded-md">Next</a>
                                    @else
                                        <button class="px-3 py-1 text-xs text-zinc-400 cursor-not-allowed">Next</button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="mt-12 mb-4 text-center">
                <p class="text-xs text-zinc-400">© 2024 MyFoodshare Platform. Reducing waste, feeding communities.</p>
            </footer>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide Icons
    lucide.createIcons();
</script>
@endpush