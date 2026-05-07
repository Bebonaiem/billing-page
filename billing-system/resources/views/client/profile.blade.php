@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Profile Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-2 bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">
                Profile Settings
            </h1>
            <p class="text-secondary text-lg">Manage your account information and preferences</p>
        </div>

        <!-- Profile Overview -->
        <div class="glass-effect rounded-2xl border border-custom shadow-glow mb-8">
            <div class="px-6 py-5 border-b border-custom">
                <h3 class="text-xl font-semibold accent-text">Account Overview</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center space-x-6">
                    <div class="w-24 h-24 accent-bg rounded-full flex items-center justify-center shadow-lg">
                        <img class="w-20 h-20 rounded-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&color=3B82F6&background=1E293B" alt="{{ Auth::user()->name }}">
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold">{{ Auth::user()->name }}</h4>
                        <p class="text-secondary">{{ Auth::user()->email }}</p>
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium success-bg text-white">
                                {{ Auth::user()->is_admin ? 'Administrator' : 'Client' }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium accent-bg text-white">
                                {{ Auth::user()->status ?? 'Active' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="glass-effect rounded-2xl border border-custom shadow-glow mb-8">
            <div class="px-6 py-5 border-b border-custom">
                <h3 class="text-xl font-semibold accent-text">Personal Information</h3>
                <p class="text-secondary text-sm mt-1">Update your personal details and contact information</p>
            </div>
            <div class="p-6">
                <form class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-secondary mb-2">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="{{ Auth::user()->first_name }}" 
                                   class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-secondary mb-2">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="{{ Auth::user()->last_name }}" 
                                   class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-secondary mb-2">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" 
                               class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-secondary mb-2">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ Auth::user()->phone ?? '' }}" 
                               class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter your phone number">
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium text-secondary mb-2">Address</label>
                        <textarea id="address" name="address" rows="3" 
                                  class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                  placeholder="Enter your address">{{ Auth::user()->address ?? '' }}</textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="accent-bg-hover text-white py-3 px-6 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="glass-effect rounded-2xl border border-custom shadow-glow mb-8">
            <div class="px-6 py-5 border-b border-custom">
                <h3 class="text-xl font-semibold accent-text">Security Settings</h3>
                <p class="text-secondary text-sm mt-1">Update your password to keep your account secure</p>
            </div>
            <div class="p-6">
                <form class="space-y-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-secondary mb-2">Current Password</label>
                        <input type="password" id="current_password" name="current_password" 
                               class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter current password">
                    </div>
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-secondary mb-2">New Password</label>
                        <input type="password" id="new_password" name="new_password" 
                               class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter new password">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-secondary mb-2">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Confirm new password">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="accent-bg-hover text-white py-3 px-6 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preferences -->
        <div class="glass-effect rounded-2xl border border-custom shadow-glow">
            <div class="px-6 py-5 border-b border-custom">
                <h3 class="text-xl font-semibold accent-text">Preferences</h3>
                <p class="text-secondary text-sm mt-1">Customize your experience</p>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium">Email Notifications</h4>
                            <p class="text-sm text-secondary">Receive email updates about your account</p>
                        </div>
                        <button class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent accent-bg transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span class="translate-x-5 inline-block h-5 w-5 transform rounded-full bg-white transition-transform duration-200 ease-in-out"></span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium">Two-Factor Authentication</h4>
                            <p class="text-sm text-secondary">Add an extra layer of security to your account</p>
                        </div>
                        <button class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-600 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span class="translate-x-0 inline-block h-5 w-5 transform rounded-full bg-white transition-transform duration-200 ease-in-out"></span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium">Marketing Emails</h4>
                            <p class="text-sm text-secondary">Receive emails about new features and offers</p>
                        </div>
                        <button class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-gray-600 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span class="translate-x-0 inline-block h-5 w-5 transform rounded-full bg-white transition-transform duration-200 ease-in-out"></span>
                        </button>
                    </div>
                </div>
            </div>
</div>
@endsection
