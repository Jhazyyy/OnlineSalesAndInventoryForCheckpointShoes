<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Purchase Order Receiving Report') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('inventory.purchase-orders.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Orders
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Report Filters</h3>
                    
                    <form method="GET" action="{{ route('inventory.purchase-orders.receiving-report') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $reportData['filters']['start_date'] ?? '' }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                        </div>
                        
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $reportData['filters']['end_date'] ?? '' }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                        </div>
                        
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supplier</label>
                            <select name="supplier_id" id="supplier_id" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->supplier_id }}" 
                                        {{ ($reportData['filters']['supplier_id'] ?? '') == $supplier->supplier_id ? 'selected' : '' }}>
                                        {{ $supplier->supplier_name ?? $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Summary</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $reportData['summary']['total_orders_received'] }}
                            </div>
                            <div class="text-sm text-blue-800 dark:text-blue-200">Total Orders</div>
                        </div>
                        
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ $reportData['summary']['fully_received_orders'] }}
                            </div>
                            <div class="text-sm text-green-800 dark:text-green-200">Fully Received</div>
                        </div>
                        
                        <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                {{ $reportData['summary']['partially_received_orders'] }}
                            </div>
                            <div class="text-sm text-orange-800 dark:text-orange-200">Partially Received</div>
                        </div>
                        
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                {{ number_format($reportData['summary']['total_items_received']) }}
                            </div>
                            <div class="text-sm text-purple-800 dark:text-purple-200">Items Received</div>
                        </div>
                        
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                ₱{{ number_format($reportData['summary']['total_value_received'], 2) }}
                            </div>
                            <div class="text-sm text-indigo-800 dark:text-indigo-200">Total Value</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Received Orders Table -->
            @if($reportData['orders']->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Received Orders</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="px-6 py-3">Order #</th>
                                        <th class="px-6 py-3">Supplier & Contact</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3">Order Date</th>
                                        <th class="px-6 py-3">Received Date</th>
                                        <th class="px-6 py-3">Items</th>
                                        <th class="px-6 py-3">Progress</th>
                                        <th class="px-6 py-3">Value</th>
                                        <th class="px-6 py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData['orders'] as $order)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                                {{ $order->order_number }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-white">{{ $order->supplier->supplier_name ?? $order->supplier->name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->supplier->phone ?? 'No phone' }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->status_badge_class }}">
                                                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $order->order_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $order->received_date ? $order->received_date->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $order->items->sum('quantity_received') }}/{{ $order->items->sum('quantity_ordered') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                    @php
                                                        $progress = $order->items->sum('quantity_ordered') > 0 ? 
                                                                    round(($order->items->sum('quantity_received') / $order->items->sum('quantity_ordered')) * 100, 1) : 0
                                                    @endphp
                                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $progress }}%</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @php
                                                    $receivedValue = $order->items->sum(function($item) {
                                                        return $item->quantity_received * $item->unit_price;
                                                    });
                                                @endphp
                                                ₱{{ number_format($receivedValue, 2) }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <a href="{{ route('inventory.purchase-orders.show', $order->order_id) }}" 
                                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No received orders found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters to see more results.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>