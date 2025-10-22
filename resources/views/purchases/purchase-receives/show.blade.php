<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Purchase Receive: {{ $receive->receive_number }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">View purchase receive details</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('purchases.purchase-receives.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Back to List
                            </a>
                            @if($receive->canBeEdited())
                                <a href="{{ route('purchases.purchase-receives.edit', $receive->receive_id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Receive Number</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $receive->receive_number }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Receive Date</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $receive->receive_date->format('M d, Y') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $receive->status_badge_class }}">
                                        {{ ucwords(str_replace('_', ' ', $receive->status)) }}
                                    </span>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Receiver Name</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $receive->receiver_name ?? 'N/A' }}</p>
                                </div>

                                @if($receive->purchaseOrder)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Purchase Order</label>
                                    <a href="{{ route('purchases.purchase-orders.show', $receive->purchase_order_id) }}" 
                                       class="mt-1 text-sm text-blue-600 hover:text-blue-900">
                                        {{ $receive->purchaseOrder->order_number }}
                                    </a>
                                </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $receive->supplier->supplier_name ?? $receive->supplier->name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            @if($receive->receiving_notes)
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Receiving Notes</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $receive->receiving_notes }}</p>
                            </div>
                            @endif

                            @if($receive->damage_notes)
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Damage Notes</label>
                                <p class="mt-1 text-sm text-red-600">{{ $receive->damage_notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Received Items</h3>
                            
                            @if($receive->items->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Expected</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Received</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Damaged</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit Price</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condition</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($receive->items as $item)
                                                <tr>
                                                    <td class="px-6 py-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $item->product->product_name ?? 'N/A' }}
                                                        </div>
                                                        @if($item->product)
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 space-y-0.5">
                                                                @if($item->product->sku)
                                                                    <div>SKU: {{ $item->product->sku }}</div>
                                                                @endif
                                                                @if($item->product->brand)
                                                                    <div>Brand: {{ $item->product->brand->brand_name }}</div>
                                                                @endif
                                                                @if($item->product->category)
                                                                    <div>Category: {{ $item->product->category->category_name }}</div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                        @if($item->item_notes)
                                                            <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                                                <strong>Notes:</strong> {{ $item->item_notes }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        {{ number_format($item->quantity_expected) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        {{ number_format($item->quantity_received) }}
                                                        @if($item->quantity_expected > 0)
                                                            <div class="text-xs text-gray-500">
                                                                {{ number_format($item->receive_percentage, 1) }}%
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        {{ number_format($item->quantity_damaged) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        ₱{{ number_format($item->unit_price, 2) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->condition_badge_class }}">
                                                            {{ ucfirst($item->condition) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        ₱{{ number_format($item->total_amount, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">No items received yet.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Summary Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Summary</h3>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Expected Quantity:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($receive->total_quantity_expected) }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Received Quantity:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($receive->total_quantity_received) }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Expected Value:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">₱{{ number_format($receive->total_amount_expected, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Received Value:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">₱{{ number_format($receive->total_amount_received, 2) }}</span>
                                </div>
                                
                                <hr class="border-gray-200 dark:border-gray-600">
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Completion:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $receive->completion_percentage }}%</span>
                                </div>
                                
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $receive->completion_percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Card -->
                    @if($receive->canBeEdited() || $receive->canBeCancelled())
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Actions</h3>
                            
                            <div class="space-y-3">
                                @if($receive->canBeEdited())
                                    <a href="{{ route('purchases.purchase-receives.edit', $receive->receive_id) }}" 
                                       class="w-full inline-flex justify-center items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit Receive
                                    </a>
                                @endif
                                
                                @if($receive->canBeCancelled())
                                    <form action="{{ route('purchases.purchase-receives.destroy', $receive->receive_id) }}" method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to delete this purchase receive? This will reverse any inventory changes.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete Receive
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>