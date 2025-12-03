@extends('restaurant.layouts.restaurant-layout')

@section('title', 'Edit Restaurant Profile - Restaurant Portal')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Edit Restaurant Profile</h1>
        <p class="text-sm text-zinc-500 mt-1">Update your restaurant information and contact details.</p>
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
                <a href="{{ route('restaurant.profile') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-zinc-200 text-zinc-700 rounded-lg text-sm font-medium hover:bg-zinc-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
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
                    <label class="text-xs font-medium text-zinc-700">Business Hours</label>
                    <input type="text" name="business_hours" value="{{ $profile->business_hours ?? '9:00 AM - 9:00 PM' }}" placeholder="e.g., 9:00 AM - 9:00 PM"
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
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endsection