@extends('admin.layouts.admin-layout')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Food Listing Details - Admin Panel')

@section('content')

<div class="space-y-8 p-6 md:p-8 w-full">

    <!-- Header -->
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Food Listing Details</h1>
        <p class="text-sm text-zinc-500">Review and manage food listing information</p>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left Column - Listing Details -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Basic Info Card -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200">
                    <h2 class="text-lg font-medium text-zinc-900">Basic Information</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start gap-4">
                        @if($foodListing->images && count($foodListing->images) > 0)
                            <img src="{{ Storage::url($foodListing->images[0]) }}" alt="{{ $foodListing->food_name }}" class="w-24 h-24 rounded-lg object-cover">
                        @else
                            <div class="w-24 h-24 rounded-lg bg-zinc-100 flex items-center justify-center">
                                <i data-lucide="package" class="w-8 h-8 text-zinc-400"></i>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-zinc-900">{{ $foodListing->food_name }}</h3>
                            <p class="text-sm text-zinc-500 mt-1">{{ $foodListing->description }}</p>
                            <div class="flex items-center gap-4 mt-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700">
                                    {{ ucfirst($foodListing->category) }}
                                </span>
                                <span class="text-sm text-zinc-600">{{ $foodListing->quantity }} {{ $foodListing->unit }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pickup Details -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200">
                    <h2 class="text-lg font-medium text-zinc-900">Pickup Details</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-zinc-500 uppercase">Pickup Location</label>
                            <p class="text-sm text-zinc-900 mt-1">{{ $foodListing->pickup_location }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-zinc-500 uppercase">Expiry Date</label>
                            <p class="text-sm text-zinc-900 mt-1">
                                {{ $foodListing->expiry_date?->format('M j, Y') }}
                                @if($foodListing->expiry_time)
                                    at {{ $foodListing->expiry_time->format('g:i A') }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-zinc-500 uppercase">Donor</label>
                            <p class="text-sm text-zinc-900 mt-1">
                                {{ $foodListing->restaurantProfile->restaurant_name ?? $foodListing->creator->name }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-zinc-500 uppercase">Status</label>
                            <p class="text-sm text-zinc-900 mt-1">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($foodListing->approval_status === 'approved')
                                        bg-emerald-100 text-emerald-700
                                    @elseif($foodListing->approval_status === 'pending')
                                        bg-amber-100 text-amber-700
                                    @else
                                        bg-red-100 text-red-700
                                    @endif
                                ">
                                    {{ ucfirst($foodListing->approval_status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dietary Information -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200">
                    <h2 class="text-lg font-medium text-zinc-900">Dietary Information</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-zinc-500 uppercase">Dietary Restrictions</label>
                            <p class="text-sm text-zinc-900 mt-1">
                                {{ $foodListing->dietary_restrictions ?? 'None specified' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-zinc-500 uppercase">Allergen Information</label>
                            <p class="text-sm text-zinc-900 mt-1">
                                {{ $foodListing->allergens ?? 'None specified' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column - Actions & Info -->
        <div class="space-y-6">

            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-5 text-white shadow-lg">
                <h3 class="text-sm font-medium mb-3">Quick Actions</h3>
                <div class="space-y-2">
                    <form action="{{ route('admin.food-listings.approve', $foodListing) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-between text-sm font-medium bg-white/20 hover:bg-white/30 rounded-md p-2 transition-colors">
                            <span class="flex items-center gap-2">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                Approve Listing
                            </span>
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </form>
                    {{-- <button class="w-full flex items-center justify-between text-sm font-medium bg-white/20 hover:bg-white/30 rounded-md p-2 transition-colors">
                        <span class="flex items-center gap-2">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                            Edit Details
                        </span>
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button> --}}
                </div>
            </div>

            <!-- Listing Images -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-5">
                <h3 class="text-sm font-medium text-zinc-900 mb-3">Listing Images</h3>
                <div class="space-y-3">
                    @if($foodListing->images && count($foodListing->images) > 0)
                        @foreach($foodListing->images as $image)
                            <div class="aspect-square bg-zinc-100 rounded-lg overflow-hidden">
                                <img src="{{ Storage::url($image) }}" alt="{{ $foodListing->food_name }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <i data-lucide="image" class="w-8 h-8 text-zinc-400 mx-auto mb-2"></i>
                            <p class="text-xs text-zinc-500">No images uploaded</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-5">
                <h3 class="text-sm font-medium text-zinc-900 mb-3">Contact Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-medium text-zinc-500">Restaurant/Donor</label>
                        <p class="text-sm text-zinc-900">{{ $foodListing->restaurantProfile->restaurant_name ?? $foodListing->creator->name }}</p>
                    </div>
                    @if($foodListing->restaurantProfile->phone ?? $foodListing->creator->phone)
                        <div>
                            <label class="text-xs font-medium text-zinc-500">Phone</label>
                            <p class="text-sm text-zinc-900">{{ $foodListing->restaurantProfile->phone ?? $foodListing->creator->phone }}</p>
                        </div>
                    @endif
                    @if($foodListing->restaurantProfile->email ?? $foodListing->creator->email)
                        <div>
                            <label class="text-xs font-medium text-zinc-500">Email</label>
                            <p class="text-sm text-zinc-900">{{ $foodListing->restaurantProfile->email ?? $foodListing->creator->email }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide Icons
    lucide.createIcons();
</script>
@endpush