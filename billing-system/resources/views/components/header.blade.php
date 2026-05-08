{{-- Header Component --}}
@php
    $isAuthenticated = auth()->check();
    $user = auth()->user();
    $isAdmin = $isAuthenticated && $user->is_admin;
@endphp

<!-- Alpine.js Initialization -->
<div x-data="{ mobileMenuOpen: false }" style="position: relative; z-index: 100;">
@if($isAuthenticated)
    {{-- Admins use client navigation with Admin Panel option --}}
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
                            <svg class="h-5 w-5 theme-icon-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0018 9a9.003 9.003 0 01-4.646 4.646z"></path>
                            </svg>
                            <svg class="h-5 w-5 theme-icon-light" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </button>
                        <!-- Notifications -->
                        <div class="relative" x-data="{ notificationDropdownOpen: false }">
                            <button @click="notificationDropdownOpen = !notificationDropdownOpen" class="relative p-2.5 text-secondary hover:text-white rounded-xl transition-all duration-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span class="notification-badge">3</span>
                            </button>
                            <!-- Notification Dropdown -->
                            <div x-show="notificationDropdownOpen" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform scale-95 translate-y-2"
                                 x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 transform scale-95 translate-y-2"
                                 @click.away="notificationDropdownOpen = false" 
                                 class="absolute right-0 mt-2 w-80 glass-effect-3d border border-custom rounded-xl shadow-glow py-2 animate-bounce-in dropdown-menu" style="z-index: 9999;">
                                <div class="px-4 py-3 border-b border-custom/50">
                                    <h3 class="font-semibold text-white">Notifications</h3>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <div class="px-4 py-3 hover:bg-card/50 transition-colors duration-200">
                                        <p class="text-sm text-white">New invoice #12345 is ready</p>
                                        <p class="text-xs text-muted">2 minutes ago</p>
                                    </div>
                                    <div class="px-4 py-3 hover:bg-card/50 transition-colors duration-200">
                                        <p class="text-sm text-white">Service #6789 has been activated</p>
                                        <p class="text-xs text-muted">1 hour ago</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- User dropdown -->
                        <div class="relative" x-data="{ userDropdownOpen: false }">
                            <button @click="userDropdownOpen = !userDropdownOpen" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent border-2 border-custom p-0.5 hover-lift">
                                <img class="avatar-sm rounded-full" src="https://ui-avatars.com/api/?name={{ $user->name }}&color=6366f1&background=1e1b4b&bold=true&size=40" alt="{{ $user->name }}">
                                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full" style="background: var(--success); border-color: var(--bg-secondary);"></div>
                            </button>
                            <div x-show="userDropdownOpen" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform scale-95 translate-y-2"
                                 x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 transform scale-95 translate-y-2"
                                 @click.away="userDropdownOpen = false" 
                                 class="absolute right-0 mt-2 w-64 glass-effect-3d border border-custom rounded-xl shadow-glow py-2 animate-bounce-in dropdown-menu" style="z-index: 9999;">
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
                                    <a href="{{ route('client.profile') }}" class="nav-item">
                                        <span class="nav-item-indicator"></span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011-1v4a1 1 0 001-1m-6 0h6"></path>
                                        </svg>
                                        Profile
                                    </a>
                                    
                                    <a href="{{ route('admin.dashboard') }}" class="nav-item">
                                        <span class="nav-item-indicator"></span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31-.826 2.37 2.37a1.724 1.724 0 00-2.573 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 001.065-2.573C-.94-1.543.826-3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Admin Panel
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
</div>
