@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Verify Your Email
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                We've sent a verification link to your email address.
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center">
                <svg class="mx-auto h-16 w-16 text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4A2 2 0 0121 8.93V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 12v-3.07a2 2 0 00-.89-1.664l-7-4A2 2 0 009.89 4.266l-7 4A2 2 0 003 8.93V12"></path>
                </svg>
                
                <h3 class="text-lg font-medium text-gray-900 mb-2">Check your inbox</h3>
                <p class="text-gray-600 mb-6">
                    Click the verification link we sent to <strong>{{ Auth::user()->email }}</strong> to complete your registration.
                </p>
                
                <div class="space-y-4">
                    <button onclick="window.location.reload()" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        I've verified my email
                    </button>
                    
                    <form method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Resend verification email
                        </button>
                    </form>
                </div>
                
                @if(session('resent'))
                    <div class="mt-4 text-sm text-green-600">
                        A new verification link has been sent to your email.
                    </div>
                @endif
            </div>
        </div>
        
        <div class="text-center text-sm text-gray-600">
            Didn't receive the email? Check your spam folder or 
            <a href="{{ route('logout') }}" class="font-medium text-blue-600 hover:text-blue-500">
                try a different email address
            </a>
        </div>
    </div>
</div>
@endsection
