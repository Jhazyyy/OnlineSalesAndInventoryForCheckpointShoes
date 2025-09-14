<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Stock Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Header with Date Range Filter -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
                        <h3 class="text-lg font-medium mb-4 md:mb-0">Stock Movement Analytics</h3>
                        
                        <!-- Date Range Filter -->
                        <form method="GET" class="flex flex-col sm:flex-row gap-4">
                            <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium">From:</label>
                                <input type="date" name="start_date" 
                                       value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                                       class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                            </div>
                            <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium">To:</label>
                                <input type="date" name="end_date" 
                                       value="{{ request('end_date', now()->format('Y-m-d')) }}"
                                       class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                            </div>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                Update
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Movements -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Movements</p>
                                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($analytics['summary']['total_movements'] ?? 0) }}
                                </p>
                            </div>
                            <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Adjustments -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Stock Adjustments</p>
                                <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                    {{ number_format($analytics['summary']['adjustments'] ?? 0) }}
                                </p>
                            </div>
                            <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Waste/Damage -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Waste/Damage</p>
                                <p class="text-3xl font-bold text-red-600 dark:text-red-400">
                                    {{ number_format($analytics['summary']['waste'] ?? 0) }}
                                </p>
                            </div>
                            <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Value Impact -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Value Impact</p>
                                <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                                    ₱{{ number_format($analytics['summary']['total_value'] ?? 0, 2) }}
                                </p>
                            </div>
                            <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    <text x="12" y="14" text-anchor="middle" font-size="8" font-weight="bold" fill="currentColor">₱</text>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Detailed Analytics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Movement Types Breakdown -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h4 class="text-lg font-medium mb-4">Movement Types Breakdown</h4>
                        @if(isset($analytics['movement_types']) && count($analytics['movement_types']) > 0)
                            <div class="space-y-4">
                                @foreach($analytics['movement_types'] as $type => $data)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded mr-3
                                                @if($type === 'adjustment') bg-blue-500
                                                @elseif($type === 'purchase') bg-green-500
                                                @elseif($type === 'sale') bg-purple-500
                                                @elseif($type === 'transfer') bg-yellow-500
                                                @elseif($type === 'waste') bg-red-500
                                                @else bg-gray-500
                                                @endif">
                                            </div>
                                            <span class="capitalize">{{ str_replace('_', ' ', $type) }}</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-semibold">{{ number_format($data['count']) }}</div>
                                            <div class="text-sm text-gray-500">{{ number_format($data['percentage'], 1) }}%</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400">No movement data available for the selected period.</p>
                        @endif
                    </div>
                </div>

                <!-- Top Products by Movement Activity -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h4 class="text-lg font-medium mb-4">Most Active Products</h4>
                        @if(isset($analytics['top_products']) && count($analytics['top_products']) > 0)
                            <div class="space-y-4">
                                @foreach($analytics['top_products'] as $product)
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium">{{ $product['name'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product['brand'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-semibold">{{ number_format($product['movement_count']) }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">movements</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400">No product movement data available for the selected period.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stock Alerts -->
            @if(isset($analytics['alerts']) && (count($analytics['alerts']['low_stock_products']) > 0 || count($analytics['alerts']['out_of_stock_products']) > 0))
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h4 class="text-lg font-medium mb-4 text-orange-600 dark:text-orange-400">Stock Alerts</h4>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Low Stock -->
                        @if(count($analytics['alerts']['low_stock_products']) > 0)
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <h5 class="font-medium text-yellow-800 dark:text-yellow-200 mb-3">Low Stock ({{ count($analytics['alerts']['low_stock_products']) }} items)</h5>
                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                @foreach($analytics['alerts']['low_stock_products'] as $product)
                                    <div class="flex justify-between items-center text-sm">
                                        <span>{{ $product['name'] }}</span>
                                        <span class="font-semibold text-yellow-700 dark:text-yellow-300">{{ $product['quantity'] }} left</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Out of Stock -->
                        @if(count($analytics['alerts']['out_of_stock_products']) > 0)
                        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-200 dark:border-red-800">
                            <h5 class="font-medium text-red-800 dark:text-red-200 mb-3">Out of Stock ({{ count($analytics['alerts']['out_of_stock_products']) }} items)</h5>
                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                @foreach($analytics['alerts']['out_of_stock_products'] as $product)
                                    <div class="flex justify-between items-center text-sm">
                                        <span>{{ $product['name'] }}</span>
                                        <span class="font-semibold text-red-700 dark:text-red-300">{{ $product['quantity'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Activity -->
            @if(isset($analytics['recent_movements']) && count($analytics['recent_movements']) > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-medium">Recent Stock Movements</h4>
                        <a href="{{ route('inventory.stock.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            View All →
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Change</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($analytics['recent_movements'] as $movement)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $movement['date'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $movement['product_name'] }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $movement['product_brand'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($movement['type'] === 'adjustment') bg-blue-100 text-blue-800
                                                @elseif($movement['type'] === 'purchase') bg-green-100 text-green-800
                                                @elseif($movement['type'] === 'sale') bg-purple-100 text-purple-800
                                                @elseif($movement['type'] === 'transfer') bg-yellow-100 text-yellow-800
                                                @elseif($movement['type'] === 'waste') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($movement['type']) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $movement['quantity_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $movement['quantity_change'] >= 0 ? '+' : '' }}{{ number_format($movement['quantity_change']) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($movement['status'] === 'confirmed') bg-green-100 text-green-800
                                                @elseif($movement['status'] === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($movement['status']) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
