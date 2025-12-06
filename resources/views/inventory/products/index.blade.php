<x-app-layout>
    <div class="py-2">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Inventory Management</h2>
                            {{-- <p class="text-gray-600 dark:text-gray-400">Manage your product catalog</p> --}}
                        </div>
                        @hasanyrole('super_admin|admin')
                            <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                                <button onclick="openCreateProductModal()" type="button"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add New Product
                                </button>
                            </div>
                        @endhasanyrole
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <form method="GET" action="{{ route('inventory.products.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                            <!-- Search -->
                            <div>
                                <label for="search"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    placeholder="Name, SKU, Brand, Category, Supplier, Size, Color..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>


                            <!-- Stock Name Filter -->
                            <div>
                                <label for="stock_names"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock
                                    Name</label>
                                <select id="stock_names" name="stock_names"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Stock Names</option>
                                    @foreach ($stockNames as $stock_name)
                                        <option value="{{ $stock_name }}"
                                            {{ request('stock_names') == $stock_name ? 'selected' : '' }}>
                                            {{ $stock_name }}
                                        </option>
                                    @endforeach
                                </select>
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
                                    <option value="critical_stock"
                                        {{ request('stock_status') == 'critical_stock' ? 'selected' : '' }}>Critical
                                        Stock
                                    </option>
                                    <option value="out_of_stock"
                                        {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock
                                    </option>
                                </select>
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
                                <a href="{{ route('inventory.products.index') }}"
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
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
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
                            <table
                                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-100">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        {{-- <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                <a
                                                    href="{{ request()->fullUrlWithQuery(['sort' => 'sku', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                                                    SKU
                                                    @if (request('sort') === 'sku')
                                                        <span
                                                            class="ml-1">{{ request('order') === 'asc' ? '↑' : '↓' }}</span>
                                                    @endif
                                                </a>
                                            </th> --}}
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
                                <tbody
                                    class="bg-slate-100 dark:bg-gray-900 divide-y divide-gray-600 dark:divide-gray-400">
                                    @foreach ($products as $product)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            {{-- <td>
                                                <div
                                                    class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                                    {{ $product->sku }}
                                                </div>
                                            </td> --}}
                                            {{-- Product Image and Name --}}
                                            <td class="px-6 py-4">
                                                <div class="flex items-start gap-3">
                                                    @if ($product->image)
                                                        <img src="{{ $product->image_url }}"
                                                            alt="{{ $product->product_name }}"
                                                            class="w-16 h-16 object-cover rounded-lg border border-gray-300 dark:border-gray-600 flex-shrink-0"
                                                            onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27100%27 height=%27100%27 viewBox=%270 0 100 100%27%3E%3Crect fill=%27%23e5e7eb%27 width=%27100%27 height=%27100%27/%3E%3Ctext fill=%27%239ca3af%27 font-family=%27sans-serif%27 font-size=%2714%27 text-anchor=%27middle%27 x=%2750%27 y=%2755%27%3ENo Image%3C/text%3E%3C/svg%3E'">
                                                    @else
                                                        <div
                                                            class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                                            <svg class="w-8 h-8 text-gray-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <div class="flex-1 min-w-0">
                                                        @if ($product->stock_name)
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                                <span class="font-medium">Stock Name:</span>
                                                                {{ $product->stock_name }}
                                                            </div>
                                                        @endif
                                                        <div
                                                            class="text-xs font-normal text-gray-900 dark:text-white break-words">
                                                            {{ $product->product_name }}
                                                        </div>
                                                        @if ($product->sku || $product->size || $product->color)
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                @if ($product->sku)
                                                                    <span
                                                                        class=" bg-gray-100 dark:bg-gray-700 py-0.5 rounded mr-1">SKU:
                                                                        {{ $product->sku }}</span>
                                                                @endif
                                                                <div
                                                                    class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                                    @if ($product->size)
                                                                        <span
                                                                            class=" bg-gray-100 dark:bg-gray-700 py-0.5 rounded mr-1">Size:
                                                                            {{ $product->size }}</span>
                                                                    @endif
                                                                    @if ($product->color)
                                                                        <span
                                                                            class=" bg-gray-100 dark:bg-gray-700 py-0.5 rounded">Color:
                                                                            {{ $product->color }}</span>
                                                                    @endif
                                                                </div>
                                                        @endif

                                                        {{-- @if ($product->description)
                                                            <div
                                                                class="text-xs font-mono text-gray-500 dark:text-gray-400 break-words">
                                                                {{ $product->description }}
                                                            </div>
                                                        @endif --}}
                                                    </div>
                                                </div>
                                            </td>
                                            {{-- Product Brand --}}
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white break-words">
                                                {{ $product->product_brand }}</td>
                                            {{-- Product Category --}}
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white break-words">
                                                {{ $product->product_category }}</td>
                                            {{-- Product Quantity --}}
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white break-words">
                                                {{ number_format($product->quantity) }}</td>
                                            {{-- Product Price --}}
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white break-words">
                                                ₱{{ number_format($product->calculateSellingPrice(), 2) }}</td>
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
                                            </td>
                                            @hasanyrole('super_admin|admin')
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                    <button onclick="openViewProductModal({{ $product->product_id }})"
                                                        class="text-blue-600" title="View Product">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <button onclick="openEditProductModal({{ $product->product_id }})"
                                                        class="text-yellow-600" title="Edit">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    @hasanyrole('super_admin|admin|inventory_clerk')
                                                        <button
                                                            onclick="openStockAdjustmentModal({{ $product->product_id }}, '{{ $product->product_name }}', {{ $product->quantity }})"
                                                            class="text-orange-400" title="Adjust Stock">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path
                                                                    d="M3 17v2h6v-2zM3 5v2h10V5zm10 16v-2h8v-2h-8v-2h-2v6zM7 9v2H3v2h4v2h2V9zm14 4v-2H11v2zm-6-4h2V7h4V5h-4V3h-2z">
                                                                </path>
                                                            </svg>
                                                        </button>

                                                        <button
                                                            onclick="openAdjustmentHistoryModal({{ $product->product_id }}, '{{ $product->product_name }}')"
                                                            class="text-purple-700" title="History">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                focusable="false" aria-hidden="true" viewBox="0 0 24 24">
                                                                <path
                                                                    d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9m-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8z">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    @endhasanyrole
                                                    @hasanyrole('super_admin|admin')
                                                        <button
                                                            onclick="openProductCostingModal({{ $product->product_id }}, '{{ $product->product_name }}')"
                                                            class="text-green-600" title="Product Costing">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    @endhasanyrole
                                                    {{-- <form method="POST"
                                                            action="{{ route('inventory.products.destroy', $product) }}"
                                                            class="inline-block"
                                                            onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                                        </form> --}}
                                                </td>
                                            @endhasanyrole
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
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No records found</h3>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Create Product Modal -->
    <div id="createProductModal"
        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto max-h-full max-w-full z-50">
        <div
            class="relative top-10 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-md bg-white dark:bg-gray-800 mb-10">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Add New Product
                    </h3>
                    <button onclick="closeCreateProductModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="createProductForm" method="POST" action="{{ route('inventory.products.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="mt-4 space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <!-- Stock Name -->
                            <div>
                                <label for="modal_stock_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Stock Name
                                </label>
                                <div class="mt-1 flex">
                                    <select id="modal_stock_name" name="stock_name"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        required>
                                        <option class="bg-slate-300" value="">Select a stock name...</option>
                                        @foreach ($stockNames as $stockName)
                                            <option value="{{ $stockName }}">{{ $stockName }}</option>
                                        @endforeach
                                        <option value="custom">+ Add New Stock Name</option>
                                    </select>
                                </div>
                                <input type="text" id="modal_custom_stock_name" name="custom_stock_name"
                                    placeholder="Enter new stock name..." style="display: none;"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Product Name -->
                            <div>
                                <label for="modal_product_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Product Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="modal_product_name" name="product_name" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <!-- Size -->
                            <div>
                                <label for="modal_size"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Size <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <input type="text" id="modal_size" name="size"
                                    placeholder="e.g., 42, Large, XL"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Color -->
                            <div>
                                <label for="modal_color"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Color <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <input type="text" id="modal_color" name="color"
                                    placeholder="e.g., Black, Red, Blue"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- SKU -->
                            <div>
                                <label for="modal_sku"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    SKU <span class="text-gray-400 text-xs">(Auto-generated if empty)</span>
                                </label>
                                <input type="text" id="modal_sku" name="sku" placeholder="e.g., SHOE-001"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <!-- Product Brand and Category Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <!-- Product Brand -->
                            <div>
                                <label for="modal_product_brand"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Brand <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 flex">
                                    <select id="modal_product_brand" name="product_brand" required
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select a brand...</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand }}">{{ $brand }}</option>
                                        @endforeach
                                        <option value="custom">+ Add New Brand</option>
                                    </select>
                                </div>
                                <input type="text" id="modal_custom_brand" name="custom_brand"
                                    placeholder="Enter new brand name..." style="display: none;"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Product Category -->
                            <div>
                                <label for="modal_product_category"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 flex">
                                    <select id="modal_product_category" name="product_category" required
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select a category...</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category }}">{{ $category }}</option>
                                        @endforeach
                                        <option value="custom">+ Add New Category</option>
                                    </select>
                                </div>
                                <input type="text" id="modal_custom_category" name="custom_category"
                                    placeholder="Enter new category name..." style="display: none;"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Preferred Supplier -->
                            <div>
                                <label for="modal_preferred_supplier_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Preferred Supplier <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <select id="modal_preferred_supplier_id" name="preferred_supplier_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select a supplier...</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Pricing Method and Markup Price -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <!-- Pricing Method -->
                            <div>
                                <label for="modal_pricing_method"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Pricing Method<span class="text-red-500">*</span>
                                </label>
                                <select id="modal_pricing_method" name="pricing_method" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="manual" selected>Manual Price</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    Manual: Fixed price (Markup pricing available after product creation with costing)
                                </p>
                            </div>

                            <!-- Markup Price Configuration -->
                            <div id="modal_markup_price_field" style="display: none;">
                                <label for="modal_markup_price_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Markup Configuration<span class="text-red-500">*</span>
                                </label>
                                <select id="modal_markup_price_id" name="markup_price_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select markup...</option>
                                    @foreach ($markupPrices as $markup)
                                        <option value="{{ $markup->id }}">{{ $markup->name }}
                                            ({{ $markup->markup_percentage }}%)
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Price = Cost + Markup %</p>
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="modal_price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Price<span class="text-red-500" id="modal_price_required">*</span>
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" id="modal_price" name="price" step="0.01"
                                        min="0" required
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <p class="mt-1 text-xs text-gray-500" id="modal_price_helper">Base price for the
                                    product</p>
                            </div>

                            <!-- Description -->
                            {{-- <div>
                                <label for="modal_description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Description
                                </label>
                                <textarea id="modal_description" name="description" rows="1" placeholder="Enter product description..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div> --}}

                            <!-- Image Upload/URL -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Product Image <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>

                                <!-- Tab Buttons -->
                                <div class="flex gap-2 mb-3">
                                    <button type="button" onclick="switchCreateImageTab('url')" id="modal_url_tab"
                                        class="px-4 py-2 text-sm font-medium rounded-md bg-blue-600 text-white">
                                        URL
                                    </button>
                                    <button type="button" onclick="switchCreateImageTab('upload')"
                                        id="modal_upload_tab"
                                        class="px-4 py-2 text-sm font-medium rounded-md bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        Upload
                                    </button>
                                </div>

                                <!-- URL Input Section -->
                                <div id="modal_url_section">
                                    <input type="url" id="modal_image_url" name="image_url"
                                        placeholder="https://example.com/image.jpg" oninput="previewCreateImage()"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <p class="mt-1 text-xs text-gray-500">Enter a direct link to an image</p>
                                </div>

                                <!-- Upload Input Section -->
                                <div id="modal_upload_section" class="hidden">
                                    <input type="file" id="modal_image_file" name="image_file" accept="image/*"
                                        onchange="previewCreateUpload()"
                                        class="block w-full text-sm text-gray-500 dark:text-gray-400
                                            file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                                            file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                                            hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                                    <p class="mt-1 text-xs text-gray-500">Upload an image file (JPG, PNG, GIF, etc.)
                                    </p>
                                </div>

                                <!-- Image Preview -->
                                <div id="modal_image_preview" class="mt-2 hidden">
                                    <img id="modal_preview_img" src="" alt="Preview"
                                        class="w-32 h-32 object-cover rounded-lg border border-gray-300 dark:border-gray-600"
                                        onerror="this.parentElement.classList.add('hidden')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t dark:border-gray-700">
                        <button type="button" onclick="closeCreateProductModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Create Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Create Product Modal --}}
    <script>
        function openCreateProductModal() {
            document.getElementById('createProductModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCreateProductModal() {
            document.getElementById('createProductModal').classList.add('hidden');
            document.body.style.overflow = 'auto';

            // Reset form
            document.getElementById('createProductForm').reset();

            // Reset image preview
            document.getElementById('modal_previewImg').src = '#';
            document.getElementById('modal_previewImg').classList.add('hidden');
            document.getElementById('modal_uploadPlaceholder').classList.remove('hidden');
            document.getElementById('modal_image_preview').classList.add('hidden');

            // Hide custom brand/category inputs
            document.getElementById('modal_custom_brand').style.display = 'none';
            document.getElementById('modal_custom_category').style.display = 'none';
        }

        // Image preview functions
        function previewCreateImage() {
            const url = document.getElementById('modal_image_url').value;
            const preview = document.getElementById('modal_image_preview');
            const img = document.getElementById('modal_preview_img');

            if (url) {
                img.src = url;
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        }

        function previewCreateUpload() {
            const file = document.getElementById('modal_image_file').files[0];
            const preview = document.getElementById('modal_image_preview');
            const img = document.getElementById('modal_preview_img');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        }

        function previewEditImage() {
            const url = document.getElementById('edit_image_url').value;
            const preview = document.getElementById('edit_image_preview');
            const img = document.getElementById('edit_preview_img');

            if (url) {
                img.src = url;
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        }

        function previewEditUpload() {
            const file = document.getElementById('edit_image_file').files[0];
            const preview = document.getElementById('edit_image_preview');
            const img = document.getElementById('edit_preview_img');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        }

        // Tab switching functions
        function switchCreateImageTab(tab) {
            const urlTab = document.getElementById('modal_url_tab');
            const uploadTab = document.getElementById('modal_upload_tab');
            const urlSection = document.getElementById('modal_url_section');
            const uploadSection = document.getElementById('modal_upload_section');
            const urlInput = document.getElementById('modal_image_url');
            const fileInput = document.getElementById('modal_image_file');

            if (tab === 'url') {
                urlTab.classList.add('bg-blue-600', 'text-white');
                urlTab.classList.remove('bg-gray-200', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
                uploadTab.classList.remove('bg-blue-600', 'text-white');
                uploadTab.classList.add('bg-gray-200', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
                urlSection.classList.remove('hidden');
                uploadSection.classList.add('hidden');
                fileInput.value = '';
            } else {
                uploadTab.classList.add('bg-blue-600', 'text-white');
                uploadTab.classList.remove('bg-gray-200', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
                urlTab.classList.remove('bg-blue-600', 'text-white');
                urlTab.classList.add('bg-gray-200', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
                uploadSection.classList.remove('hidden');
                urlSection.classList.add('hidden');
                urlInput.value = '';
            }
            document.getElementById('modal_image_preview').classList.add('hidden');
        }

        function switchEditImageTab(tab) {
            const urlTab = document.getElementById('edit_url_tab');
            const uploadTab = document.getElementById('edit_upload_tab');
            const urlSection = document.getElementById('edit_url_section');
            const uploadSection = document.getElementById('edit_upload_section');
            const urlInput = document.getElementById('edit_image_url');
            const fileInput = document.getElementById('edit_image_file');

            if (tab === 'url') {
                urlTab.classList.add('bg-blue-600', 'text-white');
                urlTab.classList.remove('bg-gray-200', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
                uploadTab.classList.remove('bg-blue-600', 'text-white');
                uploadTab.classList.add('bg-gray-200', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
                urlSection.classList.remove('hidden');
                uploadSection.classList.add('hidden');
                fileInput.value = '';
            } else {
                uploadTab.classList.add('bg-blue-600', 'text-white');
                uploadTab.classList.remove('bg-gray-200', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
                urlTab.classList.remove('bg-blue-600', 'text-white');
                urlTab.classList.add('bg-gray-200', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-gray-300');
                uploadSection.classList.remove('hidden');
                urlSection.classList.add('hidden');
                urlInput.value = '';
            }
            document.getElementById('edit_image_preview').classList.add('hidden');
        }

        // Handle custom brand/category/stock name selection
        document.addEventListener('DOMContentLoaded', function() {
            const stockNameSelect = document.getElementById('modal_stock_name');
            const customStockNameInput = document.getElementById('modal_custom_stock_name');
            const brandSelect = document.getElementById('modal_product_brand');
            const customBrandInput = document.getElementById('modal_custom_brand');
            const categorySelect = document.getElementById('modal_product_category');
            const customCategoryInput = document.getElementById('modal_custom_category');

            if (stockNameSelect) {
                stockNameSelect.addEventListener('change', function() {
                    if (this.value === 'custom') {
                        customStockNameInput.style.display = 'block';
                        customStockNameInput.required = false; // Stock name is optional
                    } else {
                        customStockNameInput.style.display = 'none';
                        customStockNameInput.required = false;
                    }
                });
            }

            if (brandSelect) {
                brandSelect.addEventListener('change', function() {
                    if (this.value === 'custom') {
                        customBrandInput.style.display = 'block';
                        customBrandInput.required = true;
                        this.required = false;
                    } else {
                        customBrandInput.style.display = 'none';
                        customBrandInput.required = false;
                        this.required = true;
                    }
                });
            }

            if (categorySelect) {
                categorySelect.addEventListener('change', function() {
                    if (this.value === 'custom') {
                        customCategoryInput.style.display = 'block';
                        customCategoryInput.required = true;
                        this.required = false;
                    } else {
                        customCategoryInput.style.display = 'none';
                        customCategoryInput.required = false;
                        this.required = true;
                    }
                });
            }

            // Form validation
            const createForm = document.getElementById('createProductForm');
            if (createForm) {
                createForm.addEventListener('submit', function(e) {
                    if (stockNameSelect && stockNameSelect.value === 'custom' && !customStockNameInput.value
                        .trim()) {
                        e.preventDefault();
                        alert('Please enter a custom stock name.');
                        return false;
                    }
                    if (brandSelect.value === 'custom' && !customBrandInput.value.trim()) {
                        e.preventDefault();
                        alert('Please enter a custom brand name.');
                        return false;
                    }
                    if (categorySelect.value === 'custom' && !customCategoryInput.value.trim()) {
                        e.preventDefault();
                        alert('Please enter a custom category name.');
                        return false;
                    }
                });
            }

            // Handle pricing method changes for create modal
            const modalPricingMethodSelect = document.getElementById('modal_pricing_method');
            const modalMarkupPriceField = document.getElementById('modal_markup_price_field');
            const modalMarkupPriceSelect = document.getElementById('modal_markup_price_id');
            const modalPriceInput = document.getElementById('modal_price');
            const modalPriceRequired = document.getElementById('modal_price_required');
            const modalPriceHelper = document.getElementById('modal_price_helper');

            if (modalPricingMethodSelect) {
                function updateModalPricingFields() {
                    const method = modalPricingMethodSelect.value;

                    if (method === 'markup') {
                        modalMarkupPriceField.style.display = 'block';
                        modalMarkupPriceSelect.setAttribute('required', 'required');
                        modalPriceInput.removeAttribute('required');
                        modalPriceRequired.style.display = 'none';
                        modalPriceHelper.textContent = 'Optional (calculated from markup)';
                    } else {
                        modalMarkupPriceField.style.display = 'none';
                        modalMarkupPriceSelect.removeAttribute('required');
                        modalPriceInput.setAttribute('required', 'required');
                        modalPriceRequired.style.display = 'inline';
                        modalPriceHelper.textContent = 'Base price for the product';
                    }
                }

                modalPricingMethodSelect.addEventListener('change', updateModalPricingFields);
                updateModalPricingFields(); // Initialize on load
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('createProductModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeCreateProductModal();
                }
            }
        });

        // Close on outside click
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('createProductModal');
            if (modal) {
                modal.addEventListener('click', function(event) {
                    if (event.target === this) {
                        closeCreateProductModal();
                    }
                });
            }

            // Setup edit modal custom input handlers
            const editStockNameSelect = document.getElementById('edit_stock_name');
            const editCustomStockNameInput = document.getElementById('edit_custom_stock_name');

            if (editStockNameSelect) {
                editStockNameSelect.addEventListener('change', function() {
                    if (this.value === 'custom') {
                        editCustomStockNameInput.style.display = 'block';
                        editCustomStockNameInput.required = false; // Stock name is optional
                    } else {
                        editCustomStockNameInput.style.display = 'none';
                        editCustomStockNameInput.required = false;
                    }
                });
            }
        });

        // EDIT PRODUCT MODAL
        let currentProductId = null;

        function openEditProductModal(productId) {
            currentProductId = productId;
            document.getElementById('editProductModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            const contentDiv = document.getElementById('historyModalContent');

            fetch(`/inventory/products/${productId}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editProductForm').action = `/inventory/products/${productId}`;
                    document.getElementById('edit_product_name').value = data.product.product_name;
                    document.getElementById('edit_size').value = data.product.size || '';
                    document.getElementById('edit_color').value = data.product.color || '';
                    document.getElementById('edit_sku').value = data.product.sku || '';
                    document.getElementById('edit_price').value = data.product.price;
                    // document.getElementById('edit_description').value = data.product.description || '';
                    document.getElementById('edit_image_url').value = data.product.image || '';

                    // Show image preview if image exists
                    if (data.product.image) {
                        document.getElementById('edit_preview_img').src = data.product.image;
                        document.getElementById('edit_image_preview').classList.remove('hidden');
                    }

                    // Populate stock names dropdown
                    const stockNameSelect = document.getElementById('edit_stock_name');
                    stockNameSelect.innerHTML = '<option class="bg-slate-300" value="">Select a stock name...</option>';
                    data.stockNames.forEach(stockName => {
                        const option = document.createElement('option');
                        option.value = stockName;
                        option.textContent = stockName;
                        option.selected = data.product.stock_name === stockName;
                        stockNameSelect.appendChild(option);
                    });
                    // Add custom option
                    const customStockOption = document.createElement('option');
                    customStockOption.value = 'custom';
                    customStockOption.textContent = '+ Add New Stock Name';
                    stockNameSelect.appendChild(customStockOption);

                    const brandSelect = document.getElementById('edit_product_brand');
                    brandSelect.innerHTML = '<option value="">Select a brand...</option>';
                    data.brands.forEach(brand => {
                        const option = document.createElement('option');
                        option.value = brand;
                        option.textContent = brand;
                        option.selected = data.product.product_brand === brand;
                        brandSelect.appendChild(option);
                    });

                    const categorySelect = document.getElementById('edit_product_category');
                    categorySelect.innerHTML = '<option value="">Select a category...</option>';
                    data.categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category;
                        option.textContent = category;
                        option.selected = data.product.product_category === category;
                        categorySelect.appendChild(option);
                    });

                    const supplierSelect = document.getElementById('edit_preferred_supplier_id');
                    supplierSelect.innerHTML = '<option value="">Select a supplier...</option>';
                    data.suppliers.forEach(supplier => {
                        const option = document.createElement('option');
                        option.value = supplier.supplier_id;
                        option.textContent = supplier.supplier_name;
                        option.selected = data.product.preferred_supplier_id === supplier.supplier_id;
                        supplierSelect.appendChild(option);
                    });

                    // Populate pricing method and markup price
                    const pricingMethodSelect = document.getElementById('edit_pricing_method');
                    const hasCostingData = data.product.total_cost && data.product.total_cost > 0;
                    
                    // Clear and rebuild pricing method options
                    pricingMethodSelect.innerHTML = '<option value="manual">Manual Price</option>';
                    
                    // Only add markup option if product has costing data
                    if (hasCostingData) {
                        const markupOption = document.createElement('option');
                        markupOption.value = 'markup';
                        markupOption.textContent = 'Markup Price';
                        pricingMethodSelect.appendChild(markupOption);
                    }
                    
                    // Set the selected value
                    pricingMethodSelect.value = data.product.pricing_method || 'manual';
                    
                    // Update help text
                    const helpText = pricingMethodSelect.parentElement.querySelector('.text-gray-500');
                    if (helpText) {
                        helpText.textContent = hasCostingData 
                            ? 'Manual: Fixed price | Markup: From markup %'
                            : 'Manual: Fixed price (Markup pricing available after adding product costing)';
                    }

                    const markupPriceSelect = document.getElementById('edit_markup_price_id');
                    if (data.markupPrices) {
                        markupPriceSelect.innerHTML = '<option value="">Select markup...</option>';
                        data.markupPrices.forEach(markup => {
                            const option = document.createElement('option');
                            option.value = markup.id;
                            option.textContent = `${markup.name} (${markup.markup_percentage}%)`;
                            option.selected = data.product.markup_price_id === markup.id;
                            markupPriceSelect.appendChild(option);
                        });
                    }

                    // Populate total cost
                    const totalCostField = document.getElementById('edit_total_cost');
                    if (data.product.total_cost) {
                        totalCostField.value = parseFloat(data.product.total_cost).toFixed(2);
                    } else {
                        totalCostField.value = '0.00';
                    }

                    // Trigger pricing field update
                    updateEditPricingFields();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load product data.');
                    closeEditProductModal();
                });
        }

        function closeEditProductModal() {
            document.getElementById('editProductModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('editProductForm').reset();
            document.getElementById('edit_current_image').classList.add('hidden');

            // Reset to file upload mode
            document.querySelector('input[name="edit_image_source"][value="file"]').checked = true;
            document.getElementById('edit_fileUploadSection').classList.remove('hidden');
            document.getElementById('edit_urlInputSection').classList.add('hidden');
            document.getElementById('edit_image').disabled = false;
            document.getElementById('edit_image_url').disabled = true;
            document.getElementById('edit_urlPreviewContainer').classList.add('hidden');
        }

        // Handle pricing method changes for edit modal
        function updateEditPricingFields() {
            const pricingMethodSelect = document.getElementById('edit_pricing_method');
            const markupPriceField = document.getElementById('edit_markup_price_field');
            const markupPriceSelect = document.getElementById('edit_markup_price_id');
            const priceInput = document.getElementById('edit_price');
            const priceRequired = document.getElementById('edit_price_required');
            const priceHelper = document.getElementById('edit_price_helper');

            const method = pricingMethodSelect.value;

            if (method === 'markup') {
                markupPriceField.style.display = 'block';
                markupPriceSelect.setAttribute('required', 'required');
                priceInput.removeAttribute('required');
                priceRequired.style.display = 'none';
                priceHelper.textContent = 'Optional (calculated from markup)';
            } else {
                markupPriceField.style.display = 'none';
                markupPriceSelect.removeAttribute('required');
                priceInput.setAttribute('required', 'required');
                priceRequired.style.display = 'inline';
                priceHelper.textContent = 'Base price for the product';
            }
        }

        // Attach event listener for edit pricing method
        document.addEventListener('DOMContentLoaded', function() {
            const editPricingMethodSelect = document.getElementById('edit_pricing_method');
            if (editPricingMethodSelect) {
                editPricingMethodSelect.addEventListener('change', updateEditPricingFields);
            }
        });

        // STOCK ADJUSTMENT MODAL
        let currentProductStock = 0;

        function openStockAdjustmentModal(productId, productName, currentStock) {
            currentProductStock = currentStock;
            document.getElementById('adj_product_id').value = productId;
            document.getElementById('adj_product_name').textContent = productName;
            document.getElementById('adj_current_stock').textContent = currentStock;
            document.getElementById('stockAdjustmentModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeStockAdjustmentModal() {
            document.getElementById('stockAdjustmentModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('stockAdjustmentForm').reset();
            document.getElementById('new_stock_preview').style.display = 'none';
            document.getElementById('custom_reason_div').style.display = 'none';
            document.getElementById('increase_reasons').style.display = 'none';
            document.getElementById('decrease_reasons').style.display = 'none';
        }

        function updateAdjustmentUI() {
            const type = document.getElementById('adj_type').value;
            const increaseReasons = document.getElementById('increase_reasons');
            const decreaseReasons = document.getElementById('decrease_reasons');
            const reasonSelect = document.getElementById('adj_reason');

            // Reset reason
            reasonSelect.value = '';
            document.getElementById('custom_reason_div').style.display = 'none';

            // Show appropriate reasons
            if (type === 'increase') {
                increaseReasons.style.display = 'block';
                decreaseReasons.style.display = 'none';
            } else if (type === 'decrease') {
                increaseReasons.style.display = 'none';
                decreaseReasons.style.display = 'block';
            } else {
                increaseReasons.style.display = 'none';
                decreaseReasons.style.display = 'none';
            }

            calculateNewStock();
        }

        function toggleCustomReason() {
            const reason = document.getElementById('adj_reason').value;
            const customDiv = document.getElementById('custom_reason_div');
            const customInput = document.getElementById('adj_custom_reason');

            if (reason === 'Other') {
                customDiv.style.display = 'block';
                customInput.required = true;
            } else {
                customDiv.style.display = 'none';
                customInput.required = false;
                customInput.value = '';
            }
        }

        function calculateNewStock() {
            const type = document.getElementById('adj_type').value;
            const quantity = parseInt(document.getElementById('adj_quantity').value) || 0;
            const preview = document.getElementById('new_stock_preview');
            const newStockSpan = document.getElementById('preview_new_stock');
            const changeSpan = document.getElementById('preview_change');

            if (!type || quantity === 0) {
                preview.style.display = 'none';
                return;
            }

            let newStock = currentProductStock;
            let changeText = '';

            if (type === 'increase') {
                newStock = currentProductStock + quantity;
                changeText = `<span class="text-green-600 dark:text-green-400">(+${quantity})</span>`;
            } else if (type === 'decrease') {
                newStock = currentProductStock - quantity;
                changeText = `<span class="text-red-600 dark:text-red-400">(-${quantity})</span>`;
            }

            newStockSpan.textContent = newStock;
            changeSpan.innerHTML = changeText;
            preview.style.display = 'block';

            // Warn if stock will be negative
            if (newStock < 0) {
                newStockSpan.classList.add('text-red-600', 'dark:text-red-400');
            } else {
                newStockSpan.classList.remove('text-red-600', 'dark:text-red-400');
                newStockSpan.classList.add('text-green-600', 'dark:text-green-400');
            }
        }

        // Adjustment History Modal Functions
        function openAdjustmentHistoryModal(productId, productName) {
            const modal = document.getElementById('adjustmentHistoryModal');
            const productNameSpan = document.getElementById('history_product_name');
            const contentDiv = document.getElementById('historyModalContent');

            productNameSpan.textContent = productName;
            modal.classList.remove('hidden');

            // Show loading spinner
            contentDiv.innerHTML = `
                <div class="flex justify-center items-center py-12">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            `;

            // Fetch history data
            fetch(`/inventory/stock-adjustments/${productId}/history`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    contentDiv.innerHTML = html;
                })
                .catch(error => {
                    contentDiv.innerHTML = `
                    <div class="text-center py-12">
                        <p class="text-red-600 dark:text-red-400">Error loading history: ${error.message}</p>
                    </div>
                `;
                });
        }

        function closeAdjustmentHistoryModal() {
            const modal = document.getElementById('adjustmentHistoryModal');
            modal.classList.add('hidden');
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeAdjustmentHistoryModal();
            }
        });

        // VIEW PRODUCT MODAL
        function openViewProductModal(productId) {
            currentProductId = productId;
            document.getElementById('viewProductModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            fetch(`/inventory/products/${productId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('view_product_name').textContent = data.product_name;
                    document.getElementById('view_product_name_detail').textContent = data.product_name;
                    document.getElementById('view_sku').textContent = data.sku || 'N/A';
                    document.getElementById('view_brand').textContent = data.product_brand;
                    document.getElementById('view_category').textContent = data.product_category;
                    document.getElementById('view_quantity').textContent = new Intl.NumberFormat().format(data
                        .quantity);
                    document.getElementById('view_price').textContent = '₱' + new Intl.NumberFormat('en-PH', {
                        minimumFractionDigits: 2
                    }).format(data.price);

                    // Product Image
                    // if (data.image) {
                    //     document.getElementById('view_product_image').src = data.image;
                    //     document.getElementById('view_image_container').style.display = 'block';
                    // } else {
                    //     document.getElementById('view_image_container').style.display = 'none';
                    // }

                    // Total Cost
                    const totalCost = data.total_cost || 0;
                    document.getElementById('view_total_cost').textContent = '₱' + new Intl.NumberFormat('en-PH', {
                        minimumFractionDigits: 2
                    }).format(totalCost);

                    // Markup
                    if (data.pricing_method === 'markup' && data.markup_price) {
                        document.getElementById('view_markup_container').style.display = 'block';
                        document.getElementById('view_markup').textContent = data.markup_price.name + ' (' + data
                            .markup_price.markup_percentage + '%)';
                    } else {
                        document.getElementById('view_markup_container').style.display = 'none';
                    }

                    // Stock Name
                    if (data.stock_name) {
                        document.getElementById('view_stock_name_container').style.display = 'block';
                        document.getElementById('view_stock_name').textContent = data.stock_name;
                    } else {
                        document.getElementById('view_stock_name_container').style.display = 'none';
                    }

                    // Size
                    if (data.size) {
                        document.getElementById('view_size_container').style.display = 'block';
                        document.getElementById('view_size').textContent = data.size;
                    } else {
                        document.getElementById('view_size_container').style.display = 'none';
                    }

                    // Color
                    if (data.color) {
                        document.getElementById('view_color_container').style.display = 'block';
                        document.getElementById('view_color').textContent = data.color;
                    } else {
                        document.getElementById('view_color_container').style.display = 'none';
                    }

                    // Preferred Supplier
                    if (data.preferred_supplier) {
                        document.getElementById('view_preferred_supplier_container').style.display = 'block';
                        document.getElementById('view_preferred_supplier').textContent = data.preferred_supplier
                            .supplier_name || data.preferred_supplier.name;
                    } else {
                        document.getElementById('view_preferred_supplier_container').style.display = 'none';
                    }

                    // Inventory Value
                    const inventoryValue = data.quantity * data.price;
                    document.getElementById('view_inventory_value').textContent = '₱' + new Intl.NumberFormat('en-PH', {
                        minimumFractionDigits: 2
                    }).format(inventoryValue);

                    // Last Supplier
                    if (data.last_supplier) {
                        document.getElementById('view_last_supplier_container').style.display = 'block';
                        document.getElementById('view_last_supplier').textContent = data.last_supplier.supplier_name ||
                            data.last_supplier.name;
                        if (data.last_received_at) {
                            document.getElementById('view_last_received').textContent = 'Last received: ' + new Date(
                                data.last_received_at).toLocaleDateString('en-US', {
                                month: 'short',
                                day: 'numeric',
                                year: 'numeric'
                            });
                        }
                        document.getElementById('view_no_supplier').style.display = 'none';
                    } else {
                        document.getElementById('view_last_supplier_container').style.display = 'none';
                    }

                    // Last Purchase Price
                    if (data.last_purchase_price) {
                        document.getElementById('view_last_purchase_price_container').style.display = 'block';
                        document.getElementById('view_last_purchase_price').textContent = '₱' + new Intl.NumberFormat(
                            'en-PH', {
                                minimumFractionDigits: 2
                            }).format(data.last_purchase_price);

                        if (data.price > data.last_purchase_price) {
                            const margin = data.price - data.last_purchase_price;
                            document.getElementById('view_price_margin').style.display = 'block';
                            document.getElementById('view_price_margin').textContent = 'Margin: ₱' + new Intl
                                .NumberFormat('en-PH', {
                                    minimumFractionDigits: 2
                                }).format(margin);
                        } else {
                            document.getElementById('view_price_margin').style.display = 'none';
                        }
                        document.getElementById('view_no_supplier').style.display = 'none';
                    } else {
                        document.getElementById('view_last_purchase_price_container').style.display = 'none';
                    }

                    // Show/hide no supplier message
                    if (!data.last_supplier && !data.preferred_supplier && !data.last_purchase_price) {
                        document.getElementById('view_no_supplier').style.display = 'block';
                    }

                    // Product Info
                    document.getElementById('view_product_id').textContent = '#' + data.product_id;
                    document.getElementById('view_created_at').textContent = new Date(data.created_at)
                        .toLocaleDateString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric'
                        });
                    document.getElementById('view_updated_at').textContent = new Date(data.updated_at)
                        .toLocaleDateString('en-US', {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric'
                        });

                    if (data.description) {
                        document.getElementById('view_description_container').style.display = 'block';
                        document.getElementById('view_description').textContent = data.description;
                    } else {
                        document.getElementById('view_description_container').style.display = 'none';
                    }

                    let statusHTML = '';
                    if (data.quantity <= 0) {
                        statusHTML =
                            '<span class="inline-flex items-center py-1 rounded-full text-sm font-medium bg-red-100 text-red-800"></span>Out of Stock</span>';
                    } else if (data.quantity > 5 && data.quantity <= 10) {
                        statusHTML =
                            '<span class="inline-flex items-center py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800"></span>Low Stock</span>';
                    } else if (data.quantity <= 5) {
                        statusHTML =
                            '<span class="inline-flex items-center py-1 rounded-full text-sm font-medium bg-red-100 text-red-600"></span>Critical Stock</span>';
                    } else {
                        statusHTML =
                            '<span class="inline-flex items-start py-1 rounded-full text-xs font-medium text-green-800"></span>In Stock</span>';
                    }
                    document.getElementById('view_stock_status').innerHTML = statusHTML;
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load product details.');
                    closeViewProductModal();
                });
        }

        function closeViewProductModal() {
            document.getElementById('viewProductModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openEditFromView() {
            closeViewProductModal();
            setTimeout(() => openEditProductModal(currentProductId), 100);
        }

        // Close Edit/View modals on Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                if (!document.getElementById('editProductModal').classList.contains('hidden')) {
                    closeEditProductModal();
                }
                if (!document.getElementById('viewProductModal').classList.contains('hidden')) {
                    closeViewProductModal();
                }
            }
        });

        // Close Edit/View modals on outside click
        ['editProductModal', 'viewProductModal'].forEach(modalId => {
            document.getElementById(modalId)?.addEventListener('click', function(event) {
                if (event.target === this) {
                    if (modalId === 'editProductModal') closeEditProductModal();
                    if (modalId === 'viewProductModal') closeViewProductModal();
                }
            });
        });
    </script>

    <!-- Edit Product Modal -->
    <div id="editProductModal"
        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto max-h-full max-w-full z-50">
        <div
            class="relative top-10 mx-auto p-5 border w-11/12 max-w-full shadow-lg rounded-md bg-white dark:bg-gray-800 mb-10">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-3 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Product</h3>
                    <button onclick="closeEditProductModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="editProductForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mt-4 space-y-6 max-h-[60vh] overflow-y-auto pr-2">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="edit_stock_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Stock Name<span class="text-gray-400 text-xs"></span>
                                </label>
                                <div class="mt-1 flex">
                                    <select id="edit_stock_name" name="stock_name"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select a stock name</option>
                                    </select>
                                </div>
                                <input type="text" id="edit_custom_stock_name" name="custom_stock_name"
                                    placeholder="Enter new stock name..." style="display: none;"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                </p>
                            </div>
                            <div>
                                <label for="edit_product_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Product Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="edit_product_name" name="product_name" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="edit_size"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Size <span class="text-gray-400 text-xs"></span>
                                </label>
                                <input type="text" id="edit_size" name="size"
                                    placeholder="e.g., 42, Large, XL"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="edit_color"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Color <span class="text-gray-400 text-xs"></span>
                                </label>
                                <input type="text" id="edit_color" name="color"
                                    placeholder="e.g., Black, Red, Blue"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label for="edit_sku"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">SKU</label>
                                <input type="text" id="edit_sku" name="sku"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <label for="edit_preferred_supplier_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Preferred Supplier <span class="text-gray-400 text-xs"></span>
                                </label>
                                <select id="edit_preferred_supplier_id" name="preferred_supplier_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select a supplier...</option>
                                </select>
                            </div>

                            <div>
                                <label for="edit_product_brand"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Brand <span class="text-red-500">*</span>
                                </label>
                                <select id="edit_product_brand" name="product_brand" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select a brand...</option>
                                </select>
                            </div>

                            <div>
                                <label for="edit_product_category"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select id="edit_product_category" name="product_category" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select a category...</option>
                                </select>
                            </div>
                        </div>

                        <!-- Pricing Method and Markup Price -->
                        <div class="grid grid-cols-3 gap-2">
                            <!-- Pricing Method -->
                            <div>
                                <label for="edit_pricing_method"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Pricing Method<span class="text-red-500">*</span>
                                </label>
                                <select id="edit_pricing_method" name="pricing_method" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="manual">Manual Price</option>
                                    <option value="markup">Markup Price</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    Manual: Fixed price | Markup: From markup %
                                </p>
                            </div>

                            <!-- Markup Price Configuration -->
                            <div id="edit_markup_price_field" style="display: none;">
                                <label for="edit_markup_price_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Markup Configuration<span class="text-red-500">*</span>
                                </label>
                                <select id="edit_markup_price_id" name="markup_price_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select markup...</option>
                                    @foreach ($markupPrices as $markup)
                                        <option value="{{ $markup->id }}">{{ $markup->name }}
                                            ({{ $markup->markup_percentage }}%)
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Price = Cost + Markup %</p>
                            </div>

                            <div>
                                <label for="edit_price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Price<span class="text-red-500" id="edit_price_required">*</span>
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" id="edit_price" name="price" step="0.01"
                                        min="0" required
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <p class="mt-1 text-xs text-gray-500" id="edit_price_helper">Base price for the
                                    product</p>
                            </div>

                            <!-- Cost (Read-only display) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Total Cost
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="text" id="edit_total_cost" readonly
                                        class="pl-7 block w-full rounded-md border-gray-300 bg-gray-50 dark:bg-gray-600 shadow-sm dark:border-gray-600 dark:text-white cursor-not-allowed"
                                        placeholder="0.00">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">From Product Costing</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            {{-- <div>
                                <label for="edit_description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea id="edit_description" name="description" rows="1"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div> --}}

                            <!-- Image URL -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Product Image <span class="text-gray-400 text-xs"></span>
                                </label>

                                <!-- Tab Buttons -->
                                <div class="flex gap-2 mb-3">
                                    <button type="button" onclick="switchEditImageTab('url')" id="edit_url_tab"
                                        class="px-4 py-2 text-sm font-medium rounded-md bg-blue-600 text-white">
                                        URL
                                    </button>
                                    <button type="button" onclick="switchEditImageTab('upload')"
                                        id="edit_upload_tab"
                                        class="px-4 py-2 text-sm font-medium rounded-md bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        Upload
                                    </button>
                                </div>

                                <!-- URL Input Section -->
                                <div id="edit_url_section">
                                    <input type="url" id="edit_image_url" name="image_url"
                                        placeholder="https://example.com/image.jpg" oninput="previewEditImage()"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <p class="mt-1 text-xs text-gray-500">Enter a direct link to an image</p>
                                </div>

                                <!-- Upload Input Section -->
                                <div id="edit_upload_section" class="hidden">
                                    <input type="file" id="edit_image_file" name="image_file" accept="image/*"
                                        onchange="previewEditUpload()"
                                        class="block w-full text-sm text-gray-500 dark:text-gray-400
                                            file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                                            file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                                            hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300">
                                    <p class="mt-1 text-xs text-gray-500">Upload an image file (JPG, PNG, GIF, etc.)
                                    </p>
                                </div>

                                <!-- Image Preview -->
                                <div id="edit_image_preview" class="mt-2 hidden">
                                    <img id="edit_preview_img" src="" alt="Preview"
                                        class="w-32 h-32 object-cover rounded-lg border border-gray-300 dark:border-gray-600"
                                        onerror="this.parentElement.classList.add('hidden')">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-2 pt-4 border-t dark:border-gray-700">
                        <button type="button" onclick="closeEditProductModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Stock Adjustment Modal -->
    <div id="stockAdjustmentModal"
        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div
            class="relative top-10 mx-auto p-5 border w-11/12 max-w-full shadow-lg rounded-md bg-white dark:bg-gray-800 mb-10">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-3 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Stock Adjustment</h3>
                    <button onclick="closeStockAdjustmentModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="stockAdjustmentForm" method="POST"
                    action="{{ route('inventory.stock-adjustments.store') }}">
                    @csrf
                    <input type="hidden" id="adj_product_id" name="product_id">

                    <div class="mt-4 space-y-4">
                        <!-- Product Info -->
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-300">Product: <span id="adj_product_name"
                                    class="font-semibold text-gray-900 dark:text-white"></span></p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Current Stock: <span
                                    id="adj_current_stock" class="font-semibold text-gray-900 dark:text-white"></span>
                            </p>
                        </div>

                        <!-- Adjustment Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Adjustment Type <span class="text-red-500">*</span>
                            </label>
                            <select id="adj_type" name="adjustment_type" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                onchange="updateAdjustmentUI()">
                                <option value="">Select Type</option>
                                <option value="increase">Increase Stock</option>
                                <option value="decrease">Decrease Stock</option>
                            </select>
                        </div>

                        <!-- Reason -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Reason <span class="text-red-500">*</span>
                            </label>
                            <select id="adj_reason" name="reason" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                onchange="toggleCustomReason()">
                                <option value="">Select Reason</option>
                                <optgroup label="Increase Stock" id="increase_reasons" style="display:none;">
                                    <option value="Found Stock">Found Stock</option>
                                    <option value="Return from Customer">Return from Customer</option>
                                    <option value="Return from Production">Return from Production</option>
                                    <option value="Correction">Inventory Correction</option>
                                    <option value="Recount">Physical Recount</option>
                                    <option value="Other">Other (Specify)</option>
                                </optgroup>
                                <optgroup label="Decrease Stock" id="decrease_reasons" style="display:none;">
                                    <option value="Damaged">Damaged Goods</option>
                                    {{-- <option value="Expired">Expired</option> --}}
                                    <option value="Lost">Lost/Missing</option>
                                    <option value="Stolen">Stolen</option>
                                    <option value="Waste">Waste/Scrapped</option>
                                    <option value="Sample">Sample/Demo</option>
                                    <option value="Correction">Inventory Correction</option>
                                    <option value="Recount">Physical Recount</option>
                                    <option value="Other">Other (Specify)</option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Custom Reason (shown when "Other" is selected) -->
                        <div id="custom_reason_div" style="display:none;">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Specify Reason <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="adj_custom_reason" name="custom_reason"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Enter custom reason">
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="adj_quantity" name="quantity" required min="1"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Enter quantity to adjust" oninput="calculateNewStock()">
                        </div>

                        <!-- New Stock Preview -->
                        <div id="new_stock_preview" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
                            style="display:none;">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                New Stock Level:
                                <span id="preview_new_stock" class="font-bold text-lg"></span>
                                <span id="preview_change" class="ml-2 text-sm"></span>
                            </p>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Additional Notes
                            </label>
                            <textarea id="adj_notes" name="notes" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Enter any additional notes..."></textarea>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6 pt-4 border-t dark:border-gray-700">
                        <button type="button" onclick="closeStockAdjustmentModal()"
                            class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Adjust Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Adjustment History Modal -->
    <div id="adjustmentHistoryModal"
        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto max-h-full max-w-full z-50">
        <div
            class="relative top-10 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-md bg-white dark:bg-gray-800 mb-10">
            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-4 border-b dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Movement History: <span id="history_product_name"></span>
                </h3>
                <button onclick="closeAdjustmentHistoryModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div id="historyModalContent" class="mt-4 max-h-[70vh] overflow-y-auto">
                <div class="flex justify-center items-center py-12">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- View Product Modal -->
    <div id="viewProductModal"
        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto max-h-full max-w-full z-50">
        <div
            class="relative top-10 mx-auto p-5 border w-11/12 max-w-full shadow-lg rounded-md bg-white dark:bg-gray-800 mb-10">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-3 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="view_product_name">Product
                        Details</h3>
                    <button onclick="closeViewProductModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mt-4 max-h-[60vh] overflow-y-auto pr-2">
                    <div class="grid grid-cols-1 gap-2">
                        <!-- Left: Product Image and Details -->
                        <div class="lg:col-span-2 space-y-2">
                            <!-- Product Image -->
                            {{-- <div id="view_image_container" class="flex justify-center" style="display:none;">
                                <img id="view_product_image" src="" alt="Product Image"
                                    class="max-w-full h-auto max-h-64 rounded-lg border border-gray-300 dark:border-gray-600 object-cover"
                                    onerror="this.parentElement.style.display='none'">
                            </div> --}}

                            <div class="grid grid-cols-3 gap-2">
                                <div id="view_stock_name_container" style="display:none;" class="min-w-0">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Stock
                                        Name</label>
                                    <p class="text-lg text-gray-900 dark:text-white break-words overflow-hidden" id="view_stock_name"></p>
                                </div>
                                <div class="min-w-0">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Product
                                        Name</label>
                                    <p class="text-lg text-gray-900 dark:text-white break-words overflow-hidden"
                                        id="view_product_name_detail">
                                    </p>
                                </div>
                                <div class="min-w-0">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400">SKU</label>
                                    <p class="text-lg text-gray-900 dark:text-white break-words overflow-hidden" id="view_sku"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div class="min-w-0">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400">Brand</label>
                                    <p class="text-lg text-gray-900 dark:text-white break-words overflow-hidden" id="view_brand"></p>
                                </div>
                                <div id="view_size_container" style="display:none;" class="min-w-0">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400">Size</label>
                                    <p class="text-lg text-gray-900 dark:text-white break-words overflow-hidden" id="view_size"></p>
                                </div>
                                <div id="view_color_container" style="display:none;" class="min-w-0">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400">Color</label>
                                    <p class="text-lg text-gray-900 dark:text-white break-words overflow-hidden" id="view_color"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div class="min-w-0">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400">Category</label>
                                    <p class="text-lg text-gray-900 dark:text-white break-words overflow-hidden" id="view_category"></p>
                                </div>
                                <div id="view_preferred_supplier_container" style="display:none;" class="min-w-0">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Preferred
                                        Supplier</label>
                                    <p class="text-lg text-gray-900 dark:text-white break-words overflow-hidden" id="view_preferred_supplier"></p>
                                </div>
                                <div class="min-w-0">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400">Quantity</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white break-words overflow-hidden" id="view_quantity">
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div class="min-w-0">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Base
                                        Price</label>
                                    <p class="text-lg font-semibold text-green-600 dark:text-green-400 break-words overflow-hidden"
                                        id="view_price"></p>
                                </div>
                                <div class="min-w-0">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Total
                                        Cost</label>
                                    <p class="text-lg font-semibold text-blue-600 dark:text-blue-400 break-words overflow-hidden"
                                        id="view_total_cost">₱0.00</p>
                                </div>

                                <div class="min-w-0">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Inventory
                                        Value</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white break-words overflow-hidden"
                                        id="view_inventory_value"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div id="view_markup_container" style="display:none;" class="min-w-0">
                                    <label
                                        class="block text-sm font-medium text-gray-500 dark:text-gray-400">Markup</label>
                                    <p class="text-lg font-semibold text-gray-600 dark:text-white break-words overflow-hidden" id="view_markup">
                                    </p>
                                </div>
                                <div class="min-w-0">
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Stock
                                        Status</label>
                                    <div id="view_stock_status"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Supplier & Product Info -->
                        <div class="lg:col-span-5 space-y-2">

                            <div class="grid grid-cols-2 gap-2">
                                <!-- Supplier Information -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Supplier
                                        Information
                                    </h4>
                                    <div class="space-y-3">
                                        <div id="view_last_supplier_container" style="display:none;">
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400">Last
                                                Supplier</label>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white"
                                                id="view_last_supplier"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-3"
                                                id="view_last_received">
                                            </p>
                                        </div>
                                        <div id="view_last_purchase_price_container" style="display:none;">
                                            <label
                                                class="block text-xs font-medium text-gray-500 dark:text-gray-400">Last
                                                Purchase Price</label>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white"
                                                id="view_last_purchase_price"></p>
                                            <p class="text-xs text-green-600" id="view_price_margin"
                                                style="display:none;"></p>
                                        </div>
                                        <div id="view_no_supplier" class="text-sm text-gray-500 dark:text-gray-400">No
                                            supplier information available</div>
                                    </div>
                                </div>

                                <!-- Product Info -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Product Info
                                    </h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Product ID</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white"
                                                id="view_product_id"></span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Created</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white"
                                                id="view_created_at"></span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Last Updated</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white"
                                                id="view_updated_at"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6" id="view_description_container" style="display:none;">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Description</label>
                        <p class="mt-1 text-gray-900 dark:text-white" id="view_description"></p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t dark:border-gray-700">
                    <button type="button" onclick="closeViewProductModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        Close
                    </button>
                    <button type="button" onclick="openEditFromView()"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                        Edit Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Costing Modal -->
    <div id="productCostingModal"
        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto max-h-full max-w-full z-50">
        <div
            class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800 mb-10">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-3 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Product Costing: <span
                            id="costing_product_name"></span></h3>
                    <button onclick="closeProductCostingModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="productCostingForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="mt-4 space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-300">Enter the cost components for this
                                product. All fields are optional. The system will calculate the total cost.</p>
                        </div>

                        <!-- Cost Components -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="costing_raw_material_cost"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Raw Material
                                    Cost</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" id="costing_raw_material_cost" name="raw_material_cost"
                                        step="0.01" min="0" oninput="calculateCostingTotalCost()"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <div>
                                <label for="costing_labor_cost"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Labor
                                    Cost</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" id="costing_labor_cost" name="labor_cost" step="0.01"
                                        min="0" oninput="calculateCostingTotalCost()"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <div>
                                <label for="costing_overhead_cost"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Overhead
                                    Cost</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" id="costing_overhead_cost" name="overhead_cost"
                                        step="0.01" min="0" oninput="calculateCostingTotalCost()"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <div>
                                <label for="costing_shipping_cost"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipping Cost
                                    per
                                    Unit</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" id="costing_shipping_cost" name="shipping_cost_per_unit"
                                        step="0.01" min="0" oninput="calculateCostingTotalCost()"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <div>
                                <label for="costing_tax_amount"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tax Amount per
                                    Unit</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" id="costing_tax_amount" name="tax_amount_per_unit"
                                        step="0.01" min="0" oninput="calculateCostingTotalCost()"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <div>
                                <label for="costing_handling_cost"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Handling
                                    Cost</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" id="costing_handling_cost" name="handling_cost"
                                        step="0.01" min="0" oninput="calculateCostingTotalCost()"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <!-- Current Price (Read-only) -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current
                                        Selling Price</label>
                                    <div class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400"
                                        id="costing_current_price">₱0.00</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current
                                        Total Cost</label>
                                    <div class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400"
                                        id="costing_current_total_cost">₱0.00</div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="costing_notes"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cost Notes</label>
                            <textarea id="costing_notes" name="cost_notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Optional notes about cost calculations..."></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t dark:border-gray-700">
                        <button type="button" onclick="closeProductCostingModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update Costing
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Product Costing Modal Functions
        let currentProductData = null; // Store current product data for calculations

        function calculateCostingTotalCost() {
            // Get all cost component values
            const rawMaterial = parseFloat(document.getElementById('costing_raw_material_cost').value) || 0;
            const labor = parseFloat(document.getElementById('costing_labor_cost').value) || 0;
            const overhead = parseFloat(document.getElementById('costing_overhead_cost').value) || 0;
            const shipping = parseFloat(document.getElementById('costing_shipping_cost').value) || 0;
            const tax = parseFloat(document.getElementById('costing_tax_amount').value) || 0;
            const handling = parseFloat(document.getElementById('costing_handling_cost').value) || 0;

            // Calculate total cost
            const totalCost = rawMaterial + labor + overhead + shipping + tax + handling;

            // Update display
            document.getElementById('costing_current_total_cost').textContent = '₱' + new Intl.NumberFormat('en-PH', {
                minimumFractionDigits: 2
            }).format(totalCost);

            // If product has markup pricing, calculate and update selling price
            if (currentProductData && currentProductData.pricing_method === 'markup' && currentProductData.markup_price) {
                const markupPercentage = currentProductData.markup_price.markup_percentage || 0;
                const calculatedPrice = totalCost * (1 + markupPercentage / 100);

                document.getElementById('costing_current_price').textContent = '₱' + new Intl.NumberFormat('en-PH', {
                    minimumFractionDigits: 2
                }).format(calculatedPrice);
            }
        }

        function openProductCostingModal(productId, productName) {
            document.getElementById('costing_product_name').textContent = productName;
            document.getElementById('productCostingModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Fetch product costing data
            fetch(`/inventory/products/${productId}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const product = data.product;
                    currentProductData = product; // Store for calculations

                    // Set form action
                    document.getElementById('productCostingForm').action =
                        `/inventory/product-costing/${productId}`;

                    // Populate cost fields
                    document.getElementById('costing_raw_material_cost').value = product.raw_material_cost || '';
                    document.getElementById('costing_labor_cost').value = product.labor_cost || '';
                    document.getElementById('costing_overhead_cost').value = product.overhead_cost || '';
                    document.getElementById('costing_shipping_cost').value = product.shipping_cost_per_unit || '';
                    document.getElementById('costing_tax_amount').value = product.tax_amount_per_unit || '';
                    document.getElementById('costing_handling_cost').value = product.handling_cost || '';
                    document.getElementById('costing_notes').value = product.cost_notes || '';

                    // Calculate and display selling price based on pricing method
                    let sellingPrice = 0;
                    if (product.pricing_method === 'markup' && product.markup_price && product.total_cost) {
                        // Use markup calculation
                        const markupPercentage = product.markup_price.markup_percentage || 0;
                        sellingPrice = product.total_cost * (1 + markupPercentage / 100);
                    } else {
                        // Use manual price
                        sellingPrice = product.price || 0;
                    }

                    // Display current selling price
                    document.getElementById('costing_current_price').textContent = '₱' + new Intl.NumberFormat(
                        'en-PH', {
                            minimumFractionDigits: 2
                        }).format(sellingPrice);

                    // Display current total cost
                    document.getElementById('costing_current_total_cost').textContent = '₱' + new Intl.NumberFormat(
                        'en-PH', {
                            minimumFractionDigits: 2
                        }).format(product.total_cost || 0);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load product costing data.');
                    closeProductCostingModal();
                });
        }

        function closeProductCostingModal() {
            document.getElementById('productCostingModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('productCostingForm').reset();
            currentProductData = null; // Clear stored data
        }

        // Close on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('productCostingModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeProductCostingModal();
                }
            }
        });

        // Close on outside click
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('productCostingModal');
            if (modal) {
                modal.addEventListener('click', function(event) {
                    if (event.target === this) {
                        closeProductCostingModal();
                    }
                });
            }
        });
    </script>

</x-app-layout>
