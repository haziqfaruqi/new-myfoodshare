@extends('restaurant.layouts.restaurant-layout')

@section('title', 'Restaurant Profile - Restaurant Portal')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Restaurant Profile</h1>
        <p class="text-sm text-zinc-500 mt-1">Manage your restaurant information and account settings.</p>
    </div>

    <!-- Profile Card -->
    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-emerald-500 to-emerald-600 relative">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -bottom-12 left-6">
                <img src="https://api.dicebear.com/7.x/avataaars/svg?seed={{ auth()->user()->name }}" alt="Restaurant" class="w-24 h-24 rounded-xl border-4 border-white shadow-lg">
            </div>
        </div>

        <div class="pt-14 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-zinc-900">{{ $profile->restaurant_name }}</h2>
                    <div class="flex items-center gap-4 mt-2">
                        <div class="flex items-center text-sm">
                            <i data-lucide="star" class="w-4 h-4 fill-yellow-400 text-yellow-400 mr-1"></i>
                            <span class="font-medium text-zinc-900">4.9</span>
                            <span class="text-zinc-500">(128 reviews)</span>
                        </div>
                        <div class="text-sm text-zinc-500">
                            <span class="text-emerald-600 font-medium">Active Member</span> since {{ auth()->user()->created_at->format('M Y') }}
                        </div>
                    </div>
                </div>
                <a href="{{ route('restaurant.profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-zinc-200 text-zinc-700 rounded-lg text-sm font-medium hover:bg-zinc-50 transition-colors">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Profile Information Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Non-Editable Information -->
        <div class="bg-white rounded-xl border border-zinc-200">
            <div class="p-6 border-b border-zinc-200">
                <h3 class="text-lg font-semibold text-zinc-900">Non-Editable Information</h3>
                <p class="text-sm text-zinc-500 mt-1">This information is system-generated and cannot be modified.</p>
            </div>
            <div class="p-6 space-y-6">
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Account Status</label>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                <i data-lucide="check-circle" class="w-3 h-3 mr-1.5"></i>
                                Active
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Business License</label>
                        <div class="mt-1">
                            <span class="text-sm text-zinc-900 font-medium">License #{{ $profile->license_number ?? 'LIC-' . str_pad($profile->id, 6, '0', STR_PAD_LEFT) }}</span>
                            <p class="text-xs text-zinc-500 mt-1">Verified on {{ $profile->license_verified_at?->format('M j, Y') ?? 'Pending verification' }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Account Type</label>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i data-lucide="building-2" class="w-3 h-3 mr-1.5"></i>
                                Restaurant Partner
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Member Since</label>
                        <div class="mt-1">
                            <span class="text-sm text-zinc-900 font-medium">{{ auth()->user()->created_at->format('F j, Y') }}</span>
                            <p class="text-xs text-zinc-500 mt-1">Member for {{ auth()->user()->created_at->diffInDays() }} days</p>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Account ID</label>
                        <div class="mt-1">
                            <code class="text-xs bg-zinc-100 text-zinc-800 px-2 py-1 rounded">#{{ str_pad($profile->id, 6, '0', STR_PAD_LEFT) }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Editable Information -->
        <div class="bg-white rounded-xl border border-zinc-200">
            <div class="p-6 border-b border-zinc-200">
                <h3 class="text-lg font-semibold text-zinc-900">Restaurant Information</h3>
                <p class="text-sm text-zinc-500 mt-1">Update your restaurant details and contact information.</p>
            </div>
            <div class="p-6">
                <form action="{{ route('restaurant.profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-xs font-medium text-zinc-700">Restaurant Name</label>
                        <input type="text" name="restaurant_name" value="{{ $profile->restaurant_name }}"
                               class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="text-xs font-medium text-zinc-700">Cuisine Type</label>
                        <select name="cuisine_type" class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            <option value="american" {{ $profile->cuisine_type == 'american' ? 'selected' : '' }}>American</option>
                            <option value="italian" {{ $profile->cuisine_type == 'italian' ? 'selected' : '' }}>Italian</option>
                            <option value="chinese" {{ $profile->cuisine_type == 'chinese' ? 'selected' : '' }}>Chinese</option>
                            <option value="mexican" {{ $profile->cuisine_type == 'mexican' ? 'selected' : '' }}>Mexican</option>
                            <option value="indian" {{ $profile->cuisine_type == 'indian' ? 'selected' : '' }}>Indian</option>
                            <option value="japanese" {{ $profile->cuisine_type == 'japanese' ? 'selected' : '' }}>Japanese</option>
                            <option value="thai" {{ $profile->cuisine_type == 'thai' ? 'selected' : '' }}>Thai</option>
                            <option value="mediterranean" {{ $profile->cuisine_type == 'mediterranean' ? 'selected' : '' }}>Mediterranean</option>
                            <option value="other" {{ $profile->cuisine_type == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-zinc-700">Address</label>
                        <textarea name="address" rows="3" class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none">{{ $profile->address }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-zinc-700">City</label>
                            <input type="text" name="city" value="{{ $profile->city }}"
                                   class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-zinc-700">State</label>
                            <input type="text" name="state" value="{{ $profile->state }}"
                                   class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-zinc-700">ZIP Code</label>
                            <input type="text" name="zip_code" value="{{ $profile->zip_code }}"
                                   class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-zinc-700">Phone</label>
                            <input type="tel" name="phone" value="{{ $profile->phone }}"
                                   class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium text-zinc-700">Email</label>
                        <input type="email" name="email" value="{{ $profile->email }}"
                               class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="text-xs font-medium text-zinc-700">Website (Optional)</label>
                        <input type="url" name="website" value="{{ $profile->website }}" placeholder="https://yourrestaurant.com"
                               class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="text-xs font-medium text-zinc-700">Description</label>
                        <textarea name="description" rows="4" class="w-full mt-1 px-3 py-2 bg-white border border-zinc-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 resize-none">{{ $profile->description }}</textarea>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-zinc-100">
                        <button type="button" onclick="window.history.back()" class="flex-1 py-2.5 border border-zinc-200 text-zinc-700 rounded-lg font-medium hover:bg-zinc-50 transition-all">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 py-2.5 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-all flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="bg-white rounded-xl border border-zinc-200">
        <div class="p-6 border-b border-zinc-200">
            <h3 class="text-lg font-semibold text-zinc-900">Contact Information</h3>
            <p class="text-sm text-zinc-500 mt-1">Your primary account contact details.</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-zinc-900 mb-3">Primary Contact</h4>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <i data-lucide="user" class="w-4 h-4 text-zinc-400"></i>
                            <div>
                                <p class="text-xs text-zinc-500">Name</p>
                                <p class="text-sm text-zinc-900">{{ auth()->user()->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <i data-lucide="mail" class="w-4 h-4 text-zinc-400"></i>
                            <div>
                                <p class="text-xs text-zinc-500">Email</p>
                                <p class="text-sm text-zinc-900">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <i data-lucide="phone" class="w-4 h-4 text-zinc-400"></i>
                            <div>
                                <p class="text-xs text-zinc-500">Phone</p>
                                <p class="text-sm text-zinc-900">{{ auth()->user()->phone ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-zinc-900 mb-3">Restaurant Contact</h4>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <i data-lucide="building-2" class="w-4 h-4 text-zinc-400"></i>
                            <div>
                                <p class="text-xs text-zinc-500">Main Office</p>
                                <p class="text-sm text-zinc-900">{{ $profile->restaurant_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <i data-lucide="map-pin" class="w-4 h-4 text-zinc-400"></i>
                            <div>
                                <p class="text-xs text-zinc-500">Address</p>
                                <p class="text-sm text-zinc-900">{{ $profile->address }}</p>
                                <p class="text-sm text-zinc-900">{{ $profile->city }}, {{ $profile->state }} {{ $profile->zip_code }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <i data-lucide="clock" class="w-4 h-4 text-zinc-400"></i>
                            <div>
                                <p class="text-xs text-zinc-500">Business Hours</p>
                                <p class="text-sm text-zinc-900">{{ $profile->business_hours ?? '9:00 AM - 9:00 PM' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
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