<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyFoodshare Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
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
    @include('admin.partials.admin-sidebar')

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden">
        @include('admin.partials.admin-header')

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

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
</body>
</html>