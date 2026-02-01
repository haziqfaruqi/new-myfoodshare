<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyFoodshare - Recipient Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
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

        /* Map Placeholder Pattern */
        .map-pattern {
            background-color: #e5e7eb;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239ca3af' fill-opacity='0.2'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-zinc-50 text-zinc-900 flex h-screen overflow-hidden">
    <!-- Mobile Sidebar Overlay -->
    <div id="recipient-sidebar-overlay" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar Navigation -->
    @include('recipient.partials.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden relative md:ml-0">
        <!-- Notification Toast Container -->
        <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none"></div>

        <!-- Header -->
        @include('recipient.partials.header')

        <!-- Page Content -->
        @yield('content')
    </main>

    <!-- Modals -->
    @yield('modals')

    <script>
        // Initialize Lucide icons globally
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // Mobile Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('recipient-sidebar');
            const overlay = document.getElementById('recipient-sidebar-overlay');

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

        // Subscribe to recipient's channels - using public channel for testing
        const recipientChannel = pusher.subscribe('recipient-notifications');
        const privateRecipientChannel = pusher.subscribe('private-matches.{{ auth()->id() }}');

        // Handle connection and subscription events
        pusher.connection.bind('connected', function() {
            console.log('Pusher connected for recipient!');
        });

        pusher.connection.bind('disconnected', function() {
            console.log('Pusher disconnected!');
        });

        pusher.connection.bind('error', function(err) {
            console.error('Pusher connection error:', err);
        });

        privateRecipientChannel.bind('pusher:subscription_succeeded', function() {
            console.log('Successfully subscribed to private-matches.{{ auth()->id() }}');
        });

        recipientChannel.bind('pusher:subscription_succeeded', function() {
            console.log('Successfully subscribed to recipient-notifications');
        });

        // Listen for match status updates (schedule, approval, etc.)
        recipientChannel.bind('match.status.updated', function(data) {
            console.log('Match status update:', data);

            // Show notification toast
            showNotification(data);

            // Play notification sound
            playNotificationSound();

            // Update pending count and add to dropdown
            updatePendingCount();
            addNotificationToList(data);

            // Refresh page if on my-matches to show updated status
            if (window.location.pathname.includes('/recipient/my-matches') ||
                window.location.pathname.includes('/recipient/matches')) {
                setTimeout(() => location.reload(), 2000);
            }
        });

        // Store notifications with read status
        let notifications = [];

        // Track read notification IDs
        let readNotificationIds = new Set();

        // Generate unique ID for notifications
        let notificationIdCounter = 0;

        // Toggle notification dropdown
        function toggleNotificationDropdown() {
            const menu = document.getElementById('notification-menu');
            if (menu) {
                menu.classList.toggle('hidden');

                // Re-initialize icons after showing dropdown
                if (!menu.classList.contains('hidden')) {
                    setTimeout(() => lucide.createIcons(), 10);
                }
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notification-dropdown');
            const menu = document.getElementById('notification-menu');
            if (dropdown && !dropdown.contains(event.target) && menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
            }
        });

        // Get time ago string
        function getTimeAgo(timestamp) {
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

        // Update pending count
        function updatePendingCount() {
            const pendingCountEl = document.getElementById('pending-count');
            if (pendingCountEl) {
                const newCount = (parseInt(pendingCountEl.textContent) || 0) + 1;
                pendingCountEl.textContent = newCount > 9 ? '9+' : newCount;
                if (newCount === 1) {
                    pendingCountEl.classList.remove('hidden');
                }
            }
        }

        // Show notification toast
        function showNotification(data) {
            const container = document.getElementById('notification-container');
            if (!container) return;

            const notification = document.createElement('div');
            notification.className = 'pointer-events-auto bg-white rounded-lg shadow-lg border-l-4 border-emerald-500 p-4 max-w-sm transform transition-all duration-300 translate-x-full opacity-0';

            // Icon based on status
            let iconSvg = '';
            let iconBg = 'bg-emerald-100';
            let iconColor = 'text-emerald-600';

            if (data.status === 'scheduled') {
                iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />';
                iconBg = 'bg-blue-100';
                iconColor = 'text-blue-600';
            } else if (data.status === 'approved') {
                iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                iconBg = 'bg-emerald-100';
                iconColor = 'text-emerald-600';
            } else if (data.status === 'rejected') {
                iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
                iconBg = 'bg-red-100';
                iconColor = 'text-red-600';
            } else {
                iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />';
            }

            notification.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 ${iconBg} rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${iconSvg}
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-zinc-900">${data.message || 'Match Update'}</p>
                        <p class="text-xs text-zinc-500 mt-1">${getNotificationDetails(data)}</p>
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

        // Get notification details based on status
        function getNotificationDetails(data) {
            if (data.status === 'scheduled' && data.pickup_scheduled_at) {
                const pickupDate = new Date(data.pickup_scheduled_at);
                return `Pickup scheduled for ${pickupDate.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })} at ${pickupDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}`;
            } else if (data.status === 'approved') {
                return `${data.restaurant_name || 'Restaurant'} approved your request`;
            } else if (data.status === 'rejected') {
                return `${data.restaurant_name || 'Restaurant'} declined your request`;
            }
            return `${data.restaurant_name || 'Restaurant'} - ${data.food_name || 'Food item'}`;
        }

        // Play notification sound
        function playNotificationSound() {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQ==');
            audio.volume = 0.3;
            audio.play().catch(e => console.log('Audio play failed:', e));
        }

        function addNotificationToList(data) {
            data.id = 'notif-' + (++notificationIdCounter) + '-' + Date.now();
            data.read = false;
            notifications.unshift(data);
            updateNotificationListUI();
        }

        function markAsRead(notificationId) {
            if (!readNotificationIds.has(notificationId)) {
                readNotificationIds.add(notificationId);
                const notification = notifications.find(function(n) { return n.id === notificationId; });
                if (notification) notification.read = true;
                updateNotificationListUI();
                updatePendingCountDisplay();
            }
        }

        function markAllAsRead() {
            notifications.forEach(function(n) { n.read = true; readNotificationIds.add(n.id); });
            updateNotificationListUI();
            updatePendingCountDisplay();
        }

        function clearAllNotifications() {
            notifications = [];
            readNotificationIds.clear();
            updateNotificationListUI();
            updatePendingCountDisplay();
        }

        function updatePendingCountDisplay() {
            const pendingCountEl = document.getElementById('pending-count');
            const unreadCount = notifications.filter(function(n) { return !n.read; }).length;
            if (pendingCountEl) {
                if (unreadCount > 0) {
                    pendingCountEl.textContent = unreadCount > 9 ? '9+' : unreadCount;
                    pendingCountEl.classList.remove('hidden');
                } else {
                    pendingCountEl.classList.add('hidden');
                }
            }
        }

        function updateNotificationListUI() {
            const listEl = document.getElementById('notification-list');
            if (!listEl) return;

            if (notifications.length === 0) {
                listEl.innerHTML = '<div class="px-4 py-6 text-center text-sm text-zinc-500"><i data-lucide="bell-off" class="w-8 h-8 mx-auto text-zinc-300 mb-2"></i><p>No notifications yet</p></div>';
                lucide.createIcons();
                return;
            }

            listEl.innerHTML = notifications.slice(0, 10).map(function(n) {
                const isRead = readNotificationIds.has(n.id) || n.read;
                const bgClass = isRead ? 'bg-zinc-50' : 'bg-white';
                const textClass = isRead ? 'text-zinc-500' : 'text-zinc-900';
                const iconBgClass = isRead ? 'bg-zinc-200' : 'bg-blue-100';
                const iconTextClass = isRead ? 'text-zinc-500' : 'text-blue-600';
                const iconName = n.status === 'scheduled' ? 'calendar' : (n.status === 'approved' ? 'check-circle' : (n.status === 'rejected' ? 'x-circle' : 'bell'));

                return '<div class="px-4 py-3 hover:bg-zinc-100 border-b border-zinc-100 last:border-0 cursor-pointer transition-colors ' + bgClass + '" onclick="markAsRead(\'' + n.id + '\'); window.location.href=\'/recipient/my-matches\'">' +
                    '<div class="flex items-start gap-3">' +
                    '<div class="w-8 h-8 ' + iconBgClass + ' rounded-full flex items-center justify-center flex-shrink-0">' +
                    '<i data-lucide="' + iconName + '" class="w-4 h-4 ' + iconTextClass + '"></i></div>' +
                    '<div class="flex-1 min-w-0">' +
                    '<p class="text-sm font-medium ' + textClass + ' truncate">' + (n.message || 'Match Update') + '</p>' +
                    '<p class="text-xs text-zinc-500 truncate">' + (n.restaurant_name || 'Restaurant') + ' - ' + (n.food_name || 'Food') + '</p>' +
                    '<p class="text-[10px] text-zinc-400 mt-0.5">' + getTimeAgo(n.timestamp) + '</p></div></div></div>';
            }).join('');

            lucide.createIcons();
        }
        @endif
    </script>

    @yield('scripts')
</body>
</html>
