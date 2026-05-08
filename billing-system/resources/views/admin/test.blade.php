@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-900/20 to-blue-900/20 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-white mb-4">Admin Test Page</h1>
            <p class="text-secondary mb-4">This is a test page to check if admin functionality works.</p>
            
            <div class="bg-card rounded-lg p-6 border border-custom">
                <h2 class="text-xl font-semibold text-white mb-4">Debug Information:</h2>
                <div class="text-left space-y-2">
                    <p class="text-white"><strong>User:</strong> {{ auth()->user()->name ?? 'Not authenticated' }}</p>
                    <p class="text-white"><strong>Email:</strong> {{ auth()->user()->email ?? 'Not authenticated' }}</p>
                    <p class="text-white"><strong>Is Admin:</strong> {{ auth()->user()->is_admin ? 'Yes' : 'No' }}</p>
                    <p class="text-white"><strong>User ID:</strong> {{ auth()->user()->id ?? 'Not authenticated' }}</p>
                    <p class="text-white"><strong>Current Route:</strong> {{ request()->route()->getName() }}</p>
                    <p class="text-white"><strong>URL:</strong> {{ request()->url() }}</p>
                </div>
            </div>
            
            <div class="mt-6">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-6 py-3 accent-bg-hover text-white rounded-lg font-medium transition-all duration-200">
                    Go to Admin Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
