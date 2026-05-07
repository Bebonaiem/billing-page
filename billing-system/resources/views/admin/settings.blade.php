@extends('layouts.admin')

@section('title', 'Settings')

@section('header', 'System Settings')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Settings Tabs -->
    <div class="glass-effect rounded-2xl border border-custom shadow-glow">
        <div class="border-b border-custom">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button class="accent-bg text-white px-1 py-4 border-b-2 border-transparent font-medium text-sm rounded-t-lg transition-colors duration-200">
                    General
                </button>
                <button class="bg-card/50 text-secondary hover:text-white px-1 py-4 border-b-2 border-transparent font-medium text-sm rounded-t-lg transition-colors duration-200">
                    Email
                </button>
                <button class="bg-card/50 text-secondary hover:text-white px-1 py-4 border-b-2 border-transparent font-medium text-sm rounded-t-lg transition-colors duration-200">
                    Payment
                </button>
                <button class="bg-card/50 text-secondary hover:text-white px-1 py-4 border-b-2 border-transparent font-medium text-sm rounded-t-lg transition-colors duration-200">
                    Security
                </button>
            </nav>
        </div>

        <!-- General Settings -->
        <div class="p-6">
            <h3 class="text-lg font-semibold accent-text mb-6">General Settings</h3>
            
            <div class="space-y-6">
                <!-- Site Name -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Site Name</label>
                    <input type="text" class="glass-effect border border-custom bg-card/50 text-white px-4 py-2 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent" value="BillingHub">
                </div>

                <!-- Site URL -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Site URL</label>
                    <input type="url" class="glass-effect border border-custom bg-card/50 text-white px-4 py-2 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent" value="https://billinghub.com">
                </div>

                <!-- Admin Email -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Admin Email</label>
                    <input type="email" class="glass-effect border border-custom bg-card/50 text-white px-4 py-2 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent" value="admin@billinghub.com">
                </div>

                <!-- Timezone -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Default Timezone</label>
                    <select class="glass-effect border border-custom bg-card/50 text-white px-4 py-2 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        <option>UTC</option>
                        <option>America/New_York</option>
                        <option>Europe/London</option>
                        <option>Asia/Tokyo</option>
                    </select>
                </div>

                <!-- Currency -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Default Currency</label>
                    <select class="glass-effect border border-custom bg-card/50 text-white px-4 py-2 rounded-lg w-full focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent">
                        <option>USD - US Dollar</option>
                        <option>EUR - Euro</option>
                        <option>GBP - British Pound</option>
                        <option>CAD - Canadian Dollar</option>
                    </select>
                </div>

                <!-- Maintenance Mode -->
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-medium text-white">Maintenance Mode</label>
                        <p class="text-sm text-secondary">Temporarily disable the site for maintenance</p>
                    </div>
                    <button class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-card transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2">
                        <span class="translate-x-0 inline-block h-5 w-5 transform rounded-full bg-white transition-transform duration-200 ease-in-out"></span>
                    </button>
                </div>
            </div>

            <!-- Save Button -->
            <div class="mt-8 flex justify-end">
                <button class="accent-bg-hover text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Save Settings
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
