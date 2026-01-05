<x-app-layout>
    <div class="py-2">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Purchase Orders</h2>
                            <p class="text-gray-600 dark:text-gray-400">Manage your purchase orders and track deliveries</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <button type="button" onclick="openCreateModal()" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Create Purchase Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <form method="GET" action="{{ route('purchases.purchase-orders.index') }}" class="space-y-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                                       placeholder="Order number, supplier..." 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select id="status" name="status" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <!-- Priority -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                <select id="priority" name="priority" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Priorities</option>
                                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>

                            <!-- Payment Status -->
                            {{-- <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment</label>
                                <select id="payment_status" name="payment_status" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Payments</option>
                                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div> --}}

                            <!-- Start Date -->
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Date</label>
                                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- End Date -->
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">To Date</label>
                                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex space-x-2">
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Filter
                                </button>
                                <a href="{{ route('purchases.purchase-orders.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Orders Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'order_number', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                                                Order #
                                                @if(request('sort') === 'order_number')
                                                    <span class="ml-1">{{ request('order') === 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supplier & Contact</th>
                                        <th class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'order_date', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                                                Order Date
                                                @if(request('sort') === 'order_date')
                                                    <span class="ml-1">{{ request('order') === 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">Expected Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">Received Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">Items</th>
                                        <th class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'total_amount', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                                                Total
                                                @if(request('sort') === 'total_amount')
                                                    <span class="ml-1">{{ request('order') === 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">Priority</th>
                                        <th class="px-6 py-3 text-left text-xs font-mono text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($orders as $order)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    <a href="{{ route('purchases.purchase-orders.show', $order->order_id) }}" class="text-blue-600 hover:text-blue-900">
                                                        {{ $order->order_number }}
                                                    </a>
                                                </div>
                                                {{-- @if($order->is_overdue)
                                                    <div class="text-xs text-red-600">OVERDUE</div>
                                                @endif --}}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">{{ $order->supplier->supplier_name ?? $order->supplier->name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->supplier->phone ?? 'No phone' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $order->order_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $order->expected_date ? $order->expected_date->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                @if($order->received_date)
                                                    {{ $order->received_date->format('M d, Y') }}
                                                    @if($order->status === 'completed')
                                                        <div class="text-xs text-green-600 dark:text-green-400">Successfully received</div>
                                                    @elseif($receive->status === 'received' && $receive->is_short_closed)
                                                        <div class="text-xs text-orange-600 dark:text-orange-400">Short Closed</div>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $order->total_products }} item(s)
                                                <div class="text-xs text-gray-500">{{ $order->total_quantity }} qty</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                ₱{{ number_format($order->total_amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status_badge_class }}">
                                                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->priority_badge_class }}">
                                                    {{ ucfirst($order->priority) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('purchases.purchase-orders.show', $order->order_id) }}" 
                                                       class="text-indigo-600 hover:text-indigo-900" title="View">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                    
                                                    @if($order->canBeEdited())
                                                        <a href="{{ route('purchases.purchase-orders.edit', $order->order_id) }}" 
                                                           class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    {{-- @if($order->canBeCancelled())
                                                        <form action="{{ route('purchases.purchase-orders.destroy', $order->order_id) }}" 
                                                              method="POST" class="inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this purchase order?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No purchase orders</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new purchase order.</p>
                            <div class="mt-6">
                                <button type="button" onclick="openCreateModal()" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    New Purchase Order
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Create Purchase Order Modal -->
    <div id="createModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Create Purchase Order</h3>
                    <button onclick="closeCreateModal()" type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="mt-4 max-h-[75vh] overflow-y-auto">
                    <form id="createForm" method="POST" action="{{ route('purchases.purchase-orders.store') }}">
                        @csrf
                        
                        <!-- Order Information -->
                        <div class="mb-4">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Order Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <!-- Supplier -->
                                <div>
                                    <label for="modal_supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                                    <select id="modal_supplier_id" name="supplier_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select Supplier</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier['id'] }}">{{ $supplier['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Order Date -->
                                <div>
                                    <label for="modal_order_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Date</label>
                                    <input id="modal_order_date" name="order_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required />
                                </div>

                                <!-- Expected Date -->
                                <div>
                                    <label for="modal_expected_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expected Delivery</label>
                                    <input id="modal_expected_date" name="expected_date" type="date"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        min="{{ date('Y-m-d') }}" />
                                </div>

                                <!-- Priority -->
                                <div>
                                    <label for="modal_priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                    <select id="modal_priority" name="priority"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="low">Low</option>
                                        <option value="normal" selected>Normal</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>

                                <!-- Payment Method -->
                                <div>
                                    <label for="modal_payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Method</label>
                                    <select id="modal_payment_method" name="payment_method" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="mb-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white">Order Items</h4>
                                <button type="button" onclick="addItem()" 
                                    class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Item
                                </button>
                            </div>

                            <div id="modal_orderItems" class="space-y-3 max-h-96 overflow-y-auto">
                                <!-- Items will be added here by JavaScript -->
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Order Summary</h4>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <div id="modal_summary_items" class="space-y-2 mb-3 text-sm">
                                    <p class="text-gray-500 dark:text-gray-400">No items selected yet</p>
                                </div>
                                <hr class="my-3 border-gray-300 dark:border-gray-600">
                                <div class="flex justify-between font-bold text-lg">
                                    <span class="text-gray-900 dark:text-white">Total:</span>
                                    <span id="modal_total_display" class="text-gray-900 dark:text-white">₱0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end gap-3 mt-6 pt-4 border-t dark:border-gray-700">
                            <button type="button" onclick="closeCreateModal()"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                Create Purchase Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let itemIndex = 0;
        const products = @json($products);

        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            // Add initial item
            if (itemIndex === 0) {
                addItem();
            }
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('createForm').reset();
            document.getElementById('modal_orderItems').innerHTML = '';
            itemIndex = 0;
            updateSummary();
        }

        function addItem() {
            const container = document.getElementById('modal_orderItems');
            const itemHtml = `
                <div class="item-row border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product</label>
                            <select name="items[${itemIndex}][product_id]" class="product-select block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" required onchange="updateItemPrice(this)">
                                <option value="">Select Product</option>
                                ${products.map(p => `<option value="${p.id}" data-price="${p.price}">${p.product_name} (Stock: ${p.stock})</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                            <input type="number" name="items[${itemIndex}][quantity_ordered]" class="quantity-input block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" min="1" required oninput="updateSummary()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit Price</label>
                            <input type="number" name="items[${itemIndex}][unit_price]" step="0.01" class="unit-price-input block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" oninput="updateSummary()">
                        </div>
                        <div class="flex items-end">
                            <button type="button" onclick="removeItem(this)" class="w-full px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
            itemIndex++;
        }

        function removeItem(button) {
            const items = document.querySelectorAll('.item-row');
            if (items.length > 1) {
                button.closest('.item-row').remove();
                updateSummary();
            } else {
                alert('At least one item is required.');
            }
        }

        function updateItemPrice(select) {
            const row = select.closest('.item-row');
            const priceInput = row.querySelector('.unit-price-input');
            const option = select.options[select.selectedIndex];
            const price = option.dataset.price || '';
            
            const supplierId = document.getElementById('modal_supplier_id').value;
            const productId = select.value;
            
            if (productId && supplierId) {
                fetch('{{ route('purchases.purchase-orders.get-supplier-cost') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        supplier_id: supplierId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    priceInput.value = data.cost || price;
                    updateSummary();
                })
                .catch(() => {
                    priceInput.value = price;
                    updateSummary();
                });
            } else {
                priceInput.value = price;
                updateSummary();
            }
        }

        function updateSummary() {
            let total = 0;
            let itemsHtml = '';
            const items = document.querySelectorAll('.item-row');
            
            items.forEach((row, index) => {
                const select = row.querySelector('.product-select');
                const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                const lineTotal = quantity * price;
                const productName = select.options[select.selectedIndex]?.text || 'Not selected';
                
                if (quantity > 0 && price > 0 && select.value) {
                    total += lineTotal;
                    itemsHtml += `
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700 dark:text-gray-300">${productName.substring(0, 40)}${productName.length > 40 ? '...' : ''}</span>
                            <span class="font-medium text-gray-900 dark:text-white">₱${lineTotal.toFixed(2)}</span>
                        </div>
                    `;
                }
            });
            
            document.getElementById('modal_summary_items').innerHTML = itemsHtml || '<p class="text-gray-500 dark:text-gray-400">No items selected yet</p>';
            document.getElementById('modal_total_display').textContent = '₱' + total.toFixed(2);
        }

        // Close on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !document.getElementById('createModal').classList.contains('hidden')) {
                closeCreateModal();
            }
        });

        // Close on outside click
        document.getElementById('createModal')?.addEventListener('click', function(event) {
            if (event.target === this) {
                closeCreateModal();
            }
        });

        // Handle supplier change to update all item prices
        document.addEventListener('change', function(e) {
            if (e.target.id === 'modal_supplier_id') {
                document.querySelectorAll('.product-select').forEach(select => {
                    if (select.value) {
                        updateItemPrice(select);
                    }
                });
            }
        });
    </script>
</x-app-layout>
