@extends('admin.layouts.admin-layout')


@section('title', 'Pickup Verification - Admin Panel')

@section('content')

<div class="max-w-6xl mx-auto space-y-8 p-6 md:p-8">

    <!-- Header -->
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Pickup Verification</h1>
        <p class="text-sm text-zinc-500">Monitor and verify food pickup activities and QR code validations</p>
    </div>

    <!-- Verification Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 uppercase">Active Pickups</span>
                <i data-lucide="truck" class="w-4 h-4 text-emerald-600"></i>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ $stats['active_pickups'] }}</span>
                <span class="text-xs font-medium text-emerald-600">In progress</span>
            </div>
        </div>

        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 uppercase">Completed Today</span>
                <i data-lucide="check-circle" class="w-4 h-4 text-blue-600"></i>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ $stats['completed_today'] }}</span>
                <span class="text-xs font-medium text-blue-600">Verified</span>
            </div>
        </div>

        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 uppercase">Verification Rate</span>
                <i data-lucide="trending-up" class="w-4 h-4 text-emerald-600"></i>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ $stats['verification_rate'] }}%</span>
                <span class="text-xs font-medium text-emerald-600">This month</span>
            </div>
        </div>

        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 uppercase">Total Verified</span>
                <i data-lucide="award" class="w-4 h-4 text-purple-600"></i>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ $stats['total_verified'] }}</span>
                <span class="text-xs font-medium text-purple-600">All time</span>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Active Pickups Needing Verification -->
        <div class="space-y-6">
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-zinc-900">Active Pickups</h2>
                        <span class="text-sm text-amber-600 font-medium">{{ $activePickups->count() }} awaiting verification</span>
                    </div>
                    <p class="text-xs text-zinc-500 mt-1">Pickups scheduled for the next 24 hours</p>
                </div>

                <div class="divide-y divide-zinc-100">
                    @forelse($activePickups as $activePickup)
                    <div class="p-4 hover:bg-zinc-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                                    <i data-lucide="clock" class="w-5 h-5 text-amber-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-medium text-zinc-900">{{ $activePickup->foodListing->food_name }}</h3>
                                        @if($activePickup->pickup_scheduled_at->diffInHours(now()) <= 2)
                                            <span class="text-xs font-medium bg-red-100 text-red-700 px-2 py-0.5 rounded-full pulse-slow">URGENT</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-zinc-500 mt-1">{{ $activePickup->foodListing->pickup_location }}</p>
                                    <div class="flex items-center gap-4 mt-2">
                                        <div class="flex items-center gap-1 text-xs text-zinc-600">
                                            <i data-lucide="user" class="w-3 h-3"></i>
                                            {{ $activePickup->recipient->organization_name ?? $activePickup->recipient->name }}
                                        </div>
                                        <div class="flex items-center gap-1 text-xs text-zinc-600">
                                            <i data-lucide="truck" class="w-3 h-3"></i>
                                            {{ $activePickup->pickup_scheduled_at->format('M j, g:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button onclick="showQRCode({{ $activePickup->id }})" class="p-2 bg-zinc-100 hover:bg-zinc-200 rounded-md transition-colors" title="View QR Code">
                                    <i data-lucide="qr-code" class="w-4 h-4 text-zinc-600"></i>
                                </button>
                                <button class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-medium rounded-md transition-colors">
                                    Verify
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center">
                        <i data-lucide="check-circle" class="w-12 h-12 text-emerald-500 mx-auto mb-4"></i>
                        <h3 class="text-lg font-medium text-zinc-900 mb-2">No Active Pickups</h3>
                        <p class="text-sm text-zinc-500">All scheduled pickups have been completed or verified.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Completed Pickups -->
        <div class="space-y-6">

            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-5 text-white shadow-lg">
                <h3 class="text-sm font-medium mb-3">Quick Actions</h3>
                <div class="space-y-2">
                    <button class="w-full flex items-center justify-between text-sm font-medium bg-white/20 hover:bg-white/30 rounded-md p-2 transition-colors">
                        <span class="flex items-center gap-2">
                            <i data-lucide="qr-code-scanner" class="w-4 h-4"></i>
                            Scan QR Code
                        </span>
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                    <button class="w-full flex items-center justify-between text-sm font-medium bg-white/20 hover:bg-white/30 rounded-md p-2 transition-colors">
                        <span class="flex items-center gap-2">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            Generate Report
                        </span>
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-5">
                <h3 class="text-sm font-medium text-zinc-900 mb-4">Recent Verifications</h3>
                <div class="space-y-4">
                    @forelse($recentPickups as $recentPickup)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-zinc-900 truncate">{{ $recentPickup->match->foodListing->food_name }}</p>
                            <div class="flex items-center gap-2 text-xs text-zinc-500 mt-0.5">
                                <span>{{ $recentPickup->match->recipient->organization_name ?? $recentPickup->match->recipient->name }}</span>
                                <span>•</span>
                                <span>{{ $recentPickup->scanned_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <button class="text-zinc-400 hover:text-emerald-600 transition-colors" title="View Details">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @empty
                    <p class="text-xs text-zinc-500 text-center py-4">No recent verifications</p>
                    @endforelse
                </div>
                <button class="w-full mt-4 text-xs font-medium text-emerald-600 hover:text-emerald-700 border-t border-emerald-100 pt-3">
                    View Full History
                </button>
            </div>

            <!-- Verification Alerts -->
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-amber-800 mb-1">Verification Alerts</h3>
                        <p class="text-xs text-amber-700">3 pickups are overdue for verification. Please check these immediately to ensure compliance.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="mt-12 mb-4 text-center">
        <p class="text-xs text-zinc-400">© 2024 FoodShare Platform. Reducing waste, feeding communities.</p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    function showQRCode(pickupId) {
        document.getElementById('qr-modal').classList.remove('hidden');
        // In a real app, you would fetch the specific pickup data here
        const activePickup = {{ $activePickups->count() > 0 ? json_encode($activePickups->first()) : 'null' }};
    }

    function closeQRModal() {
        document.getElementById('qr-modal').classList.add('hidden');
    }

    // Initialize Lucide Icons
    lucide.createIcons();
</script>
@endpush

<!-- QR Code Modal -->
<div id="qr-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeQRModal()"></div>
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between">
            <h3 class="font-semibold text-zinc-900">Pickup QR Code</h3>
            <button onclick="closeQRModal()" class="text-zinc-400 hover:text-zinc-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="p-6 text-center">
            <div class="w-48 h-48 mx-auto bg-zinc-100 rounded-lg flex items-center justify-center mb-4">
                <div class="text-center">
                    <i data-lucide="qr-code" class="w-16 h-16 text-zinc-400 mx-auto mb-2"></i>
                    <p class="text-xs text-zinc-500">QR Code Image</p>
                </div>
            </div>
            <div class="space-y-2 text-left">
                @if($activePickups->count() > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-600">Pickup ID:</span>
                    <span class="font-medium text-zinc-900">#{{ str_pad($activePickups->first()->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-600">Food Item:</span>
                    <span class="font-medium text-zinc-900">{{ $activePickups->first()->foodListing->food_name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-600">Scheduled:</span>
                    <span class="font-medium text-zinc-900">{{ $activePickups->first()->pickup_scheduled_at->format('M j, Y g:i A') }}</span>
                </div>
                @else
                <div class="text-center text-sm text-zinc-500">
                    No pickup data available
                </div>
                @endif
            </div>
            <div class="mt-6 flex gap-3">
                <button class="flex-1 py-2 border border-zinc-200 text-zinc-700 rounded-md font-medium hover:bg-zinc-50 transition-colors">
                    Copy Code
                </button>
                <button class="flex-1 py-2 bg-emerald-600 text-white rounded-md font-medium hover:bg-emerald-500 transition-colors">
                    Mark as Verified
                </button>
            </div>
        </div>
    </div>
</div>