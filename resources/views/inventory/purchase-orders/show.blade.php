<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Purchase Order {{ $order->order_number }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">View purchase order details and manage status</p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            <a href="{{ route('inventory.purchase-orders.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Orders
                            </a>
                            
                            @if($order->canBeEdited())
                                <a href="{{ route('inventory.purchase-orders.edit', $order->order_id) }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
                <!-- Main Order Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Details -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Number</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $order->order_number }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Date</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->order_date->format('M d, Y') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expected Date</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $order->expected_date ? $order->expected_date->format('M d, Y') : 'Not specified' }}
                                        @if($order->is_overdue)
                                            <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                OVERDUE
                                            </span>
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Received Date</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $order->received_date ? $order->received_date->format('M d, Y') : 'Not received' }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                    <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->priority_badge_class }}">
                                        {{ ucfirst($order->priority) }}
                                    </span>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Method</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $order->payment_method ? ucwords(str_replace('_', ' ', $order->payment_method)) : 'Not specified' }}
                                    </p>
                                </div>

                                @if($order->reference_number)
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reference Number</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->reference_number }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Supplier Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Supplier Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier Name</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->supplier->supplier_name ?? $order->supplier->name ?? 'N/A' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->supplier->email ?? 'N/A' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->supplier->phone ?? 'N/A' }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->supplier->full_address ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Items</h3>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ordered</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Received</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit Price</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Discount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Line Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ $item->product->sku ?? 'N/A' }}</div>
                                                    @if($item->notes)
                                                        <div class="text-xs text-gray-500 italic mt-1">{{ $item->notes }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    {{ $item->quantity_ordered }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    <div>{{ $item->quantity_received }}</div>
                                                    @if($item->quantity_received > 0 && $item->quantity_received < $item->quantity_ordered)
                                                        <div class="text-xs text-orange-600">
                                                            {{ $item->received_percentage }}% received
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    ₱{{ number_format($item->unit_price, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    ₱{{ number_format($item->discount_amount, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    ₱{{ number_format($item->line_total, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Addresses and Notes -->
                    @if($order->delivery_address || $order->billing_address || $order->notes || $order->internal_notes)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Additional Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if($order->delivery_address)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Address</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $order->delivery_address }}</p>
                                </div>
                                @endif

                                @if($order->billing_address)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Billing Address</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $order->billing_address }}</p>
                                </div>
                                @endif

                                @if($order->notes)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Public Notes</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $order->notes }}</p>
                                </div>
                                @endif

                                @if($order->internal_notes)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Internal Notes</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $order->internal_notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Summary</h3>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                                    <span class="text-gray-900 dark:text-white">₱{{ number_format($order->subtotal, 2) }}</span>
                                </div>

                                @if($order->tax_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Tax:</span>
                                    <span class="text-gray-900 dark:text-white">₱{{ number_format($order->tax_amount, 2) }}</span>
                                </div>
                                @endif

                                @if($order->shipping_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Shipping:</span>
                                    <span class="text-gray-900 dark:text-white">₱{{ number_format($order->shipping_amount, 2) }}</span>
                                </div>
                                @endif

                                @if($order->discount_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                                    <span class="text-red-600">₱{{ number_format($order->discount_amount, 2) }}</span>
                                </div>
                                @endif

                                <div class="border-t pt-3">
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-gray-900 dark:text-white">Total:</span>
                                        <span class="text-gray-900 dark:text-white">₱{{ number_format($order->total_amount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Status Information</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Status</label>
                                    <div class="mt-1 flex items-center space-x-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status_badge_class }}">
                                            {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Status</label>
                                    <div class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->payment_status_badge_class }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Progress</label>
                                    <div class="mt-1">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $order->total_products }} items • {{ $order->total_quantity }} total quantity
                                        </div>
                                        @if($order->items->sum('quantity_received') > 0)
                                            <div class="mt-2">
                                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                    <span>Receiving Progress</span>
                                                    <span>
                                                        {{ $order->items->sum('quantity_ordered') > 0 ? round(($order->items->sum('quantity_received') / $order->items->sum('quantity_ordered')) * 100, 1) : 0 }}%
                                                    </span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                    <div class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                                                         style="width: {{ $order->items->sum('quantity_ordered') > 0 ? round(($order->items->sum('quantity_received') / $order->items->sum('quantity_ordered')) * 100, 1) : 0 }}%"></div>
                                                </div>
                                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    <span>Received: {{ $order->items->sum('quantity_received') }}</span>
                                                    <span>Pending: {{ $order->items->sum('pending_quantity') }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Receiving History -->
                    @if($order->internal_notes && (str_contains($order->internal_notes, 'Received by') || in_array($order->status, ['partial_received', 'received'])))
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Receiving History</h3>
                                
                                @if($order->internal_notes)
                                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                                        <pre class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-mono">{{ trim($order->internal_notes) }}</pre>
                                    </div>
                                @endif
                                
                                @if($order->received_date)
                                    <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                        <div class="flex items-center text-sm">
                                            <svg class="w-4 h-4 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span class="text-green-800 dark:text-green-200 font-medium">Order fully received on:</span>
                                            <span class="ml-2 text-green-700 dark:text-green-300 font-semibold">{{ $order->received_date->format('M d, Y \a\t h:i A') }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Actions</h3>
                            
                            <div class="space-y-3">
                                <!-- Status Change -->
                                @if(count((new \App\Services\PurchaseOrderService())->getValidStatusTransitions($order->status)) > 0)
                                    <form method="POST" action="{{ route('inventory.purchase-orders.change-status', $order->order_id) }}" class="inline">
                                        @csrf
                                        <div class="flex space-x-2">
                                            <select name="status" 
                                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                                @foreach((new \App\Services\PurchaseOrderService())->getValidStatusTransitions($order->status) as $status)
                                                    <option value="{{ $status }}">{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" 
                                                class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                Update
                                            </button>
                                        </div>
                                    </form>
                                @endif

                                <!-- Receive Items -->
                                @if($order->canReceiveItems())
                                    <div class="pt-3 border-t">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Receive Items</h4>
                                        
                                        <!-- Receive Statistics -->
                                        <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                            <div class="grid grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <span class="text-gray-600 dark:text-gray-400">Total Items:</span>
                                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $order->items->count() }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600 dark:text-gray-400">Pending:</span>
                                                    <span class="font-semibold text-orange-600 dark:text-orange-400">{{ $order->items->sum('pending_quantity') }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600 dark:text-gray-400">Received:</span>
                                                    <span class="font-semibold text-green-600 dark:text-green-400">{{ $order->items->sum('quantity_received') }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600 dark:text-gray-400">Progress:</span>
                                                    <span class="font-semibold text-blue-600 dark:text-blue-400">
                                                        {{ $order->items->sum('quantity_ordered') > 0 ? round(($order->items->sum('quantity_received') / $order->items->sum('quantity_ordered')) * 100, 1) : 0 }}%
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <form method="POST" action="{{ route('inventory.purchase-orders.receive-items', $order->order_id) }}" id="receiveItemsForm">
                                            @csrf
                                            
                                            <!-- Bulk Actions -->
                                            <div class="mb-4 flex flex-wrap gap-2">
                                                <button type="button" onclick="receiveAllItems()" 
                                                    class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Receive All
                                                </button>
                                                <button type="button" onclick="clearAllItems()" 
                                                    class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Clear All
                                                </button>
                                            </div>
                                            
                                            <div class="space-y-3 max-h-64 overflow-y-auto">
                                                @foreach($order->items as $index => $item)
                                                    @if($item->pending_quantity > 0)
                                                        <div class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                                            <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                                            
                                                            <!-- Product Header -->
                                                            <div class="flex items-start justify-between mb-2">
                                                                <div class="flex-1">
                                                                    <h5 class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</h5>
                                                                    <p class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $item->product->sku ?? 'N/A' }}</p>
                                                                </div>
                                                                <div class="text-right">
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400">Unit Price:</div>
                                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">₱{{ number_format($item->unit_price, 2) }}</div>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Quantities and Input -->
                                                            <div class="grid grid-cols-4 gap-3 items-end">
                                                                <div class="text-center">
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Ordered</div>
                                                                    <div class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $item->quantity_ordered }}</div>
                                                                </div>
                                                                <div class="text-center">
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Received</div>
                                                                    <div class="text-sm font-semibold text-green-600 dark:text-green-400">{{ $item->quantity_received }}</div>
                                                                </div>
                                                                <div class="text-center">
                                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Pending</div>
                                                                    <div class="text-sm font-semibold text-orange-600 dark:text-orange-400">{{ $item->pending_quantity }}</div>
                                                                </div>
                                                                <div>
                                                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Receive Now</label>
                                                                    <input type="number" name="items[{{ $index }}][quantity_received]" 
                                                                        min="0" max="{{ $item->pending_quantity }}" value="0"
                                                                        data-pending="{{ $item->pending_quantity }}"
                                                                        class="w-full text-center rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm receive-input">
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Progress Bar -->
                                                            <div class="mt-3">
                                                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                                    <span>Progress</span>
                                                                    <span>{{ $item->received_percentage }}%</span>
                                                                </div>
                                                                <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $item->received_percentage }}%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            
                                            <!-- Receiving Notes -->
                                            <div class="mt-4">
                                                <label for="receiving_notes" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Receiving Notes (Optional)</label>
                                                <textarea id="receiving_notes" name="receiving_notes" rows="2" 
                                                    placeholder="Add any notes about this receiving session..."
                                                    class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"></textarea>
                                            </div>
                                            
                                            <div class="mt-4 flex space-x-2">
                                                <button type="submit" 
                                                    class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                                    id="receiveButton" disabled>
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Receive Selected Items
                                                </button>
                                            </div>
                                        </form>
                                        
                                        <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const receiveInputs = document.querySelectorAll('.receive-input');
                                            const receiveButton = document.getElementById('receiveButton');
                                            
                                            // Check if any items are selected for receiving
                                            function updateReceiveButton() {
                                                let hasItems = false;
                                                receiveInputs.forEach(input => {
                                                    if (parseInt(input.value) > 0) {
                                                        hasItems = true;
                                                    }
                                                });
                                                receiveButton.disabled = !hasItems;
                                                receiveButton.textContent = hasItems ? 'Receive Selected Items' : 'No Items Selected';
                                            }
                                            
                                            // Add event listeners to all receive inputs
                                            receiveInputs.forEach(input => {
                                                input.addEventListener('input', updateReceiveButton);
                                                input.addEventListener('change', function() {
                                                    const max = parseInt(this.getAttribute('max'));
                                                    const value = parseInt(this.value);
                                                    if (value > max) {
                                                        this.value = max;
                                                    }
                                                    if (value < 0) {
                                                        this.value = 0;
                                                    }
                                                    updateReceiveButton();
                                                });
                                            });
                                            
                                            // Initial button state update
                                            updateReceiveButton();
                                        });
                                        
                                        function receiveAllItems() {
                                            document.querySelectorAll('.receive-input').forEach(input => {
                                                input.value = input.getAttribute('data-pending');
                                            });
                                            document.getElementById('receiveButton').disabled = false;
                                            document.getElementById('receiveButton').innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Receive Selected Items';
                                        }
                                        
                                        function clearAllItems() {
                                            document.querySelectorAll('.receive-input').forEach(input => {
                                                input.value = 0;
                                            });
                                            document.getElementById('receiveButton').disabled = true;
                                            document.getElementById('receiveButton').textContent = 'No Items Selected';
                                        }
                                        </script>
                                    </div>
                                @endif

                                <!-- Delete Order -->
                                @if($order->canBeCancelled())
                                    <div class="pt-3 border-t">
                                        <form method="POST" action="{{ route('inventory.purchase-orders.destroy', $order->order_id) }}" 
                                              onsubmit="return confirm('Are you sure you want to delete this purchase order?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                Delete Order
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
