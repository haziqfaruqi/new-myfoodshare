@extends('admin.layouts.admin-layout')

@section('title', 'Pickup Monitoring Report - Admin Panel')

@section('content')

<div class="space-y-8 p-6 md:p-8 w-full">

    <!-- Header -->
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Pickup Monitoring Report</h1>
        <p class="text-sm text-zinc-500">Detailed report of pickup verifications and monitoring activities</p>
    </div>

    <!-- Date Filters and Export -->
    <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <form method="GET" class="flex items-center gap-4">
                    <div>
                        <label class="text-xs font-medium text-zinc-500 uppercase">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="mt-1 px-3 py-2 border border-zinc-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-zinc-500 uppercase">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="mt-1 px-3 py-2 border border-zinc-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-sm font-medium transition-colors">
                        Apply Filters
                    </button>
                </form>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.pickup-monitoring.report', ['export' => 'true', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export CSV
                </a>
                {{-- <a href="{{ route('admin.pickup-monitoring') }}" class="px-4 py-2 border border-zinc-300 text-zinc-700 rounded-lg text-sm font-medium hover:bg-zinc-50 transition-colors">
                    Back to Monitoring
                </a> --}}
            </div>
        </div>
    </div>

    <!-- Report Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 uppercase">Total Pickups</span>
                <i data-lucide="truck" class="w-4 h-4 text-emerald-600"></i>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ $totalPickups }}</span>
                <span class="text-xs font-medium text-zinc-600">All time</span>
            </div>
        </div>

        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 uppercase">Verified</span>
                <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ $verifiedPickups }}</span>
                <span class="text-xs font-medium text-emerald-600">{{ number_format($successRate, 1) }}% rate</span>
            </div>
        </div>

        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 uppercase">Failed</span>
                <i data-lucide="x-circle" class="w-4 h-4 text-red-600"></i>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ $failedPickups }}</span>
                <span class="text-xs font-medium text-red-600">Issues</span>
            </div>
        </div>

        <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-zinc-500 uppercase">Pending</span>
                <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-semibold tracking-tight text-zinc-900">{{ $pendingPickups }}</span>
                <span class="text-xs font-medium text-amber-600">Pending review</span>
            </div>
        </div>
    </div>

    <!-- Daily Statistics -->
    <div class="bg-white border border-zinc-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200">
            <h2 class="text-lg font-medium text-zinc-900">Daily Statistics</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Total Pickups</th>
                            <th class="px-4 py-3">Verified</th>
                            <th class="px-4 py-3">Failed</th>
                            <th class="px-4 py-3">Success Rate</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-zinc-200">
                        @foreach($dailyStats as $date => $pickups)
                        <tr>
                            <td class="px-4 py-3 text-sm text-zinc-900">{{ \Carbon\Carbon::parse($date)->format('M j, Y') }}</td>
                            <td class="px-4 py-3 text-sm text-zinc-900">{{ $pickups->count() }}</td>
                            <td class="px-4 py-3 text-sm text-emerald-600">{{ $pickups->where('verification_status', 'verified')->count() }}</td>
                            <td class="px-4 py-3 text-sm text-red-600">{{ $pickups->where('verification_status', 'failed')->count() }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-zinc-200 rounded-full h-2">
                                        <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ ($pickups->where('verification_status', 'verified')->count() / max(1, $pickups->count())) * 100 }}%"></div>
                                    </div>
                                    <span class="text-xs text-zinc-600 min-w-[45px]">{{ number_format(($pickups->where('verification_status', 'verified')->count() / max(1, $pickups->count())) * 100, 0) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detailed Pickup Verifications -->
    <div class="bg-white border border-zinc-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200">
            <h2 class="text-lg font-medium text-zinc-900">Detailed Pickup Records</h2>
            <p class="text-xs text-zinc-500 mt-1">{{ $pickupVerifications->count() }} pickup records found for the selected period</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-zinc-50 text-xs font-medium text-zinc-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Pickup ID</th>
                        <th class="px-6 py-3">Food Item</th>
                        <th class="px-6 py-3">Restaurant</th>
                        <th class="px-6 py-3">Recipient</th>
                        <th class="px-6 py-3">Scheduled</th>
                        <th class="px-6 py-3">Scanned</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">QR Code</th>
                        <th class="px-6 py-3">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-zinc-200">
                    @foreach($pickupVerifications as $verification)
                    <tr class="hover:bg-zinc-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-zinc-900">#{{ $verification->match->id }}</td>
                        <td class="px-6 py-4 text-sm text-zinc-900">{{ $verification->match->foodListing->food_name }}</td>
                        <td class="px-6 py-4 text-sm text-zinc-600">{{ $verification->match->restaurant->restaurant_name ?? $verification->match->foodListing->creator->name }}</td>
                        <td class="px-6 py-4 text-sm text-zinc-600">{{ $verification->match->recipient->organization_name ?? $verification->match->recipient->name }}</td>
                        <td class="px-6 py-4 text-sm text-zinc-900">{{ $verification->match->pickup_scheduled_at->format('M j, Y g:i A') }}</td>
                        <td class="px-6 py-4 text-sm text-zinc-900">{{ $verification->scanned_at->format('M j, Y g:i A') }}</td>
                        <td class="px-6 py-4">
                            @if($verification->verification_status === 'verified')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Verified
                                </span>
                            @elseif($verification->verification_status === 'failed')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Failed
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-600 font-mono text-xs">{{ $verification->qr_code }}</td>
                        <td class="px-6 py-4 text-sm text-zinc-600">{{ $verification->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination if needed -->
        @if($pickupVerifications->count() > 50)
        <div class="px-6 py-4 border-t border-zinc-200 bg-zinc-50">
            <p class="text-xs text-zinc-500 text-center">Showing first 50 of {{ $pickupVerifications->count() }} records</p>
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