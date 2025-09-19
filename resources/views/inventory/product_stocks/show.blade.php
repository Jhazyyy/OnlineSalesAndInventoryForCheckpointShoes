<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Stock Movement Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Movement #{{ $stock->movement_id }}</h3>
                        <div class="flex space-x-2">
                            @if($stock->status === 'pending')
                                <form action="{{ route('inventory.product_stocks.confirm', $stock) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                        Confirm Movement
                                    </button>
                                </form>
                                <a href="{{ route('inventory.product_stocks.edit', $stock) }}"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    Edit
                                </a>
                            @endif
                            <a href="{{ route('inventory.product_stocks.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to List
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-md font-semibold mb-3">Basic Information</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="font-medium">Movement Date:</span>
                                    <span>{{ $stock->movement_date ? $stock->movement_date->format('M d, Y H:i') : 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Movement Type:</span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($stock->movement_type === 'adjustment') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($stock->movement_type === 'sale') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($stock->movement_type === 'purchase') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                        @elseif($stock->movement_type === 'return') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($stock->movement_type === 'transfer') bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200
                                        @elseif($stock->movement_type === 'waste') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                        {{ $stock->movement_type_label }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Status:</span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($stock->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($stock->status === 'confirmed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                        {{ ucfirst($stock->status) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Created By:</span>
                                    <span>{{ $stock->user->name ?? 'System' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Product Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-md font-semibold mb-3">Product Information</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="font-medium">Product:</span>
                                    <span>{{ $stock->product->product_name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Brand:</span>
                                    <span>{{ $stock->product->product_brand ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">SKU:</span>
                                    <span>{{ $stock->product->product_code ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-md font-semibold mb-3">Quantity Information</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="font-medium">Quantity Before:</span>
                                    <span>{{ number_format($stock->quantity_before) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Quantity Change:</span>
                                    <span
                                        class="{{ $stock->quantity_change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $stock->quantity_change >= 0 ? '+' : '' }}{{ number_format($stock->quantity_change) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Quantity After:</span>
                                    <span class="font-semibold">{{ number_format($stock->quantity_after) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-md font-semibold mb-3">Financial Information</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="font-medium">Unit Cost:</span>
                                    <span>{{ $stock->unit_cost ? '$' . number_format($stock->unit_cost, 2) : 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-medium">Total Value:</span>
                                    <span>{{ $stock->total_value ? '$' . number_format($stock->total_value, 2) : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="mt-6 grid grid-cols-1 gap-6">
                        @if($stock->reason)
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h4 class="text-md font-semibold mb-2">Reason</h4>
                                <p class="text-gray-700 dark:text-gray-300">{{ $stock->reason }}</p>
                            </div>
                        @endif

                        @if($stock->notes)
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h4 class="text-md font-semibold mb-2">Notes</h4>
                                <p class="text-gray-700 dark:text-gray-300">{{ $stock->notes }}</p>
                            </div>
                        @endif

                        @if($stock->location)
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h4 class="text-md font-semibold mb-2">Location</h4>
                                <p class="text-gray-700 dark:text-gray-300">{{ $stock->location }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Timestamps -->
                    <div class="mt-6 text-sm text-gray-500 dark:text-gray-400 border-t pt-4">
                        <div class="flex justify-between">
                            <span>Created: {{ $stock->created_at ? $stock->created_at->format('M d, Y H:i') : 'N/A' }}</span>
                            <span>Updated: {{ $stock->updated_at ? $stock->updated_at->format('M d, Y H:i') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
