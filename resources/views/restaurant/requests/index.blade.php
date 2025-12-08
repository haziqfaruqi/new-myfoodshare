@extends('restaurant.layouts.restaurant-layout')

@section('title', 'Manage Requests - Restaurant Portal')

@section('content')
<div class="space-y-6 w-full">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Manage Requests</h1>
        <p class="text-sm text-zinc-500 mt-1">Review and manage pickup requests from recipients.</p>
    </div>

    <!-- Request Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Pending</span>
                <div class="p-2 bg-amber-50 rounded-lg">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $pendingRequests }}</span>
                <span class="text-xs font-medium text-amber-600">awaiting review</span>
            </div>
        </div>

        <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Approved</span>
                <div class="p-2 bg-emerald-50 rounded-lg">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $approvedRequests }}</span>
                <span class="text-xs font-medium text-emerald-600">scheduled</span>
            </div>
        </div>

        <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Rejected</span>
                <div class="p-2 bg-red-50 rounded-lg">
                    <i data-lucide="x-circle" class="w-4 h-4 text-red-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $rejectedRequests }}</span>
                <span class="text-xs font-medium text-red-600">not approved</span>
            </div>
        </div>

        <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Completed</span>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i data-lucide="package-check" class="w-4 h-4 text-blue-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $completedRequests }}</span>
                <span class="text-xs font-medium text-blue-600">this month</span>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-xl border border-zinc-200">
        <div class="border-b border-zinc-200">
            <nav class="flex -mb-px">
                <a href="{{ route('restaurant.requests') }}?status=pending"
                   class="py-4 px-6 border-b-2 text-sm font-medium {{ request('status') == 'pending' || !request('status') ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-gray-300' }}">
                    Pending Requests
                    <span class="ml-2 bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full">{{ $pendingRequests }}</span>
                </a>
                <a href="{{ route('restaurant.requests') }}?status=approved"
                   class="py-4 px-6 border-b-2 text-sm font-medium {{ request('status') == 'approved' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-gray-300' }}">
                    Approved
                    <span class="ml-2 bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full">{{ $approvedRequests }}</span>
                </a>
                <a href="{{ route('restaurant.requests') }}?status=rejected"
                   class="py-4 px-6 border-b-2 text-sm font-medium {{ request('status') == 'rejected' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-gray-300' }}">
                    Rejected
                    <span class="ml-2 bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full">{{ $rejectedRequests }}</span>
                </a>
                <a href="{{ route('restaurant.requests') }}?status=completed"
                   class="py-4 px-6 border-b-2 text-sm font-medium {{ request('status') == 'completed' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-gray-300' }}">
                    Completed
                    <span class="ml-2 bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full">{{ $completedRequests }}</span>
                </a>
            </nav>
        </div>

        <!-- Requests List -->
        <div class="p-6">
            @forelse ($requests as $request)
            <div class="border border-zinc-200 rounded-xl p-6 mb-4 hover:shadow-md transition-shadow">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-start gap-4">
                            @if($request->foodListing->images && isset($request->foodListing->images[0]))
                                <img src="{{ asset('storage/' . $request->foodListing->images[0]) }}"
                                     class="w-12 h-12 rounded-lg object-cover"
                                     alt="{{ $request->foodListing->food_name }}">
                            @else
                                <div class="w-12 h-12 bg-zinc-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="package" class="w-6 h-6 text-zinc-600"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-semibold text-zinc-900">{{ $request->foodListing->food_name }}</h3>
                                        <p class="text-sm text-zinc-500 mt-1">{{ Str::limit($request->foodListing->description, 100) }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request->getStatusColorClass() }}">
                                        {{ $request->status_label }}
                                    </span>
                                </div>

                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-xs text-zinc-500">Recipient:</span>
                                        <span class="font-medium text-zinc-900 ml-1">{{ $request->recipient ? ($request->recipient->organization_name ?? $request->recipient->name) : 'Unknown Recipient' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-zinc-500">Requested:</span>
                                        <span class="font-medium text-zinc-900 ml-1">{{ $request->created_at->format('M j, Y g:i A') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-zinc-500">Quantity:</span>
                                        <span class="font-medium text-zinc-900 ml-1">{{ $request->foodListing->quantity }} {{ $request->foodListing->unit }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-zinc-500">Expiry:</span>
                                        <span class="font-medium text-zinc-900 ml-1">{{ $request->foodListing->expiry_date?->format('M j, Y') }}</span>
                                    </div>
                                </div>

                                @if($request->status === 'approved' && $request->pickup_scheduled_at)
                                <div class="mt-3 p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                                    <div class="flex items-center gap-2 text-sm text-emerald-800">
                                        <i data-lucide="calendar-check" class="w-4 h-4"></i>
                                        <span class="font-medium">Pickup Scheduled:</span>
                                        <span>{{ $request->pickup_scheduled_at->format('M j, Y g:i A') }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('restaurant.requests.show', $request->id) }}" class="text-xs font-medium text-zinc-600 hover:text-zinc-900 px-3 py-1.5 rounded-lg hover:bg-zinc-50 transition-colors">
                            View Details
                        </a>

                        @if($request->status === 'pending')
                        <form action="{{ route('restaurant.requests.approve', $request->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-medium bg-emerald-600 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-colors">
                                Approve
                            </button>
                        </form>
                        <form action="{{ route('restaurant.requests.reject', $request->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-medium bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 transition-colors">
                                Reject
                            </button>
                        </form>
                        @endif

                        @if($request->status === 'approved')
                        <a href="{{ route('restaurant.schedule') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-zinc-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="inbox" class="w-8 h-8 text-zinc-400"></i>
                </div>
                <h3 class="font-medium text-zinc-900 mb-1">No requests found</h3>
                <p class="text-sm text-zinc-500 mb-4">
                    @if(request('status'))
                        No {{ request('status') }} requests at the moment.
                    @else
                        No pending requests to review.
                    @endif
                </p>
                @if(request('status') !== 'pending')
                    <a href="{{ route('restaurant.requests') }}?status=pending" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700 transition-colors">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        View Pending Requests
                    </a>
                @endif
            </div>
            @endforelse
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