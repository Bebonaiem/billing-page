<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BillingHub') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <div id="app">
        <!-- Navigation -->
        @auth
            <nav class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <a href="{{ route('home') }}" class="text-xl font-bold text-blue-600">
                                    BillingHub
                                </a>
                            </div>
                            
                            <!-- Main Navigation -->
                            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                                @if(Auth::user()->email === 'admin@example.com' || Auth::user()->is_admin ?? false)
                                    <a href="{{ route('admin.dashboard') }}" class="text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-blue-500 text-sm font-medium">
                                        Dashboard
                                    </a>
                                    <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium">
                                        Orders
                                    </a>
                                    <a href="{{ route('admin.invoices.index') }}" class="text-gray-500 hover:text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium">
                                        Invoices
                                    </a>
                                    <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium">
                                        Users
                                    </a>
                                @else
                                    <a href="{{ route('client.dashboard') }}" class="text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-blue-500 text-sm font-medium">
                                        Dashboard
                                    </a>
                                    <a href="{{ route('client.services') }}" class="text-gray-500 hover:text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium">
                                        Services
                                    </a>
                                    <a href="{{ route('client.invoices') }}" class="text-gray-500 hover:text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium">
                                        Invoices
                                    </a>
                                    <a href="{{ route('client.tickets') }}" class="text-gray-500 hover:text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium">
                                        Support
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Right side buttons -->
                        <div class="flex items-center">
                            <!-- Notifications -->
                            <button class="relative p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400"></span>
                            </button>
                            
                            <!-- Profile dropdown -->
                            <div class="ml-3 relative">
                                <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&color=7C3AED&background=EBF4FF" alt="{{ Auth::user()->name }}">
                                </button>
                            </div>
                            
                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}" class="ml-3">
                                @csrf
                                <button type="submit" class="text-gray-500 hover:text-gray-900 text-sm font-medium">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
        @else
            <!-- Guest Navigation -->
            <nav class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center">
                                <a href="{{ route('home') }}" class="text-xl font-bold text-blue-600">
                                    BillingHub
                                </a>
                            </div>
                            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                                <a href="{{ route('home') }}" class="text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-blue-500 text-sm font-medium">
                                    Home
                                </a>
                                <a href="{{ route('order') }}" class="text-gray-500 hover:text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium">
                                    Services
                                </a>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-900 text-sm font-medium">
                                Sign in
                            </a>
                            <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
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
        <footer class="bg-white border-t border-gray-200">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <p class="text-gray-500 text-sm">
                        © {{ date('Y') }} {{ config('app.name', 'BillingHub') }}. All rights reserved.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-gray-500 text-sm">
                            Privacy
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-500 text-sm">
                            Terms
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-500 text-sm">
                            Contact
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>
