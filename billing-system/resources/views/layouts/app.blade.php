<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BillingHub') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <style>
        :root {
            --bg-primary: 15 23 42;
            --bg-secondary: 30 41 59;
            --bg-tertiary: 51 65 85;
            --text-primary: 248 250 252;
            --text-secondary: 203 213 225;
            --text-tertiary: 148 163 184;
            --accent: 59 130 246;
            --accent-hover: 37 99 235;
            --border: 71 85 105;
            --card: 30 41 59;
            --success: 34 197 94;
            --warning: 251 146 60;
            --error: 239 68 68;
        }

        [data-theme="light"] {
            --bg-primary: 255 255 255;
            --bg-secondary: 249 250 251;
            --bg-tertiary: 243 244 246;
            --text-primary: 17 24 39;
            --text-secondary: 75 85 99;
            --text-tertiary: 107 114 128;
            --accent: 59 130 246;
            --accent-hover: 37 99 235;
            --border: 229 231 235;
            --card: 255 255 255;
            --success: 34 197 94;
            --warning: 251 146 60;
            --error: 239 68 68;
        }

        body {
            background: rgb(var(--bg-primary));
            color: rgb(var(--text-primary));
            transition: all 0.3s ease;
        }

        .bg-card {
            background: rgb(var(--card));
            border-color: rgb(var(--border));
        }

        .text-secondary {
            color: rgb(var(--text-secondary));
        }

        .text-tertiary {
            color: rgb(var(--text-tertiary));
        }

        .border-custom {
            border-color: rgb(var(--border));
        }

        .accent-bg {
            background: rgb(var(--accent));
        }

        .accent-bg-hover:hover {
            background: rgb(var(--accent-hover));
        }

        .accent-text {
            color: rgb(var(--accent));
        }

        .success-bg {
            background: rgb(var(--success));
        }

        .warning-bg {
            background: rgb(var(--warning));
        }

        .error-bg {
            background: rgb(var(--error));
        }

        .glass-effect {
            background: rgba(var(--card), 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(var(--border), 0.5);
        }

        .gradient-bg {
            background: linear-gradient(135deg, rgb(var(--accent)) 0%, rgb(var(--accent-hover)) 100%);
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .shadow-glow {
            box-shadow: 0 0 20px rgba(var(--accent), 0.3);
        }

        .shadow-glow-hover:hover {
            box-shadow: 0 0 30px rgba(var(--accent), 0.5);
        }
    </style>
</head>
<body class="font-sans antialiased transition-colors duration-300" data-theme="dark">
    <div id="app">
        <!-- Navigation -->
        @auth
            <nav class="glass-effect border-b border-custom">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <a href="{{ route('home') }}" class="text-2xl font-bold accent-text flex items-center">
                                    <svg class="w-8 h-8 mr-2 accent-bg rounded-lg p-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    BillingHub
                                </a>
                            </div>
                            
                            <!-- Main Navigation -->
                            <div class="hidden sm:ml-8 sm:flex sm:space-x-1">
                                @if(Auth::user()->is_admin)
                                    <a href="{{ route('admin.dashboard') }}" class="accent-bg-hover text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                                        Dashboard
                                    </a>
                                    <a href="{{ route('admin.orders.index') }}" class="text-secondary hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                                        Orders
                                    </a>
                                    <a href="{{ route('admin.invoices.index') }}" class="text-secondary hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                                        Invoices
                                    </a>
                                    <a href="{{ route('admin.users.index') }}" class="text-secondary hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                                        Users
                                    </a>
                                @else
                                    <a href="{{ route('client.dashboard') }}" class="accent-bg-hover text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                                        Dashboard
                                    </a>
                                    <a href="{{ route('client.services') }}" class="text-secondary hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                                        Services
                                    </a>
                                    <a href="{{ route('client.invoices') }}" class="text-secondary hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                                        Invoices
                                    </a>
                                    <a href="{{ route('client.tickets') }}" class="text-secondary hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                                        Support
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Right side buttons -->
                        <div class="flex items-center space-x-3">
                            <!-- Theme Toggle -->
                            <button onclick="toggleTheme()" class="p-2 text-secondary hover:text-white rounded-lg transition-all duration-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0018 9a9.003 9.003 0 01-4.646 4.646z"></path>
                                </svg>
                            </button>
                            
                            <!-- Notifications -->
                            <button class="relative p-2 text-secondary hover:text-white rounded-lg transition-all duration-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span class="absolute top-1 right-1 block h-2 w-2 rounded-full error-bg animate-pulse"></span>
                            </button>
                            
                            <!-- Profile dropdown -->
                            <div class="relative">
                                <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 border-2 border-custom p-0.5">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&color=3B82F6&background=1E293B" alt="{{ Auth::user()->name }}">
                                </button>
                            </div>
                            
                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-secondary hover:text-white text-sm font-medium px-3 py-2 rounded-lg transition-all duration-200">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
        @else
            <!-- Guest Navigation -->
            <nav class="glass-effect border-b border-custom">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center">
                                <a href="{{ route('home') }}" class="text-2xl font-bold accent-text flex items-center">
                                    <svg class="w-8 h-8 mr-2 accent-bg rounded-lg p-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    BillingHub
                                </a>
                            </div>
                            <div class="hidden sm:ml-8 sm:flex sm:space-x-1">
                                <a href="{{ route('home') }}" class="accent-bg-hover text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                                    Home
                                </a>
                                <a href="{{ route('order') }}" class="text-secondary hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                                    Services
                                </a>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Theme Toggle -->
                            <button onclick="toggleTheme()" class="p-2 text-secondary hover:text-white rounded-lg transition-all duration-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0018 9a9.003 9.003 0 01-4.646 4.646z"></path>
                                </svg>
                            </button>
                            <a href="{{ route('login') }}" class="text-secondary hover:text-white text-sm font-medium px-4 py-2 rounded-lg transition-all duration-200">
                                Sign in
                            </a>
                            <a href="{{ route('register') }}" class="accent-bg-hover text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                                Sign up
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
        @endauth

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="glass-effect border-t border-custom mt-12">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <p class="text-tertiary text-sm">
                        © {{ date('Y') }} {{ config('app.name', 'BillingHub') }}. All rights reserved.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-tertiary hover:text-white text-sm transition-colors duration-200">
                            Privacy
                        </a>
                        <a href="#" class="text-tertiary hover:text-white text-sm transition-colors duration-200">
                            Terms
                        </a>
                        <a href="#" class="text-tertiary hover:text-white text-sm transition-colors duration-200">
                            Contact
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Theme Toggle Script -->
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update theme toggle icon
            const themeIcon = document.querySelector('[onclick="toggleTheme()"] svg');
            if (themeIcon) {
                if (newTheme === 'light') {
                    themeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>';
                } else {
                    themeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0018 9a9.003 9.003 0 01-4.646 4.646z"></path>';
                }
            }
        }
        
        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            // Update theme toggle icon based on saved theme
            const themeIcon = document.querySelector('[onclick="toggleTheme()"] svg');
            if (themeIcon && savedTheme === 'light') {
                themeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>';
            }
        });
    </script>

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>
