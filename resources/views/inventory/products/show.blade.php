<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $product->product_name }}</h2>
                                <p class="text-gray-600 dark:text-gray-400">{{ $product->product_brand }}</p>
                            </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            <a href="{{ route('inventory.products.edit', $product) }}"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Edit Product
                            </a>
                            <a href="{{ route('inventory.products.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Product Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Product Image -->
                                <div>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->product_name }}"
                                            class="w-full h-64 object-cover rounded-lg border">
                                    @else
                                        <div
                                            class="w-full h-64 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center border">
                                            <div class="text-center">
                                                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No image available
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Details -->
                                <div class="space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-500 dark:text-gray-400">Product
                                            Name</label>
                                        <p class="text-lg text-gray-900 dark:text-white">{{ $product->product_name }}
                                        </p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-500 dark:text-gray-400">Brand</label>
                                        <p class="text-lg text-gray-900 dark:text-white">{{ $product->product_brand }}
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-500 dark:text-gray-400">Quantity</label>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ number_format($product->quantity) }}</p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-500 dark:text-gray-400">Price</label>
                                            <p class="text-lg font-semibold text-green-600 dark:text-green-400">
                                                ₱{{ number_format($product->price, 2) }}</p>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Stock
                                            Status</label>
                                        @if($product->quantity <= 0)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                <span class="w-2 h-2 mr-1 bg-red-500 rounded-full"></span>
                                                Out of Stock
                                            </span>
                                        @elseif($product->quantity <= 10)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                <span class="w-2 h-2 mr-1 bg-yellow-500 rounded-full"></span>
                                                Low Stock
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <span class="w-2 h-2 mr-1 bg-green-500 rounded-full"></span>
                                                In Stock
                                            </span>
                                        @endif
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-500 dark:text-gray-400">Inventory
                                            Value</label>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                            ₱{{ number_format($product->inventory_value, 2) }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($product->description)
                                <div class="mt-6">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400">Description</label>
                                    <p class="mt-1 text-gray-900 dark:text-white">{{ $product->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recent Activity</h3>

                            <div class="space-y-4">
                                @if($recentSales->count() > 0)
                                    <div>
                                        <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Recent Sales
                                        </h4>
                                        <div class="space-y-2">
                                            @foreach($recentSales->take(3) as $sale)
                                                <div class="flex items-center justify-between text-sm">
                                                    <span
                                                        class="text-gray-600 dark:text-gray-400">{{ $sale->date->format('M d, Y') }}</span>
                                                    <span class="text-red-600 dark:text-red-400">-{{ $sale->quantity }}
                                                        units</span>
                                                    <span
                                                        class="text-green-600 dark:text-green-400">₱{{ number_format($sale->total_amount, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($recentPurchases->count() > 0)
                                    <div>
                                        <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Recent
                                            Purchases</h4>
                                        <div class="space-y-2">
                                            @foreach($recentPurchases->take(3) as $purchase)
                                                <div class="flex items-center justify-between text-sm">
                                                    <span
                                                        class="text-gray-600 dark:text-gray-400">{{ $purchase->purchase_date->format('M d, Y') }}</span>
                                                    <span class="text-green-600 dark:text-green-400">+{{ $purchase->quantity }}
                                                        units</span>
                                                    <span
                                                        class="text-blue-600 dark:text-blue-400">₱{{ number_format($purchase->total_amount, 2) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($recentSales->count() == 0 && $recentPurchases->count() == 0)
                                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No recent activity</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Sidebar -->
                <div class="space-y-6">
                    <!-- Supplier Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Supplier Information</h3>
                            
                            <div class="space-y-4">
                                @if($product->lastSupplier)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Supplier</label>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->lastSupplier->supplier_name ?? $product->lastSupplier->name }}</p>
                                        @if($product->last_received_at)
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Last received: {{ $product->last_received_at->format('M d, Y') }}</p>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($product->preferredSupplier)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Preferred Supplier</label>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->preferredSupplier->supplier_name ?? $product->preferredSupplier->name }}</p>
                                    </div>
                                @endif
                                
                                @if($product->last_purchase_price)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Purchase Price</label>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">₱{{ number_format($product->last_purchase_price, 2) }}</p>
                                        @if($product->price > $product->last_purchase_price)
                                            <p class="text-xs text-green-600">Margin: ₱{{ number_format($product->price - $product->last_purchase_price, 2) }}</p>
                                        @endif
                                    </div>
                                @endif
                                
                                @if(!$product->lastSupplier && !$product->preferredSupplier)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No supplier information available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- Key Metrics -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Key Metrics</h3>

                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Total Sold</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($product->total_sold) }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Total Purchased</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($product->total_purchased) }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</span>
                                    <span
                                        class="text-sm font-medium text-green-600 dark:text-green-400">₱{{ number_format($product->total_revenue, 2) }}</span>
                                </div>

                                @if($product->profit_margin > 0)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Profit Margin</span>
                                        <span
                                            class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ number_format($product->profit_margin, 1) }}%</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Info</h3>

                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Product ID</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">#{{ $product->product_id }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Created</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->created_at->format('M d, Y') }}</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Last Updated</span>
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->updated_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>