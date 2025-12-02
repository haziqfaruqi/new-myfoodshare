<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MyFoodshare') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Configuration for Zinc/Emerald Theme -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        zinc: {
                            50: '#fafafa',
                            100: '#f4f4f5',
                            200: '#e4e4e7',
                            300: '#d4d4d8',
                            400: '#a1a1aa',
                            500: '#71717a',
                            600: '#52525b',
                            700: '#3f3f46',
                            800: '#27272a',
                            900: '#18181b',
                            950: '#09090b',
                        },
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            900: '#064e3b',
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Hide scrollbar for clean UI */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        body {
            background-color: #fafafa; /* Zinc-50 */
            color: #18181b; /* Zinc-900 */
        }
    </style>
</head>
<body class="antialiased font-sans">
    @auth
        <!-- Navigation for authenticated users -->
        <nav class="bg-white shadow-sm border-b border-zinc-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center gap-2">
                            <div class="bg-emerald-100 p-1.5 rounded-md">
                                <i data-lucide="leaf" class="w-5 h-5 text-emerald-600"></i>
                            </div>
                            <span class="font-bold text-xl tracking-tight text-zinc-900">MyFoodshare</span>
                        </a>
                    </div>

                    <div class="flex items-center space-x-8">
                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-zinc-500 hover:text-emerald-600 transition-colors">Admin</a>
                        @endif

                        @if (Auth::user()->isRestaurantOwner())
                            <a href="{{ route('restaurant.dashboard') }}" class="text-sm font-medium text-zinc-500 hover:text-emerald-600 transition-colors">Restaurant</a>
                        @endif

                        @if (Auth::user()->isRecipient())
                            <a href="{{ route('recipient.dashboard') }}" class="text-sm font-medium text-zinc-500 hover:text-emerald-600 transition-colors">Recipient</a>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="text-sm text-zinc-600">Welcome, {{ Auth::user()->name }}</span>
                        <a href="{{ route('logout') }}"
                           class="px-4 py-2 text-sm text-red-600 hover:text-red-800 transition-colors"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    @endif

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Content -->
    @yield('content')

    <!-- Scripts -->
    <script>
        // Initialize Lucide Icons
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>
</html>