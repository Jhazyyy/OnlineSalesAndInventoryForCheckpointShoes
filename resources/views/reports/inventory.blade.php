<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Inventory Report</h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            Stock status, valuation, and movement analysis
                        </p>
                    </div>

                    <a href="{{ route('reports.index') }}"
                       class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out w-fit">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Reports
                    </a>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.inventory') }}" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Category</label>
                                <select name="category"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Categories</option>
                                    @foreach(\App\Models\Product::select('product_category')->distinct()->whereNotNull('product_category')->orderBy('product_category')->pluck('product_category') as $category)
                                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Movement Category</label>
                                <select name="movement_category"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Movement Types</option>
                                    <option value="fast" {{ request('movement_category') == 'fast' ? 'selected' : '' }}>Fast Moving</option>
                                    <option value="slow" {{ request('movement_category') == 'slow' ? 'selected' : '' }}>Slow Moving</option>
                                    <option value="non-moving" {{ request('movement_category') == 'non-moving' ? 'selected' : '' }}>Non-Moving</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stock Status</label>
                                <select name="stock_status"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Stock Status</option>
                                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                    <option value="critical" {{ request('stock_status') == 'critical' ? 'selected' : '' }}>Critical</option>
                                    <option value="overstocked" {{ request('stock_status') == 'overstocked' ? 'selected' : '' }}>Overstocked</option>
                                </select>
                            </div>

                            <div class="flex flex-wrap gap-2 sm:justify-end sm:items-end">
                                <button type="submit"
                                        class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Apply Filters
                                </button>
                                <a href="{{ route('reports.inventory') }}"
                                   class="inline-flex items-center justify-center px-3 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                @php
                    $cards = [
                        ['label' => 'Total Products', 'color' => 'from-green-500 to-green-600', 'value' => $report['summary']['total_products'] ?? 0, 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                        ['label' => 'Total Stock', 'color' => 'from-blue-500 to-blue-600', 'value' => $report['summary']['total_stock'] ?? 0, 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'],
                        ['label' => 'Total Value', 'color' => 'from-yellow-500 to-yellow-600', 'value' => '₱' . number_format($report['summary']['total_value'] ?? 0, 2), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Low Stock', 'color' => 'from-orange-500 to-orange-600', 'value' => $report['summary']['low_stock'] ?? 0, 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                        ['label' => 'Out of Stock', 'color' => 'from-red-500 to-red-600', 'value' => $report['summary']['out_of_stock'] ?? 0, 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z']
                    ];
                @endphp

                @foreach($cards as $card)
                    <div class="bg-gradient-to-br {{ $card['color'] }} text-white rounded-lg shadow-lg p-5 transform hover:scale-[1.03] transition duration-300 ease-in-out">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs sm:text-sm opacity-90">{{ $card['label'] }}</p>
                                <p class="text-2xl sm:text-3xl font-bold mt-1">{{ $card['value'] }}</p>
                            </div>
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Export Buttons -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 flex flex-wrap gap-3 sm:gap-4">
                    <a href="{{ route('reports.export-pdf', array_merge(['reportType' => 'inventory'], request()->only(['category', 'movement_category', 'stock_status']))) }}"
                       target="_blank"
                       class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-medium rounded-md transition w-fit">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export PDF
                    </a>

                    <a href="{{ route('reports.export-excel', array_merge(['reportType' => 'inventory'], request()->only(['category', 'movement_category', 'stock_status']))) }}"
                       class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-green-600 hover:bg-green-700 text-white text-xs sm:text-sm font-medium rounded-md transition w-fit">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>

            <!-- Analytics Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Products by Movement Category -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Products by Movement Category</h3>
                            <div class="space-y-3">
                                @forelse($report['by_movement_category'] ?? [] as $category)
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded">
                                    <div>
                                        <p class="font-medium capitalize text-gray-900 dark:text-white">{{ $category->movement_category ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500">{{ $category->total_stock ?? 0 }} units in stock</p>
                                    </div>
                                    <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-3 py-1 rounded-full font-bold">
                                        {{ $category->count ?? 0 }}
                                    </span>
                                </div>
                                @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No movement data available</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Products by Stock Status -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Products by Stock Status</h3>
                            <div class="space-y-3">
                                @forelse($report['by_stock_status'] ?? [] as $status)
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded">
                                    <div>
                                        <p class="font-medium capitalize text-gray-900 dark:text-white">
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
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No stock status data</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Low Stock Products -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Low Stock Alert</h3>
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @forelse($report['low_stock_products'] ?? [] as $product)
                                <div class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition border-l-4 border-orange-500">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $product->product_name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500">SKU: {{ $product->sku ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-orange-600 dark:text-orange-400">{{ $product->quantity ?? 0 }} units</p>
                                        <p class="text-xs text-gray-500">Min: {{ $product->reorder_level ?? 0 }}</p>
                                    </div>
                                </div>
                                @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No low stock items</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Out of Stock Products -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Out of Stock</h3>
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @forelse($report['out_of_stock_products'] ?? [] as $product)
                                <div class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition border-l-4 border-red-500">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $product->product_name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-500">SKU: {{ $product->sku ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded text-xs font-bold">
                                            OUT OF STOCK
                                        </span>
                                    </div>
                                </div>
                                @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No out of stock items</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Top Value Products -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6 lg:col-span-2">
                            <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Top 10 Highest Value Products</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @forelse($report['top_value_products'] ?? [] as $index => $product)
                                <div class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition">
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-lg text-gray-400">#{{ $index + 1 }}</span>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $product->product_name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">{{ $product->quantity ?? 0 }} units @ ₱{{ number_format($product->price ?? 0, 2) }}</p>
                                        </div>
                                    </div>
                                    <span class="text-green-600 dark:text-green-400 font-bold">
                                        ₱{{ number_format($product->total_value ?? 0, 2) }}
                                    </span>
                                </div>
                                @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4 col-span-2">No product data</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
