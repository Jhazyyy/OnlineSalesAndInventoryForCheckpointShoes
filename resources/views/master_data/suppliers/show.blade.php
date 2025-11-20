<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div
                                class="h-16 w-16 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center mr-4">
                                <span class="text-xl font-bold text-gray-600 dark:text-gray-300">
                                    {{ substr($supplier->supplier_name, 0, 2) }}
                                </span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $supplier->supplier_name }}</h2>
                                <p class="text-gray-600 dark:text-gray-400">
                                    {{ ucfirst(str_replace('_', ' ', $supplier->type)) }} Supplier
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2
                                        {{ $supplier->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($supplier->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('master_data.suppliers.edit', $supplier) }}"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Edit Supplier
                            </a>
                            <a href="{{ route('master_data.suppliers.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to Suppliers
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supplier Information and Performance -->
            <div class="grid grid-cols-1 lg:grid-cols-1 gap-6 mb-6">
                <!-- Contact Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                                <span
                                    class="text-sm text-gray-900 dark:text-white">{{ $supplier->phone ?? 'N/A' }}</span>
                            </div>
                            @if ($supplier->email)
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <a href="mailto:{{ $supplier->email }}"
                                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">{{ $supplier->email }}</a>
                                </div>
                            @endif
                            @if ($supplier->full_address)
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span
                                        class="text-sm text-gray-900 dark:text-white">{{ $supplier->full_address }}</span>
                                </div>
                            @endif
                            @if ($supplier->tax_id)
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <span class="text-sm text-gray-900 dark:text-white">Tax ID:
                                        {{ $supplier->tax_id }}</span>
                                </div>
                            @endif
                            @if ($supplier->supplier_type)
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <span class="text-sm text-gray-900 dark:text-white">Supplier Type:
                                        {{ ucwords(str_replace('_', ' ',$supplier->supplier_type)) }}</span>
                                </div>
                            @endif
                            @if ($supplier->payment_terms)
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                        <text x="12" y="14" text-anchor="middle" font-size="8" font-weight="bold"
                                            fill="currentColor">₱</text>
                                    </svg>
                                    <span class="text-sm text-gray-900 dark:text-white">Payment Terms:
                                        {{ $supplier->payment_terms }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Performance Summary -->
                {{-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Purchased:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">₱{{ number_format($performance['total_purchased'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Orders:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $performance['total_orders'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Average Order Value:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">₱{{ number_format($performance['average_order_value'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Order Frequency:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $performance['order_frequency_per_month'] }}/month</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Performance Rating:</span>
                                <span class="text-sm font-medium 
                                    @if ($performance['performance_rating'] === 'Excellent') text-green-600
                                    @elseif($performance['performance_rating'] === 'Good') text-blue-600
                                    @elseif($performance['performance_rating'] === 'Average') text-yellow-600
                                    @elseif($performance['performance_rating'] === 'Below Average') text-orange-600
                                    @else text-red-600
                                    @endif">
                                    {{ $performance['performance_rating'] }} ({{ $performance['performance_score'] }}/100)
                                </span>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <!-- Quick Stats -->
                {{-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Stats</h3>
                        <div class="space-y-3">
                            @if ($performance['first_order_date'])
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">First Order:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $performance['first_order_date']->format('M d, Y') }}</span>
                                </div>
                            @endif
                            @if ($performance['last_order_date'])
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Last Order:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $performance['last_order_date']->format('M d, Y') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Recent Orders (90 days):</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $performance['recent_orders_90_days'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Recent Value (90 days):</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">₱{{ number_format($performance['recent_value_90_days'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Reliable Supplier:</span>
                                <span class="text-sm font-medium {{ $performance['is_reliable'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $performance['is_reliable'] ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>

            <!-- Activity Log -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Activity Log</h3>
                        @if ($recentActivities->count() > 0)
                            <span class="text-sm text-gray-500 dark:text-gray-400">Recent 20 activities</span>
                        @endif
                    </div>

                    @if ($recentActivities->count() > 0)
                        <div class="space-y-4">
                            @foreach ($recentActivities as $activity)
                                <div
                                    class="flex items-start space-x-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0 mt-1">
                                        <div
                                            class="h-8 w-8 rounded-full flex items-center justify-center {{ $activity->activity_badge_class }}">
                                            {!! $activity->activity_icon !!}
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $activity->description }}
                                                </p>
                                                <div
                                                    class="mt-1 flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ $activity->created_at->diffForHumans() }}
                                                    </span>
                                                    @if ($activity->user)
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                                </path>
                                                            </svg>
                                                            {{ $activity->user->name }}
                                                        </span>
                                                    @endif
                                                    @if ($activity->amount)
                                                        <span
                                                            class="flex items-center font-semibold text-gray-700 dark:text-gray-300">
                                                            <svg class="w-4 h-4 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                </path>
                                                            </svg>
                                                            ₱{{ number_format($activity->amount, 2) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 ml-4">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $activity->activity_badge_class }}">
                                                    {{ ucwords(str_replace('_', ' ', $activity->activity_type)) }}
                                                </span>
                                            </div>
                                        </div>

                                        @if ($activity->metadata && is_array($activity->metadata))
                                            <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                                @if (isset($activity->metadata['order_number']))
                                                    <span class="inline-flex items-center mr-3">
                                                        <strong class="mr-1">Order:</strong>
                                                        @if ($activity->related_id && $activity->related_type === 'PurchaseOrder')
                                                            <a href="{{ route('purchases.purchase-orders.show', $activity->related_id) }}"
                                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                                                {{ $activity->metadata['order_number'] }}
                                                            </a>
                                                        @else
                                                            {{ $activity->metadata['order_number'] }}
                                                        @endif
                                                    </span>
                                                @endif
                                                @if (isset($activity->metadata['items_count']))
                                                    <span class="inline-flex items-center mr-3">
                                                        <strong class="mr-1">Items:</strong>
                                                        {{ $activity->metadata['items_count'] }}
                                                    </span>
                                                @endif
                                                @if (isset($activity->metadata['priority']))
                                                    <span class="inline-flex items-center mr-3">
                                                        <strong class="mr-1">Priority:</strong>
                                                        <span
                                                            class="capitalize">{{ $activity->metadata['priority'] }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No activity yet</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Activity will appear here when you
                                interact with this supplier.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Purchase History -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Purchase Orders</h3>
                        @if ($recentPurchaseOrders->count() > 0)
                            <span class="text-sm text-gray-500 dark:text-gray-400">Last 10 orders</span>
                        @endif
                    </div>

                    @if ($recentPurchaseOrders->count() > 0)
                        <div class="overflow-x-auto -mx-4 sm:mx-0">
                            <div class="inline-block min-w-full align-middle">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th
                                                class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                                Order #</th>
                                            <th
                                                class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                                Date</th>
                                            <th
                                                class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Products</th>
                                            <th
                                                class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                                Status</th>
                                            <th
                                                class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                                Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($recentPurchaseOrders as $order)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-3 sm:px-4 lg:px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route('purchases.purchase-orders.show', $order->order_id) }}"
                                                        class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                        {{ $order->order_number }}
                                                    </a>
                                                    @if ($order->priority && $order->priority !== 'normal')
                                                        <span
                                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                            {{ $order->priority === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : '' }}
                                                            {{ $order->priority === 'high' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300' : '' }}
                                                            {{ $order->priority === 'low' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}">
                                                            {{ ucfirst($order->priority) }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td
                                                    class="px-3 sm:px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    {{ $order->order_date->format('M d, Y') }}
                                                </td>
                                                <td class="px-3 sm:px-4 lg:px-6 py-4">
                                                    <div class="text-sm text-gray-900 dark:text-white">
                                                        @if ($order->items->count() > 0)
                                                            <div class="space-y-1">
                                                                @foreach ($order->items->take(3) as $item)
                                                                    <div class="flex items-center justify-between">
                                                                        <span
                                                                            class="truncate max-w-xs">{{ $item->product->product_name ?? 'N/A' }}</span>
                                                                        <span
                                                                            class="ml-2 text-gray-500 dark:text-gray-400">×{{ $item->quantity_ordered }}</span>
                                                                    </div>
                                                                @endforeach
                                                                @if ($order->items->count() > 3)
                                                                    <div
                                                                        class="text-xs text-gray-500 dark:text-gray-400">
                                                                        +{{ $order->items->count() - 3 }} more items
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="text-gray-500">No items</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-3 sm:px-4 lg:px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        {{ $order->status === 'received' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                                                        {{ $order->status === 'approved' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                                                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : '' }}
                                                        {{ $order->status === 'ordered' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : '' }}
                                                        {{ $order->status === 'partial_received' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300' : '' }}">
                                                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                                    </span>
                                                </td>
                                                <td
                                                    class="px-3 sm:px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                    ₱{{ number_format($order->total_amount, 2) }}
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $order->items->sum('quantity_ordered') }} items
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No purchase orders yet</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start ordering from this supplier to see purchase history.</p>
                            <div class="mt-6">
                                <a href="{{ route('purchases.purchase-orders.create', ['supplier_id' => $supplier->supplier_id]) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Create Purchase Order
                                </a>
                            </div>
                        </div> --}}
                    @endif
                </div>
            </div>

            <!-- Monthly Purchase Chart (if data available) -->
            @if ($monthlyPurchases->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Purchase Activity
                            ({{ date('Y') }})</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ substr($month, 0, 3) }}
                                    </div>
                                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $monthlyPurchases->get($month)['count'] ?? 0 }}
                                    </div>
                                    <div class="text-xs text-gray-600 dark:text-gray-300">
                                        ₱{{ number_format($monthlyPurchases->get($month)['total_amount'] ?? 0, 0) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Notes Section -->
            @if ($supplier->notes)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $supplier->notes }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Supplier Details -->
            {{-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Supplier Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Supplier ID</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $supplier->supplier_id }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $supplier->type)) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $supplier->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($supplier->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">
                                        {{ $supplier->created_at ? $supplier->created_at->format('M d, Y \a\t h:i A') : 'Not available' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">
                                        {{ $supplier->updated_at ? $supplier->updated_at->format('M d, Y \a\t h:i A') : 'Not available' }}
                                    </dd>
                                </div>
                                @if ($performance['last_order_date'])
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Order</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $performance['last_order_date']->format('M d, Y') }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</x-app-layout>
