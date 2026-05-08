<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') | {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased transition-colors duration-300" data-theme="dark">
    <div id="app">
        <div class="min-h-screen bg-gradient-to-br from-blue-900/20 to-purple-900/20">
            <div class="flex flex-col md:flex-row">
                <!-- Mobile Menu Toggle -->
                <div class="md:hidden flex items-center justify-between p-4 glass-effect border-b border-custom sticky top-0 z-40">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center text-lg font-bold accent-text">
                        <div class="w-6 h-6 accent-bg rounded-lg flex items-center justify-center mr-2">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        {{ config('app.name') }}
                    </a>
                    <button onclick="toggleMobileMenu()" class="p-2 text-secondary hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- Sidebar -->
                <aside id="mobile-sidebar" class="hidden md:flex fixed md:relative inset-y-0 left-0 z-40 w-64 glass-effect border-r border-custom flex-col transition-transform duration-300 md:translate-x-0">
                    <div class="flex items-center justify-between p-4 md:justify-center h-16 border-b border-custom">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center text-xl font-bold accent-text">
                            <div class="w-8 h-8 accent-bg rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <span class="hidden md:inline">{{ config('app.name') }}</span>
                        </a>
                        <button onclick="toggleMobileMenu()" class="md:hidden p-2 text-secondary hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <nav class="mt-4 flex-1 overflow-y-auto">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 text-secondary hover:bg-card/50 hover:text-white transition-all duration-200 rounded-lg mx-2 mb-2">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 text-secondary hover:bg-card/50 hover:text-white transition-all duration-200 rounded-lg mx-2 mb-2">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>Users</span>
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="flex items-center px-6 py-3 text-secondary hover:bg-card/50 hover:text-white transition-all duration-200 rounded-lg mx-2 mb-2">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span>Products</span>
                        </a>
                        <a href="{{ route('admin.services.index') }}" class="flex items-center px-6 py-3 text-secondary hover:bg-card/50 hover:text-white transition-all duration-200 rounded-lg mx-2 mb-2">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                            </svg>
                            <span>Services</span>
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="flex items-center px-6 py-3 text-secondary hover:bg-card/50 hover:text-white transition-all duration-200 rounded-lg mx-2 mb-2">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <span>Orders</span>
                        </a>
                        <a href="{{ route('admin.invoices.index') }}" class="flex items-center px-6 py-3 text-secondary hover:bg-card/50 hover:text-white transition-all duration-200 rounded-lg mx-2 mb-2">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                            </svg>
                            <span>Invoices</span>
                        </a>
                        <a href="{{ route('admin.tickets.index') }}" class="flex items-center px-6 py-3 text-secondary hover:bg-card/50 hover:text-white transition-all duration-200 rounded-lg mx-2 mb-2">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <span>Tickets</span>
                        </a>
                        <a href="{{ route('admin.settings') }}" class="flex items-center px-6 py-3 text-secondary hover:bg-card/50 hover:text-white transition-all duration-200 rounded-lg mx-2 mb-2">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Settings</span>
                        </a>
                    </nav>
                </aside>

                <!-- Main content -->
                <div class="flex-1 w-full md:w-auto">
                    <!-- Header Component -->
                    <header class="glass-effect border-b border-custom sticky top-0 z-30 md:relative md:top-auto hidden md:block">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex justify-between items-center py-4">
                                <div>
                                    <h1 class="text-2xl font-semibold accent-text">@yield('header', 'Admin Dashboard')</h1>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <!-- Theme Toggle -->
                                    <button onclick="window.ThemeManager?.init ? window.ThemeManager.init() : toggleTheme()" class="p-2 text-secondary hover:text-white rounded-lg transition-all duration-200 hover-lift">
                                        <svg class="h-5 w-5 theme-icon-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0018 9a9.003 9.003 0 01-4.646 4.646z"></path>
                                        </svg>
                                        <svg class="h-5 w-5 theme-icon-light hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </button>
                                    <!-- Notifications -->
                                    <button class="relative p-2 text-secondary hover:text-white rounded-lg transition-all duration-200 hover-lift">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        </svg>
                                        <span class="notification-badge absolute top-1 right-1 block h-2 w-2 rounded-full error-bg animate-pulse">3</span>
                                    </button>
                                    <!-- User dropdown -->
                                    <div class="relative">
                                        <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 border-2 border-custom p-0.5 hover-lift">
                                            <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&color=3B82F6&background=1E293B" alt="{{ Auth::user()->name }}">
                                        </button>
                                    </div>
                                    <!-- Logout -->
                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-secondary hover:text-white text-sm font-medium px-3 py-2 rounded-lg transition-all duration-200 hover-lift">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </header>

                    <!-- Page Content -->
                    <main class="py-4 md:py-6 px-4 md:px-0">
                        <div class="max-w-7xl mx-auto">
                            @yield('content')
                        </div>
                    </main>
                    
                    <!-- Footer Component -->
                    @include('components.footer')
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Toggle Script -->
    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('mobile-sidebar');
            if (sidebar) {
                sidebar.classList.toggle('hidden');
            }
        }

        function toggleTheme() {
            if (window.ThemeManager && window.ThemeManager.init) {
                window.ThemeManager.init();
            } else {
                const html = document.documentElement;
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            }
        }
        
        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('#mobile-sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                const sidebar = document.getElementById('mobile-sidebar');
                if (sidebar) {
                    sidebar.classList.add('hidden');
                }
            });
        });
    </script>

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>
