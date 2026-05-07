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
            <h2 class="text-4xl font-bold mb-2 accent-text">Verify Your Email</h2>
            <p class="text-secondary text-center">
                Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
            </p>
        </div>

        <!-- Verification Form -->
        <div class="glass-effect rounded-2xl p-8 border border-custom shadow-glow">
            @if (session('status') == 'verification-link-sent')
                <div class="rounded-lg bg-green-500/20 border border-green-500/50 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-400">
                                A new verification link has been sent to the email address you provided during registration.
                            </h3>
                        </div>
                    </div>
                </div>
            @endif

            <div class="space-y-6">
                <form method="POST" action="{{ route('verification.send') }}" class="space-y-6">
                    @csrf
                    <button type="submit" class="w-full accent-bg-hover text-white py-3 px-4 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Resend Verification Email
                    </button>
                </form>

                <div class="text-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-secondary hover:text-white text-sm font-medium transition-colors duration-200">
                            Log Out
                        </button>
                    </form>
                </div>
                    <div class="mt-4 text-sm text-green-600">
                        A new verification link has been sent to your email.
                    </div>
                @endif
            </div>
        </div>

        <!-- Additional Info -->
        <div class="mt-8 text-center">
            <p class="text-tertiary text-sm">
                Didn't receive the email? Check your spam folder or try again.
            </p>
        </div>
    </div>
</div>
@endsection
