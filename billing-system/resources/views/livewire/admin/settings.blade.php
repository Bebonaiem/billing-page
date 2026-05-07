@extends('layouts.admin')

@section('header', 'Settings')

@section('content')
<div>
    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="-mb-px flex space-x-8">
            <button wire:click="setTab('general')" class="{{ $activeTab === 'general' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                General
            </button>
            <button wire:click="setTab('billing')" class="{{ $activeTab === 'billing' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Billing
            </button>
            <button wire:click="setTab('payment')" class="{{ $activeTab === 'payment' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Payment Gateways
            </button>
            <button wire:click="setTab('email')" class="{{ $activeTab === 'email' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Email
            </button>
        </nav>
    </div>

    <!-- General Settings -->
    @if($activeTab === 'general')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">General Settings</h2>
            
            <form wire:submit.prevent="saveGeneralSettings" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Name</label>
                    <input type="text" wire:model="general.company_name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Email</label>
                    <input type="email" wire:model="general.company_email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Support Email</label>
                    <input type="email" wire:model="general.support_email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Default Currency</label>
                    <select wire:model="general.default_currency" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->code }}">{{ $currency->name }} ({{ $currency->symbol }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Default Language</label>
                    <select wire:model="general.default_language" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                        <option value="en">English</option>
                        <option value="es">Spanish</option>
                        <option value="fr">French</option>
                        <option value="de">German</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Timezone</label>
                    <select wire:model="general.timezone" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                        <option value="UTC">UTC</option>
                        <option value="America/New_York">Eastern Time</option>
                        <option value="America/Chicago">Central Time</option>
                        <option value="America/Denver">Mountain Time</option>
                        <option value="America/Los_Angeles">Pacific Time</option>
                        <option value="Europe/London">London</option>
                        <option value="Europe/Paris">Paris</option>
                        <option value="Asia/Tokyo">Tokyo</option>
                    </select>
                </div>
                
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Save General Settings
                </button>
            </form>
        </div>
    @endif

    <!-- Billing Settings -->
    @if($activeTab === 'billing')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Billing Settings</h2>
            
            <form wire:submit.prevent="saveBillingSettings" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Invoice Prefix</label>
                        <input type="text" wire:model="billing.invoice_prefix" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Days</label>
                        <input type="number" wire:model="billing.invoice_due_days" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                    </div>
                </div>
                
                <div class="border-t dark:border-gray-700 pt-4 mt-4">
                    <h3 class="text-md font-medium text-gray-800 dark:text-white mb-3">Late Fees</h3>
                    
                    <label class="flex items-center mb-3">
                        <input type="checkbox" wire:model="billing.late_fee_enabled" class="rounded border-gray-300 text-blue-600 shadow-sm">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Enable Late Fees</span>
                    </label>
                    
                    @if($billing['late_fee_enabled'])
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Late Fee Amount</label>
                                <input type="number" step="0.01" wire:model="billing.late_fee_amount" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apply After (Days)</label>
                                <input type="number" wire:model="billing.late_fee_after_days" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="border-t dark:border-gray-700 pt-4 mt-4">
                    <h3 class="text-md font-medium text-gray-800 dark:text-white mb-3">Auto-Suspend</h3>
                    
                    <label class="flex items-center mb-3">
                        <input type="checkbox" wire:model="billing.auto_suspend_enabled" class="rounded border-gray-300 text-blue-600 shadow-sm">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Enable Auto-Suspend for Unpaid Invoices</span>
                    </label>
                    
                    @if($billing['auto_suspend_enabled'])
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Suspend After (Days Overdue)</label>
                            <input type="number" wire:model="billing.suspend_after_days" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                        </div>
                    @endif
                </div>
                
                <div class="border-t dark:border-gray-700 pt-4 mt-4">
                    <h3 class="text-md font-medium text-gray-800 dark:text-white mb-3">Auto-Terminate</h3>
                    
                    <label class="flex items-center mb-3">
                        <input type="checkbox" wire:model="billing.auto_terminate_enabled" class="rounded border-gray-300 text-blue-600 shadow-sm">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Enable Auto-Terminate for Suspended Services</span>
                    </label>
                    
                    @if($billing['auto_terminate_enabled'])
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Terminate After (Days Suspended)</label>
                            <input type="number" wire:model="billing.terminate_after_days" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                        </div>
                    @endif
                </div>
                
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Save Billing Settings
                </button>
            </form>
        </div>
    @endif

    <!-- Payment Gateways -->
    @if($activeTab === 'payment')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Payment Gateways</h2>
            
            <div class="space-y-4">
                @foreach($gateways as $gateway)
                    <div class="border dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-800 dark:text-white">{{ $gateway->display_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $gateway->description }}</p>
                                <div class="mt-2 flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $gateway->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $gateway->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($gateway->sandbox_mode)
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Sandbox</span>
                                    @endif
                                </div>
                            </div>
                            <button class="text-blue-600 hover:text-blue-800 text-sm">Configure</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Email Settings -->
    @if($activeTab === 'email')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Email Templates</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Customize transactional email content used across the billing system.</p>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($emailTemplates as $template)
                    <div class="border dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start gap-4">
                            <div>
                                <h3 class="font-medium text-gray-800 dark:text-white">{{ $template['name'] }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $template['subject'] }}</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $template['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $template['is_active'] ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($template['from_email'])
                                        <span class="text-xs text-gray-500 dark:text-gray-400">From: {{ $template['from_email'] }}</span>
                                    @endif
                                </div>
                            </div>

                            <button type="button" wire:click="editEmailTemplate({{ $template['id'] }})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Edit
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($editingEmailTemplateId)
                @php $editingTemplate = collect($emailTemplates)->firstWhere('id', $editingEmailTemplateId); @endphp
                @if($editingTemplate)
                    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cancelEmailTemplateEdit"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Edit {{ $editingTemplate['name'] }}</h3>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                                            <input type="text" wire:model.defer="emailTemplates.{{ $editingTemplate['id'] }}.subject" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">HTML Body</label>
                                            <textarea rows="8" wire:model.defer="emailTemplates.{{ $editingTemplate['id'] }}.body_html" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm font-mono text-sm"></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plain Text Body</label>
                                            <textarea rows="6" wire:model.defer="emailTemplates.{{ $editingTemplate['id'] }}.body_text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm font-mono text-sm"></textarea>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Name</label>
                                                <input type="text" wire:model.defer="emailTemplates.{{ $editingTemplate['id'] }}.from_name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Email</label>
                                                <input type="email" wire:model.defer="emailTemplates.{{ $editingTemplate['id'] }}.from_email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                            </div>
                                        </div>

                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" wire:model.defer="emailTemplates.{{ $editingTemplate['id'] }}.is_active" class="rounded border-gray-300 text-blue-600 shadow-sm">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">Template active</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 flex justify-end gap-3">
                                    <button type="button" wire:click="cancelEmailTemplateEdit" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        Cancel
                                    </button>
                                    <button type="button" wire:click="saveEmailTemplate" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                        Save Template
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>

@endsection
