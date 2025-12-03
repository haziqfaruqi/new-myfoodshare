@extends('admin.layouts.admin-layout')

@php
    use Illuminate\Support\Facades\Storage;
    use App\Models\FoodListing;
    use App\Models\User;
    use App\Models\PickupVerification;
@endphp

@section('title', 'Dashboard - Admin Panel')

@section('content')

<div class="flex-1 overflow-y-auto p-6 md:p-8 scroll-smooth">

    <div class="max-w-6xl mx-auto space-y-8">

        <!-- Welcome & Stats -->
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Dashboard</h1>
                    <p class="text-sm text-zinc-500 mt-1">Manage listings, track impact, and help the community.</p>
                </div>
              </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Stat Card 1 -->
                    <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-medium text-zinc-500 uppercase">Active Listings</span>
                            <i data-lucide="package" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-semibold tracking-tight text-zinc-900">24</span>
                            <span class="text-xs font-medium text-emerald-600">+4 today</span>
                        </div>
                    </div>

                    <!-- Stat Card 2 -->
                    <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-medium text-zinc-500 uppercase">Matches Found</span>
                            <i data-lucide="users" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-semibold tracking-tight text-zinc-900">1,204</span>
                            <span class="text-xs font-medium text-zinc-500">Lifetime</span>
                        </div>
                    </div>

                    <!-- Stat Card 3 -->
                    <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-medium text-zinc-500 uppercase">Meals Saved</span>
                            <i data-lucide="utensils" class="w-4 h-4 text-orange-600"></i>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-semibold tracking-tight text-zinc-900">856</span>
                            <span class="text-xs font-medium text-emerald-600">↑ 12%</span>
                        </div>
                    </div>

                    <!-- Stat Card 4 -->
                    <div class="p-4 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-medium text-zinc-500 uppercase">Verification Rate</span>
                            <i data-lucide="shield-check" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-semibold tracking-tight text-zinc-900">98.2%</span>
                            <span class="text-xs font-medium text-zinc-500">Last 30 days</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left: Food Listings (Broad View) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-zinc-900">Food Listings Pending Approval</h2>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium text-zinc-500">Sort by:</span>
                            <select class="bg-transparent text-xs font-medium text-zinc-900 border-none focus:ring-0 cursor-pointer pr-6">
                                <option>Latest</option>
                                <option>Expiry (Soonest)</option>
                                <option>Quantity</option>
                            </select>
                        </div>
                    </div>

                    @if(isset($pendingFoodListings) && $pendingFoodListings->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($pendingFoodListings as $listing)
                                <div class="group bg-white rounded-xl border border-zinc-200 overflow-hidden hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300">
                                    <div class="h-32 bg-zinc-100 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent z-10"></div>
                                        @if($listing->images && count($listing->images) > 0)
                                            <img src="{{ Storage::url($listing->images[0]) }}" alt="{{ $listing->food_name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1547592180-85f173990554?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80');"></div>
                                        @endif
                                        <div class="absolute bottom-3 left-3 z-20 flex flex-col">
                                            <span class="text-white font-medium text-sm">{{ $listing->food_name }}</span>
                                            <span class="text-zinc-200 text-xs">{{ ucfirst($listing->category) }}</span>
                                        </div>
                                        <span class="absolute top-3 right-3 z-20 bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full tracking-wide">PENDING</span>
                                    </div>
                                    <div class="p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="text-xs text-zinc-500 flex items-center gap-1">
                                                    <i data-lucide="map-pin" class="w-3 h-3"></i> {{ $listing->pickup_location }}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-1 text-xs font-medium text-orange-600 bg-orange-50 px-2 py-0.5 rounded">
                                                <i data-lucide="clock" class="w-3 h-3"></i>
                                                Exp: {{ $listing->expiry_date?->format('M d') }}
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="w-5 h-5 rounded-full bg-zinc-200 flex items-center justify-center text-[10px] font-bold text-zinc-600">
                                                {{ substr($listing->restaurantProfile->restaurant_name ?? $listing->creator->name, 0, 2) }}
                                            </div>
                                            <span class="text-xs text-zinc-600">
                                                {{ $listing->restaurantProfile->restaurant_name ?? $listing->creator->name }} •
                                                {{ $listing->quantity }} {{ $listing->unit }}
                                            </span>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.food-listings') }}" class="flex-1 py-2 bg-amber-500 hover:bg-amber-400 text-white text-sm font-medium rounded-lg transition-colors text-center">
                                                Review
                                            </a>
                                            <a href="{{ route('admin.food-listings.show', $listing->id) }}" class="flex-1 py-2 bg-white border border-zinc-200 text-zinc-900 text-sm font-medium rounded-lg hover:border-emerald-500 hover:text-emerald-600 transition-colors text-center">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-xl border border-zinc-200 p-8 text-center">
                            <i data-lucide="check-circle" class="w-12 h-12 text-emerald-500 mx-auto mb-4"></i>
                            <h3 class="text-lg font-medium text-zinc-900 mb-2">No Pending Approvals</h3>
                            <p class="text-sm text-zinc-500">All food listings have been reviewed and processed.</p>
                        </div>
                    @endif
                </div>

                <!-- Right: Activity & Verification -->
                <div class="space-y-6">

                    <!-- Active Pickup Panel -->
                    <div class="bg-gradient-to-br from-zinc-900 to-zinc-800 rounded-xl p-5 text-white shadow-lg relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-32 bg-emerald-500/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-sm font-medium text-zinc-100">Active Pickup</h3>
                                    <p class="text-xs text-zinc-400 mt-1">Order #4922-A • 15 mins ago</p>
                                </div>
                                <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-bold px-2 py-0.5 rounded-full">READY</span>
                            </div>

                            <div class="flex items-center gap-3 bg-white/5 p-3 rounded-lg border border-white/10 mb-4">
                                <div class="w-10 h-10 rounded bg-white/10 flex items-center justify-center">
                                    <i data-lucide="coffee" class="w-5 h-5 text-zinc-300"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">Starbucks Leftovers</p>
                                    <p class="text-xs text-zinc-400">12 items • Bagged</p>
                                </div>
                            </div>

                            <button class="w-full py-2 bg-emerald-500 hover:bg-emerald-400 text-zinc-900 text-sm font-semibold rounded-lg shadow-md transition-colors flex items-center justify-center gap-2">
                                <i data-lucide="qr-code" class="w-4 h-4"></i>
                                Show Pickup QR
                            </button>
                        </div>
                    </div>

                    <!-- Recent Activity List -->
                    <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-5">
                        <h3 class="text-sm font-medium text-zinc-900 mb-4">Recent Activity</h3>
                        <div class="space-y-4">
                            <!-- Item 1 -->
                            <div class="flex gap-3 relative">
                                <div class="absolute left-[11px] top-8 bottom-[-16px] w-[1px] bg-zinc-100"></div>
                                <div class="w-6 h-6 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center shrink-0 z-10">
                                    <i data-lucide="check" class="w-3 h-3 text-emerald-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-zinc-900 font-medium">Donation Approved</p>
                                    <p class="text-[10px] text-zinc-500">Admin verified "Sushi Platter"</p>
                                    <p class="text-[10px] text-zinc-400 mt-0.5">2m ago</p>
                                </div>
                            </div>
                            <!-- Item 2 -->
                            <div class="flex gap-3 relative">
                                <div class="absolute left-[11px] top-8 bottom-[-16px] w-[1px] bg-zinc-100"></div>
                                <div class="w-6 h-6 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0 z-10">
                                    <i data-lucide="user-check" class="w-3 h-3 text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-zinc-900 font-medium">Match Found</p>
                                    <p class="text-[10px] text-zinc-500">Linked with local shelter</p>
                                    <p class="text-[10px] text-zinc-400 mt-0.5">1h ago</p>
                                </div>
                            </div>
                            <!-- Item 3 -->
                            <div class="flex gap-3 relative">
                                <div class="w-6 h-6 rounded-full bg-zinc-50 border border-zinc-100 flex items-center justify-center shrink-0 z-10">
                                    <i data-lucide="upload-cloud" class="w-3 h-3 text-zinc-500"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-zinc-900 font-medium">Listing Created</p>
                                    <p class="text-[10px] text-zinc-500">By Joe's Pizza</p>
                                    <p class="text-[10px] text-zinc-400 mt-0.5">3h ago</p>
                                </div>
                            </div>
                        </div>
                        <button class="w-full mt-4 text-xs font-medium text-zinc-500 hover:text-zinc-900 border-t border-zinc-100 pt-3">
                            View Full History
                        </button>
                    </div>

                </div>
            </div>

            <!-- Admin Table Section (Simulated Data) -->
            <div class="pt-2">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-medium text-zinc-900">Pending Approvals (Admin)</h3>
                    <button class="text-xs text-emerald-600 font-medium hover:underline">Manage All</button>
                </div>
                <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-zinc-50 text-zinc-500 border-b border-zinc-200">
                                <tr>
                                    <th class="px-6 py-3 font-medium">Donor</th>
                                    <th class="px-6 py-3 font-medium">Food Type</th>
                                    <th class="px-6 py-3 font-medium">Qty</th>
                                    <th class="px-6 py-3 font-medium">Submitted</th>
                                    <th class="px-6 py-3 font-medium">Status</th>
                                    <th class="px-6 py-3 font-medium text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                <tr class="hover:bg-zinc-50/50 transition-colors">
                                    <td class="px-6 py-3 font-medium text-zinc-900">Downtown Bistro</td>
                                    <td class="px-6 py-3 text-zinc-600">Soup & Salads</td>
                                    <td class="px-6 py-3 text-zinc-600">5kg</td>
                                    <td class="px-6 py-3 text-zinc-500">10 mins ago</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                            <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                            Pending
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button class="text-zinc-400 hover:text-red-600"><i data-lucide="x" class="w-4 h-4"></i></button>
                                            <button class="text-zinc-400 hover:text-emerald-600"><i data-lucide="check" class="w-4 h-4"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-zinc-50/50 transition-colors">
                                    <td class="px-6 py-3 font-medium text-zinc-900">City Market</td>
                                    <td class="px-6 py-3 text-zinc-600">Dairy Products</td>
                                    <td class="px-6 py-3 text-zinc-600">12 units</td>
                                    <td class="px-6 py-3 text-zinc-500">45 mins ago</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                            <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                            Pending
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button class="text-zinc-400 hover:text-red-600"><i data-lucide="x" class="w-4 h-4"></i></button>
                                            <button class="text-zinc-400 hover:text-emerald-600"><i data-lucide="check" class="w-4 h-4"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <footer class="mt-12 mb-4 text-center">
            <p class="text-xs text-zinc-400">© 2024 MyFoodshare Platform. Reducing waste, feeding communities.</p>
        </footer>

    </div>

    <!-- Floating Action Button for Mobile -->
    <button class="md:hidden absolute bottom-6 right-6 w-12 h-12 bg-zinc-900 text-white rounded-full shadow-lg flex items-center justify-center z-30">
        <i data-lucide="plus" class="w-6 h-6"></i>
    </button>

</div>

@endsection

@push('scripts')
<script>
    // Initialize Lucide Icons
    lucide.createIcons();
</script>
@endpush