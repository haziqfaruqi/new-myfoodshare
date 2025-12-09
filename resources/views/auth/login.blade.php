@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-amber-50 relative overflow-hidden">
    <!-- Background decoration -->
    <div class="absolute inset-0">
        <div class="absolute top-10 left-5 w-32 h-32 bg-emerald-200 rounded-full mix-blend-multiply filter blur-xl opacity-60 animate-blob"></div>
        <div class="absolute top-20 right-5 w-32 h-32 bg-amber-200 rounded-full mix-blend-multiply filter blur-xl opacity-60 animate-blob animation-delay-2000"></div>
    </div>

    <!-- Main container -->
    <div class="relative min-h-screen flex flex-col">
        <!-- Header -->
<header class="bg-white/80 backdrop-blur-md border-b border-emerald-100/50 sticky top-0 z-50">
        <div class="px-4 sm:px-6 lg:px-8 w-full">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center">
                        {!! \App\Helpers\LogoHelper::getLogoHtml('h-6', 'leaf') !!}
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">MyFoodshare</h1>
                        <p class="text-xs text-emerald-600 font-medium">Login Portal</p>
                    </div>
                </div>
                <nav class="hidden md:flex items-center gap-6 text-sm">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-emerald-600 transition-colors">Home</a>
                    <a href="{{ route('register') }}" class="text-gray-600 font-medium">Register</a>
                    <a href="#" class="text-emerald-600 hover:text-emerald-600 transition-colors">Login</a>
                </nav>
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

        <!-- Login Form - takes full remaining height -->
        <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <!-- Header with gradient -->
                    <div class="bg-gradient-to-r from-emerald-600 via-emerald-500 to-emerald-400 px-6 py-6 relative overflow-hidden">
                        <div class="absolute inset-0 bg-black/10"></div>
                        <div class="relative z-10 text-center">
                            @php
                                $logo = \App\Models\Setting::get('site_logo');
                            @endphp
                            @if($logo)
                                <img src="{{ asset($logo) }}" alt="Logo" class="h-12 w-auto object-contain mx-auto mb-3 rounded-lg">
                            @else
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="user-circle" class="w-6 h-6 text-white"></i>
                                </div>
                            @endif
                            <h3 class="text-xl font-bold text-white">MyFoodshare</h3>
                            <p class="text-emerald-100 text-sm mt-1">Access your dashboard</p>
                            <div class="flex justify-center gap-4 text-xs text-emerald-100 mt-3">
                                <div class="flex items-center gap-1">
                                    <i data-lucide="utensils" class="w-3 h-3"></i>
                                    <span>Restaurant</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i data-lucide="heart-handshake" class="w-3 h-3"></i>
                                    <span>NGO</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i data-lucide="shield-check" class="w-3 h-3"></i>
                                    <span>Admin</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form class="px-6 py-6" action="{{ route('login') }}" method="POST">
                        @csrf
                        @if (session('error'))
                            <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3 flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <i data-lucide="alert-circle" class="h-4 w-4 text-red-400"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xs font-semibold text-red-800 mb-1">Login Failed</h3>
                                    <div class="text-xs text-red-700">
                                        <p>{{ session('error') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-5">
                            <!-- Email Section -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                        <i data-lucide="mail" class="w-4 h-4 text-emerald-600"></i>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900">Email Address</h3>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="mail" class="h-4 w-4 text-gray-400"></i>
                                    </div>
                                    <input id="email" name="email" type="email" autocomplete="email" required
                                           value="{{ old('email') }}"
                                           class="block w-full pl-10 pr-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 placeholder-gray-400"
                                           placeholder="john@example.com">
                                </div>
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Section -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                        <i data-lucide="lock" class="w-4 h-4 text-amber-600"></i>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900">Password</h3>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="lock" class="h-4 w-4 text-gray-400"></i>
                                    </div>
                                    <input id="password" name="password" type="password" autocomplete="current-password" required
                                           class="block w-full pl-10 pr-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 placeholder-gray-400"
                                           placeholder="Enter your password">
                                </div>
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input id="remember" name="remember" type="checkbox"
                                           class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                    <label for="remember" class="ml-2 block text-xs text-gray-700">
                                        Remember me
                                    </label>
                                </div>
                                <div class="text-xs">
                                    <a href="#" class="font-medium text-emerald-600 hover:text-emerald-500 transition-colors">
                                        Forgot your password?
                                    </a>
                                </div>
                            </div>

                            <!-- Login Button -->
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-bold rounded-lg hover:from-emerald-700 hover:to-emerald-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200">
                                <i data-lucide="log-in" class="w-4 h-4"></i>
                                Sign In
                            </button>

                            <!-- Register Link -->
                            <div class="text-center pt-4">
                                <p class="text-xs text-gray-600">
                                    Don't have an account yet?
                                </p>
                                <div class="flex flex-col gap-2 mt-2">
                                    <a href="{{ route('register.restaurant') }}" class="inline-flex items-center justify-center gap-1 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-xs font-medium rounded-lg hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200">
                                        <i data-lucide="utensils" class="w-3 h-3"></i>
                                        Register as Restaurant
                                    </a>
                                    <a href="{{ route('register.recipient') }}" class="inline-flex items-center justify-center gap-1 px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-xs font-medium rounded-lg hover:from-emerald-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200">
                                        <i data-lucide="heart-handshake" class="w-3 h-3"></i>
                                        Register as NGO/Recipient
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Animated background blobs */
    .animate-blob {
        animation: blob 7s infinite;
    }

    .animation-delay-2000 {
        animation-delay: 2s;
    }

    .animation-delay-4000 {
        animation-delay: 4s;
    }

    @keyframes blob {
        0% {
            transform: translate(0px, 0px) scale(1);
        }
        33% {
            transform: translate(30px, -50px) scale(1.1);
        }
        66% {
            transform: translate(-20px, 20px) scale(0.9);
        }
        100% {
            transform: translate(0px, 0px) scale(1);
        }
    }

    /* Custom scrollbar for better UX */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #10b981;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #059669;
    }
</style>

<script>
    // Initialize Lucide icons
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        // Check if user is authenticated and redirect if they are
        fetch('{{ route("auth.check") }}', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.authenticated) {
                alert('You are already logged in. Redirecting to dashboard...');
                window.location.href = data.redirect_url || '{{ route("dashboard") }}';
            }
        })
        .catch(error => {
            // Silent fail - don't break the page if the check fails
            console.log('Auth check failed:', error);
        });

        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
</script>
@endsection