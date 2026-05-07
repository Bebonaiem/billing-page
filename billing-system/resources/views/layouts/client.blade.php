@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-900/20 to-purple-900/20">
    <!-- Top navigation -->
    <nav class="glass-effect border-b border-custom sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('client.dashboard') }}" class="flex items-center text-xl font-bold accent-text">
                        <div class="w-8 h-8 accent-bg rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        {{ config('app.name') }}
                    </a>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="{{ route('client.services') }}" class="text-secondary hover:text-white transition-colors duration-200">Services</a>
                    <a href="{{ route('client.invoices') }}" class="text-secondary hover:text-white transition-colors duration-200">Invoices</a>
                    <a href="{{ route('client.tickets') }}" class="text-secondary hover:text-white transition-colors duration-200">Support</a>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-secondary hover:text-white transition-colors duration-200">
                            <div class="w-8 h-8 accent-bg rounded-full flex items-center justify-center mr-2">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            {{ auth()->user()->name }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 glass-effect border border-custom rounded-xl shadow-glow py-2">
                            <a href="{{ route('client.profile') }}" class="block px-4 py-2 text-secondary hover:text-white hover:bg-card/50 transition-all duration-200">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-secondary hover:text-white hover:bg-card/50 transition-all duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
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

    <!-- Page content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-500/20 border border-green-500/50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-400">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-lg bg-red-500/20 border border-red-500/50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-400">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="glass-effect border-t border-custom mt-12">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-secondary">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
                <div class="mt-4 flex justify-center space-x-6">
                    <a href="#" class="text-secondary hover:text-white transition-colors duration-200">Privacy Policy</a>
                    <a href="#" class="text-secondary hover:text-white transition-colors duration-200">Terms of Service</a>
                    <a href="#" class="text-secondary hover:text-white transition-colors duration-200">Contact</a>
                </div>
            </div>
        </div>
    </footer>
</div>
@endsection
