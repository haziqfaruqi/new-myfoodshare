<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MyFoodshare Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Custom Scrollbar for sleek look */
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

        /* Toggle Switch */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #10b981;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #10b981;
        }

        /* Hide number input arrows */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* Pulse animation for urgent pickups */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .pulse-slow {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body class="bg-zinc-50 text-zinc-900 flex h-screen overflow-hidden selection:bg-emerald-100 selection:text-emerald-900">
    <!-- Notification Toast Container -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none"></div>

    <!-- Mobile Sidebar Overlay -->
    <div id="admin-sidebar-overlay" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden" onclick="toggleSidebar()"></div>

    @include('admin.partials.admin-sidebar')

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden md:ml-0">
        @include('admin.partials.admin-header')

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            @yield('content')
        </main>
    </div>

@auth
<script>
    // Pusher Configuration for Real-time Notifications
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

    // Subscribe to admin's private channel
    // Using public channel for testing (temporarily)
    const adminChannel = pusher.subscribe('admin-listings');

    // Handle connection and subscription events
    pusher.connection.bind('connected', function() {
        console.log('Pusher connected for admin!');
    });

    pusher.connection.bind('disconnected', function() {
        console.log('Pusher disconnected!');
    });

    pusher.connection.bind('error', function(err) {
        console.error('Pusher connection error:', err);
    });

    adminChannel.bind('pusher:subscription_succeeded', function() {
        console.log('Successfully subscribed to private-admin.matches');
    });

    adminChannel.bind('pusher:subscription_error', function(error) {
        console.error('Subscription to private-admin.matches failed:', error);
    });

    // Load database notifications on page load
    loadDatabaseNotifications();

    async function loadDatabaseNotifications() {
        try {
            const response = await fetch('/admin/notifications', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (response.ok) {
                const data = await response.json();
                console.log('Loaded database notifications:', data);

                // Process each notification
                data.notifications.forEach(function(notif) {
                    const notificationData = {
                        id: 'db-' + notif.id,
                        title: getNotificationTitle(notif),
                        message: getNotificationMessage(notif),
                        type: getNotificationType(notif.type),
                        url: getNotificationUrl(notif),
                        timestamp: notif.created_at,
                        read: false
                    };

                    addAdminNotificationToList(notificationData);
                });

                // Update unread count from server
                unreadCount = data.unread_count;
                updateAdminNotificationCount();
                updateAdminNotificationList();
            }
        } catch (error) {
            console.error('Failed to load notifications:', error);
        }
    }

    function getNotificationTitle(notif) {
        if (notif.type.includes('FoodListing')) return 'New Food Listing';
        if (notif.type.includes('Match')) return 'New Match Request';
        return 'Notification';
    }

    function getNotificationMessage(notif) {
        if (notif.data && notif.data.food_name) {
            const restaurant = notif.data.restaurant_name || notif.data.created_by || 'A restaurant';
            return restaurant + ' posted: ' + notif.data.food_name;
        }
        if (notif.data && notif.data.message) {
            return notif.data.message;
        }
        return 'You have a new notification';
    }

    function getNotificationType(type) {
        if (type.includes('FoodListing')) return 'food_listing_created';
        if (type.includes('Match')) return 'match_created';
        return 'notification';
    }

    function getNotificationUrl(notif) {
        if (notif.type.includes('FoodListing')) return '/admin/food-listings';
        if (notif.type.includes('Match')) return '/admin/pickup-monitoring';
        return null;
    }

    // Store notifications with read status
    let adminNotifications = [];
    let unreadCount = 0;
    let notificationIdCounter = 0;

    // Toggle notification dropdown
    function toggleAdminNotificationDropdown() {
        const menu = document.getElementById('admin-notification-menu');
        if (menu) {
            menu.classList.toggle('hidden');
            if (!menu.classList.contains('hidden')) {
                setTimeout(() => lucide.createIcons(), 10);
            }
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('admin-notification-dropdown');
        const menu = document.getElementById('admin-notification-menu');
        if (dropdown && !dropdown.contains(event.target) && menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
        }
    });

    // Update notification count badge
    function updateAdminNotificationCount() {
        const countEl = document.getElementById('admin-notification-count');
        if (countEl) {
            if (unreadCount > 0) {
                countEl.textContent = unreadCount > 9 ? '9+' : unreadCount;
                countEl.classList.remove('hidden');
            } else {
                countEl.classList.add('hidden');
            }
        }
    }

    // Update notification list UI
    function updateAdminNotificationList() {
        const listEl = document.getElementById('admin-notification-list');
        if (!listEl) return;

        if (adminNotifications.length === 0) {
            listEl.innerHTML = '<div class="px-4 py-6 text-center text-sm text-zinc-500"><i data-lucide="bell-off" class="w-8 h-8 mx-auto text-zinc-300 mb-2"></i><p>No notifications yet</p></div>';
            lucide.createIcons();
            return;
        }

        // Sort by timestamp newest first, then take first 10
        const sortedNotifications = adminNotifications.slice().sort(function(a, b) {
            return new Date(b.timestamp) - new Date(a.timestamp);
        });

        listEl.innerHTML = sortedNotifications.slice(0, 10).map(function(n) {
            const isRead = n.read;
            const bgClass = isRead ? 'bg-zinc-50' : 'bg-white';
            const textClass = isRead ? 'text-zinc-500' : 'text-zinc-900';
            const iconBgClass = isRead ? 'bg-zinc-200' : 'bg-amber-100';
            const iconTextClass = isRead ? 'text-zinc-500' : 'text-amber-600';
            let iconName = 'bell';

            if (n.type === 'food_listing_created') {
                iconName = 'plus-circle';
            } else if (n.type === 'match_created') {
                iconName = 'heart';
            }

            return '<div class="px-4 py-3 hover:bg-zinc-100 border-b border-zinc-100 last:border-0 cursor-pointer transition-colors ' + bgClass + '" onclick="markAdminAsRead(\'' + n.id + '\'); ' + (n.url ? 'window.location.href=\'' + n.url + '\'' : '') + '">' +
                '<div class="flex items-start gap-3">' +
                '<div class="w-8 h-8 ' + iconBgClass + ' rounded-full flex items-center justify-center flex-shrink-0">' +
                '<i data-lucide="' + iconName + '" class="w-4 h-4 ' + iconTextClass + '"></i></div>' +
                '<div class="flex-1 min-w-0">' +
                '<p class="text-sm font-medium ' + textClass + ' truncate">' + (n.title || 'Notification') + '</p>' +
                '<p class="text-xs text-zinc-500 truncate">' + (n.message || '') + '</p>' +
                '<p class="text-[10px] text-zinc-400 mt-0.5">' + getTimeAgo(n.timestamp) + '</p></div></div></div>';
        }).join('');

        lucide.createIcons();
    }

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

    // Mark notification as read
    function markAdminAsRead(notificationId) {
        const notification = adminNotifications.find(function(n) { return n.id === notificationId; });
        if (notification && !notification.read) {
            notification.read = true;
            unreadCount--;
            updateAdminNotificationCount();
            updateAdminNotificationList();
        }
    }

    // Mark all as read
    function markAllAdminAsRead() {
        adminNotifications.forEach(function(n) { n.read = true; });
        unreadCount = 0;
        updateAdminNotificationCount();
        updateAdminNotificationList();
    }

    // Clear all notifications
    function clearAllAdminNotifications() {
        adminNotifications = [];
        unreadCount = 0;
        updateAdminNotificationCount();
        updateAdminNotificationList();
    }

    // Show notification toast
    function showAdminNotification(data) {
        const container = document.getElementById('notification-container');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = 'pointer-events-auto bg-white rounded-lg shadow-lg border-l-4 border-amber-500 p-4 max-w-sm transform transition-all duration-300 translate-x-full opacity-0';

        let iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />';
        let iconBg = 'bg-amber-100';
        let iconColor = 'text-amber-600';

        if (data.type === 'match_created') {
            iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />';
            iconBg = 'bg-emerald-100';
            iconColor = 'text-emerald-600';
        }

        notification.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 ${iconBg} rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${iconSvg}
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-zinc-900">${data.title || 'New Notification'}</p>
                    <p class="text-xs text-zinc-500 mt-1">${data.message || ''}</p>
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

        setTimeout(() => {
            notification.classList.remove('translate-x-full', 'opacity-0');
        }, 10);

        setTimeout(() => {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => notification.remove(), 300);
        }, 8000);
    }

    // Add notification to list
    function addAdminNotificationToList(data) {
        data.id = 'admin-notif-' + (++notificationIdCounter) + '-' + Date.now();
        data.read = false;
        adminNotifications.unshift(data);
        unreadCount++;
        updateAdminNotificationCount();
        updateAdminNotificationList();
    }

    // Listen for food listing created events
    adminChannel.bind('food.listing.created', function(data) {
        console.log('Food listing created:', data);

        const notificationData = {
            title: 'New Food Listing',
            message: data.restaurant_name + ' posted: ' + data.food_name,
            type: 'food_listing_created',
            url: '/admin/approvals',
            timestamp: data.timestamp
        };

        showAdminNotification(notificationData);
        addAdminNotificationToList(notificationData);

        // Play notification sound
        playAdminNotificationSound();
    });

    // Listen for match status updates
    adminChannel.bind('match.status.updated', function(data) {
        console.log('Match status update:', data);

        const notificationData = {
            title: 'Match Status Updated',
            message: data.message || 'Match status has been updated',
            type: 'match_created',
            url: '/admin/pickup-monitoring',
            timestamp: data.timestamp
        };

        showAdminNotification(notificationData);
        addAdminNotificationToList(notificationData);

        // Play notification sound
        playAdminNotificationSound();
    });

    // Play notification sound
    function playAdminNotificationSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQ==');
        audio.volume = 0.3;
        audio.play().catch(e => console.log('Audio play failed:', e));
    }
