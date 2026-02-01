<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyFoodshare - Restaurant Partner Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #e4e4e7;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #d4d4d8;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
        }

        /* Animation for modals */
        .modal-enter {
            opacity: 0;
            transform: scale(0.95);
        }
        .modal-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: opacity 300ms, transform 300ms;
        }
        .modal-exit {
            opacity: 1;
            transform: scale(1);
        }
        .modal-exit-active {
            opacity: 0;
            transform: scale(0.95);
            transition: opacity 200ms, transform 200ms;
        }

        /* Animate CSS utilities */
        @keyframes fade-in-90 {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes zoom-in-90 {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .animate-in {
            animation: fade-in-90 0.3s ease-out;
        }

        .fade-in-90 {
            animation: fade-in-90 0.3s ease-out;
        }

        .zoom-in-90 {
            animation: zoom-in-90 0.3s ease-out;
        }

        /* Pulse animation for active status */
        @keyframes pulse-ring {
            0% { transform: scale(0.33); }
            80%, 100% { opacity: 0; }
        }
        .pulse-ring::before {
            content: '';
            position: absolute;
            left: 0; top: 0;
            display: block;
            width: 100%; height: 100%;
            background-color: #10b981;
            border-radius: 50%;
            animation: pulse-ring 1.25s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
    </style>
</head>
<body class="bg-zinc-50 text-zinc-900 flex h-screen overflow-hidden">
    <!-- Mobile Sidebar Overlay -->
    <div id="restaurant-sidebar-overlay" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden" onclick="toggleSidebar()"></div>

    @include('restaurant.partials.restaurant-sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden relative md:ml-0">
        <!-- Notification Toast Container -->
        <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none"></div>

        @include('restaurant.partials.restaurant-header')

        <!-- Dashboard Content -->
        <div class="flex-1 overflow-y-auto p-4 md:p-6 scroll-smooth">
            @yield('content')
        </div>
    </main>

    @yield('modals')

    <script>
        // Initialize Icons
        lucide.createIcons();

        // Mobile Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('restaurant-sidebar');
            const overlay = document.getElementById('restaurant-sidebar-overlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Pusher Configuration for Real-time Notifications
        @if(auth()->check())
        Pusher.logToConsole = true;  // Enable for debugging

        // Get CSRF token for authentication
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            forceTLS: {{ config('broadcasting.connections.pusher.options.useTLS') ? 'true' : 'false' }},
            auth: {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            },
            authEndpoint: '/broadcasting/auth'
        });

        // Subscribe to restaurant's channel - using public channel for testing
        const restaurantChannel = pusher.subscribe('restaurant-notifications');

        // Handle connection and subscription events
        pusher.connection.bind('connected', function() {
            console.log('Pusher connected!');
        });

        pusher.connection.bind('disconnected', function() {
            console.log('Pusher disconnected!');
        });

        pusher.connection.bind('error', function(err) {
            console.error('Pusher connection error:', err);
        });

        restaurantChannel.bind('pusher:subscription_succeeded', function() {
            console.log('Subscribed to restaurant-notifications');
        });

        restaurantChannel.bind('pusher:subscription_error', function(error) {
            console.error('Subscription error:', error);
        });

        // Listen for new match requests
        restaurantChannel.bind('match.status.updated', function(data) {
            console.log('New match notification:', data);

            // Show notification toast
            showNotification(data);

            // Play notification sound (optional)
            playNotificationSound();

            // Update pending count and add to dropdown
            updatePendingCount();
            addNotificationToList(data);
        });

        // Listen for QR code scanned events
        restaurantChannel.bind('qr.code.scanned', function(data) {
            console.log('QR code scanned notification:', data);

            // Show notification toast
            showNotification({
                message: data.message,
                recipient_name: data.recipient_name,
                food_name: data.food_name,
                timestamp: data.scanned_at,
                status: 'scanned'
            });

            // Play notification sound
            playNotificationSound();
        });

        // Listen for pickup completed events
        restaurantChannel.bind('pickup.completed', function(data) {
            console.log('Pickup completed notification:', data);

            // Show notification toast
            showNotification({
                message: data.message,
                recipient_name: data.recipient_name,
                food_name: data.food_name,
                timestamp: data.completed_at,
                status: 'completed'
            });

            // Play notification sound
            playNotificationSound();
        });

        // Show notification toast
        function showNotification(data) {
            const container = document.getElementById('notification-container');

            const notification = document.createElement('div');
            notification.className = 'pointer-events-auto bg-white rounded-lg shadow-lg border-l-4 border-blue-500 p-4 max-w-sm transform transition-all duration-300 translate-x-full opacity-0';
            notification.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-zinc-900">${data.message || 'New Match Request'}</p>
                        <p class="text-xs text-zinc-500 mt-1">${data.recipient_name || 'A recipient'} requested ${data.food_name || 'food'} (${data.quantity || ''})</p>
                        <p class="text-[10px] text-zinc-400 mt-1">${getTimeAgo(data.timestamp)}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-zinc-400 hover:text-zinc-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;

            container.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full', 'opacity-0');
            }, 10);

            // Auto remove after 8 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => notification.remove(), 300);
            }, 8000);
        }

        // Play notification sound
        function playNotificationSound() {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fLUfCkFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBzON2fL');
            audio.volume = 0.3;
            audio.play().catch(() => {});
        }

        // Update pending count on dashboard
        function updatePendingCount() {
            const pendingCountEl = document.getElementById('pending-count');
            if (pendingCountEl) {
                const currentCount = parseInt(pendingCountEl.textContent) || 0;
                pendingCountEl.textContent = currentCount + 1;
                pendingCountEl.classList.add('animate-bounce');
                setTimeout(() => pendingCountEl.classList.remove('animate-bounce'), 1000);
            }
        }

        // Get time ago string
        function getTimeAgo(timestamp) {
            if (!timestamp) return 'Just now';
            const date = new Date(timestamp);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);
            if (seconds < 60) return 'Just now';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return minutes + ' min ago';
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return hours + 'h ago';
            return date.toLocaleDateString();
        }

        // Store notifications with read status
        let notifications = [];

        // Track read notification IDs
        let readNotificationIds = new Set();

        // Generate unique ID for notifications
        let notificationIdCounter = 0;

        // Toggle notification dropdown
        function toggleNotificationDropdown() {
            const menu = document.getElementById('notification-menu');
            menu.classList.toggle('hidden');

            // Re-initialize icons after showing dropdown
            if (!menu.classList.contains('hidden')) {
                setTimeout(() => lucide.createIcons(), 10);
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notification-dropdown');
            const menu = document.getElementById('notification-menu');
            if (dropdown && !dropdown.contains(event.target) && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
            }
        });

        // Update pending count and notification list
        function updatePendingCount() {
            const pendingCountEl = document.getElementById('pending-count');
            if (pendingCountEl) {
                const currentCount = parseInt(pendingCountEl.textContent) || 0;
                const newCount = currentCount + 1;
                pendingCountEl.textContent = newCount > 9 ? '9+' : newCount;
                pendingCountEl.classList.remove('hidden');
                if (newCount === 1) {
                    pendingCountEl.classList.remove('hidden');
                }
            }
        }

        // Add notification to the dropdown list
        function addNotificationToList(data) {
            // Assign unique ID to notification
            data.id = 'notif-' + (++notificationIdCounter) + '-' + Date.now();
            data.read = false;
            notifications.unshift(data);
            updateNotificationListUI();
        }

        // Mark notification as read
        function markAsRead(notificationId) {
            if (!readNotificationIds.has(notificationId)) {
                readNotificationIds.add(notificationId);

                // Update the notification in the array
                const notification = notifications.find(n => n.id === notificationId);
                if (notification) {
                    notification.read = true;
                }

                // Update UI
                updateNotificationListUI();

                // Update pending count (decrease if there are still unread)
                updatePendingCountDisplay();
            }
        }

        // Mark all notifications as read
        function markAllAsRead() {
            notifications.forEach(n => {
                n.read = true;
                readNotificationIds.add(n.id);
            });
            updateNotificationListUI();
            updatePendingCountDisplay();
        }

        // Clear all notifications
        function clearAllNotifications() {
            notifications = [];
            readNotificationIds.clear();
            updateNotificationListUI();
            updatePendingCountDisplay();
        }

        // Update pending count display based on unread notifications
        function updatePendingCountDisplay() {
            const pendingCountEl = document.getElementById('pending-count');
            const unreadCount = notifications.filter(n => !n.read).length;

            if (pendingCountEl) {
                if (unreadCount > 0) {
                    pendingCountEl.textContent = unreadCount > 9 ? '9+' : unreadCount;
                    pendingCountEl.classList.remove('hidden');
                } else {
                    pendingCountEl.classList.add('hidden');
                }
            }
        }

        // Update the notification list UI
        function updateNotificationListUI() {
            const listEl = document.getElementById('notification-list');
            if (!listEl) return;

            if (notifications.length === 0) {
                listEl.innerHTML = `
                    <div class="px-4 py-6 text-center text-sm text-zinc-500">
                        <i data-lucide="bell-off" class="w-8 h-8 mx-auto text-zinc-300 mb-2"></i>
                        <p>No notifications yet</p>
                    </div>
                `;
                lucide.createIcons();
                return;
            }

            listEl.innerHTML = notifications.slice(0, 10).map(n => {
                const isRead = readNotificationIds.has(n.id) || n.read;
                const bgClass = isRead ? 'bg-zinc-50' : 'bg-white';
                const textClass = isRead ? 'text-zinc-500' : 'text-zinc-900';
                const iconBgClass = isRead ? 'bg-zinc-200' : 'bg-blue-100';
                const iconTextClass = isRead ? 'text-zinc-500' : 'text-blue-600';

                return `
                    <div class="px-4 py-3 hover:bg-zinc-100 border-b border-zinc-100 last:border-0 cursor-pointer transition-colors ${bgClass}"
                         onclick="markAsRead('${n.id}'); window.location.href='/restaurant/requests'">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 ${iconBgClass} rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-lucide="${n.status === 'pending' ? 'clock' : 'check-circle'}" class="w-4 h-4 ${iconTextClass}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium ${textClass} truncate">${n.message || 'New Match Request'}</p>
                                <p class="text-xs text-zinc-500 truncate">${n.recipient_name} - ${n.food_name}</p>
                                <p class="text-[10px] text-zinc-400 mt-0.5">${getTimeAgo(n.timestamp)}</p>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            lucide.createIcons();
        }
        @endif
    </script>

    @yield('scripts')
</body>
</html>