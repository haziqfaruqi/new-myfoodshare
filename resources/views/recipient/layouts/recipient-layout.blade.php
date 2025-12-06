<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFoodshare - NGO Recipient Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
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

    <!-- Sidebar Navigation -->
    @include('recipient.partials.sidebar')

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">

        <!-- Header -->
        @include('recipient.partials.header')

        <!-- Page Content -->
        @yield('content')
    </main>

    <!-- Modals -->
    @yield('modals')

    <!-- Scripts -->
    @yield('scripts')
</body>
</html>