</script>
@endauth

<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        // Mobile Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('admin-sidebar-overlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Set active state for sidebar navigation
        const currentPath = window.location.pathname;
        const sidebarLinks = document.querySelectorAll('aside a[href]');

        sidebarLinks.forEach(link => {
            const linkPath = new URL(link.href).pathname;

            // Special handling for pickup monitoring routes
            if (linkPath.includes('/pickup-monitoring')) {
                if (linkPath.includes('/pickup-monitoring/report')) {
                    // Only highlight report link when on report page
                    if (currentPath === '/admin/pickup-monitoring/report' || currentPath.startsWith('/admin/pickup-monitoring/report/')) {
                        link.classList.add('text-emerald-600', 'bg-emerald-50');
                        link.classList.remove('text-zinc-600');
                        const icon = link.querySelector('i');
                        if (icon) {
                            icon.classList.remove('text-zinc-400');
                            icon.classList.add('text-emerald-600');
                        }
                    } else {
                        link.classList.remove('text-emerald-600', 'bg-emerald-50');
                        link.classList.add('text-zinc-600');
                        const icon = link.querySelector('i');
                        if (icon) {
                            icon.classList.remove('text-emerald-600');
                            icon.classList.add('text-zinc-400');
                        }
                    }
                } else {
                    // Only highlight main pickup monitoring link when not on report subpage
                    if (currentPath === '/admin/pickup-monitoring' &&
                        !currentPath.startsWith('/admin/pickup-monitoring/report')) {
                        link.classList.add('text-emerald-600', 'bg-emerald-50');
                        link.classList.remove('text-zinc-600');
                        const icon = link.querySelector('i');
                        if (icon) {
                            icon.classList.remove('text-zinc-400');
                            icon.classList.add('text-emerald-600');
                        }
                    } else {
                        link.classList.remove('text-emerald-600', 'bg-emerald-50');
                        link.classList.add('text-zinc-600');
                        const icon = link.querySelector('i');
                        if (icon) {
                            icon.classList.remove('text-emerald-600');
                            icon.classList.add('text-zinc-400');
                        }
                    }
                }
                return; // Skip the general logic for pickup monitoring links
            }

            // General logic for all other links
            if (currentPath === linkPath || currentPath.startsWith(linkPath + '/')) {
                link.classList.add('text-emerald-600', 'bg-emerald-50');
                link.classList.remove('text-zinc-600');

                // Update icon color for active links
                const icon = link.querySelector('i');
                if (icon) {
                    icon.classList.remove('text-zinc-400');
                    icon.classList.add('text-emerald-600');
                }
            } else {
                link.classList.remove('text-emerald-600', 'bg-emerald-50');
                link.classList.add('text-zinc-600');

                // Reset icon color for inactive links
                const icon = link.querySelector('i');
                if (icon && !link.closest('.h-16')) { // Don't reset the logo icon
                    icon.classList.remove('text-emerald-600');
                    icon.classList.add('text-zinc-400');
                }
            }
        });
    });

    function toggleSidebar() {
        const sidebar = document.querySelector('aside');
        const mainContent = document.querySelector('.flex-1.flex.flex-col');
        if (sidebar.classList.contains('hidden')) {
            sidebar.classList.remove('hidden');
            mainContent.classList.add('md:ml-64');
        } else {
            sidebar.classList.add('hidden');
            mainContent.classList.remove('md:ml-64');
        }
    }
</script>

@stack('scripts')
</body>
</html>