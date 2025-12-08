<x-app-layout>
    <div class="w-full min-h-screen">
        <!-- Main Content Area -->
        <div class="h-full overflow-y-auto">
            <div class="bg-gray-50 dark:bg-gray-900 min-h-full flex flex-col">
                <div class="flex-1 p-6 text-gray-900 dark:text-gray-100">
                    {{-- <h2 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-100">Dashboard</h2> --}}

                    <!-- Main Dashboard Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-2">

                        <!-- Left Column:Item Details -->
                        <div class="lg:col-span-2">
                            <!-- Item Details Section -->
                            <div
                                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Product Details
                                </h3>

                                <div class="grid grid-cols-4 gap-6">
                                    <!-- Low Stock Items -->
                                    <div class="text-left">
                                        <div class="text-sm text-yellow-600 dark:text-yellow-400 font-medium mb-2">Low
                                            Stock
                                            Items</div>
                                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                            {{ $inventoryStats['low_stock_products'] ?? 0 }}
                                        </div>
                                    </div>

                                    <!-- Low Stock Items -->
                                    <div class="text-left">
                                        <div class="text-sm text-red-600 dark:text-red-400 font-medium mb-2">Out of
                                            Stock
                                            Items</div>
                                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                            {{ $inventoryStats['out_of_stock_products'] ?? 0 }}
                                        </div>
                                    </div>

                                    <!-- All Item Groups -->
                                    <div class="text-left">
                                        <div class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-2">All Item
                                            Groups</div>
                                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                            {{ \App\Models\Product::distinct('product_category')->count('category') }}
                                        </div>
                                    </div>

                                    <!-- All Items -->
                                    <div class="text-left">
                                        <div class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-2">All Items
                                        </div>
                                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                            {{ $inventoryStats['total_products'] ?? 0 }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Active Items Donut Chart -->
                                <div class="mt-6">
                                    <div class="flex items-center">
                                        <div class="relative" style="width: 90px; height: 90px;">
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
                                            {{-- <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                                Out of {{ $inventoryStats['total_products'] ?? 0 }} total items
                                            </div> --}}
                                        </div>
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
                                <div class="mb-3 pb-6 border-b border-gray-200 dark:border-gray-700">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-4">
                                        Quantity In Hand
                                    </div>
                                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                        {{ number_format(\App\Models\Product::sum('quantity') ?? 0) }}
                                    </div>
                                </div>

                                <!-- Quantity to be Received -->
                                <div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-2">
                                        Quantity to be Received
                                    </div>
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ number_format($purchaseReceiveStats['pending_quantity'] ?? 0) }}
                                    </div>

                                    <!-- Breakdown of pending quantities -->
                                    {{-- <div class="mt-4 space-y-2">
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Approved</span>
                                            <span class="font-semibold text-gray-800 dark:text-gray-100">
                                                {{ number_format($purchaseReceiveStats['approved_quantity'] ?? 0) }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">To Be Approved</span>
                                            <span class="font-semibold text-gray-800 dark:text-gray-100">
                                                {{ number_format($purchaseReceiveStats['to_be_approved_quantity'] ?? 0) }}
                                            </span>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Trend and Period Statistics Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-2 mt-2">
                        <!-- Revenue Trend Chart -->
                        <div class="lg:col-span-2">
                            <div
                                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                <div
                                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                        Revenue Trend
                                    </h3>
                                    <select id="revenueTrendPeriod"
                                        class="appearance-none w-full lg:w-32 sm:w-auto text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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

                                <div id="revenueTrendContainer" style="height: 250px; position: relative;">
                                    <canvas id="revenueTrendChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Period Statistics Card -->
                        <div class="lg:col-span-1">
                            <div>
                                <div
                                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Period
                                        Statistics</h3>
                                    <!-- Total Revenue -->
                                    <div
                                        class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg mb-2">
                                        <div class="flex items-center">
                                            <div class="p-2 bg-green-500 rounded-lg mr-3">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 8h4a3 3 0 0 1 0 6H9m0-6v10m0-10V6m0 4h7m-7 4h7M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-xs text-gray-600 dark:text-gray-400">Total Revenue
                                                </div>
                                                <div class="text-xs font-bold text-gray-800 dark:text-gray-100"
                                                    id="periodTotalRevenue">₱0.00</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total Profit -->
                                    <div
                                        class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg mb-2">
                                        <div class="flex items-center">
                                            <div class="p-2 bg-blue-500 rounded-lg mr-3">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-xs text-gray-600 dark:text-gray-400">Total Profit
                                                </div>
                                                <div class="text-xs font-bold text-gray-800 dark:text-gray-100"
                                                    id="periodTotalProfit">₱0.00</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total Sales -->
                                    <div
                                        class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg mb-2">
                                        <div class="flex items-center">
                                            <div class="p-2 bg-purple-500 rounded-lg mr-3">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-xs text-gray-600 dark:text-gray-400">Total Sales</div>
                                                <div class="text-xs font-bold text-gray-800 dark:text-gray-100"
                                                    id="periodTotalSales">0</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total Items Sold -->
                                    <div
                                        class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg mb-2">
                                        <div class="flex items-center">
                                            <div class="p-2 bg-orange-500 rounded-lg mr-3">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-xs text-gray-600 dark:text-gray-400">Total Items Sold
                                                </div>
                                                <div class="text-xs font-bold text-gray-800 dark:text-gray-100"
                                                    id="periodTotalItems">0</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Selling, Purchase Items & Recent Activity Section (Full Width) -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-2 mt-2">
                        <!-- Top Selling Items Section -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4 flex-shrink-0">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                    Top Selling Items
                                </h3>
                                <select id="topSellingPeriod"
                                    class="appearance-none lg:w-32 w-full sm:w-auto text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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

                            <div id="topSellingItemsContainer" class="space-y-3 overflow-y-auto flex-1"
                                style="max-height: 400px;">
                                @forelse($topSellingItems->take(100) ?? [] as $item)
                                    <div
                                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors flex-shrink-0">
                                        <div class="flex items-center space-x-2 flex-1 min-w-0">
                                            <!-- Product Image -->
                                            <div
                                                class="flex-shrink-0 w-5 h-5 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center overflow-hidden">
                                                @if (!empty($item['image']))
                                                    @php
                                                        $imageUrl =
                                                            str_starts_with($item['image'], 'http://') ||
                                                            str_starts_with($item['image'], 'https://')
                                                                ? $item['image']
                                                                : asset('storage/' . $item['image']);
                                                    @endphp
                                                    <img src="{{ $imageUrl }}"
                                                        alt="{{ $item['name'] ?? 'Product' }}"
                                                        class="w-5 h-5 object-cover"
                                                        onerror="this.onerror=null; this.parentElement.innerHTML='<svg class=\'w-5 h-5 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\'></path></svg>';">
                                                @else
                                                    <svg class="w-5 h-5 text-white" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                        </path>
                                                    </svg>
                                                @endif
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-semibold text-gray-800 dark:text-gray-100 break-words"
                                                    title="{{ $item['name'] ?? 'Unknown Product' }}">
                                                    {{ $item['name'] ?? 'Unknown Product' }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $item['sku'] ?? '' }}
                                                    @if (!empty($item['size']) || !empty($item['color']))
                                                        <span class="mx-1">|</span>
                                                        @if (!empty($item['size']))
                                                            <span>{{ $item['size'] }}</span>
                                                        @endif
                                                        @if (!empty($item['color']))
                                                            @if (!empty($item['size']))
                                                                <span class="mx-1">|</span>
                                                            @endif
                                                            <span>{{ $item['color'] }}</span>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                                    Qty Sold: {{ number_format($item['quantity'] ?? 0) }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div class="text-right ml-3 flex-shrink-0">
                                                    <div
                                                        class="text-base font-bold text-gray-800 dark:text-gray-100 whitespace-nowrap">
                                                        {{ number_format($item['quantity'] ?? 0) }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">PCS</div>
                                                </div> --}}
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <svg class="w-5 h-5 mx-auto mb-2 opacity-50" fill="none"
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
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col ">
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4 flex-shrink-0">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                    Top Purchase Items
                                </h3>
                                <select id="topPurchasePeriod"
                                    class="appearance-none lg:w-32 w-full sm:w-auto text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent break-words">
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

                            <div id="topPurchaseItemsContainer" class="space-y-3 overflow-y-auto flex-1"
                                style="max-height: 400px;">
                                @forelse($topPurchaseItems->take(100) ?? [] as $item)
                                    <div
                                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors flex-shrink-0">
                                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                                            <!-- Product Image -->
                                            <div
                                                class="flex-shrink-0 w-5 h-5 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center overflow-hidden">
                                                @if (!empty($item['image']))
                                                    @php
                                                        $imageUrl =
                                                            str_starts_with($item['image'], 'http://') ||
                                                            str_starts_with($item['image'], 'https://')
                                                                ? $item['image']
                                                                : asset('storage/' . $item['image']);
                                                    @endphp
                                                    <img src="{{ $imageUrl }}"
                                                        alt="{{ $item['name'] ?? 'Product' }}"
                                                        class="w-5 h-5 object-cover"
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
                                                <div class="text-xs font-semibold text-gray-800 dark:text-gray-100 break-words"
                                                    title="{{ $item['name'] ?? 'Unknown Product' }}">
                                                    {{ $item['name'] ?? 'Unknown Product' }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $item['sku'] ?? '' }}
                                                    @if (!empty($item['size']) || !empty($item['color']))
                                                        <span class="mx-1">|</span>
                                                        @if (!empty($item['size']))
                                                            <span>{{ $item['size'] }}</span>
                                                        @endif
                                                        @if (!empty($item['color']))
                                                            @if (!empty($item['size']))
                                                                <span class="mx-1">|</span>
                                                            @endif
                                                            <span>{{ $item['color'] }}</span>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 break-words">
                                                    Qty Purchased: {{ number_format($item['quantity'] ?? 0) }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div class="text-right ml-3 flex-shrink-0">
                                                    <div
                                                        class="text-base font-bold text-gray-800 dark:text-gray-100 whitespace-nowrap">
                                                        {{ number_format($item['quantity'] ?? 0) }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">PCS</div>
                                                </div> --}}
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <svg class="w-5 h-5 mx-auto mb-2 opacity-50" fill="none"
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

                        <!-- Recent Activity Section -->
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                            <div class="mb-4 flex-shrink-0">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                    Recent Activity
                                </h3>
                            </div>

                            <div class="space-y-3 overflow-y-auto flex-1" style="max-height: 400px;">
                                @php
                                    $recentActivities = \App\Models\AuditLog::with('user')
                                        ->orderBy('created_at', 'desc')
                                        ->take(20)
                                        ->get();
                                @endphp

                                @forelse($recentActivities as $activity)
                                    <div
                                        class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors flex-shrink-0">
                                        <div class="flex-shrink-0">
                                            @if ($activity->action === 'create')
                                                <div
                                                    class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                </div>
                                            @elseif($activity->action === 'update')
                                                <div
                                                    class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @elseif($activity->action === 'delete')
                                                <div
                                                    class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div
                                                    class="w-8 h-8 bg-gray-100 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-800 dark:text-gray-100">
                                                {{ $activity->user->name ?? 'System' }}
                                                <span
                                                    class="text-gray-500 dark:text-gray-400">{{ $activity->action }}</span>
                                                <span
                                                    class="text-gray-600 dark:text-gray-300">{{ $activity->record_name ?? $activity->model_type }}</span>
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                                {{ $activity->description }}
                                            </p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                            </path>
                                        </svg>
                                        <p class="text-sm">No recent activity</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
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
                    {{-- <div
                                class="hidden md:flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                <a href="#"
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">About</a>
                                <a href="#"
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Support</a>
                                <a href="#"
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Documentation</a>
                                <a href="#"
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Privacy</a>
                            </div> --}}
                </div>

                <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-4 mt-4 md:mt-0">
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
                    <a href="#" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">About</a>
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

            // Revenue Trend Chart
            let revenueTrendChart = null;

            function loadRevenueTrend(period = 'this_month') {
                console.log('Loading revenue trend for period:', period);
                const container = document.getElementById('revenueTrendContainer');
                const canvas = document.getElementById('revenueTrendChart');

                if (!canvas) {
                    console.error('Canvas element not found');
                    return;
                }

                // Show loading state
                const loadingDiv = document.createElement('div');
                loadingDiv.className =
                    'absolute inset-0 flex justify-center items-center bg-white dark:bg-gray-800 bg-opacity-90';
                loadingDiv.innerHTML = `
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                `;
                container.appendChild(loadingDiv);

                // Fetch revenue data
                fetch('/dashboard/revenue-trend?period=' + period)
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            throw new Error('HTTP error ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Revenue data received:', data);
                        // Remove loading state
                        loadingDiv.remove();

                        // Destroy existing chart
                        if (revenueTrendChart) {
                            revenueTrendChart.destroy();
                        }

                        // Create new chart
                        revenueTrendChart = new Chart(canvas, {
                            type: 'line',
                            data: {
                                labels: data.labels || [],
                                datasets: [{
                                    label: 'Revenue',
                                    data: data.data || [],
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 3,
                                    pointHoverRadius: 5,
                                    pointBackgroundColor: 'rgb(59, 130, 246)',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                        padding: 12,
                                        titleColor: '#fff',
                                        bodyColor: '#fff',
                                        borderColor: 'rgb(59, 130, 246)',
                                        borderWidth: 1,
                                        displayColors: false,
                                        callbacks: {
                                            label: function(context) {
                                                return 'Revenue: ₱' + context.parsed.y
                                                    .toLocaleString('en-US', {
                                                        minimumFractionDigits: 2,
                                                        maximumFractionDigits: 2
                                                    });
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return '₱' + value.toLocaleString('en-US');
                                            },
                                            color: 'rgb(107, 114, 128)'
                                        },
                                        grid: {
                                            color: 'rgba(107, 114, 128, 0.1)'
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            color: 'rgb(107, 114, 128)',
                                            maxRotation: 45,
                                            minRotation: 0
                                        },
                                        grid: {
                                            display: false
                                        }
                                    }
                                }
                            }
                        });
                        console.log('Chart created successfully');
                    })
                    .catch(error => {
                        console.error('Error fetching revenue trend:', error);
                        loadingDiv.innerHTML = `
                            <div class="text-center text-red-500 dark:text-red-400">
                                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm">Error loading revenue data</p>
                                <p class="text-xs mt-1">${error.message}</p>
                            </div>
                        `;
                    });
            }

            // Initialize revenue trend chart
            const revenueTrendPeriodSelect = document.getElementById('revenueTrendPeriod');
            const initialPeriod = revenueTrendPeriodSelect ? revenueTrendPeriodSelect.value : 'this_month';

            console.log('Initializing dashboard with period:', initialPeriod);
            console.log('Revenue trend select element:', revenueTrendPeriodSelect);

            loadRevenueTrend(initialPeriod);

            // Load Period Statistics
            function loadPeriodStatistics(period = 'this_month') {
                console.log('Loading period statistics for:', period);
                console.log('Current timestamp:', new Date().toISOString());

                // Check if elements exist
                const revenueEl = document.getElementById('periodTotalRevenue');
                const profitEl = document.getElementById('periodTotalProfit');
                const salesEl = document.getElementById('periodTotalSales');
                const itemsEl = document.getElementById('periodTotalItems');

                console.log('Elements found:', {
                    revenueEl: !!revenueEl,
                    profitEl: !!profitEl,
                    salesEl: !!salesEl,
                    itemsEl: !!itemsEl
                });

                if (!revenueEl || !profitEl || !salesEl || !itemsEl) {
                    console.error('Period statistics elements not found!');
                    setTimeout(() => loadPeriodStatistics(period), 1000); // Retry after 1 second
                    return;
                }

                fetch('/dashboard/period-statistics?period=' + period, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || ''
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            if (response.status === 401) {
                                throw new Error('Authentication required');
                            }
                            throw new Error('HTTP error ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Period statistics received:', data);

                        // Update Total Revenue
                        revenueEl.textContent =
                            '₱' + parseFloat(data.total_revenue || 0).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });

                        // Update Total Profit
                        profitEl.textContent =
                            '₱' + parseFloat(data.total_profit || 0).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });

                        // Update Total Sales
                        salesEl.textContent =
                            parseInt(data.total_sales || 0).toLocaleString('en-US');

                        // Update Total Items Sold
                        itemsEl.textContent =
                            parseInt(data.total_items || 0).toLocaleString('en-US');
                    })
                    .catch(error => {
                        console.error('Error fetching period statistics:', error);
                        // Set to 0 on error
                        revenueEl.textContent = '₱0.00';
                        profitEl.textContent = '₱0.00';
                        salesEl.textContent = '0';
                        itemsEl.textContent = '0';
                    });
            }

            // Initialize period statistics with the same period as revenue trend
            loadPeriodStatistics(initialPeriod);

            // Revenue Trend Period Filter
            if (revenueTrendPeriodSelect) {
                console.log('Setting up change listener for revenue trend select');
                revenueTrendPeriodSelect.addEventListener('change', function() {
                    const period = this.value;
                    console.log('Revenue trend period changed to:', period);
                    loadRevenueTrend(period);
                    loadPeriodStatistics(period);
                });
            } else {
                console.error('Revenue trend period select element not found!');
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
                                            <div class="flex-shrink-0 w-5 h-5 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center overflow-hidden">
                                                ${item.image 
                                                    ? `<img src="/storage/${item.image}" 
                                                                                                               alt="${item.name}" 
                                                                                                               class="w-5 h-5 object-cover"
                                                                                                               onerror="this.onerror=null; this.parentElement.innerHTML='<svg class=\\'w-5 h-5 text-white\\' fill=\\'none\\' stroke=\\'currentColor\\' viewBox=\\'0 0 24 24\\'><path stroke-linecap=\\'round\\' stroke-linejoin=\\'round\\' stroke-width=\\'2\\' d=\\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\\'></path></svg>';">`
                                                    : `<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                                                                           </svg>`
                                                }
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-semibold text-gray-800 dark:text-gray-100 break-words" title="${item.name}">
                                                    ${item.name}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    ${item.sku || 'N/A'}
                                                    ${(item.size || item.color) ? '<span class="mx-1">•</span>' : ''}
                                                    ${item.size ? item.size : ''}
                                                    ${(item.size && item.color) ? '<span class="mx-1">/</span>' : ''}
                                                    ${item.color ? item.color : ''}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                                    Qty Sold: ${item.quantity.toLocaleString()}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `).join('');
                            } else {
                                container.innerHTML = `
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <svg class="w-5 h-5 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-yellow-600"></div>
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
                                            <div class="flex-shrink-0 w-5 h-5 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center overflow-hidden">
                                                ${item.image 
                                                    ? `<img src="/storage/${item.image}" 
                                                                                                               alt="${item.name}" 
                                                                                                               class="w-5 h-5 object-cover"
                                                                                                               onerror="this.onerror=null; this.parentElement.innerHTML='<svg class=\\'w-5 h-5 text-white\\' fill=\\'none\\' stroke=\\'currentColor\\' viewBox=\\'0 0 24 24\\'><path stroke-linecap=\\'round\\' stroke-linejoin=\\'round\\' stroke-width=\\'2\\' d=\\'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\\'></path></svg>';">`
                                                    : `<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                                                                           </svg>`
                                                }
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-semibold text-gray-800 dark:text-gray-100 break-words" title="${item.name}">
                                                    ${item.name}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    ${item.sku || 'N/A'}
                                                    ${(item.size || item.color) ? '<span class="mx-1">|</span>' : ''}
                                                    ${item.size ? item.size : ''}
                                                    ${(item.size && item.color) ? '<span class="mx-1">|</span>' : ''}
                                                    ${item.color ? item.color : ''}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 break-words">
                                                    Qty Purchased: ${item.quantity.toLocaleString()}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `).join('');
                            } else {
                                container.innerHTML = `
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <svg class="w-5 h-5 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
