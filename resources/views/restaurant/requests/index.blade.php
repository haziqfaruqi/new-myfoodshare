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
                <span class="text-xs font-medium text-blue-600">completed</span>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-xl border border-zinc-200">
        <div class="border-b border-zinc-200">
            <nav class="flex -mb-px">
                <a href="{{ route('restaurant.requests') }}"
                   class="py-4 px-6 border-b-2 text-sm font-medium {{ !request('status') ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-gray-300' }}">
                    All
                    <span class="ml-2 bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full">{{ $pendingRequests + $approvedRequests + $rejectedRequests + $completedRequests + $scheduledRequests }}</span>
                </a>
                <a href="{{ route('restaurant.requests') }}?status=pending"
                   class="py-4 px-6 border-b-2 text-sm font-medium {{ request('status') == 'pending' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-gray-300' }}">
                    Pending Requests
                    <span class="ml-2 bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full">{{ $pendingRequests }}</span>
                </a>
                <a href="{{ route('restaurant.requests') }}?status=approved"
                   class="py-4 px-6 border-b-2 text-sm font-medium {{ request('status') == 'approved' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-gray-300' }}">
                    Approved
                    <span class="ml-2 bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full">{{ $approvedRequests }}</span>
                </a>
                <a href="{{ route('restaurant.requests') }}?status=scheduled"
                   class="py-4 px-6 border-b-2 text-sm font-medium {{ request('status') == 'scheduled' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-gray-300' }}">
                    Scheduled
                    <span class="ml-2 bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full">{{ $scheduledRequests }}</span>
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
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('restaurant.requests.show', $request->id) }}" class="text-xs font-medium text-zinc-600 hover:text-zinc-900 px-3 py-1.5 rounded-lg hover:bg-zinc-50 transition-colors flex items-center gap-1">
                            <i data-lucide="eye" class="w-3 h-3"></i>
                            View Details
                        </a>

                        @if($request->status === 'pending')
                        <form action="{{ route('restaurant.requests.approve', $request->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-medium bg-emerald-600 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-1">
                                <i data-lucide="check" class="w-3 h-3"></i>
                                Approve
                            </button>
                        </form>
                        <form action="{{ route('restaurant.requests.reject', $request->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-medium bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 transition-colors flex items-center gap-1">
                                <i data-lucide="x" class="w-3 h-3"></i>
                                Reject
                            </button>
                        </form>
                        @endif

                        @if($request->status === 'approved')
                            <button type="button" onclick="openScheduleModal({{ $request->id }})" class="text-xs font-medium bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                Schedule
                            </button>
                        @endif

                        @if($request->status === 'scheduled')
                            <button class="text-xs font-medium bg-green-600 text-white px-3 py-1.5 rounded-lg hover:bg-green-700 transition-colors flex items-center gap-1">
                                <i data-lucide="check-circle" class="w-3 h-3"></i>
                                Scheduled
                            </button>
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

    <!-- Schedule Modal -->
    <div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-zinc-900">Schedule Pickup</h3>
                <button onclick="closeScheduleModal()" class="text-zinc-400 hover:text-zinc-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="scheduleForm" action="{{ route('restaurant.requests.schedule', '__MATCH_ID__') }}" method="POST">
                @csrf
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="space-y-4">
                    <div>
                        <label for="pickup_date" class="block text-sm font-medium text-zinc-700 mb-1">
                            Pickup Date
                        </label>
                        <input type="date"
                               id="pickup_date"
                               name="pickup_date"
                               required
                               class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               min="{{ date('Y-m-d') }}">
                    </div>

                    <div>
                        <label for="pickup_time" class="block text-sm font-medium text-zinc-700 mb-1">
                            Pickup Time
                        </label>
                        <input type="time"
                               id="pickup_time"
                               name="pickup_time"
                               required
                               class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-zinc-700 mb-1">
                            Notes (Optional)
                        </label>
                        <textarea id="notes"
                                  name="notes"
                                  rows="3"
                                  maxlength="500"
                                  placeholder="Any special instructions for the pickup..."
                                  class="w-full px-3 py-2 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                        <p class="text-xs text-zinc-500 mt-1">{{ 500 - (strlen(request('notes') ?? '')) }} characters remaining</p>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Schedule Pickup
                    </button>
                    <button type="button" onclick="closeScheduleModal()" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                        Cancel
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

        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('pickup_date').setAttribute('min', today);
    });

    // Schedule Modal Functions
    function openScheduleModal(matchId) {
        // Set the form action with the correct match ID
        const form = document.getElementById('scheduleForm');
        form.action = form.action.replace('__MATCH_ID__', matchId);

        // Show modal
        document.getElementById('scheduleModal').classList.remove('hidden');
        document.getElementById('scheduleModal').classList.add('flex');

        // Reset form
        form.reset();

        // Set default time to current time + 1 hour
        const now = new Date();
        now.setHours(now.getHours() + 1);
        const timeString = now.toTimeString().slice(0, 5);
        document.getElementById('pickup_time').value = timeString;

        // Set default date to today
        document.getElementById('pickup_date').value = new Date().toISOString().split('T')[0];
    }

    function closeScheduleModal() {
        document.getElementById('scheduleModal').classList.add('hidden');
        document.getElementById('scheduleModal').classList.remove('flex');
    }

    // Handle character count for notes
    document.getElementById('notes')?.addEventListener('input', function() {
        const remaining = 500 - this.value.length;
        const counter = this.parentElement.querySelector('.text-xs');
        if (counter) {
            counter.textContent = `${remaining} characters remaining`;
            if (remaining < 0) {
                counter.classList.add('text-red-500');
                counter.classList.remove('text-zinc-500');
            } else {
                counter.classList.add('text-zinc-500');
                counter.classList.remove('text-red-500');
            }
        }
    });

    // Close modal when clicking outside
    document.getElementById('scheduleModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeScheduleModal();
        }
    });

    // Handle form submission
    document.getElementById('scheduleForm')?.addEventListener('submit', function(e) {
        // Add loading state to button
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Scheduling...';
        submitBtn.disabled = true;

        // Restore button state on error
        this.addEventListener('error', function() {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
</script>
@endsection