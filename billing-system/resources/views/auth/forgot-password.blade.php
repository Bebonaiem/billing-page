@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900/20 to-purple-900/20 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 accent-bg rounded-2xl mb-6 shadow-lg animate-float">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="text-4xl font-bold mb-2 accent-text">Forgot Password?</h2>
            <p class="text-secondary">
                No worries, we'll send you reset instructions.
            </p>
        </div>

        <!-- Forgot Password Form -->
        <div class="glass-effect rounded-2xl p-8 border border-custom shadow-glow">
            <form class="space-y-6" method="POST" action="{{ route('password.email') }}">
                @csrf
                <input type="hidden" name="remember" value="false">
                
                @if ($errors->any())
                    <div class="rounded-lg bg-red-500/20 border border-red-500/50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-400">
                                    There were errors with your submission
                                </h3>
                                <div class="mt-2 text-sm text-red-300">
                                    <ul class="list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('status'))
                    <div class="rounded-lg bg-green-500/20 border border-green-500/50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-400">
                                    Success
                                </h3>
                                <div class="mt-2 text-sm text-green-300">
                                    <p>{{ session('status') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-secondary mb-2">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter your email">
                    </div>
                </div>

                <div class="space-y-3">
                    <button type="submit" class="w-full accent-bg-hover text-white py-3 px-4 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Send password reset link
                    </button>
                    
                    <div class="text-center">
                        <p class="text-secondary text-sm">
                            Remember your password?
                            <a href="{{ route('login') }}" class="accent-text hover:opacity-80 font-medium transition-opacity duration-200">
                                Sign in here
                            </a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
