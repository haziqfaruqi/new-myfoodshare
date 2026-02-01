@extends('restaurant.layouts.restaurant-layout')

@section('title', 'Match Details')

@section('content')
<div class="flex-1 flex flex-col h-screen overflow-hidden">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 p-6 md:p-8 pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Match Details</h1>
            <p class="text-sm text-zinc-500 mt-1">View and manage food matching details.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('restaurant.requests') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back to Requests
            </a>
        </div>
    </div>

    <!-- Match Details Card -->
    <div class="flex-1 overflow-y-auto px-6 md:px-8 pb-8">
        <div class="max-w-4xl mx-auto space-y-6">

            <!-- Food Information -->
            <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-zinc-900 mb-4 flex items-center">
                    <i data-lucide="utensils" class="w-5 h-5 mr-2 text-green-600"></i>
                    Food Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-zinc-500">Food Name</label>
                        <p class="text-base text-zinc-900">{{ $match->foodListing->food_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-zinc-500">Quantity</label>
                        <p class="text-base text-zinc-900">{{ $match->foodListing->quantity }} {{ $match->foodListing->unit }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-zinc-500">Category</label>
                        <p class="text-base text-zinc-900">{{ $match->foodListing->category }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-zinc-500">Expiry Date</label>
                        <p class="text-base text-zinc-900">{{ $match->foodListing->expiry_date->format('M d, Y') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-zinc-500">Description</label>
                        <p class="text-base text-zinc-900">{{ $match->foodListing->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Recipient Information -->
            <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-zinc-900 mb-4 flex items-center">
                    <i data-lucide="users" class="w-5 h-5 mr-2 text-blue-600"></i>
                    Recipient Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
    // Get recipient user from recipient_id
    $recipientUser = App\Models\User::find($match->recipient_id);
@endphp
<div>
    <label class="text-sm font-medium text-zinc-500">Name</label>
    <p class="text-base text-zinc-900">{{ $recipientUser?->name ?? 'Recipient information not available' }}</p>
</div>
<div>
    <label class="text-sm font-medium text-zinc-500">Email</label>
    <p class="text-base text-zinc-900">{{ $recipientUser?->email ?? 'N/A' }}</p>
</div>
<div>
    <label class="text-sm font-medium text-zinc-500">Phone</label>
    <p class="text-base text-zinc-900">{{ $recipientUser?->phone ?? 'N/A' }}</p>
</div>
<div>
    <label class="text-sm font-medium text-zinc-500">Organization</label>
    <p class="text-base text-zinc-900">{{ $recipientUser?->description ?? 'N/A' }}</p>
</div>
<div class="md:col-span-2">
    <label class="text-sm font-medium text-zinc-500">Address</label>
    <p class="text-base text-zinc-900">{{ $recipientUser?->address ?? 'N/A' }}</p>
</div>
                </div>
            </div>

            <!-- Match Status -->
            <div class="bg-white rounded-xl border border-zinc-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-zinc-900 mb-4 flex items-center">
                    <i data-lucide="info" class="w-5 h-5 mr-2 text-purple-600"></i>
                    Match Status
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-zinc-500">Current Status</label>
                        @php
    $statusClass = match($match->status) {
        'pending' => 'bg-yellow-100 text-yellow-800',
        'approved' => 'bg-green-100 text-green-800',
        'scheduled' => 'bg-blue-100 text-blue-800',
        default => 'bg-gray-100 text-gray-800'
    };
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                            {{ ucfirst($match->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-zinc-500">Request Date</label>
                        <p class="text-base text-zinc-900">{{ $match->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    @if($match->status === 'scheduled')
                    <div>
                        <label class="text-sm font-medium text-zinc-500">Scheduled Pickup</label>
                        <p class="text-base text-zinc-900">{{ $match->pickup_scheduled_at->format('M d, Y g:i A') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex flex-wrap gap-3">
                    @if($match->status === 'pending')
                        <form method="POST" action="{{ route('restaurant.requests.approve', $match) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                Approve Request
                            </button>
                        </form>
                        <form method="POST" action="{{ route('restaurant.requests.reject', $match) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
                                <i data-lucide="x" class="w-4 h-4"></i>
                                Reject Request
                            </button>
                        </form>
                    @endif

                    @if($match->status === 'approved')
                        <form method="POST" action="{{ route('restaurant.requests.schedule', $match) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                Schedule Pickup
                            </button>
                        </form>
                    @endif

                    @if($match->status === 'scheduled')
                        <a href="{{ route('restaurant.qr.generate', $match->id) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium transition-colors">
                            <i data-lucide="qrcode" class="w-4 h-4"></i>
                            Generate QR Code
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
@endpush
@endsection