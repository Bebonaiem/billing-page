@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-900/20 to-blue-900/20 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 accent-bg rounded-2xl mb-6 shadow-lg animate-float">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <h2 class="text-4xl font-bold mb-2 accent-text">Reset Password</h2>
            <p class="text-secondary">
                Choose your new password
            </p>
        </div>

        <!-- Reset Password Form -->
        <div class="glass-effect rounded-2xl p-8 border border-custom shadow-glow">
            <form class="space-y-6" method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                
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

                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-secondary mb-2">New Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                               class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter new password">
                        @error('password')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-secondary mb-2">Confirm New Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                               class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Confirm new password">
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-3">
                    <button type="submit" class="w-full accent-bg-hover text-white py-3 px-4 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Reset Password
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
