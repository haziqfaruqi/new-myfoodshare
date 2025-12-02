@extends('layouts.app')

@section('content')
<!-- Restaurant Registration Enhanced UI -->
<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-zinc-50">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-md border-b border-emerald-100/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="leaf" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">MyFoodshare</h1>
                        <p class="text-xs text-emerald-600 font-medium">NGO/Recipient Partner Portal</p>
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-800 px-4 py-2 rounded-full text-sm font-medium mb-6">
                    <i data-lucide="heart-handshake" class="w-4 h-4"></i>
                    NGO/Recipient Registration
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Help Us Fight Hunger in Your Community
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Access food donations to support those in need. Join our network of caring organizations making a real difference.
                </p>
                <div class="flex justify-center gap-8 mt-8 text-sm">
                    <div class="flex items-center gap-2 text-gray-600">
                        <i data-lucide="users" class="w-4 h-4 text-emerald-600"></i>
                        <span>Help Your Community</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                        <span>Verified Sources</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <i data-lucide="clock" class="w-4 h-4 text-emerald-600"></i>
                        <span>Real-time Updates</span>
                    </div>
                </div>
            </div>

            <!-- Progress Steps -->
            <div class="flex justify-center mb-12">
                <nav class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-emerald-600 text-white rounded-full flex items-center justify-center text-sm font-medium">1</div>
                        <span class="ml-2 text-sm font-medium text-emerald-600">Basic Info</span>
                    </div>
                    <div class="w-16 h-1 bg-emerald-200"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-emerald-600 text-white rounded-full flex items-center justify-center text-sm font-medium">2</div>
                        <span class="ml-2 text-sm font-medium text-emerald-600">Organization</span>
                    </div>
                    <div class="w-16 h-1 bg-emerald-200"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-emerald-600 text-white rounded-full flex items-center justify-center text-sm font-medium">3</div>
                        <span class="ml-2 text-sm font-medium text-emerald-600">Dietary</span>
                    </div>
                    <div class="w-16 h-1 bg-emerald-200"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm font-medium">4</div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Complete</span>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <!-- Registration Form -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-8 py-6">
                <h3 class="text-2xl font-bold text-white">NGO/Recipient Registration</h3>
                <p class="text-emerald-100 mt-1">Complete your profile to start receiving food donations</p>
            </div>

            <form class="px-8 py-8" action="{{ route('register.recipient.store') }}" method="POST">
                @csrf
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="space-y-8">
                    <!-- Basic Information Section -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="user" class="w-5 h-5 text-emerald-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                                <p class="text-sm text-gray-500">Your contact and account details</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Contact Person Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input id="name" name="name" type="text" required
                                           value="{{ old('name') }}"
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                           placeholder="John Doe">
                                </div>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input id="email" name="email" type="email" autocomplete="email" required
                                           value="{{ old('email') }}"
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                           placeholder="john@example.com">
                                </div>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input id="phone" name="phone" type="tel" required
                                           value="{{ old('phone') }}"
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                           placeholder="+60123456789">
                                </div>
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input id="password" name="password" type="password" autocomplete="new-password" required
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                           placeholder="Create a password">
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                       class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                       placeholder="Confirm your password">
                            </div>
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Organization Information Section -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="building-2" class="w-5 h-5 text-emerald-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Organization Information</h3>
                                <p class="text-sm text-gray-500">Details about your NGO/organization</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label for="organization_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Organization Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="building" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input id="organization_name" name="organization_name" type="text" required
                                           placeholder="Hope Foundation Malaysia"
                                           value="{{ old('organization_name') }}"
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                </div>
                                @error('organization_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">
                                    Main Contact Person <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input id="contact_person" name="contact_person" type="text" required
                                           value="{{ old('contact_person') }}"
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
                                           placeholder="Sarah Johnson">
                                </div>
                                @error('contact_person')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Organization Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="map-pin" class="absolute left-3 top-3 w-4 h-4 text-gray-400"></i>
                                    <textarea id="address" name="address" rows="3" required
                                              placeholder="Complete address including city and state"
                                              class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors resize-none">{{ old('address') }}</textarea>
                                </div>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="recipient_capacity" class="block text-sm font-medium text-gray-700 mb-2">
                                        Daily Serving Capacity <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <i data-lucide="users" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                        <input id="recipient_capacity" name="recipient_capacity" type="number" min="1" required
                                               placeholder="Number of people you serve daily"
                                               value="{{ old('recipient_capacity') }}"
                                               class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                    </div>
                                    @error('recipient_capacity')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="ngo_registration" class="block text-sm font-medium text-gray-700 mb-2">
                                        NGO Registration Number <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <i data-lucide="shield-check" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                        <input id="ngo_registration" name="ngo_registration" type="text" required
                                               placeholder="e.g., NGO2024001"
                                               value="{{ old('ngo_registration') }}"
                                               class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Required for verification as legitimate non-profit</p>
                                    @error('ngo_registration')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="organization_description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Organization Description <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="file-text" class="absolute left-3 top-3 w-4 h-4 text-gray-400"></i>
                                    <textarea id="organization_description" name="organization_description" rows="4" required
                                              placeholder="Mission, community served, and organizational goals"
                                              class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors resize-none">{{ old('organization_description') }}</textarea>
                                </div>
                                @error('organization_description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dietary Requirements Section -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="heart" class="w-5 h-5 text-emerald-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Dietary Requirements & Preferences</h3>
                                <p class="text-sm text-gray-500">Select multiple dietary requirements for your community</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div class="flex items-center">
                                <input id="halal" name="dietary_requirements[]" type="checkbox" value="Halal"
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                <label for="halal" class="ml-3 flex items-center text-sm text-gray-700">
                                    <i data-lucide="check-circle" class="h-4 w-4 text-emerald-500 mr-1"></i>
                                    Halal
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="vegetarian" name="dietary_requirements[]" type="checkbox" value="Vegetarian"
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                <label for="vegetarian" class="ml-3 flex items-center text-sm text-gray-700">
                                    <i data-lucide="check-circle" class="h-4 w-4 text-emerald-500 mr-1"></i>
                                    Vegetarian
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="vegan" name="dietary_requirements[]" type="checkbox" value="Vegan"
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                <label for="vegan" class="ml-3 flex items-center text-sm text-gray-700">
                                    <i data-lucide="check-circle" class="h-4 w-4 text-emerald-500 mr-1"></i>
                                    Vegan
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="kosher" name="dietary_requirements[]" type="checkbox" value="Kosher"
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                <label for="kosher" class="ml-3 flex items-center text-sm text-gray-700">
                                    <i data-lucide="check-circle" class="h-4 w-4 text-emerald-500 mr-1"></i>
                                    Kosher
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="gluten_free" name="dietary_requirements[]" type="checkbox" value="Gluten-Free"
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                <label for="gluten_free" class="ml-3 flex items-center text-sm text-gray-700">
                                    <i data-lucide="check-circle" class="h-4 w-4 text-emerald-500 mr-1"></i>
                                    Gluten-Free
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="nut_free" name="dietary_requirements[]" type="checkbox" value="Nut-Free"
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                <label for="nut_free" class="ml-3 flex items-center text-sm text-gray-700">
                                    <i data-lucide="check-circle" class="h-4 w-4 text-emerald-500 mr-1"></i>
                                    Nut-Free
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="dairy_free" name="dietary_requirements[]" type="checkbox" value="Dairy-Free"
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                <label for="dairy_free" class="ml-3 flex items-center text-sm text-gray-700">
                                    <i data-lucide="check-circle" class="h-4 w-4 text-emerald-500 mr-1"></i>
                                    Dairy-Free
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="low_sodium" name="dietary_requirements[]" type="checkbox" value="Low-Sodium"
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                <label for="low_sodium" class="ml-3 flex items-center text-sm text-gray-700">
                                    <i data-lucide="check-circle" class="h-4 w-4 text-emerald-500 mr-1"></i>
                                    Low-Sodium
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input id="diabetic_friendly" name="dietary_requirements[]" type="checkbox" value="Diabetic-Friendly"
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                <label for="diabetic_friendly" class="ml-3 flex items-center text-sm text-gray-700">
                                    <i data-lucide="check-circle" class="h-4 w-4 text-emerald-500 mr-1"></i>
                                    Diabetic-Friendly
                                </label>
                            </div>
                        </div>
                        @error('dietary_requirements')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Special Needs Section -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="settings" class="w-5 h-5 text-emerald-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Special Needs & Preferences</h3>
                                <p class="text-sm text-gray-500">Optional: Specific food preferences or requirements</p>
                            </div>
                        </div>

                        <div>
                            <label for="needs_preferences" class="block text-sm font-medium text-gray-700 mb-2">
                                Special Needs & Preferences
                            </label>
                            <div class="relative">
                                <i data-lucide="file-text" class="absolute left-3 top-3 w-4 h-4 text-gray-400"></i>
                                <textarea id="needs_preferences" name="needs_preferences" rows="4"
                                          placeholder="Specific food preferences, preparation requirements, or scheduling constraints"
                                          class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors resize-none">{{ old('needs_preferences') }}</textarea>
                            </div>
                            @error('needs_preferences')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Legal Agreement Section -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="shield-check" class="w-5 h-5 text-emerald-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Terms and Conditions</h3>
                                <p class="text-sm text-gray-500">Please read and agree to our terms</p>
                            </div>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <input id="terms" name="terms" type="checkbox" required
                                           class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-amber-300 rounded">
                                </div>
                                <div class="ml-3 flex-1">
                                    <label for="terms" class="block text-sm font-medium text-gray-900 mb-2">
                                        <i data-lucide="shield-check" class="h-4 w-4 inline text-amber-600 mr-1"></i>
                                        I agree to the terms and conditions <span class="text-red-500">*</span>
                                    </label>
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        I confirm that all information provided is accurate and my organization is a legitimate non-profit.
                                        I understand that MyFoodshare may verify my NGO registration and that I will receive notifications
                                        about food matches that align with my organization's needs and capacity.
                                    </p>
                                </div>
                            </div>
                            @error('terms')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <button type="submit"
                            class="flex-1 flex justify-center items-center py-3 px-6 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-medium rounded-lg hover:from-emerald-700 hover:to-emerald-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200">
                        <i data-lucide="heart-handshake" class="w-4 h-4 mr-2"></i>
                        Register NGO Account
                    </button>
                    <a href="{{ route('login') }}"
                       class="flex-1 flex justify-center items-center py-3 px-6 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200">
                        <i data-lucide="log-in" class="w-4 h-4 mr-2"></i>
                        Already have an account? Sign in
                    </a>
                </div>

                <!-- Switch Links -->
                <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('register') }}" class="inline-flex items-center text-sm text-emerald-600 hover:text-emerald-500 transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i>
                        Back to general registration
                    </a>
                    <a href="{{ route('register.restaurant') }}" class="inline-flex items-center text-sm text-emerald-600 hover:text-emerald-500 transition-colors">
                        <i data-lucide="utensils" class="w-4 h-4 mr-1"></i>
                        Switch to Restaurant Registration
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
@endsection