<x-app-layout>
    <div class="w-full min-h-screen">
        <div class="h-full overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 min-h-full flex flex-col">
                <div class="flex-1 p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-3xl font-bold">Inventory Report</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Stock status, valuation, and movement analysis</p>
                        </div>
                        <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            ← Back to Reports
                        </a>
                    </div>

                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('reports.inventory') }}" class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Start Date</label>
                                <input type="date" name="start_date" value="{{ request('start_date', $startDate ?? '') }}" 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">End Date</label>
                                <input type="date" name="end_date" value="{{ request('end_date', $endDate ?? '') }}" 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                            </div>
                            <div class="flex items-end gap-2">
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                                    Apply Filter
                                </button>
                                <a href="{{ route('reports.inventory') }}" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white px-4 py-2 rounded-lg font-medium">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
                        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Total Products</p>
                                    <p class="text-3xl font-bold mt-1">{{ $report['summary']['total_products'] ?? 0 }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Total Stock</p>
                                    <p class="text-3xl font-bold mt-1">{{ $report['summary']['total_stock'] ?? 0 }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Total Value</p>
                                    <p class="text-2xl font-bold mt-1">₱{{ number_format($report['summary']['total_value'] ?? 0, 2) }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Low Stock</p>
                                    <p class="text-3xl font-bold mt-1">{{ $report['summary']['low_stock'] ?? 0 }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-red-500 to-red-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Out of Stock</p>
                                    <p class="text-3xl font-bold mt-1">{{ $report['summary']['out_of_stock'] ?? 0 }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Export Buttons -->
                    <div class="flex gap-4 mb-6">
                        <a href="{{ route('reports.export-pdf', ['reportType' => 'inventory', 'start_date' => request('start_date', $startDate ?? ''), 'end_date' => request('end_date', $endDate ?? '')]) }}" target="_blank" class="inline-block">
                            <span class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export PDF
                            </span>
                        </a>

                        <a href="{{ route('reports.export-excel', ['reportType' => 'inventory', 'start_date' => request('start_date', $startDate ?? ''), 'end_date' => request('end_date', $endDate ?? '')]) }}" class="inline-block">
                            <span class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export Excel
                            </span>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Products by Movement Category -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-xl font-bold mb-4">Products by Movement Category</h3>
                            <div class="space-y-3">
                                @forelse($report['by_movement_category'] ?? [] as $category)
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded">
                                    <div>
                                        <p class="font-medium capitalize">{{ $category->movement_category ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500">{{ $category->total_stock ?? 0 }} units in stock</p>
                                    </div>
                                    <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-3 py-1 rounded-full font-bold">
                                        {{ $category->count ?? 0 }}
                                    </span>
                                </div>
                                @empty
                                <p class="text-gray-500 text-center py-4">No movement data available</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Products by Stock Status -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-xl font-bold mb-4">Products by Stock Status</h3>
                            <div class="space-y-3">
                                @forelse($report['by_stock_status'] ?? [] as $status)
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded">
                                    <div>
                                        <p class="font-medium capitalize">
                                            @if($status->stock_status == 'in_stock')
                                                In Stock
                                            @elseif($status->stock_status == 'low_stock')
                                                Low Stock
                                            @elseif($status->stock_status == 'out_of_stock')
                                                Out of Stock
                                            @else
                                                {{ $status->stock_status }}
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $status->total_quantity ?? 0 }} units</p>
                                    </div>
                                    <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full font-bold">
                                        {{ $status->count ?? 0 }}
                                    </span>
                                </div>
                                @empty
                                <p class="text-gray-500 text-center py-4">No stock status data</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Low Stock Products -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-xl font-bold mb-4">Low Stock Alert</h3>
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @forelse($report['low_stock_products'] ?? [] as $product)
                                <div class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition border-l-4 border-orange-500">
                                    <div>
                                        <p class="font-medium">{{ $product->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500">SKU: {{ $product->sku ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-orange-600 dark:text-orange-400">{{ $product->quantity ?? 0 }} units</p>
                                        <p class="text-xs text-gray-500">Min: {{ $product->reorder_level ?? 0 }}</p>
                                    </div>
                                </div>
                                @empty
                                <p class="text-gray-500 text-center py-4">No low stock items</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Out of Stock Products -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-xl font-bold mb-4">Out of Stock Critical</h3>
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @forelse($report['out_of_stock_products'] ?? [] as $product)
                                <div class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition border-l-4 border-red-500">
                                    <div>
                                        <p class="font-medium">{{ $product->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500">SKU: {{ $product->sku ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded text-xs font-bold">
                                            OUT OF STOCK
                                        </span>
                                    </div>
                                </div>
                                @empty
                                <p class="text-gray-500 text-center py-4">No out of stock items</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Top Value Products -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6 lg:col-span-2">
                            <h3 class="text-xl font-bold mb-4">Top 10 Highest Value Products</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @forelse($report['top_value_products'] ?? [] as $index => $product)
                                <div class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition">
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-lg text-gray-400">#{{ $index + 1 }}</span>
                                        <div>
                                            <p class="font-medium">{{ $product->name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">{{ $product->quantity ?? 0 }} units @ ₱{{ number_format($product->price ?? 0, 2) }}</p>
                                        </div>
                                    </div>
                                    <span class="text-green-600 dark:text-green-400 font-bold">
                                        ₱{{ number_format($product->total_value ?? 0, 2) }}
                                    </span>
                                </div>
                                @empty
                                <p class="text-gray-500 text-center py-4 col-span-2">No product data</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
