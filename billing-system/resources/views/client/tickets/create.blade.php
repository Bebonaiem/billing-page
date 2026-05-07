@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-2 bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">
                Create Support Ticket
            </h1>
            <p class="text-secondary text-lg">Get help from our support team by creating a new ticket</p>
        </div>

        <div class="glass-effect rounded-2xl border border-custom shadow-glow">
            <form method="POST" action="{{ route('client.tickets.store') }}" class="space-y-6 px-6 py-6">
                @csrf
                
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

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="subject" class="block text-sm font-medium text-secondary mb-2">Subject</label>
                        <input type="text" name="subject" id="subject" required
                               value="{{ old('subject') }}"
                               class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Brief description of your issue">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="department" class="block text-sm font-medium text-secondary mb-2">Department</label>
                        <select name="department" id="department" required
                                class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="">Select a department</option>
                            <option value="billing">Billing</option>
                            <option value="technical">Technical Support</option>
                            <option value="sales">Sales</option>
                            <option value="general">General Inquiry</option>
                        </select>
                        @error('department')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-secondary mb-2">Priority</label>
                    <select name="priority" id="priority" required
                            class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        <option value="">Select priority level</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-secondary mb-2">Message</label>
                    <textarea name="message" id="message" rows="6" required
                              class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                              placeholder="Please describe your issue in detail...">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('client.tickets') }}" class="bg-card text-secondary px-6 py-3 rounded-xl font-semibold hover:bg-card/80 transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="accent-bg-hover text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Create Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
