@extends('restaurant.layouts.restaurant-layout')

@section('title', 'Manage Schedule - Restaurant Portal')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Manage Schedule</h1>
        <p class="text-sm text-zinc-500 mt-1">Track and manage scheduled pickups and verification status.</p>
    </div>

    <!-- Schedule Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Today</span>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $todayPickups->count() }}</span>
                <span class="text-xs font-medium text-blue-600">pickups</span>
            </div>
        </div>

        <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Pending</span>
                <div class="p-2 bg-amber-50 rounded-lg">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $pendingPickups->count() }}</span>
                <span class="text-xs font-medium text-amber-600">awaiting pickup</span>
            </div>
        </div>

        <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Completed</span>
                <div class="p-2 bg-emerald-50 rounded-lg">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $completedPickups }}</span>
                <span class="text-xs font-medium text-emerald-600">this week</span>
            </div>
        </div>

        <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Total Donated</span>
                <div class="p-2 bg-purple-50 rounded-lg">
                    <i data-lucide="scale" class="w-4 h-4 text-purple-600"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-zinc-900">{{ $totalDonated }} kg</span>
                <span class="text-xs font-medium text-emerald-600">this month</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Pickups -->
        <div class="bg-white rounded-xl border border-zinc-200">
            <div class="p-6 border-b border-zinc-200">
                <h2 class="text-lg font-semibold text-zinc-900">Upcoming Pickups</h2>
                <p class="text-sm text-zinc-500 mt-1">Pickups scheduled for today</p>
            </div>
            <div class="p-6 space-y-4">
                @forelse($todayPickups as $pickup)
                <div class="border border-zinc-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="package" class="w-5 h-5 text-emerald-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-zinc-900">{{ $pickup->foodListing->food_name }}</h3>
                                <p class="text-sm text-zinc-500 mt-1">{{ Str::limit($pickup->foodListing->description, 60) }}</p>
                                <div class="mt-2 flex items-center gap-4 text-xs">
                                    <div class="flex items-center gap-1 text-zinc-600">
                                        <i data-lucide="users" class="w-3 h-3"></i>
                                        <span>{{ $pickup->recipient->organization_name ?? $pickup->recipient->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-zinc-600">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        <span>{{ $pickup->pickup_scheduled_at?->format('g:i A') ?? 'Not scheduled' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($pickup->pickupVerification)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    Completed
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    Pending
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i data-lucide="calendar-check" class="w-8 h-8 text-zinc-400 mx-auto mb-2"></i>
                    <p class="text-sm text-zinc-500">No pickups scheduled for today</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl border border-zinc-200">
            <div class="p-6 border-b border-zinc-200">
                <h2 class="text-lg font-semibold text-zinc-900">Recent Activity</h2>
                <p class="text-sm text-zinc-500 mt-1">Latest pickup verifications and updates</p>
            </div>
            <div class="p-6 space-y-4">
                @forelse($recentActivity as $activity)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 bg-emerald-100">
                        <i data-lucide="activity" class="w-4 h-4 text-emerald-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-zinc-900">Pickup activity updated</p>
                        <p class="text-xs text-zinc-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i data-lucide="activity" class="w-8 h-8 text-zinc-400 mx-auto mb-2"></i>
                    <p class="text-sm text-zinc-500">No recent activity</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Simple Calendar View -->
    <div class="bg-white rounded-xl border border-zinc-200">
        <div class="p-6 border-b border-zinc-200">
            <h2 class="text-lg font-semibold text-zinc-900">Pickup Calendar</h2>
            <p class="text-sm text-zinc-500 mt-1">Simple calendar view (temporary)</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-7 gap-1 text-center text-xs">
                <!-- Week headers -->
                <div class="p-2 font-medium text-zinc-400">Sun</div>
                <div class="p-2 font-medium text-zinc-400">Mon</div>
                <div class="p-2 font-medium text-zinc-400">Tue</div>
                <div class="p-2 font-medium text-zinc-400">Wed</div>
                <div class="p-2 font-medium text-zinc-400">Thu</div>
                <div class="p-2 font-medium text-zinc-400">Fri</div>
                <div class="p-2 font-medium text-zinc-400">Sat</div>

                <!-- Simple days - just show current week -->
                @foreach(range(1, 7) as $day)
                    <div class="p-2 border border-zinc-100 rounded-lg min-h-[60px] bg-white">
                        <div class="text-sm font-medium text-zinc-900">{{ $day }}</div>
                    </div>
                @endforeach
            </div>
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