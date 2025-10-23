<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Delivery: {{ $delivery->delivery_number }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">View delivery details</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('purchases.deliveries.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Back to List
                            </a>
                            @if($delivery->canBeEdited())
                                <a href="{{ route('purchases.deliveries.edit', $delivery->delivery_id) }}" 
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
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Number</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $delivery->delivery_number }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Date</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $delivery->delivery_date->format('M d, Y') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $delivery->status_badge_class }}">
                                        {{ ucwords(str_replace('_', ' ', $delivery->status)) }}
                                    </span>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $delivery->priority_badge_class }}">
                                        {{ ucwords($delivery->priority) }}
                                    </span>
                                </div>

                                @if($delivery->purchaseOrder)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Purchase Order</label>
                                    <a href="{{ route('purchases.purchase-orders.show', $delivery->purchase_order_id) }}" 
                                       class="mt-1 text-sm text-blue-600 hover:text-blue-900">
                                        {{ $delivery->purchaseOrder->order_number }}
                                    </a>
                                </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $delivery->supplier->supplier_name ?? $delivery->supplier->name ?? 'N/A' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Carrier</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $delivery->carrier ?? 'N/A' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tracking Number</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $delivery->tracking_number ?? 'N/A' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scheduled Delivery</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $delivery->scheduled_delivery_date ? $delivery->scheduled_delivery_date->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Actual Delivery</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $delivery->actual_delivery_date ? $delivery->actual_delivery_date->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recipient</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $delivery->recipient_name ?? 'N/A' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Phone</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $delivery->recipient_phone ?? 'N/A' }}</p>
                                </div>
                            </div>

                            @if($delivery->delivery_address)
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Address</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $delivery->delivery_address }}</p>
                            </div>
                            @endif

                            @if($delivery->delivery_notes)
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Notes</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $delivery->delivery_notes }}</p>
                            </div>
                            @endif

                            @if($delivery->damage_notes)
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Damage Notes</label>
                                <p class="mt-1 text-sm text-red-600">{{ $delivery->damage_notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Delivery Items</h3>
                            
                            @if($delivery->items->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Expected</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Delivered</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Damaged</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit Price</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condition</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($delivery->items as $item)
                                                <tr>
                                                    <td class="px-6 py-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $item->product->product_name ?? 'N/A' }}
                                                        </div>
                                                        @if($item->product && $item->product->sku)
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $item->product->sku }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        {{ number_format($item->quantity_expected) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        {{ number_format($item->quantity_delivered) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                                        {{ number_format($item->quantity_damaged ?? 0) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        ₱{{ number_format($item->unit_price, 2) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->condition_badge_class }}">
                                                            {{ ucwords($item->condition) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                        ₱{{ number_format($item->line_total, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                                    Total:
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                                    ₱{{ number_format($delivery->total_amount_delivered, 2) }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">No items in this delivery.</p>
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
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Items:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($delivery->total_quantity_delivered) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Packages:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $delivery->total_packages }}</span>
                                </div>
                                @if($delivery->total_weight)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Weight:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($delivery->total_weight, 2) }} kg</span>
                                </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Shipping Cost:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($delivery->shipping_cost, 2) }}</span>
                                </div>
                                @if($delivery->insurance_cost > 0)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Insurance:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($delivery->insurance_cost, 2) }}</span>
                                </div>
                                @endif
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">Total Amount:</span>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">${{ number_format($delivery->total_amount_delivered, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Change -->
                    @if($delivery->status !== 'delivered' && $delivery->status !== 'cancelled')
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Change Status</h3>
                            
                            <form action="{{ route('purchases.deliveries.change-status', $delivery->delivery_id) }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Status</label>
                                        <select id="status" name="status" required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="scheduled">Scheduled</option>
                                            <option value="in_transit">In Transit</option>
                                            <option value="out_for_delivery">Out for Delivery</option>
                                            <option value="delivered">Delivered</option>
                                            <option value="delayed">Delayed</option>
                                            <option value="failed">Failed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes (Optional)</label>
                                        <textarea id="notes" name="notes" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                                    </div>

                                    <button type="submit" 
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Update Status
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
