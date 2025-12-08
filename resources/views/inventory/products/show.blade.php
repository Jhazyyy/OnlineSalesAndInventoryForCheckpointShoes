<x-app-layout>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $product->product_name }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">{{ $product->sku }}</p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            <a href="{{ route('inventory.products.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Back to Product List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-2">
                <!-- Product Image and Basic Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <!-- Product Image -->
                            @if ($product->image)
                                <img src="{{ $product->image_url }}" alt="{{ $product->product_name }}"
                                    class="w-full h-auto object-cover rounded-lg border mb-4">
                            @else
                                <div
                                    class="w-full h-48 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center border mb-4">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">No image</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Stock Status -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Stock
                                    Status</label>
                                @if ($product->quantity == 0)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Out
                                        of Stock</span>
                                @elseif($product->quantity <= 10 && $product->quantity > 5)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Low
                                        Stock</span>
                                @elseif($product->quantity <= 5)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-600">Critical
                                        Stock</span>
                                @else
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">In
                                        Stock</span>
                                @endif
                            </div>

                            <!-- Key Metrics -->
                            <div class="space-y-3">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400">Quantity</label>
                                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                                        {{ number_format($product->quantity) }}</p>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400">Price</label>
                                    <p class="text-xl font-bold text-green-600 dark:text-green-400">
                                        ₱{{ number_format($product->price, 2) }}</p>
                                </div>
                                {{-- <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Inventory
                                        Value</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        ₱{{ number_format($product->inventory_value, 2) }}</p>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Information</h3>

                            <div class="grid grid-cols-2 gap-4">
                                @if ($product->stock_name)
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-500 dark:text-gray-400">Stock
                                            Name</label>
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $product->stock_name }}
                                        </p>
                                    </div>
                                @endif

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400">Product
                                        Name</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $product->product_name }}
                                    </p>
                                </div>

                                @if ($product->size)
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-500 dark:text-gray-400">Size</label>
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $product->size }}</p>
                                    </div>
                                @endif

                                @if ($product->color)
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-500 dark:text-gray-400">Color</label>
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $product->color }}</p>
                                    </div>
                                @endif

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400">Brand</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $product->product_brand }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400">Category</label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $product->product_category }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">SKU</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $product->sku }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Product ID</label>
                                    <p class="text-sm text-gray-900 dark:text-white">#{{ $product->product_id }}</p>
                                </div>
                            </div>

                            @if ($product->description)
                                <div class="mt-4">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400">Description</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $product->description }}</p>
                                </div>
                            @endif

                            <!-- Timestamps -->
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="grid grid-cols-2 gap-4 text-xs text-gray-500 dark:text-gray-400">
                                    <div>
                                        <span class="font-medium">Created:</span> {{ $product->created_at->format('M d, Y h:i A') }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Last Updated:</span> {{ $product->updated_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Supplier Information -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Supplier Info</h3>

                            <div class="space-y-4">
                                @if ($product->suppliers && $product->suppliers->count() > 0)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Assigned Suppliers</label>
                                        <div class="space-y-2">
                                            @foreach ($product->suppliers as $supplier)
                                                <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $supplier->supplier_name }}
                                                        @if ($supplier->pivot->is_primary)
                                                            <span class="ml-1 px-1.5 py-0.5 text-xs font-semibold rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Primary</span>
                                                        @endif
                                                    </p>
                                                    @if ($supplier->pivot->cost)
                                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                                            Cost: ₱{{ number_format($supplier->pivot->cost, 2) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if ($product->lastSupplier)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Last
                                            Supplier</label>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $product->lastSupplier->supplier_name ?? $product->lastSupplier->name }}
                                        </p>
                                        @if ($product->last_received_at)
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Last received:
                                                {{ $product->last_received_at->format('M d, Y') }}</p>
                                        @endif
                                    </div>
                                @endif

                                @if ($product->last_purchase_price)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Last
                                            Purchase Price</label>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            ₱{{ number_format($product->last_purchase_price, 2) }}</p>
                                        @if ($product->price > $product->last_purchase_price)
                                            <p class="text-xs text-green-600">Margin:
                                                ₱{{ number_format($product->price - $product->last_purchase_price, 2) }}
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                @if (!$product->lastSupplier && (!$product->suppliers || $product->suppliers->count() == 0))
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No supplier information
                                        available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
