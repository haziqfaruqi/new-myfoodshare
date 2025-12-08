@extends('layouts.app')

@section('content')
<!-- Restaurant Registration Enhanced UI -->
<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-zinc-50">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md border-b border-emerald-100/50 sticky top-0 z-50">
        <div class="px-4 sm:px-6 lg:px-8 w-full">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center gap-3">
                    @php
                        $logo = \App\Models\Setting::get('site_logo');
                    @endphp
                    @if($logo)
                        <img src="{{ asset($logo) }}" alt="Logo" class="h-10 w-auto object-contain">
                    @else
                        <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="leaf" class="w-6 h-6 text-white"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">MyFoodshare</h1>
                        <p class="text-xs text-emerald-600 font-medium">Registration Portal</p>
                    </div>
                </div>
                <nav class="hidden md:flex items-center gap-6 text-sm">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-emerald-600 transition-colors">Home</a>
                    <a href="#" class="text-emerald-600 font-medium">Register</a>
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-emerald-600 transition-colors">Login</a>
                </nav>
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/10 via-transparent to-emerald-500/5"></div>
        <div class="px-4 sm:px-6 lg:px-8 py-12 w-full">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-800 px-4 py-2 rounded-full text-sm font-medium mb-6">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Join Our Movement
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Choose Your Role
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Select how you'd like to participate in MyFoodshare and start making a difference in your community.
                </p>
            </div>
        </div>
    </div>

    <!-- Role Selection Cards -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Restaurant Registration Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-8">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <i data-lucide="utensils" class="w-8 h-8 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white">Restaurant Owner</h3>
                            <p class="text-orange-100">Food Partner</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="space-y-4 mb-6">
                        <div class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check-circle" class="w-5 h-5 text-orange-500"></i>
                            <span class="text-sm">Donate surplus food</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check-circle" class="w-5 h-5 text-orange-500"></i>
                            <span class="text-sm">Reduce food waste</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check-circle" class="w-5 h-5 text-orange-500"></i>
                            <span class="text-sm">Save costs</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check-circle" class="w-5 h-5 text-orange-500"></i>
                            <span class="text-sm">Help your community</span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Required Information:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Business license number</li>
                            <li>• Cuisine type</li>
                            <li>• Contact person details</li>
                            <li>• Restaurant capacity</li>
                        </ul>
                    </div>

                    <a href="{{ route('register.restaurant') }}"
                       class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium rounded-lg hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200">
                        <i data-lucide="utensils" class="w-4 h-4"></i>
                        Register as Restaurant
                    </a>
                </div>
            </div>

            <!-- Recipient Registration Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-8">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <i data-lucide="heart-handshake" class="w-8 h-8 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white">NGO/Recipient</h3>
                            <p class="text-emerald-100">Community Partner</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="space-y-4 mb-6">
                        <div class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
                            <span class="text-sm">Access food donations</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
                            <span class="text-sm">Support those in need</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
                            <span class="text-sm">Verified food sources</span>
                        </div>
                        <div class="flex items-center gap-3 text-gray-600">
                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
                            <span class="text-sm">Real-time notifications</span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Required Information:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• NGO registration number</li>
                            <li>• Organization details</li>
                            <li>• Serving capacity</li>
                            <li>• Dietary requirements</li>
                        </ul>
                    </div>

                    <a href="{{ route('register.recipient') }}"
                       class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-medium rounded-lg hover:from-emerald-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200">
                        <i data-lucide="heart-handshake" class="w-4 h-4"></i>
                        Register as NGO/Recipient
                    </a>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="mt-12 text-center">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-medium mb-4">
                    <i data-lucide="info" class="w-4 h-4"></i>
                    Why Join MyFoodshare?
                </div>
                <p class="text-lg text-gray-600 mb-6">
                    MyFoodshare is a comprehensive platform connecting restaurants with surplus food to NGOs and communities in need.
                    Together, we can reduce food waste while making a meaningful impact on hunger and sustainability.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mb-3">
                            <i data-lucide="leaf" class="w-6 h-6 text-emerald-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-1">Sustainability</h4>
                        <p class="text-sm text-gray-600">Reduce environmental impact</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mb-3">
                            <i data-lucide="users" class="w-6 h-6 text-emerald-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-1">Community</h4>
                        <p class="text-sm text-gray-600">Support local communities</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mb-3">
                            <i data-lucide="trending-up" class="w-6 h-6 text-emerald-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-1">Impact</h4>
                        <p class="text-sm text-gray-600">Maximize your social impact</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
@endsection