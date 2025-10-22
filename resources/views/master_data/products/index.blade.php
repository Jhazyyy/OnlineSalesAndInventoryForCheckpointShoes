<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">

            <!-- Info Banner about Stock Management -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Inventory Management</h3>
                        <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                            Stock quantities shown below are managed through <strong>Stock Movements</strong>.
                            To adjust inventory, use <a href="{{ route('inventory.product_stock_adjustment.index') }}"
                                class="underline hover:text-blue-900 dark:hover:text-blue-200">Stock Adjustments</a>,
                            Purchase Receives, or Sales Orders.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Product Inventory</h2>
                            <p class="text-gray-600 dark:text-gray-400">Manage your product catalog</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('master_data.products.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                New Product
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('master_data.products.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                            <!-- Search -->
                            <div>
                                <label for="search"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    placeholder="Search products..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Brand Filter -->
                            <div>
                                <label for="brand"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Brand</label>
                                <select id="brand" name="brand"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Brands</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand }}"
                                            {{ request('brand') == $brand ? 'selected' : '' }}>
                                            {{ $brand }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Category Filter -->
                            <div>
                                <label for="category"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                <select id="category" name="category"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}"
                                            {{ request('category') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Stock Status -->
                            <div>
                                <label for="stock_status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock
                                    Status</label>
                                <select id="stock_status" name="stock_status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Stock</option>
                                    <option value="in_stock"
                                        {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                    <option value="low_stock"
                                        {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock
                                    </option>
                                    <option value="out_of_stock"
                                        {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock
                                    </option>
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div class="flex space-x-2">
                                <div class="flex-1">
                                    <label for="min_price"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Min
                                        Price</label>
                                    <input type="number" id="min_price" name="min_price"
                                        value="{{ request('min_price') }}" step="0.01" placeholder="0.00"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div class="flex-1">
                                    <label for="max_price"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max
                                        Price</label>
                                    <input type="number" id="max_price" name="max_price"
                                        value="{{ request('max_price') }}" step="0.01" placeholder="999.99"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex space-x-2">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Filter
                                </button>
                                <a href="{{ route('master_data.products.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Products Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($products->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Image</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <a
                                                href="{{ request()->fullUrlWithQuery(['sort' => 'product_name', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                                                Product Name
                                                @if (request('sort') === 'product_name')
                                                    <span
                                                        class="ml-1">{{ request('order') === 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <a
                                                href="{{ request()->fullUrlWithQuery(['sort' => 'product_brand', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                                                Brand
                                                @if (request('sort') === 'product_brand')
                                                    <span
                                                        class="ml-1">{{ request('order') === 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <a
                                                href="{{ request()->fullUrlWithQuery(['sort' => 'product_category', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                                                Category
                                                @if (request('sort') === 'product_category')
                                                    <span
                                                        class="ml-1">{{ request('order') === 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <a
                                                href="{{ request()->fullUrlWithQuery(['sort' => 'quantity', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                                                Quantity
                                                @if (request('sort') === 'quantity')
                                                    <span
                                                        class="ml-1">{{ request('order') === 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <a
                                                href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                                                Price
                                                @if (request('sort') === 'price')
                                                    <span
                                                        class="ml-1">{{ request('order') === 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        {{-- <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Last Supplier</th> --}}
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($products as $product)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            {{-- Product Image --}}
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}"
                                                        alt="{{ $product->product_name }}"
                                                        class="h-24 w-24 object-cover rounded-lg">
                                                @else
                                                    <div
                                                        class="h-16 w-16 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                        <svg class="h-8 w-8 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </td>
                                            {{-- Product Name and Description --}}
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $product->product_name }}</div>
                                                @if ($product->description)
                                                    <div
                                                        class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                                        {{ $product->description }}</div>
                                                @endif
                                            </td>
                                            {{-- Product Brand --}}
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $product->product_brand }}</td>
                                            {{-- Product Category --}}
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $product->product_category }}</td>
                                            {{-- Product Quantity --}}
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ number_format($product->quantity) }}</td>
                                            {{-- Product Price --}}
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                ₱{{ number_format($product->price, 2) }}</td>
                                            {{-- Last Supplier --}}
                                            {{-- <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($product->lastSupplier)
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $product->lastSupplier->supplier_name ?? $product->lastSupplier->name }}
                                                    </div>
                                                    @if ($product->last_received_at)
                                                        <div class="text-xs text-gray-500">
                                                            {{ $product->last_received_at->format('M d, Y') }}</div>
                                                    @endif
                                                @else
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                                @endif
                                            </td> --}}
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($product->quantity <= 0)
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Out
                                                        of Stock</span>
                                                @elseif($product->quantity <= 10)
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Low
                                                        Stock</span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">In
                                                        Stock</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <a href="{{ route('master_data.products.show', $product) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</a>
                                                <a href="{{ route('master_data.products.edit', $product) }}"
                                                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">Edit</a>
                                                <form method="POST"
                                                    action="{{ route('master_data.products.destroy', $product) }}"
                                                    class="inline-block"
                                                    onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                                </form>
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
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No products found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding your first
                                product.</p>
                            <div class="mt-6">
                                <a href="{{ route('master_data.products.create') }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Product
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
