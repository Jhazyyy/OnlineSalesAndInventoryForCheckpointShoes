<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Inventory Thresholds</h2>
                            <p class="text-gray-600 dark:text-gray-400">Manage product reorder levels and stock alerts</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            {{-- <a href="{{ route('inventory.thresholds.alerts') }}" 
                               class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L5.732 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                View Alerts ({{ $stats['active_alerts'] }})
                            </a> --}}
                            {{-- <a href="{{ route('inventory.thresholds.export') }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                </svg>
                                Export Data
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Products</div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_products']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">With Thresholds</div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['products_with_thresholds']) }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['threshold_coverage'] }}% coverage</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L5.732 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Alerts</div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['active_alerts']) }}</div>
                                <div class="text-xs text-red-500">{{ number_format($stats['critical_alerts']) }} critical</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Auto Reorder</div>
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['auto_reorder_enabled']) }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">products enabled</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('inventory.thresholds.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search Products</label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                                       placeholder="Search by name or brand..." 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock Status</label>
                                <select id="status" name="status" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Status</option>
                                    @foreach($filterOptions['status_options'] as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sort -->
                            <div>
                                <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort By</label>
                                <select id="sort" name="sort" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="product_name" {{ request('sort') == 'product_name' ? 'selected' : '' }}>Product Name</option>
                                    <option value="product_brand" {{ request('sort') == 'product_brand' ? 'selected' : '' }}>Brand</option>
                                    <option value="quantity" {{ request('sort') == 'quantity' ? 'selected' : '' }}>Current Stock</option>
                                    <option value="reorder_level" {{ request('sort') == 'reorder_level' ? 'selected' : '' }}>Reorder Level</option>
                                    <option value="critical_level" {{ request('sort') == 'critical_level' ? 'selected' : '' }}>Critical Level</option>
                                </select>
                            </div>

                            <!-- Order -->
                            <div>
                                <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order</label>
                                <select id="order" name="order" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                    <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Descending</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex space-x-2">
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Filter
                                </button>
                                <a href="{{ route('inventory.thresholds.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Clear
                                </a>
                                <button type="button" onclick="runThresholdCheck()" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Run Check
                                </button>
                            </div>
                        </div>
                    </form>
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

            @if(session('warning'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('warning') }}</span>
                </div>
            @endif

            <!-- Products Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($products->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Current Stock</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reorder Level</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Critical Level</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Preferred Supplier</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($products as $product)
                                        @php
                                            $stockStatus = $product->getStockStatus();
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $product->sku }}</div>
                                                <div class="text-sm text-gray-700 dark:text-gray-300">{{ $product->product_name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $product->product_brand }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ number_format($product->quantity) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $product->reorder_level ? number_format($product->reorder_level) : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $product->critical_level ? number_format($product->critical_level) : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusClass = $stockStatus['status_class'] ?? 'green';
                                                    $statusText = $stockStatus['status'] ?? 'In Stock';
                                                    $statusColors = [
                                                        'red' => 'bg-red-100 text-red-800',
                                                        'yellow' => 'bg-yellow-100 text-yellow-800',
                                                        'green' => 'bg-green-100 text-green-800'
                                                    ];
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$statusClass] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $product->preferredSupplier?->supplier_name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <a href="{{ route('inventory.thresholds.show', $product) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</a>
                                                <button onclick="openEditThresholdModal({{ $product->product_id }})" 
                                                   class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">Edit</button>
                                                <a href="{{ route('master_data.products.show', $product) }}" 
                                                   class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">Product Details</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m0 0l3-3m-3 3l3 3M15 8h3"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No products found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search criteria.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Threshold Modal -->
    <div id="editThresholdModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-3/4 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Edit Product Threshold
                    </h3>
                    <button onclick="closeEditThresholdModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="editThresholdForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mt-4 space-y-4">
                        <!-- Product Info -->
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2" id="modalProductName">Loading...</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400" id="modalProductBrand"></p>
                        </div>

                        <!-- Threshold Levels -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="modal_reorder_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Reorder Level
                                </label>
                                <input type="number" min="0" id="modal_reorder_level" name="reorder_level"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="modal_critical_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Critical Level
                                </label>
                                <input type="number" min="0" id="modal_critical_level" name="critical_level"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="modal_ceiling_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Ceiling Level
                                </label>
                                <input type="number" min="0" id="modal_ceiling_level" name="ceiling_level"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="modal_floor_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Floor Level
                                </label>
                                <input type="number" min="0" id="modal_floor_level" name="floor_level"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <!-- EOQ / Lead Time / Supplier -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="modal_economic_order_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Economic Order Quantity
                                </label>
                                <input type="number" min="1" id="modal_economic_order_quantity" name="economic_order_quantity"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="modal_lead_time_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Lead Time (days)
                                </label>
                                <input type="number" min="1" id="modal_lead_time_days" name="lead_time_days"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="modal_preferred_supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Preferred Supplier
                                </label>
                                <select id="modal_preferred_supplier_id" name="preferred_supplier_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">— None —</option>
                                    <!-- Suppliers will be loaded dynamically -->
                                </select>
                            </div>
                        </div>

                        <!-- Toggles -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="inline-flex items-center">
                                <input type="hidden" name="auto_reorder_enabled" value="0" />
                                <input type="checkbox" id="modal_auto_reorder_enabled" name="auto_reorder_enabled" value="1"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Enable Auto Reorder</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="hidden" name="threshold_alerts_enabled" value="0" />
                                <input type="checkbox" id="modal_threshold_alerts_enabled" name="threshold_alerts_enabled" value="1"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Enable Alerts</span>
                            </label>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Threshold Guidelines</h3>
                                    <p class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                                        Set appropriate threshold levels to manage inventory efficiently. The system will alert you when stock levels reach these thresholds.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t dark:border-gray-700">
                        <button type="button" onclick="closeEditThresholdModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let suppliers = [];
        
        function openEditThresholdModal(productId) {
            // Show loading state
            document.getElementById('modalProductName').textContent = 'Loading...';
            document.getElementById('modalProductBrand').textContent = '';
            
            // Show modal
            document.getElementById('editThresholdModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Fetch product data
            fetch(`/inventory/thresholds/${productId}/edit`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update form action
                document.getElementById('editThresholdForm').action = `/inventory/thresholds/${productId}`;
                
                // Populate product info
                document.getElementById('modalProductName').textContent = data.product.product_name;
                document.getElementById('modalProductBrand').textContent = data.product.product_brand || '';
                
                // Populate form fields
                document.getElementById('modal_reorder_level').value = data.product.reorder_level || '';
                document.getElementById('modal_critical_level').value = data.product.critical_level || '';
                document.getElementById('modal_ceiling_level').value = data.product.ceiling_level || '';
                document.getElementById('modal_floor_level').value = data.product.floor_level || '';
                document.getElementById('modal_economic_order_quantity').value = data.product.economic_order_quantity || '';
                document.getElementById('modal_lead_time_days').value = data.product.lead_time_days || '';
                document.getElementById('modal_auto_reorder_enabled').checked = data.product.auto_reorder_enabled || false;
                document.getElementById('modal_threshold_alerts_enabled').checked = data.product.threshold_alerts_enabled || false;
                
                // Populate suppliers dropdown
                const supplierSelect = document.getElementById('modal_preferred_supplier_id');
                supplierSelect.innerHTML = '<option value="">— None —</option>';
                data.suppliers.forEach(supplier => {
                    const option = document.createElement('option');
                    option.value = supplier.supplier_id;
                    option.textContent = supplier.supplier_name;
                    option.selected = data.product.preferred_supplier_id == supplier.supplier_id;
                    supplierSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load product data. Please try again.');
                closeEditThresholdModal();
            });
        }

        function closeEditThresholdModal() {
            document.getElementById('editThresholdModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            // Reset form
            document.getElementById('editThresholdForm').reset();
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('editThresholdModal');
                if (!modal.classList.contains('hidden')) {
                    closeEditThresholdModal();
                }
            }
        });

        // Close modal on outside click
        document.getElementById('editThresholdModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeEditThresholdModal();
            }
        });

        function runThresholdCheck() {
            if (confirm('Run threshold check for all products? This may generate new alerts.')) {
                fetch('{{ route("inventory.thresholds.run-check") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while running the threshold check.');
                });
            }
        }
    </script>
</x-app-layout>