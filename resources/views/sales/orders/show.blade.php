<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Sales Order {{ $order->order_number }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">View and manage sales order details</p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            @if($order->canBeEdited())
                                <a href="{{ route('sales.orders.edit', $order->order_id) }}" 
                                    class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Order
                                </a>
                            @endif
                            <a href="{{ route('sales.orders.index') }}" 
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Orders
                            </a>
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

            <!-- Order Status Actions -->
            @if($order->canBeConfirmed() || $order->status === 'confirmed' || $order->canBeCancelled())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Actions</h3>
                        <div class="flex flex-wrap gap-3">
                            @if($order->canBeConfirmed())
                                <form method="POST" action="{{ route('sales.orders.change-status', $order->order_id) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Confirm Order
                                    </button>
                                </form>
                            @endif

                            @if($order->status === 'confirmed')
                                <form method="POST" action="{{ route('sales.orders.fulfill', $order->order_id) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Start Fulfillment
                                    </button>
                                </form>
                            @endif

                            @if($order->canBeShipped())
                                <form method="POST" action="{{ route('sales.orders.change-status', $order->order_id) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="shipped">
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Mark as Shipped
                                    </button>
                                </form>
                            @endif

                            @if($order->status === 'shipped')
                                <form method="POST" action="{{ route('sales.orders.change-status', $order->order_id) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="delivered">
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 focus:bg-teal-700 active:bg-teal-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Mark as Delivered
                                    </button>
                                </form>
                            @endif

                            @if($order->canBeCancelled())
                                <form method="POST" action="{{ route('sales.orders.change-status', $order->order_id) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" 
                                            onclick="return confirm('Are you sure you want to cancel this order?')"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Cancel Order
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Order Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Details -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Order Number</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->order_number }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Order Date</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->order_date->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Required Date</label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $order->required_date ? $order->required_date->format('M d, Y') : 'Not specified' }}
                                        @if($order->is_overdue)
                                            <span class="ml-2 px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">OVERDUE</span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Shipped Date</label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $order->shipped_date ? $order->shipped_date->format('M d, Y') : 'Not shipped' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status_badge_class }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Priority</label>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->priority_badge_class }}">
                                        {{ ucfirst($order->priority) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method</label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $order->payment_method ? ucfirst(str_replace('_', ' ', $order->payment_method)) : 'Not specified' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Status</label>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->payment_status_badge_class }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Tracking Number</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->tracking_number ?: 'Not available' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Shipping Carrier</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->shipping_carrier ?: 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Items</h3>
                            
                            <div class="overflow-x-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
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
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ $item->product->sku }}</div>
                                                    @if($item->hasStockShortage())
                                                        <div class="text-xs text-red-600">Stock shortage: {{ $item->stock_shortage }} units</div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    {{ number_format($item->quantity) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    ₱{{ number_format($item->unit_price, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    ₱{{ number_format($item->discount_amount, 2) }}
                                                    @if($item->discount_percentage > 0)
                                                        <span class="text-gray-500">({{ number_format($item->discount_percentage, 1) }}%)</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                    ₱{{ number_format($item->line_total, 2) }}
                                                </td>
                                            </tr>
                                            @if($item->notes)
                                                <tr>
                                                    <td colspan="5" class="px-6 py-2 text-sm text-gray-600 dark:text-gray-400">
                                                        <strong>Note:</strong> {{ $item->notes }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Addresses -->
                    @if($order->shipping_address || $order->billing_address)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Addresses</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @if($order->shipping_address)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Shipping Address</label>
                                            <div class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $order->shipping_address }}</div>
                                        </div>
                                    @endif
                                    @if($order->billing_address)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Billing Address</label>
                                            <div class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $order->billing_address }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Notes -->
                    @if($order->notes || $order->internal_notes)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Notes</h3>
                                
                                @if($order->notes)
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Customer Notes</label>
                                        <div class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $order->notes }}</div>
                                    </div>
                                @endif
                                @if($order->internal_notes)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Internal Notes</label>
                                        <div class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $order->internal_notes }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Customer Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Customer Information</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Customer</label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        <a href="{{ route('sales.customers.show', $order->customer->customer_id) }}" class="text-blue-600 hover:text-blue-900">
                                            {{ $order->customer->display_name }}
                                        </a>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->customer->email }}</p>
                                </div>
                                @if($order->customer->phone)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $order->customer->phone }}</p>
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Customer Type</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ ucfirst($order->customer->customer_type) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Summary</h3>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Subtotal:</span>
                                    <span class="text-gray-900 dark:text-white">₱{{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                @if($order->tax_amount > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Tax:</span>
                                        <span class="text-gray-900 dark:text-white">₱{{ number_format($order->tax_amount, 2) }}</span>
                                    </div>
                                @endif
                                @if($order->shipping_amount > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Shipping:</span>
                                        <span class="text-gray-900 dark:text-white">₱{{ number_format($order->shipping_amount, 2) }}</span>
                                    </div>
                                @endif
                                @if($order->discount_amount > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Discount:</span>
                                        <span class="text-red-600">-₱{{ number_format($order->discount_amount, 2) }}</span>
                                    </div>
                                @endif
                                <hr class="border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between text-base font-medium">
                                    <span class="text-gray-900 dark:text-white">Total:</span>
                                    <span class="text-gray-900 dark:text-white">₱{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Stats -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Statistics</h3>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Total Products:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $order->total_products }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Total Quantity:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $order->total_quantity }}</span>
                                </div>
                                @if($order->days_until_required !== null)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Days until due:</span>
                                        <span class="text-gray-900 dark:text-white {{ $order->days_until_required < 0 ? 'text-red-600' : '' }}">
                                            {{ $order->days_until_required }}
                                        </span>
                                    </div>
                                @endif
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Created:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $order->created_at->format('M d, Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Last Updated:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $order->updated_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
