<x-app-layout>
    <div class="w-full min-h-screen">
        <!-- Main Content Area -->
        <div class="h-full overflow-y-auto">
            <div class="bg-gray-50 dark:bg-gray-900 min-h-full flex flex-col">
                <div class="flex-1 p-6 text-gray-900 dark:text-gray-100">
                    {{-- <h2 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-100">Dashboard</h2> --}}

                    <!-- Main Dashboard Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        <!-- Left Column: Sales Activity & Item Details -->
                        <div class="lg:col-span-2 space-y-6">

                            <!-- Sales Activity Section -->
                            {{-- <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Sales Activity</h3>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <!-- To be Packed -->
                                    <div class="text-center">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 mb-2">
                                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                                            {{ $salesOrderData->sum('draft') ?? 0 }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">
                                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z"/>
                                            </svg>
                                            To be Packed
                                        </div>
                                    </div>

                                    <!-- To be Shipped -->
                                    <div class="text-center">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900 mb-2">
                                            <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                            </svg>
                                        </div>
                                        <div class="text-3xl font-bold text-red-600 dark:text-red-400">
                                            {{ $salesOrderData->sum('packed') ?? 0 }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">
                                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z"/>
                                            </svg>
                                            To be Shipped
                                        </div>
                                    </div>

                                    <!-- To be Delivered -->
                                    <div class="text-center">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 dark:bg-green-900 mb-2">
                                            <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                                            </svg>
                                        </div>
                                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                                            {{ $salesOrderData->sum('shipped') ?? 0 }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">
                                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z"/>
                                            </svg>
                                            To be Delivered
                                        </div>
                                    </div>

                                    <!-- To be Invoiced -->
                                    <div class="text-center">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900 mb-2">
                                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                                            {{ $salesOrderData->sum('confirmed') ?? 0 }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">
                                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z"/>
                                            </svg>
                                            To be Invoiced
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            <!-- Item Details Section -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Product Details
                                </h3>

                                <div class="grid grid-cols-3 gap-6">
                                    <!-- Low Stock Items -->
                                    <div class="text-left">
                                        <div class="text-sm text-red-600 dark:text-red-400 font-medium mb-2">Low Stock
                                            Items</div>
                                        <div class="text-4xl font-bold text-gray-800 dark:text-gray-100">
                                            {{ $inventoryStats['low_stock_products'] ?? 0 }}
                                        </div>
                                    </div>

                                    <!-- All Item Groups -->
                                    <div class="text-left">
                                        <div class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-2">All Item
                                            Groups</div>
                                        <div class="text-4xl font-bold text-gray-800 dark:text-gray-100">
                                            {{ \App\Models\Product::distinct('product_category')->count('category') }}
                                        </div>
                                    </div>

                                    <!-- All Items -->
                                    <div class="text-left">
                                        <div class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-2">All Items
                                        </div>
                                        <div class="text-4xl font-bold text-gray-800 dark:text-gray-100">
                                            {{ $inventoryStats['total_products'] ?? 0 }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Active Items Donut Chart -->
                                <div class="mt-6">
                                    <div class="flex items-center">
                                        <div class="relative" style="width: 120px; height: 120px;">
                                            <canvas id="activeItemsChart"></canvas>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                                        {{ $inventoryStats['active_products'] > 0 ? round(($inventoryStats['active_products'] / ($inventoryStats['total_products'] ?: 1)) * 100) : 0 }}%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-6">
                                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Active Items
                                            </div>
                                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                                {{ $inventoryStats['active_products'] ?? 0 }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                                Out of {{ $inventoryStats['total_products'] ?? 0 }} total items
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Top Selling & Purchase Items Section (Grid Layout) -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Top Selling Items Section -->
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                                    <div
                                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4 flex-shrink-0">
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                            Top Selling Items
                                        </h3>
                                        <select id="topSellingPeriod"
                                            class="appearance-none w-full sm:w-auto text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="today">Today</option>
                                            <option value="yesterday">Yesterday</option>
                                            <option value="this_week">This Week</option>
                                            <option value="last_week">Last Week</option>
                                            <option value="this_month" selected>This Month</option>
                                            <option value="last_month">Last Month</option>
                                            <option value="this_year">This Year</option>
                                            <option value="all_time">All Time</option>
                                        </select>
                                    </div>

                                    <div id="topSellingItemsContainer" class="space-y-3 overflow-y-auto flex-1" style="max-height: 600px;">
                                    @forelse($topSellingItems->take(10) ?? [] as $item)
                                        <div
                                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors flex-shrink-0">
                                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                                <!-- Product Image -->
                                                <div
                                                    class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center overflow-hidden">
                                                    @if (!empty($item['image']))
                                                        @php
                                                            $imageUrl = (str_starts_with($item['image'], 'http://') || str_starts_with($item['image'], 'https://'))
                                                                ? $item['image']
                                                                : asset('storage/' . $item['image']);
                                                        @endphp
                                                        <img src="{{ $imageUrl }}"
                                                            alt="{{ $item['name'] ?? 'Product' }}"
                                                            class="w-full h-full object-cover"
                                                            onerror="this.onerror=null; this.parentElement.innerHTML='<svg class=\'w-8 h-8 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\'></path></svg>';">
                                                    @else
                                                        <svg class="w-8 h-8 text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                            </path>
                                                        </svg>
                                                    @endif
                                                </div>

                                                <div class="flex-1 min-w-0">
                                                    <div
                                                        class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate"
                                                        title="{{ $item['name'] ?? 'Unknown Product' }}">
                                                        {{ $item['name'] ?? 'Unknown Product' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                                        Sold: {{ number_format($item['quantity'] ?? 0) }} units
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-right ml-3 flex-shrink-0">
                                                <div class="text-base font-bold text-gray-800 dark:text-gray-100 whitespace-nowrap">
                                                    {{ number_format($item['quantity'] ?? 0) }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">PCS</div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                                </path>
                                            </svg>
                                            <p class="text-sm">No sales data available</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Top Purchase Items Section -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                                <div
                                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4 flex-shrink-0">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                        Top Purchase Items
                                    </h3>
                                    <select id="topPurchasePeriod"
                                        class="appearance-none w-full sm:w-auto text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="today">Today</option>
                                        <option value="yesterday">Yesterday</option>
                                        <option value="this_week">This Week</option>
                                        <option value="last_week">Last Week</option>
                                        <option value="this_month" selected>This Month</option>
                                        <option value="last_month">Last Month</option>
                                        <option value="this_year">This Year</option>
                                        <option value="all_time">All Time</option>
                                    </select>
                                </div>

                                <div id="topPurchaseItemsContainer" class="space-y-3 overflow-y-auto flex-1" style="max-height: 600px;">
                                    @forelse($topPurchaseItems->take(10) ?? [] as $item)
                                        <div
                                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors flex-shrink-0">
                                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                                <!-- Product Image -->
                                                <div
                                                    class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center overflow-hidden">
                                                    @if (!empty($item['image']))
                                                        @php
                                                            $imageUrl = (str_starts_with($item['image'], 'http://') || str_starts_with($item['image'], 'https://'))
                                                                ? $item['image']
                                                                : asset('storage/' . $item['image']);
                                                        @endphp
                                                        <img src="{{ $imageUrl }}"
                                                            alt="{{ $item['name'] ?? 'Product' }}"
                                                            class="w-full h-full object-cover"
                                                            onerror="this.onerror=null; this.parentElement.innerHTML='<svg class=\'w-8 h-8 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\'></path></svg>';">
                                                    @else
                                                        <svg class="w-8 h-8 text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                            </path>
                                                        </svg>
                                                    @endif
                                                </div>

                                                <div class="flex-1 min-w-0">
                                                    <div
                                                        class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate"
                                                        title="{{ $item['name'] ?? 'Unknown Product' }}">
                                                        {{ $item['name'] ?? 'Unknown Product' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                                        Purchased: {{ number_format($item['quantity'] ?? 0) }} units
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-right ml-3 flex-shrink-0">
                                                <div class="text-base font-bold text-gray-800 dark:text-gray-100 whitespace-nowrap">
                                                    {{ number_format($item['quantity'] ?? 0) }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">PCS</div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8.5m-8.5 0a2 2 0 11-4 0 2 2 0 014 0zm8.5 0a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            <p class="text-sm">No purchase data available</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        </div>

                        <!-- Right Column: Inventory Summary -->
                        <div class="lg:col-span-1">
                            <div
                                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-6">
                                <h3 class="text-lg font-semibold mb-6 text-gray-800 dark:text-gray-100">Inventory
                                    Summary</h3>

                                <!-- Quantity in Hand -->
                                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-2">
                                        QUANTITY IN HAND
                                    </div>
                                    <div class="text-5xl font-bold text-gray-800 dark:text-gray-100">
                                        {{ number_format(\App\Models\Product::sum('quantity') ?? 0) }}
                                    </div>
                                </div>

                                <!-- Quantity to be Received -->
                                <div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-2">
                                        QUANTITY TO BE RECEIVED
                                    </div>
                                    <div class="text-5xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ number_format($purchaseReceiveStats['pending_quantity'] ?? 0) }}
                                    </div>

                                    <!-- Breakdown of pending quantities -->
                                    @if (($purchaseReceiveStats['pending_quantity'] ?? 0) > 0)
                                        <div class="mt-4 space-y-2 text-xs">
                                            @if (($purchaseReceiveStats['pending_from_orders'] ?? 0) > 0)
                                                <div
                                                    class="flex items-center justify-between p-2 bg-blue-50 dark:bg-blue-900/20 rounded">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 mr-2"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                            </path>
                                                        </svg>
                                                        <span class="text-gray-700 dark:text-gray-300">Purchase
                                                            Orders</span>
                                                    </div>
                                                    <span class="font-semibold text-blue-600 dark:text-blue-400">
                                                        {{ number_format($purchaseReceiveStats['pending_from_orders']) }}
                                                    </span>
                                                </div>
                                            @endif

                                            @if (($purchaseReceiveStats['pending_from_deliveries'] ?? 0) > 0)
                                                <div
                                                    class="flex items-center justify-between p-2 bg-orange-50 dark:bg-orange-900/20 rounded">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 mr-2"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path
                                                                d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0">
                                                            </path>
                                                        </svg>
                                                        <span class="text-gray-700 dark:text-gray-300">In
                                                            Delivery</span>
                                                    </div>
                                                    <span class="font-semibold text-orange-600 dark:text-orange-400">
                                                        {{ number_format($purchaseReceiveStats['pending_from_deliveries']) }}
                                                    </span>
                                                </div>
                                            @endif

                                            @if (($purchaseReceiveStats['pending_from_receives'] ?? 0) > 0)
                                                <div
                                                    class="flex items-center justify-between p-2 bg-green-50 dark:bg-green-900/20 rounded">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400 mr-2"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                                            </path>
                                                        </svg>
                                                        <span class="text-gray-700 dark:text-gray-300">Goods
                                                            Receipt</span>
                                                    </div>
                                                    <span class="font-semibold text-green-600 dark:text-green-400">
                                                        {{ number_format($purchaseReceiveStats['pending_from_receives']) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Additional Quick Stats (Optional Secondary Row) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mt-6">
                        <!-- Purchases Card -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-yellow-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8.5m-8.5 0a2 2 0 11-4 0 2 2 0 014 0zm8.5 0a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                        Purchases</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $purchaseStats['total_purchases'] ?? 0 }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        ₱{{ number_format($purchaseStats['total_purchase_value'] ?? 0, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Suppliers Card -->
                        {{-- <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-indigo-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                        Suppliers</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $supplierStats['total_suppliers'] ?? 0 }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $supplierStats['active_suppliers'] ?? 0 }} active
                                    </p>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Customers Card -->
                        {{-- <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                        Customers</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $customerStats['total_customers'] ?? 0 }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $customerStats['active_customers'] ?? 0 }} active
                                    </p>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Revenue Card -->
                        {{-- <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                        Total Revenue</p>
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                        ₱{{ number_format(($monthlyRevenue ?? collect())->sum(), 2) }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Last 6 months
                                    </p>
                                </div>
                            </div>
                        </div> --}}
                    </div>

                </div>

                <!-- Footer -->
                <footer
                    class="mt-auto pt-2 pb-4 px-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-6">
                            <!-- Company Info -->
                            <div class="flex items-center space-x-2">
                                <div class="bg-blue-600 p-1.5 rounded-md">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Checkpoint
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Sales & Inventory System
                                    </p>
                                </div>
                            </div>

                            <!-- Quick Links -->
                            <div
                                class="hidden md:flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                <a href="#"
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">About</a>
                                <a href="#"
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Support</a>
                                <a href="#"
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Documentation</a>
                                <a href="#"
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Privacy</a>
                            </div>
                        </div>

                        <div
                            class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-4 mt-4 md:mt-0">
                            <!-- Version Info -->
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">v1.0.0</span>
                            </div>

                            <!-- Copyright -->
                            <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                <p>&copy; {{ date('Y') }} Checkpoint. All Rights Reserved.</p>
                                <p class="mt-1">Built with Laravel</p>
                            </div>

                            <!-- Social Links -->
                            {{-- Facebook --}}
                            <div class="flex items-center space-x-2">
                                <a href="https://www.facebook.com/checkpointshoesph" target="_blank"
                                    class="text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                                {{-- Twitter --}}
                                <a href="#"
                                    class="text-gray-400 hover:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84">
                                        </path>
                                    </svg>
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- Mobile Quick Links -->
                    <div class="md:hidden mt-4 pt-4 border-t border-gray-400 dark:border-gray-700">
                        <div class="flex justify-center space-x-6 text-xs text-gray-500 dark:text-gray-400">
                            <a href="#"
                                class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">About</a>
                            <a href="#"
                                class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Support</a>
                            {{-- <a href="#"
                                class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Documentation</a>
                            --}}
                            <a href="#"
                                class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Privacy</a>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Active Items Donut Chart
            const activeItemsCtx = document.getElementById('activeItemsChart');
            if (activeItemsCtx) {
                const activeProducts = {{ $inventoryStats['active_products'] ?? 0 }};
                const totalProducts = {{ $inventoryStats['total_products'] ?? 1 }};
                const inactiveProducts = totalProducts - activeProducts;

                new Chart(activeItemsCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Active', 'Inactive'],
                        datasets: [{
                            data: [activeProducts, inactiveProducts],
                            backgroundColor: [
                                'rgb(34, 197, 94)', // green for active
                                'rgb(229, 231, 235)' // light gray for inactive
                            ],
                            borderWidth: 0,
                            cutout: '75%' // Makes it a donut
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            }
                        }
                    }
                });
            }

            // Top Selling Items Period Filter
            const topSellingPeriodSelect = document.getElementById('topSellingPeriod');

            if (topSellingPeriodSelect) {
                topSellingPeriodSelect.addEventListener('change', function() {
                    const period = this.value;
                    const selectedText = this.options[this.selectedIndex].text;
                    const container = document.getElementById('topSellingItemsContainer');

                    // Show loading state
                    container.innerHTML = `
                        <div class="flex justify-center items-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        </div>
                    `;

                    // Fetch new data
                    fetch('/dashboard/top-selling-items?period=' + period)
                        .then(response => response.json())
                        .then(data => {
                            if (data.items && data.items.length > 0) {
                                container.innerHTML = data.items.map(item => `
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors flex-shrink-0">
                                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                                            <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center overflow-hidden">
                                                ${item.image 
                                                    ? `<img src="/storage/${item.image}" 
                                                               alt="${item.name}" 
                                                               class="w-full h-full object-cover"
                                                               onerror="this.onerror=null; this.parentElement.innerHTML='<svg class=\\'w-8 h-8 text-white\\' fill=\\'none\\' stroke=\\'currentColor\\' viewBox=\\'0 0 24 24\\'><path stroke-linecap=\\'round\\' stroke-linejoin=\\'round\\' stroke-width=\\'2\\' d=\\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\\'></path></svg>';">`
                                                    : `<svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                           </svg>`
                                                }
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate" title="${item.name}">
                                                    ${item.name}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                                    Sold: ${item.quantity.toLocaleString()} units
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right ml-3 flex-shrink-0">
                                            <div class="text-base font-bold text-gray-800 dark:text-gray-100 whitespace-nowrap">
                                                ${item.quantity.toLocaleString()}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">PCS</div>
                                        </div>
                                    </div>
                                `).join('');
                            } else {
                                container.innerHTML = `
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <p class="text-sm">No sales data available for this period</p>
                                    </div>
                                `;
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching top selling items:', error);
                            container.innerHTML = `
                                <div class="text-center py-8 text-red-500 dark:text-red-400">
                                    <p class="text-sm">Error loading data. Please try again.</p>
                                </div>
                            `;
                        });
                });
            }

            // Top Purchase Items Period Filter
            const topPurchasePeriodSelect = document.getElementById('topPurchasePeriod');

            if (topPurchasePeriodSelect) {
                topPurchasePeriodSelect.addEventListener('change', function() {
                    const period = this.value;
                    const selectedText = this.options[this.selectedIndex].text;
                    const container = document.getElementById('topPurchaseItemsContainer');

                    // Show loading state
                    container.innerHTML = `
                        <div class="flex justify-center items-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600"></div>
                        </div>
                    `;

                    // Fetch new data
                    fetch('/dashboard/top-purchase-items?period=' + period)
                        .then(response => response.json())
                        .then(data => {
                            if (data.items && data.items.length > 0) {
                                container.innerHTML = data.items.map(item => `
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors flex-shrink-0">
                                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                                            <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center overflow-hidden">
                                                ${item.image 
                                                    ? `<img src="/storage/${item.image}" 
                                                               alt="${item.name}" 
                                                               class="w-full h-full object-cover"
                                                               onerror="this.onerror=null; this.parentElement.innerHTML='<svg class=\\'w-8 h-8 text-white\\' fill=\\'none\\' stroke=\\'currentColor\\' viewBox=\\'0 0 24 24\\'><path stroke-linecap=\\'round\\' stroke-linejoin=\\'round\\' stroke-width=\\'2\\' d=\\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\\'></path></svg>';">`
                                                    : `<svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                           </svg>`
                                                }
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate" title="${item.name}">
                                                    ${item.name}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                                    Purchased: ${item.quantity.toLocaleString()} units
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right ml-3 flex-shrink-0">
                                            <div class="text-base font-bold text-gray-800 dark:text-gray-100 whitespace-nowrap">
                                                ${item.quantity.toLocaleString()}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">PCS</div>
                                        </div>
                                    </div>
                                `).join('');
                            } else {
                                container.innerHTML = `
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8.5m-8.5 0a2 2 0 11-4 0 2 2 0 014 0zm8.5 0a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <p class="text-sm">No purchase data available for this period</p>
                                    </div>
                                `;
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching top purchase items:', error);
                            container.innerHTML = `
                                <div class="text-center py-8 text-red-500 dark:text-red-400">
                                    <p class="text-sm">Error loading data. Please try again.</p>
                                </div>
                            `;
                        });
                });
            }
        });
    </script>
</x-app-layout>
