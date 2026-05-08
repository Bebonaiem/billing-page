{{-- Header Component --}}
@php
    $isAuthenticated = auth()->check();
    $user = auth()->user();
    $isAdmin = $isAuthenticated && $user->is_admin;
@endphp

<!-- Navigation -->
@if($isAuthenticated)
    @if($isAdmin)
        {{-- Admin Navigation --}}
        <nav class="glass-effect border-b border-custom">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center group">
                            <div class="w-10 h-10 accent-bg rounded-xl flex items-center justify-center mr-3 shadow-lg animate-float hover-tilt magnetic-button">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <span class="text-xl font-bold accent-text group-hover:scale-105 transition-transform duration-300">{{ config('app.name', 'BillingHub') }}</span>
                        </a>
                    </div>
                    <div class="flex items-center space-x-6">
                        <!-- Theme Toggle -->
                        <button onclick="toggleTheme()" class="p-2 text-secondary hover:text-white rounded-lg transition-all duration-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0018 9a9.003 9.003 0 01-4.646 4.646z"></path>
                            </svg>
                        </button>
                        <!-- Notifications -->
                        <button class="relative p-2.5 text-secondary hover:text-white rounded-xl transition-all duration-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="notification-badge">3</span>
                        </button>
                        <!-- User dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent border-2 border-custom p-0.5 hover-lift">
                                <img class="avatar-sm rounded-full" src="https://ui-avatars.com/api/?name={{ $user->name }}&color=6366f1&background=1e1b4b&bold=true&size=40" alt="{{ $user->name }}">
                                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full" style="background: var(--success); border-color: var(--bg-secondary);"></div>
                            </button>
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform scale-95 translate-y-2"
                                 x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 transform scale-95 translate-y-2"
                                 @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-64 glass-effect-3d border border-custom rounded-xl shadow-glow py-2 animate-bounce-in">
                                <!-- User Info Header -->
                                <div class="px-4 py-3 border-b border-custom/50">
                                    <div class="flex items-center">
                                        <img class="avatar rounded-full mr-3" src="https://ui-avatars.com/api/?name={{ $user->name }}&color=6366f1&background=1e1b4b&bold=true&size=80" alt="{{ $user->name }}">
                                        <div>
                                            <p class="font-bold text-white">{{ $user->name }}</p>
                                            <p class="text-xs text-muted">{{ $user->email }}</p>
                                            <span class="badge badge-accent mt-1">Admin</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Navigation Links -->
                                <div class="py-2">
                                    <a href="#" class="nav-item">
                                        <span class="nav-item-indicator"></span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011-1v4a1 1 0 001-1m-6 0h6"></path>
                                        </svg>
                                        Profile
                                    </a>
                                    
                                    <a href="#" class="nav-item">
                                        <span class="nav-item-indicator"></span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31-.826 2.37 2.37a1.724 1.724 0 00-2.573 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 001.065-2.573C-.94-1.543.826-3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Settings
                                    </a>
                                </div>
                                
                                <div class="border-t border-custom/50 mt-2 pt-2">
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="nav-item w-full text-left">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 011-3h2a3 3 0 013-3v1"/>
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    @else
        {{-- Client Navigation --}}
        <nav class="glass-effect border-b border-custom sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('client.dashboard') }}" class="flex items-center text-xl font-bold accent-text">
                            <div class="w-8 h-8 accent-bg rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011-1v4a1 1 0 001-1m-6 0h6"></path>
                                </svg>
                            </div>
                            {{ config('app.name') }}
                        </a>
                    </div>
                    <div class="flex items-center space-x-6">
                        <a href="{{ route('client.services') }}" class="text-secondary hover:text-white transition-colors duration-200">Services</a>
                        <a href="{{ route('client.invoices') }}" class="text-secondary hover:text-white transition-colors duration-200">Invoices</a>
                        <a href="{{ route('client.tickets') }}" class="text-secondary hover:text-white transition-colors duration-200">Support</a>
                        <!-- Theme Toggle -->
                        <button onclick="toggleTheme()" class="p-2 text-secondary hover:text-white rounded-lg transition-all duration-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0018 9a9.003 9.003 0 01-4.646 4.646z"></path>
                            </svg>
                        </button>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-secondary hover:text-white transition-colors duration-200">
                                <div class="w-8 h-8 accent-bg rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                {{ $user->name }}
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform scale-95 translate-y-2"
                                 x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 transform scale-95 translate-y-2"
                                 @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-64 glass-effect-3d border border-custom rounded-xl shadow-glow py-2 animate-bounce-in">
                                <a href="{{ route('client.profile') }}" class="block px-4 py-2 text-secondary hover:text-white hover:bg-card/50 transition-all duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="nav-item w-full text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 011-3h2a3 3 0 013-3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    @endif
@else
    {{-- Guest Navigation --}}
    <nav class="glass-effect border-b border-custom">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="text-2xl font-bold accent-text flex items-center">
                            <div class="w-8 h-8 accent-bg rounded-lg p-1.5 mr-2">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            {{ config('app.name') }}
                        </a>
                    </div>
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-1">
                        <a href="{{ route('home') }}" class="accent-bg-hover text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                            Home
                        </a>
                        <a href="{{ route('order') }}" class="text-secondary hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13l8 8M5 12h14m-7.5 10.5h.01M12 17a9 9 0 011-18 0h.01"></path>
                            </svg>
                            Order Services
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
@endif
