@extends('layouts.admin')

@section('header', 'Users')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex space-x-4">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search users..." class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            
            <select wire:model="status" class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
                <option value="banned">Banned</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Services</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Balance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($users as $user)
                    <tr>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $user->getFullName() }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($user->status === 'active') bg-green-100 text-green-800
                                @elseif($user->status === 'suspended') bg-yellow-100 text-yellow-800
                                @elseif($user->status === 'banned') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $user->orders->count() }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $user->services->where('status', 'active')->count() }} active
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-white">
                            ${{ number_format($user->getCreditBalance(), 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <button wire:click="viewUser({{ $user->id }})" class="text-blue-600 hover:text-blue-800 mr-2">View</button>
                            <button wire:click="toggleStatus({{ $user->id }})" class="text-gray-600 hover:text-gray-800">
                                {{ $user->status === 'active' ? 'Suspend' : 'Activate' }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No users found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal && $editingUser)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">User Details</h3>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-500">Name</p>
                                <p class="font-medium">{{ $editingUser->getFullName() }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($editingUser->status === 'active') bg-green-100 text-green-800
                                    @elseif($editingUser->status === 'suspended') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($editingUser->status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium">{{ $editingUser->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-medium">{{ $editingUser->phone ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($editingUser->address_line1)
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Address</p>
                                <p class="text-sm">{!! nl2br(e($editingUser->getFormattedAddress())) !!}</p>
                            </div>
                        @endif

                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-4 mb-4 bg-gray-50 dark:bg-gray-700 p-4 rounded">
                            <div class="text-center">
                                <p class="text-2xl font-bold">{{ $editingUser->orders->count() }}</p>
                                <p class="text-xs text-gray-500">Orders</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold">{{ $editingUser->services->where('status', 'active')->count() }}</p>
                                <p class="text-xs text-gray-500">Active Services</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold">{{ $editingUser->invoices->where('status', 'unpaid')->count() }}</p>
                                <p class="text-xs text-gray-500">Unpaid Invoices</p>
                            </div>
                        </div>

                        <!-- Recent Orders -->
                        @if($editingUser->orders->count() > 0)
                            <div class="mb-4">
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Recent Orders</h4>
                                <div class="space-y-2">
                                    @foreach($editingUser->orders->take(3) as $order)
                                        <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                            <span>{{ $order->order_number }}</span>
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if($order->status === 'active') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="closeModal" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
