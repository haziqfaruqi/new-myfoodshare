<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FoodShare Admin')</title>
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