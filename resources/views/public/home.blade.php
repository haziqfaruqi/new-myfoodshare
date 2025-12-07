<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFoodshare - Food Rescue Platform</title>
    
    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
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
                        // Explicitly defining the palette requested
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
<body class="antialiased flex flex-col min-h-screen">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-zinc-200">
        <div class="px-4 sm:px-6 lg:px-8 w-full">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2 cursor-pointer">
                    <div class="bg-emerald-100 p-2 rounded-lg">
                        <i data-lucide="leaf" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-zinc-900">MyFoodshare</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#how-it-works" class="text-sm font-medium text-zinc-500 hover:text-emerald-600 transition-colors">How it Works</a>
                    <a href="#impact" class="text-sm font-medium text-zinc-500 hover:text-emerald-600 transition-colors">Impact</a>
                    <a href="#partners" class="text-sm font-medium text-zinc-500 hover:text-emerald-600 transition-colors">Partners</a>
                </div>

                <!-- CTA & Mobile Menu Button -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900">Home</a>
                    <a href="{{ route('register') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900">Register</a>
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors shadow-lg shadow-emerald-900/10">
                        Login
                    </a>
                </div>

                <!-- Mobile Menu Trigger -->
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-md text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 focus:outline-none">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu (Hidden by default) -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-zinc-200 absolute w-full">
            <div class="px-4 pt-2 pb-6 space-y-1 shadow-lg">
                <a href="#how-it-works" class="block px-3 py-3 rounded-md text-base font-medium text-zinc-600 hover:text-emerald-600 hover:bg-zinc-50">How it Works</a>
                <a href="#impact" class="block px-3 py-3 rounded-md text-base font-medium text-zinc-600 hover:text-emerald-600 hover:bg-zinc-50">Impact</a>
                <a href="#login-section" class="block mt-4 px-3 py-3 text-center rounded-md text-base font-medium bg-emerald-600 text-white">Login / Sign Up</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pt-24 pb-12 px-4 sm:px-6 lg:px-8">

        <!-- Hero Section -->
        <div class="grid lg:grid-cols-2 gap-12 items-center mb-24 w-full">
            <div class="space-y-6 animate-fade-in-up">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-medium">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Connecting Surplus to Needs
                </div>
                
                <h1 class="text-4xl md:text-6xl font-bold text-zinc-900 tracking-tight leading-[1.1]">
                    Turn excess food into <br>
                    <span class="text-emerald-600">community impact.</span>
                </h1>
                
                <p class="text-lg text-zinc-500 max-w-lg leading-relaxed">
                    MyFoodshare connects restaurants with surplus inventory to local communities and NGOs. Reduce waste, save costs, and feed those in need.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <div class="relative">
                        <button id="register-dropdown-btn" class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-md hover:shadow-xl hover:shadow-emerald-900/10">
                            Join the Movement
                            <i data-lucide="chevron-down" class="ml-2 w-4 h-4"></i>
                        </button>
                        <!-- Dropdown menu -->
                        <div id="register-dropdown" class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden">
                            <div class="py-1">
                                <a href="{{ route('register.restaurant') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <div class="flex items-center">
                                        <i data-lucide="store" class="w-4 h-4 mr-2 text-emerald-600"></i>
                                        Restaurant Partner
                                    </div>
                                </a>
                                <a href="{{ route('register.recipient') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <div class="flex items-center">
                                        <i data-lucide="heart-handshake" class="w-4 h-4 mr-2 text-emerald-600"></i>
                                        NGO / Recipient
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Abstract Visual -->
            <div class="relative hidden lg:block animate-fade-in-up" style="animation-delay: 200ms;">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-emerald-100 rounded-full blur-3xl opacity-50 mix-blend-multiply"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 bg-zinc-200 rounded-full blur-3xl opacity-50 mix-blend-multiply"></div>
                
                <div class="relative grid grid-cols-2 gap-4">
                    <div class="space-y-4 pt-12">
                        <div class="bg-white p-4 rounded-2xl shadow-xl border border-zinc-100 transform hover:-translate-y-1 transition-transform duration-300">
                            <div class="h-32 bg-zinc-100 rounded-xl mb-3 overflow-hidden">
                                <img src="https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg" class="w-full h-full object-cover" alt="Food sharing">
                            </div>
                            <div class="h-2 w-1/2 bg-zinc-200 rounded mb-2"></div>
                            <div class="h-2 w-3/4 bg-emerald-100 rounded"></div>
                        </div>
                        <div class="bg-white p-4 rounded-2xl shadow-xl border border-zinc-100 transform hover:-translate-y-1 transition-transform duration-300">
                             <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </div>
                                <div class="text-sm font-medium text-zinc-900">Delivery Verified</div>
                             </div>
                             <p class="text-xs text-zinc-500">120kg saved today</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-emerald-600 p-6 rounded-2xl shadow-xl transform hover:-translate-y-1 transition-transform duration-300 text-white">
                            <i data-lucide="trending-up" class="w-8 h-8 mb-4 text-emerald-100"></i>
                            <div class="text-3xl font-bold mb-1">12.5k</div>
                            <div class="text-sm text-emerald-100">Meals Rescued</div>
                        </div>
                        <div class="bg-white p-4 rounded-2xl shadow-xl border border-zinc-100 transform hover:-translate-y-1 transition-transform duration-300">
                             <div class="h-40 bg-zinc-100 rounded-xl mb-3 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover">
                             </div>
                             <div class="flex justify-between items-center">
                                 <div class="h-2 w-1/3 bg-zinc-200 rounded"></div>
                                 <div class="px-2 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase rounded">Verified</div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Portal Section -->
        <section id="login-section" class="py-12 border-t border-zinc-200">
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold text-zinc-900 mb-2">Choose your Portal</h2>
                <p class="text-zinc-500">Select your role to access your dedicated dashboard.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 max-w-6xl mx-auto">

                <!-- Card 1: Restaurant Owner -->
                <div class="group relative bg-white border border-zinc-200 rounded-2xl p-8 hover:border-emerald-500 hover:shadow-xl hover:shadow-emerald-900/5 transition-all duration-300">
                    <div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
                        <i data-lucide="arrow-up-right" class="text-emerald-500 w-5 h-5"></i>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-zinc-100 group-hover:bg-emerald-50 flex items-center justify-center mb-6 transition-colors">
                        <i data-lucide="store" class="w-6 h-6 text-zinc-600 group-hover:text-emerald-600 transition-colors"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 mb-2">Restaurant Partner</h3>
                    <p class="text-sm text-zinc-500 mb-6 leading-relaxed">
                        List surplus food, manage inventory, and track your sustainability impact analytics.
                    </p>
                    <a href="{{ route('register.restaurant') }}" class="w-full py-2.5 rounded-lg border border-emerald-600 text-emerald-600 text-sm font-medium hover:bg-emerald-600 hover:text-white transition-all block text-center">
                        Register Restaurant
                    </a>
                </div>

                <!-- Card 2: Recipient/NGO -->
                <div class="group relative bg-white border border-zinc-200 rounded-2xl p-8 hover:border-emerald-500 hover:shadow-xl hover:shadow-emerald-900/5 transition-all duration-300">
                    <div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
                        <i data-lucide="arrow-up-right" class="text-emerald-500 w-5 h-5"></i>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-zinc-100 group-hover:bg-emerald-50 flex items-center justify-center mb-6 transition-colors">
                        <i data-lucide="heart-handshake" class="w-6 h-6 text-zinc-600 group-hover:text-emerald-600 transition-colors"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 mb-2">Recipient / NGO</h3>
                    <p class="text-sm text-zinc-500 mb-6 leading-relaxed">
                        Browse available donations, schedule pickups, and distribute to your community.
                    </p>
                    <a href="{{ route('register.recipient') }}" class="w-full py-2.5 rounded-lg border border-emerald-600 text-emerald-600 text-sm font-medium hover:bg-emerald-600 hover:text-white transition-all block text-center">
                        Register NGO
                    </a>
                </div>
            </div>
            
            <div class="text-center mt-8">
                <p class="text-sm text-zinc-400">Already have an account? <a href="{{ route('login') }}" class="text-emerald-600 hover:underline font-medium">Sign in here</a></p>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="mt-8 bg-zinc-900 rounded-3xl p-8 md:p-16 text-white text-center relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute top-0 left-0 w-full h-full">
                <div class="absolute top-1/2 left-1/4 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-1/4 w-64 h-64 bg-zinc-700/20 rounded-full blur-3xl"></div>
            </div>

            <div class="relative z-10 max-w-6xl mx-auto">
                <h2 class="text-2xl md:text-3xl font-bold mb-12">Our Collective Impact</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-emerald-500 mb-2">2.5T</div>
                        <div class="text-sm text-zinc-400">Food Rescued</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-emerald-500 mb-2">150+</div>
                        <div class="text-sm text-zinc-400">Partner Restaurants</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-emerald-500 mb-2">85</div>
                        <div class="text-sm text-zinc-400">Active NGOs</div>
                    </div>
                    <div>
                        <div class="text-3xl md:text-4xl font-bold text-emerald-500 mb-2">$40k</div>
                        <div class="text-sm text-zinc-400">Costs Saved</div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-zinc-200">
        <div class="px-6 py-12 flex flex-col md:flex-row justify-between items-center gap-6 w-full max-w-7xl mx-auto">
            <div class="flex items-center gap-2">
                <div class="bg-emerald-100 p-1.5 rounded-md">
                    <i data-lucide="leaf" class="w-4 h-4 text-emerald-600"></i>
                </div>
                <span class="font-bold text-sm tracking-tight text-zinc-900">MyFoodshare</span>
            </div>
            
            <div class="flex gap-8 text-sm text-zinc-500">
                <a href="#" class="hover:text-zinc-900">Privacy</a>
                <a href="#" class="hover:text-zinc-900">Terms</a>
                <a href="#" class="hover:text-zinc-900">Support</a>
            </div>

            <div class="text-xs text-zinc-400">
                &copy; 2025 MyFoodshare Inc. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- JS for Mobile Menu -->
    <script>
        // Initialize Icons
        lucide.createIcons();

        // Mobile Menu Toggle
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
            // Toggle icon based on state (optional visual polish)
            const icon = menu.classList.contains('hidden') ? 'menu' : 'x';
            btn.innerHTML = `<i data-lucide="${icon}" class="w-6 h-6"></i>`;
            lucide.createIcons();
        });

        // Registration Dropdown Toggle
        const registerBtn = document.getElementById('register-dropdown-btn');
        const dropdown = document.getElementById('register-dropdown');

        if (registerBtn && dropdown) {
            registerBtn.addEventListener('click', (e) => {
                e.preventDefault();
                dropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!registerBtn.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>