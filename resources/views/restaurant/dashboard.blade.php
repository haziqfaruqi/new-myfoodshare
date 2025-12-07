@extends('restaurant.layouts.restaurant-layout')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')

<div class="space-y-8 w-full">
                
                <!-- Welcome Section -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Good Evening, {{ auth()->user()->restaurantProfile->restaurant_name ?? 'Restaurant' }}!</h1>
                        <p class="text-sm text-zinc-500 mt-1">Here's what's happening with your donations today.</p>
                    </div>
                    <div class="flex gap-3">
                         {{-- <button onclick="document.getElementById('pickup-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-zinc-200 shadow-sm rounded-lg text-sm font-medium text-zinc-700 hover:bg-zinc-50 transition-all">
                            <i data-lucide="scan-line" class="w-4 h-4"></i>
                            Scan QR
                        </button> --}}
                        <button onclick="document.getElementById('post-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white shadow-lg shadow-zinc-900/20 rounded-lg text-sm font-medium transition-all">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Post Donation
                        </button>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Total Donated</span>
                            <div class="p-2 bg-emerald-50 rounded-lg">
                                <i data-lucide="scale" class="w-4 h-4 text-emerald-600"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-zinc-900">145 kg</span>
                            <span class="text-xs font-medium text-emerald-600">+12kg this week</span>
                        </div>
                    </div>
                    
                    <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">People Fed</span>
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <i data-lucide="users" class="w-4 h-4 text-blue-600"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-zinc-900">320</span>
                            <span class="text-xs font-medium text-blue-600">Community Members</span>
                        </div>
                    </div>

                    <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Waste Diverted</span>
                            <div class="p-2 bg-orange-50 rounded-lg">
                                <i data-lucide="trash-2" class="w-4 h-4 text-orange-600"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-zinc-900">88%</span>
                            <span class="text-xs font-medium text-emerald-600">Efficiency Score</span>
                        </div>
                    </div>

                    <div class="p-5 bg-white rounded-xl border border-zinc-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Trust Score</span>
                            <div class="p-2 bg-purple-50 rounded-lg">
                                <i data-lucide="award" class="w-4 h-4 text-purple-600"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-zinc-900">Top Rated</span>
                            <span class="text-xs font-medium text-zinc-500">4.9/5.0</span>
                        </div>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Left: My Active Listings -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-zinc-900">Your Active Listings</h2>
                            <div class="flex gap-2">
                                <span class="text-xs font-medium text-zinc-500 self-center">Filter:</span>
                                <button class="px-3 py-1 text-xs font-medium bg-zinc-900 text-white rounded-full">All</button>
                                <button class="px-3 py-1 text-xs font-medium bg-white border border-zinc-200 text-zinc-600 rounded-full hover:bg-zinc-50">Pending Pickup</button>
                            </div>
                        </div>

                        @foreach ($recentListings as $listing)
                        <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col md:flex-row">
                                <div class="w-full md:w-48 h-32 md:h-auto bg-zinc-100 relative">
                                    @if($listing->images && count($listing->images) > 0)
                                        <img src="{{ Storage::url($listing->images[0]) }}" class="w-full h-full object-cover" alt="Food image">
                                    @else
                                        <img src="https://images.unsplash.com/photo-1555244162-803834f70033?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover" alt="Food placeholder">
                                    @endif
                                    <div class="absolute inset-0 bg-black/10"></div>
                                    <div class="absolute top-2 left-2 bg-white/90 backdrop-blur-sm px-2 py-0.5 rounded text-[10px] font-bold text-zinc-900 uppercase tracking-wide border border-white/20">
                                        {{ $listing->category ?? 'Food' }}
                                    </div>
                                </div>
                                <div class="p-5 flex-1 flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h3 class="font-semibold text-zinc-900 text-lg">{{ $listing->food_name }}</h3>
                                                <p class="text-sm text-zinc-500 mt-0.5">Quantity: {{ $listing->quantity }} • Exp: {{ $listing->expiry_date?->format('M j, Y') ?? 'N/A' }}, {{ $listing->expiry_time?->format('g:i A') ?? 'N/A' }}</p>
                                            </div>
                                            @if($listing->status === 'reserved')
                                                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-1 rounded-full border border-amber-200 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                    RESERVED
                                                </span>
                                            @else
                                                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-1 rounded-full border border-emerald-200">
                                                    AVAILABLE
                                                </span>
                                            @endif
                                        </div>
                                        <div class="mt-3 text-sm text-zinc-600">
                                            <p class="line-clamp-2">{{ $listing->description }}</p>
                                        </div>
                                        @if($listing->status === 'reserved' && $listing->matches->first())
                                            <div class="mt-4 flex items-center gap-4 text-sm">
                                                <div class="flex items-center gap-2 text-zinc-600">
                                                    <div class="w-6 h-6 rounded-full bg-zinc-100 flex items-center justify-center">
                                                        <i data-lucide="user" class="w-3 h-3 text-zinc-500"></i>
                                                    </div>
                                                    <span class="font-medium">Claimed by: {{ $listing->matches->first()->recipient->organization_name }}</span>
                                                </div>
                                                <div class="h-4 w-px bg-zinc-200"></div>
                                                <div class="text-zinc-500">
                                                    Pickup: <span class="font-medium text-zinc-900">{{ $listing->expiry_time?->format('g:i A') ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-zinc-100 flex justify-end items-center gap-3">
                                        @if($listing->status === 'reserved')
                                            <button class="text-xs font-medium text-zinc-500 hover:text-zinc-900">Contact Receiver</button>
                                            <button onclick="showQRCode({{ $listing->id }})" class="text-xs font-medium bg-zinc-900 text-white px-4 py-2 rounded-lg hover:bg-zinc-800 transition-colors flex items-center gap-2">
                                                <i data-lucide="scan-line" class="w-3 h-3"></i> View Pickup QR
                                            </button>
                                        @else
                                        <div class="mt-4 flex items-center justify-end gap-4 border-t border-zinc-100 pt-3">
                                            <a href="{{ route('restaurant.listings.edit', $listing->id) }}" 
                                            class="inline-flex items-center gap-1.5 text-xs font-medium text-zinc-500 hover:text-zinc-800 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                                </svg>
                                                Edit Details
                                            </a>

                                            <form action="{{ route('restaurant.listings.destroy', $listing->id) }}" method="POST" class="inline-flex">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-1.5 text-xs font-medium text-red-500 hover:text-red-700 transition-colors"
                                                        onclick="return confirm('Are you sure you want to delete this listing?');">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M3 6h18"></path>
                                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                                    </svg>
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        @if($recentListings->isEmpty())
                        <div class="bg-white rounded-xl border border-zinc-200 p-8 text-center">
                            <div class="w-16 h-16 bg-zinc-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="package" class="w-8 h-8 text-zinc-400"></i>
                            </div>
                            <h3 class="font-medium text-zinc-900 mb-1">No active listings</h3>
                            <p class="text-sm text-zinc-500 mb-4">You haven't posted any food donations yet.</p>
                            <button onclick="document.getElementById('post-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-900 text-white rounded-lg text-sm font-medium hover:bg-zinc-800 transition-colors">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Post Your First Donation
                            </button>
                        </div>
                        @endif

                    </div>

                    <!-- Right: Notifications & Schedule -->
                    <div class="space-y-6">
                        
                        <!-- Notifications Panel -->
                        <div class="bg-white border border-zinc-200 rounded-xl shadow-sm p-5">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-semibold text-zinc-900">Recent Notifications</h3>
                                <button class="text-xs text-emerald-600 hover:underline">Mark all read</button>
                            </div>
                            <div class="space-y-0">
                                @forelse ($notifications as $notification)
                                <div class="relative pl-6 pb-6 border-l border-zinc-200 last:pb-0">
                                    <div class="absolute left-[-5px] top-0 w-2.5 h-2.5 rounded-full {{ $notification['color'] }} ring-4 ring-white"></div>
                                    <div class="text-sm">
                                        <p class="font-medium text-zinc-900">{{ $notification['message'] }}</p>
                                        @if($notification['details'])
                                            <p class="text-zinc-500 text-xs mt-0.5">{{ $notification['details'] }}</p>
                                        @endif
                                        <p class="text-zinc-400 text-[10px] mt-1">{{ $notification['time'] }}</p>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 bg-zinc-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i data-lucide="bell" class="w-8 h-8 text-zinc-400"></i>
                                    </div>
                                    <p class="text-sm text-zinc-500">No notifications yet</p>
                                </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Impact Map Stub -->
                        <div class="bg-zinc-900 rounded-xl p-5 text-white overflow-hidden relative group cursor-pointer">
                            <div class="absolute inset-0 opacity-20 bg-[url('https://api.mapbox.com/styles/v1/mapbox/dark-v10/static/-122.4194,37.7749,12,0/400x300@2x?access_token=pk.eyJ1IjoiZXhhbXBsZSIsImEiOiJja2xsYnI1b2IwMnZjMnBzbmF0YnF4YmF2In0.1')] bg-cover bg-center transition-transform duration-700 group-hover:scale-110"></div>
                            <div class="relative z-10">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-medium">Local Reach</h3>
                                        <p class="text-xs text-zinc-400 mt-1">Your donations help people within 3 miles.</p>
                                    </div>
                                    <div class="bg-white/10 p-2 rounded-lg backdrop-blur-sm">
                                        <i data-lucide="map" class="w-4 h-4"></i>
                                    </div>
                                </div>
                                <div class="mt-8">
                                    <div class="flex items-center gap-2 text-xs">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span>4 Active Shelters nearby</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Post Donation Modal -->
    <div id="post-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('post-modal').classList.add('hidden')"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50">
                <h3 class="font-semibold text-zinc-900">Post New Donation</h3>
                <button onclick="document.getElementById('post-modal').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('restaurant.listings.store') }}" method="POST" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
                @csrf
                <!-- Form Fields -->
                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Food Image</label>
                    <div class="border-2 border-dashed border-zinc-200 rounded-lg p-6 text-center hover:border-zinc-300 transition-colors cursor-pointer" onclick="document.getElementById('food-image').click()">
                        <input type="file" id="food-image" name="image" class="hidden" accept="image/*" onchange="previewImage(event)">
                        <div id="image-preview" class="hidden">
                            <img id="preview-img" class="w-20 h-20 object-cover rounded-lg mx-auto mb-2">
                            <p class="text-xs text-emerald-600 font-medium">Click to change image</p>
                        </div>
                        <div id="image-upload-placeholder">
                            <i data-lucide="image-plus" class="w-8 h-8 text-zinc-400 mx-auto mb-2"></i>
                            <p class="text-xs text-zinc-500 font-medium">Click to upload food image</p>
                            <p class="text-[10px] text-zinc-400 mt-1">PNG, JPG up to 5MB</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Listing Title</label>
                    <input type="text" name="title" placeholder="e.g. 5kg of Assorted Vegetables" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Description</label>
                    <textarea name="description" rows="2" placeholder="Describe the food items, condition, and any other relevant details..." class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-zinc-700">Food Category</label>
                        <select name="food_category" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            <option>Prepared Meals</option>
                            <option>Bakery</option>
                            <option>Produce</option>
                            <option>Dairy</option>
                            <option>Canned Goods</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-zinc-700">Quantity (Est.)</label>
                        <input type="text" name="quantity" placeholder="e.g. 10 boxes / 5kg" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Expiry Date & Time</label>
                    <div class="flex gap-2">
                        <input type="date" name="expiry_date" class="flex-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <input type="time" name="expiry_time" class="flex-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Pickup Instructions</label>
                    <textarea name="pickup_instructions" rows="3" placeholder="Enter rear entrance code, ask for Manager Mike..." class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none"></textarea>
                </div>

                <!-- Location Preview (Dynamic from Restaurant Profile) -->
                <div class="p-3 bg-zinc-50 rounded-lg border border-zinc-200 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-zinc-200 flex items-center justify-center">
                        <i data-lucide="map-pin" class="w-4 h-4 text-zinc-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-zinc-900">Pickup Location</p>
                        <p class="text-[10px] text-zinc-500" id="restaurant-location">
                            {{ auth()->user()->restaurantProfile?->address ?? 'Set restaurant location in profile' }}
                        </p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3 pt-4 border-t border-zinc-100">
                    <button type="button" onclick="document.getElementById('post-modal').classList.add('hidden')" class="flex-1 py-2.5 border border-zinc-200 text-zinc-700 rounded-lg font-medium hover:bg-zinc-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-2.5 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-all flex items-center justify-center gap-2">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        Post Donation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div id="pickup-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('pickup-modal').classList.add('hidden')"></div>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm relative z-10 overflow-hidden text-center">
            <div class="p-8 pb-6">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="scan-line" class="w-8 h-8 text-emerald-600"></i>
                </div>
                <h3 class="text-lg font-bold text-zinc-900">Scan Receiver's QR</h3>
                <p class="text-sm text-zinc-500 mt-2">Ask the pickup volunteer to show their transaction QR code to verify the handover.</p>
                
                <!-- Simulated Camera View -->
                <div class="mt-6 aspect-square bg-zinc-900 rounded-xl relative overflow-hidden flex items-center justify-center group cursor-pointer">
                    <img src="https://images.unsplash.com/photo-1550989460-0adf9ea622e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" class="absolute inset-0 w-full h-full object-cover opacity-60">
                    <div class="absolute inset-0 border-2 border-white/30 rounded-xl m-4"></div>
                    <div class="absolute top-1/2 left-4 right-4 h-0.5 bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.8)] animate-[pulse_2s_infinite]"></div>
                    <p class="relative z-10 text-white text-xs font-medium bg-black/50 px-3 py-1 rounded-full">Camera Active</p>
                </div>
            </div>
            <div class="px-8 pb-8">
                 <button onclick="document.getElementById('pickup-modal').classList.add('hidden')" class="w-full py-2.5 border border-zinc-200 text-zinc-700 rounded-lg font-medium hover:bg-zinc-50 transition-all">
                    Cancel
                </button>
                <p class="text-[10px] text-zinc-400 mt-4">Manual Entry Code? <a href="#" class="underline text-zinc-600">Click here</a></p>
            </div>
        </div>
    </div>
    <!-- QR Code Verification Modal -->
    <div id="qrModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-zinc-900/80 backdrop-blur-sm" onclick="closeQRModal()"></div>
        <div class="bg-white relative z-10 w-full max-w-sm rounded-2xl p-8 text-center shadow-2xl animate-fade-in mx-4">
            <button onclick="closeQRModal()" class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-900">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
            
            <h3 class="text-xl font-bold text-zinc-900 mb-1">Pickup Verification</h3>
            <p class="text-sm text-zinc-500 mb-6" id="qrItemTitle">Item Title</p>
            
            <div class="bg-white p-4 rounded-xl border-2 border-dashed border-zinc-300 inline-block mb-6 relative group">
                <!-- QR Placeholder generation -->
                <div id="qrcode" class="opacity-90 group-hover:opacity-100 transition-opacity"></div>
                
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="bg-white/90 px-3 py-1 rounded shadow text-xs font-bold text-zinc-900">
                        Scan to Confirm
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-center gap-2 text-xs text-emerald-600 font-medium bg-emerald-50 py-2 px-4 rounded-full mx-auto w-fit">
                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                Secure Transaction ID: #8X92-LM
            </div>

            <p class="text-xs text-zinc-400 mt-6">Show this code to the charity representative upon arrival.</p>
        </div>
    </div>
@endsection

@section('modals')
    <!-- Post Donation Modal -->
    <div id="post-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('post-modal').classList.add('hidden')"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50">
                <h3 class="font-semibold text-zinc-900">Post New Donation</h3>
                <button onclick="document.getElementById('post-modal').classList.add('hidden')" class="text-zinc-400 hover:text-zinc-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('restaurant.listings.store') }}" method="POST" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-4">
                @csrf
                <!-- Form Fields -->
                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Food Image</label>
                    <div class="border-2 border-dashed border-zinc-200 rounded-lg p-6 text-center hover:border-zinc-300 transition-colors cursor-pointer" onclick="document.getElementById('food-image').click()">
                        <input type="file" id="food-image" name="image" class="hidden" accept="image/*" onchange="previewImage(event)">
                        <div id="image-preview" class="hidden">
                            <img id="preview-img" class="w-20 h-20 object-cover rounded-lg mx-auto mb-2">
                            <p class="text-xs text-emerald-600 font-medium">Click to change image</p>
                        </div>
                        <div id="image-upload-placeholder">
                            <i data-lucide="image-plus" class="w-8 h-8 text-zinc-400 mx-auto mb-2"></i>
                            <p class="text-xs text-zinc-500 font-medium">Click to upload food image</p>
                            <p class="text-[10px] text-zinc-400 mt-1">PNG, JPG up to 5MB</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Listing Title</label>
                    <input type="text" name="title" placeholder="e.g. 5kg of Assorted Vegetables" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Description</label>
                    <textarea name="description" rows="2" placeholder="Describe the food items, condition, and any other relevant details..." class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-zinc-700">Food Category</label>
                        <select name="food_category" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            <option>Prepared Meals</option>
                            <option>Bakery</option>
                            <option>Produce</option>
                            <option>Dairy</option>
                            <option>Canned Goods</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-zinc-700">Quantity (Est.)</label>
                        <input type="text" name="quantity" placeholder="e.g. 10 boxes / 5kg" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Expiry Date & Time</label>
                    <div class="flex gap-2">
                        <input type="date" name="expiry_date" class="flex-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        <input type="time" name="expiry_time" class="flex-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-zinc-700">Pickup Instructions</label>
                    <textarea name="pickup_instructions" rows="3" placeholder="Enter rear entrance code, ask for Manager Mike..." class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none"></textarea>
                </div>

                <!-- Location Preview (Dynamic from Restaurant Profile) -->
                <div class="p-3 bg-zinc-50 rounded-lg border border-zinc-200 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-zinc-200 flex items-center justify-center">
                        <i data-lucide="map-pin" class="w-4 h-4 text-zinc-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium text-zinc-900">Pickup Location</p>
                        <p class="text-[10px] text-zinc-500" id="restaurant-location">
                            {{ auth()->user()->restaurantProfile?->address ?? 'Set restaurant location in profile' }}
                        </p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3 pt-4 border-t border-zinc-100">
                    <button type="button" onclick="document.getElementById('post-modal').classList.add('hidden')" class="flex-1 py-2.5 border border-zinc-200 text-zinc-700 rounded-lg font-medium hover:bg-zinc-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-2.5 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-all flex items-center justify-center gap-2">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        Post Donation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div id="pickup-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('pickup-modal').classList.add('hidden')"></div>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm relative z-10 overflow-hidden text-center">
            <div class="p-8 pb-6">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="scan-line" class="w-8 h-8 text-emerald-600"></i>
                </div>
                <h3 class="text-lg font-bold text-zinc-900">Scan Receiver's QR</h3>
                <p class="text-sm text-zinc-500 mt-2">Ask the pickup volunteer to show their transaction QR code to verify the handover.</p>

                <!-- Simulated Camera View -->
                <div class="mt-6 aspect-square bg-zinc-900 rounded-xl relative overflow-hidden flex items-center justify-center group cursor-pointer">
                    <img src="https://images.unsplash.com/photo-1550989460-0adf9ea622e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" class="absolute inset-0 w-full h-full object-cover opacity-60">
                    <div class="absolute inset-0 border-2 border-white/30 rounded-xl m-4"></div>
                    <div class="absolute top-1/2 left-4 right-4 h-0.5 bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.8)] animate-[pulse_2s_infinite]"></div>
                    <p class="relative z-10 text-white text-xs font-medium bg-black/50 px-3 py-1 rounded-full">Camera Active</p>
                </div>
            </div>
            <div class="px-8 pb-8">
                 <button onclick="document.getElementById('pickup-modal').classList.add('hidden')" class="w-full py-2.5 border border-zinc-200 text-zinc-700 rounded-lg font-medium hover:bg-zinc-50 transition-all">
                    Cancel
                </button>
                <p class="text-[10px] text-zinc-400 mt-4">Manual Entry Code? <a href="#" class="underline text-zinc-600">Click here</a></p>
            </div>
        </div>
    </div>
    <!-- QR Code Verification Modal -->
    <div id="qrModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-zinc-900/80 backdrop-blur-sm" onclick="closeQRModal()"></div>
        <div class="bg-white relative z-10 w-full max-w-sm rounded-2xl p-8 text-center shadow-2xl animate-fade-in mx-4">
            <button onclick="closeQRModal()" class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-900">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>

            <h3 class="text-xl font-bold text-zinc-900 mb-1">Pickup Verification</h3>
            <p class="text-sm text-zinc-500 mb-6" id="qrItemTitle">Item Title</p>

            <div class="bg-white p-4 rounded-xl border-2 border-dashed border-zinc-300 inline-block mb-6 relative group">
                <!-- QR Placeholder generation -->
                <div id="qrcode" class="opacity-90 group-hover:opacity-100 transition-opacity"></div>

                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="bg-white/90 px-3 py-1 rounded shadow text-xs font-bold text-zinc-900">
                        Scan to Confirm
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-center gap-2 text-xs text-emerald-600 font-medium bg-emerald-50 py-2 px-4 rounded-full mx-auto w-fit">
                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                Secure Transaction ID: #8X92-LM
            </div>

            <p class="text-xs text-zinc-400 mt-6">Show this code to the charity representative upon arrival.</p>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // QR Modal Logic
    const qrModal = document.getElementById('qrModal');

    function showQRCode(title) {
        document.getElementById('qrItemTitle').innerText = title;
        document.getElementById('qrcode').innerHTML = ""; // Clear previous

        // Generate QR Code
        new QRCode(document.getElementById("qrcode"), {
            text: "https://foodshare.com/verify/" + Math.random().toString(36).substring(7),
            width: 180,
            height: 180,
            colorDark : "#202022",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        qrModal.classList.remove('hidden');
    }

    function closeQRModal() {
        qrModal.classList.add('hidden');
    }

    // Image upload preview
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            // Validate file size (5MB limit)
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('image-preview').classList.remove('hidden');
                document.getElementById('image-upload-placeholder').classList.add('hidden');

                // Add success animation
                const previewImg = document.getElementById('preview-img');
                previewImg.classList.add('scale-105');
                setTimeout(() => previewImg.classList.remove('scale-105'), 200);
            };
            reader.readAsDataURL(file);
        }
    }

    // Initialize Lucide Icons when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endsection