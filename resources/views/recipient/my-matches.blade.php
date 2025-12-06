@extends('recipient.layouts.recipient-layout')

@section('title', 'My Matches')

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-hidden">

    <!-- Welcome & Key Actions -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 p-6 md:p-8 pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">My Matches</h1>
            <p class="text-sm text-zinc-500 mt-1">Manage your food matching requests and track pickup status.</p>
        </div>
        <div class="flex gap-3">
            <select class="px-4 py-2 border border-zinc-200 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option>All Status</option>
                <option>Pending</option>
                <option>Approved</option>
                <option>Scheduled</option>
                <option>Completed</option>
            </select>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                <i data-lucide="filter" class="w-4 h-4 inline mr-2"></i>
                Filter
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 px-6 md:px-8 pb-4">
        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Total Matches</span>
                <div class="p-1.5 bg-blue-50 rounded-lg">
                    <i data-lucide="link" class="w-4 h-4 text-blue-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $matches->count() }}</span>
                <span class="text-xs font-medium text-zinc-500">All time</span>
            </div>
        </div>

        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Pending</span>
                <div class="p-1.5 bg-amber-50 rounded-lg">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $matches->where('status', 'pending')->count() }}</span>
                <span class="text-xs font-medium text-amber-600">Waiting approval</span>
            </div>
        </div>

        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Active</span>
                <div class="p-1.5 bg-emerald-50 rounded-lg">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $matches->whereIn('status', ['approved', 'scheduled'])->count() }}</span>
                <span class="text-xs font-medium text-emerald-600">Scheduled</span>
            </div>
        </div>

        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Completed</span>
                <div class="p-1.5 bg-green-50 rounded-lg">
                    <i data-lucide="utensils" class="w-4 h-4 text-green-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $matches->where('status', 'completed')->count() }}</span>
                <span class="text-xs font-medium text-zinc-500">Successfully picked up</span>
            </div>
        </div>
    </div>

    <!-- Matches List -->
    <div class="flex-1 overflow-y-auto px-6 md:px-8 pb-6">
        <div class="space-y-4">
            @foreach ($matches as $match)
            @php
                $foodListing = $match->foodListing;
                $restaurantName = $foodListing->restaurantProfile->restaurant_name ?? $foodListing->creator->name;
                $foodType = $foodListing->food_type ?? 'Food';
                $quantity = $foodListing->quantity ?? 'N/A';
                $unit = $foodListing->unit ?? 'units';
                $pickupScheduledAt = $match->pickup_scheduled_at;
                $distance = $match->distance;
                $status = $match->status;
                $rating = $match->rating ?? 0;
            @endphp
            <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex gap-4">
                        {{-- Status Icon --}}
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0
                            @if($status == 'pending')
                                bg-amber-100
                            @elseif($status == 'approved' || $status == 'scheduled')
                                bg-emerald-100
                            @elseif($status == 'completed')
                                bg-green-100
                            @else
                                bg-zinc-100
                            @endif">
                            <i data-lucide="@if($status == 'pending') clock @elseif($status == 'approved' || $status == 'scheduled') check-circle @elseif($status == 'completed') utensils @else clock @endif"
                               class="w-6 h-6
                                    @if($status == 'pending') text-amber-600
                                    @elseif($status == 'approved' || $status == 'scheduled') text-emerald-600
                                    @elseif($status == 'completed') text-green-600
                                    @else text-zinc-600 @endif"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-1">
                                <h3 class="font-semibold text-zinc-900">{{ $restaurantName }}</h3>
                                <span class="@if($status == 'pending') bg-amber-100 text-amber-800
                                            @elseif($status == 'approved' || $status == 'scheduled') bg-emerald-100 text-emerald-800
                                            @elseif($status == 'completed') bg-green-100 text-green-800
                                            @else bg-zinc-100 text-zinc-800 @endif
                                            text-xs font-medium px-2 py-1 rounded-full">
                                    {{ ucfirst($status) }}
                                </span>
                                {{-- Ready badge for approved matches that are due --}}
                                @if($status == 'approved' && $pickupScheduledAt && $pickupScheduledAt <= now())
                                    <span class="bg-white text-emerald-700 text-[8px] font-bold px-1.5 py-0.5 rounded shadow-sm uppercase">Ready</span>
                                @endif
                            </div>
                            <p class="text-sm text-zinc-600 mb-2">{{ $quantity }} {{ $unit }} of {{ $foodType }}</p>
                            <div class="flex items-center gap-4 text-xs text-zinc-500">
                                @if($pickupScheduledAt)
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-3 h-3"></i>
                                        {{ \Carbon\Carbon::parse($pickupScheduledAt)->format('M j, Y g:i A') }}
                                    </span>
                                @endif
                                @if($distance)
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                                        {{ $distance }} km away
                                    </span>
                                @endif
                                <span class="flex items-center gap-1">
                                    <i data-lucide="package" class="w-3 h-3"></i>
                                    Quantity: {{ $quantity }}
                                </span>
                                {{-- Show rating for completed matches --}}
                                @if($status == 'completed' && $rating > 0)
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="star" class="w-3 h-3 text-amber-500"></i>
                                        Rated {{ $rating }}/5
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    {{-- Action buttons based on status --}}
                    @if($status == 'approved' && $pickupScheduledAt && $pickupScheduledAt <= now())
                        <button onclick="openVerificationModal('{{ $restaurantName }}', {{ $match->id }})"
                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors">
                            <i data-lucide="scan-line" class="w-4 h-4 inline mr-2"></i>
                            Verify Pickup
                        </button>
                    @endif

                    @if($status != 'completed')
                        <button class="px-4 py-2 bg-white border border-zinc-300 text-zinc-700 rounded-lg text-sm font-medium hover:bg-zinc-50 transition-colors">
                            <i data-lucide="message-circle" class="w-4 h-4 inline mr-2"></i>
                            Message Restaurant
                        </button>
                        <button class="px-4 py-2 border border-zinc-300 text-zinc-700 rounded-lg text-sm font-medium hover:bg-zinc-50 transition-colors">
                            <i data-lucide="phone" class="w-4 h-4 inline mr-2"></i>
                            Call Restaurant
                        </button>
                    @else
                        <button class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium hover:bg-green-200 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4 inline mr-2"></i>
                            View Details
                        </button>
                        <button class="px-4 py-2 bg-white border border-zinc-300 text-zinc-700 rounded-lg text-sm font-medium hover:bg-zinc-50 transition-colors">
                            <i data-lucide="repeat" class="w-4 h-4 inline mr-2"></i>
                            Request Again
                        </button>
                    @endif
                </div>
            </div>
            @endforeach

            {{-- Empty state --}}
            @if ($matches->isEmpty())
            <div class="text-center py-12">
                <i data-lucide="link" class="w-16 h-16 text-zinc-300 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-zinc-900 mb-2">No matches found</h3>
                <p class="text-zinc-500 mb-6">You haven't matched with any food listings yet.</p>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('recipient.available-food') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <i data-lucide="search" class="w-4 h-4 inline mr-2"></i>
                        Browse Food
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- Verification & Rating Modal -->
<div id="verification-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('verification-modal').classList.add('hidden')"></div>
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-zinc-100 text-center">
            <h3 class="lg font-bold text-zinc-900">Complete Pickup</h3>
            <p class="text-sm text-zinc-500" id="verification-restaurant-name">Restaurant Name • Order #<span id="verification-order-id">0000</span></p>
        </div>

        <div class="p-6 overflow-y-auto space-y-6">
            <!-- Step 1: Scan -->
            <div class="space-y-3">
                <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider">1. Verification</label>
                <div class="bg-zinc-900 rounded-xl p-4 text-center relative overflow-hidden group cursor-pointer">
                    <i data-lucide="camera" class="w-8 h-8 text-zinc-500 mx-auto mb-2"></i>
                    <p class="text-sm text-zinc-400">QR Code Scanner</p>
                    <p class="text-xs text-zinc-500 mt-1">Tap to scan pickup code</p>
                </div>
            </div>

            <!-- Step 2: Confirm -->
            <div class="space-y-3">
                <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider">2. Confirmation</label>
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="check" class="w-5 h-5 text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-emerald-900">Pickup Verified</p>
                            <p class="text-xs text-emerald-700">Food successfully collected</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Rating -->
            <div class="space-y-3">
                <label class="block text-xs font-bold text-zinc-400 uppercase tracking-wider">3. Rate Your Experience</label>
                <div class="space-y-2">
                    <p class="text-sm text-zinc-700">How was your pickup experience?</p>
                    <div class="flex gap-1">
                        <button class="p-2 text-amber-400 hover:text-amber-500">
                            <i data-lucide="star" class="w-5 h-5"></i>
                        </button>
                        <button class="p-2 text-amber-400 hover:text-amber-500">
                            <i data-lucide="star" class="w-5 h-5"></i>
                        </button>
                        <button class="p-2 text-amber-400 hover:text-amber-500">
                            <i data-lucide="star" class="w-5 h-5"></i>
                        </button>
                        <button class="p-2 text-amber-400 hover:text-amber-500">
                            <i data-lucide="star" class="w-5 h-5"></i>
                        </button>
                        <button class="p-2 text-gray-300 hover:text-amber-500">
                            <i data-lucide="star" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-zinc-100">
            <button onclick="completePickup()" class="w-full py-3 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors mb-2">
                Complete Pickup
            </button>
            <button onclick="document.getElementById('verification-modal').classList.add('hidden')" class="w-full text-xs text-zinc-500 hover:text-zinc-900">Cancel</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openVerificationModal(restaurantName, orderId) {
        document.getElementById('verification-restaurant-name').textContent = restaurantName + ' • Order #<span id="verification-order-id">' + orderId + '</span>';
        document.getElementById('verification-modal').classList.remove('hidden');
    }

    function completePickup() {
        // Placeholder for pickup completion logic
        alert('Pickup completed successfully!');
        document.getElementById('verification-modal').classList.add('hidden');
    }

    // Initialize Lucide icons
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection