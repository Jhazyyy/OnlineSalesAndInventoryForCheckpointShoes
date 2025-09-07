<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                Shipment {{ $shipment->shipment_number }}
                            </h2>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $shipment->status_badge_class }}">
                                    {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                                </span>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $shipment->priority_badge_class }}">
                                    {{ ucfirst($shipment->priority) }} Priority
                                </span>
                                @if($shipment->is_overdue)
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                        OVERDUE
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('sales.shipments.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to List
                            </a>
                            @if($shipment->canBeEdited())
                                <a href="{{ route('sales.shipments.edit', $shipment->shipment_id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Shipment
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
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Shipment Details -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Shipment Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Sales Order</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        <a href="{{ route('sales.orders.show', $shipment->salesOrder->order_id) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            {{ $shipment->salesOrder->order_number }}
                                        </a>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Customer</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        <a href="{{ route('sales.customers.show', $shipment->salesOrder->customer->customer_id) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            {{ $shipment->salesOrder->customer->display_name }}
                                        </a>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Carrier</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $shipment->carrier ?: 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Service Type</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $shipment->service_type ?: 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Tracking Number</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $shipment->tracking_number ?: 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Reference Number</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $shipment->reference_number ?: 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Shipment Date</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $shipment->shipment_date ? $shipment->shipment_date->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Expected Delivery</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $shipment->expected_delivery_date ? $shipment->expected_delivery_date->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>
                                @if($shipment->actual_delivery_date)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Actual Delivery</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $shipment->actual_delivery_date->format('M d, Y') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Shipment Items -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Shipment Items</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKU</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Package</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($shipment->items as $item)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->product_name }}</div>
                                                    @if($item->notes)
                                                        <div class="text-xs text-gray-500">{{ $item->notes }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-mono">
                                                    {{ $item->product_sku }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    {{ $item->quantity_shipped }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    ₱{{ number_format($item->unit_price, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    ₱{{ number_format($item->line_total, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    {{ $item->package_number ?: 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status_badge_class }}">
                                                        {{ ucfirst($item->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Addresses -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Addresses</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Shipping Address</h4>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $shipment->shipping_address }}</div>
                                </div>
                                @if($shipment->billing_address)
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Billing Address</h4>
                                        <div class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $shipment->billing_address }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tracking History -->
                    @if($shipment->tracking_history && count($shipment->tracking_history) > 0)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tracking History</h3>
                                <div class="flow-root">
                                    <ul role="list" class="-mb-8">
                                        @foreach(array_reverse($shipment->tracking_history) as $key => $update)
                                            <li>
                                                <div class="relative pb-8">
                                                    @if(!$loop->last)
                                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                    @endif
                                                    <div class="relative flex space-x-3">
                                                        <div>
                                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                            <div>
                                                                <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $update['description'] }}</p>
                                                                @if(isset($update['location']) && $update['location'])
                                                                    <p class="text-sm text-gray-500">{{ $update['location'] }}</p>
                                                                @endif
                                                            </div>
                                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                                {{ \Carbon\Carbon::parse($update['timestamp'])->format('M d, Y H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                @if($shipment->canBeShipped())
                                    <form method="POST" action="{{ route('sales.shipments.ship', $shipment->shipment_id) }}" class="w-full">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                            Ship Now
                                        </button>
                                    </form>
                                @endif

                                @if($shipment->status === 'shipped' && !$shipment->delivered_at)
                                    <form method="POST" action="{{ route('sales.shipments.change-status', $shipment->shipment_id) }}" class="w-full">
                                        @csrf
                                        <input type="hidden" name="status" value="delivered">
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Mark as Delivered
                                        </button>
                                    </form>
                                @endif

                                @if($shipment->canBeCancelled())
                                    <form method="POST" action="{{ route('sales.shipments.change-status', $shipment->shipment_id) }}" class="w-full">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to cancel this shipment?')"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Cancel Shipment
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Recipient Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recipient</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Name</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $shipment->recipient_name }}</p>
                                </div>
                                @if($shipment->recipient_phone)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $shipment->recipient_phone }}</p>
                                    </div>
                                @endif
                                @if($shipment->recipient_email)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $shipment->recipient_email }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Package Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Package Info</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Total Packages</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $shipment->total_packages }}</p>
                                </div>
                                @if($shipment->total_weight)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Total Weight</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $shipment->total_weight }} kg</p>
                                    </div>
                                @endif
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @if($shipment->is_insured)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Insured</span>
                                    @endif
                                    @if($shipment->requires_signature)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Signature Required</span>
                                    @endif
                                    @if($shipment->is_fragile)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Fragile</span>
                                    @endif
                                    @if($shipment->is_perishable)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Perishable</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cost Summary -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cost Summary</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Shipping Cost</span>
                                    <span class="text-sm text-gray-900 dark:text-white">₱{{ number_format($shipment->shipping_cost, 2) }}</span>
                                </div>
                                @if($shipment->insurance_cost > 0)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Insurance</span>
                                        <span class="text-sm text-gray-900 dark:text-white">₱{{ number_format($shipment->insurance_cost, 2) }}</span>
                                    </div>
                                @endif
                                @if($shipment->additional_fees > 0)
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Additional Fees</span>
                                        <span class="text-sm text-gray-900 dark:text-white">₱{{ number_format($shipment->additional_fees, 2) }}</span>
                                    </div>
                                @endif
                                <div class="border-t pt-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">Total</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">₱{{ number_format($shipment->total_shipping_cost, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Special Instructions -->
                    @if($shipment->special_instructions || $shipment->internal_notes)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h3>
                                @if($shipment->special_instructions)
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Special Instructions</label>
                                        <div class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $shipment->special_instructions }}</div>
                                    </div>
                                @endif
                                @if($shipment->internal_notes)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Internal Notes</label>
                                        <div class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $shipment->internal_notes }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
