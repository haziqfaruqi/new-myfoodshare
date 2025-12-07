<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFoodshare - Restaurant Partner Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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
    </script>

    @yield('scripts')
</body>
</html>