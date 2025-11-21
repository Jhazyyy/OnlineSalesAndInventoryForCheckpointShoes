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
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <form method="GET" action="{{ route('inventory.products.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
                            <!-- Search -->
                            <div>
                                <label for="search"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    placeholder="Name, SKU, Brand, Category..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>


                            <!-- Stock Name Filter -->
                            <div>
                                <label for="stock_names"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock Name</label>
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
                                    <option value="out_of_stock"
                                        {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock
                                    </option>
                                </select>
                            </div>

                            <!-- Price Range -->
                            {{-- <div class="flex space-x-2">
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
                            </div> --}}
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
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Image</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <a
                                                href="{{ request()->fullUrlWithQuery(['sort' => 'sku', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                                                SKU
                                                @if (request('sort') === 'sku')
                                                    <span
                                                        class="ml-1">{{ request('order') === 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
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
                                            {{-- Product Image --}}
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($product->image)
                                                    <img src="{{ $product->image_url }}"
                                                        alt="{{ $product->product_name }}"
                                                        class="h-16 w-16 object-cover rounded-none border border-gray-300 dark:border-gray-200">
                                                @else
                                                    <div
                                                        class="h-16 w-16 bg-gray-200 dark:bg-gray-600 rounded-none flex items-center justify-center">
                                                        <svg class="h-8 w-8 text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.56a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div
                                                    class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                                    {{ $product->sku }}
                                                </div>
                                            </td>
                                            {{-- Product Name and Description --}}
                                            <td class="px-6 py-4">
                                                @if ($product->stock_name)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                        <span class="font-medium">Stock Name:</span>
                                                        {{ $product->stock_name }}
                                                    </div>
                                                @endif
                                                <div
                                                    class="text-sm font-medium text-gray-900 dark:text-white break-words">
                                                    {{ $product->product_name }}
                                                </div>
                                                @if ($product->size || $product->color)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        @if ($product->size)
                                                            <span
                                                                class="inline-block bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded mr-1">Size:
                                                                {{ $product->size }}</span>
                                                        @endif
                                                        @if ($product->color)
                                                            <span
                                                                class="inline-block bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">Color:
                                                                {{ $product->color }}</span>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if ($product->description)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400 break-words">
                                                        {{ $product->description }}
                                                    </div>
                                                @endif
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
                                                @if ($product->quantity == 0)
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
                                            @hasrole('super_admin')
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <button onclick="openViewProductModal({{ $product->product_id }})"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View</button>
                                                <button onclick="openEditProductModal({{ $product->product_id }})"
                                                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">Edit</button>
                                                <form method="POST"
                                                    action="{{ route('inventory.products.destroy', $product) }}"
                                                    class="inline-block"
                                                    onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                                </form>
                                            </td>
                                            @endhasrole
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
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Create Product Modal -->
    <div id="createProductModal"
        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div
            class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-3/4 shadow-lg rounded-md bg-white dark:bg-gray-800">
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
                    <div class="mt-4 space-y-6 max-h-[60vh] overflow-y-auto pr-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Stock Name (Base Product) -->
                            <div>
                                <label for="modal_stock_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Stock Name (Base Product) <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <div class="mt-1 flex">
                                    <select id="modal_stock_name" name="stock_name"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">The parent/base product name
                                </p>
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        </div>

                        <!-- Supplier -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                        <!-- Price -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="modal_price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Price<span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" id="modal_price" name="price" step="0.01"
                                        min="0"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>
                        </div>

                        {{-- Product Image and Description --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Product Image -->
                            <div>
                                <label for="modal_image"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Product Image
                                </label>

                                <!-- Image Source Toggle -->
                                <div class="mt-2 flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="modal_image_source" value="file" checked
                                            onchange="toggleModalImageSource()"
                                            class="form-radio text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Upload File</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="modal_image_source" value="url"
                                            onchange="toggleModalImageSource()"
                                            class="form-radio text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Image URL</span>
                                    </label>
                                </div>

                                <!-- File Upload Section -->
                                <div id="modal_fileUploadSection" class="mt-2">
                                    <div class="relative flex justify-center items-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md dark:border-gray-600 overflow-hidden cursor-pointer"
                                        onclick="document.getElementById('modal_image').click()">
                                        <!-- Upload placeholder -->
                                        <div id="modal_uploadPlaceholder" class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                fill="none" viewBox="0 0 48 48">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                                <span
                                                    class="relative bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                                    Click to upload
                                                </span>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 2MB
                                            </p>
                                        </div>
                                        <!-- Hidden File Input -->
                                        <input id="modal_image" name="image" type="file" class="sr-only"
                                            accept="image/*" onchange="previewModalImage(this)">
                                        <!-- Image Preview -->
                                        <img id="modal_previewImg" src="#" alt="Preview"
                                            class="inset-0 max-w-12 h-12 object-cover rounded-none hidden" />
                                    </div>
                                </div>

                                <!-- URL Input Section -->
                                <div id="modal_urlInputSection" class="mt-2 hidden">
                                    <input type="url" id="modal_image_url" name="image_url"
                                        placeholder="https://example.com/image.jpg"
                                        onchange="previewModalImageFromUrl(this.value)"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter a direct image URL
                                    </p>

                                    <!-- URL Image Preview -->
                                    <div id="modal_urlPreviewContainer" class="mt-3 hidden">
                                        <div
                                            class="relative border-2 border-gray-300 border-dashed rounded-md dark:border-gray-600 p-4">
                                            <img id="modal_urlPreviewImg" src="#" alt="URL Preview"
                                                class="max-w-auto h-auto object-cover rounded-none" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="modal_description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Description
                                </label>
                                <textarea id="modal_description" name="description" rows="5" placeholder="Enter product description..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
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

            // Hide custom brand/category inputs
            document.getElementById('modal_custom_brand').style.display = 'none';
            document.getElementById('modal_custom_category').style.display = 'none';
        }

        function previewModalImage(input) {
            const previewImg = document.getElementById('modal_previewImg');
            const uploadPlaceholder = document.getElementById('modal_uploadPlaceholder');
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                    uploadPlaceholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                previewImg.src = '#';
                previewImg.classList.add('hidden');
                uploadPlaceholder.classList.remove('hidden');
            }
        }

        function toggleModalImageSource() {
            const source = document.querySelector('input[name="modal_image_source"]:checked').value;
            const fileSection = document.getElementById('modal_fileUploadSection');
            const urlSection = document.getElementById('modal_urlInputSection');
            const fileInput = document.getElementById('modal_image');
            const urlInput = document.getElementById('modal_image_url');

            if (source === 'file') {
                fileSection.classList.remove('hidden');
                urlSection.classList.add('hidden');
                urlInput.value = '';
                urlInput.disabled = true;
                fileInput.disabled = false;
                document.getElementById('modal_urlPreviewContainer').classList.add('hidden');
            } else {
                fileSection.classList.add('hidden');
                urlSection.classList.remove('hidden');
                fileInput.value = '';
                fileInput.disabled = true;
                urlInput.disabled = false;
                const previewImg = document.getElementById('modal_previewImg');
                const uploadPlaceholder = document.getElementById('modal_uploadPlaceholder');
                previewImg.classList.add('hidden');
                uploadPlaceholder.classList.remove('hidden');
            }
        }

        function previewModalImageFromUrl(url) {
            const urlPreviewContainer = document.getElementById('modal_urlPreviewContainer');
            const urlPreviewImg = document.getElementById('modal_urlPreviewImg');

            if (url && url.trim() !== '') {
                urlPreviewImg.src = url;
                urlPreviewImg.onerror = function() {
                    urlPreviewContainer.classList.add('hidden');
                    alert('Unable to load image from the provided URL. Please check the URL and try again.');
                };
                urlPreviewImg.onload = function() {
                    urlPreviewContainer.classList.remove('hidden');
                };
            } else {
                urlPreviewContainer.classList.add('hidden');
            }
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
                    if (stockNameSelect && stockNameSelect.value === 'custom' && !customStockNameInput.value.trim()) {
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
                    document.getElementById('edit_description').value = data.product.description || '';

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

                    if (data.product.image) {
                        document.getElementById('edit_current_image').classList.remove('hidden');
                        // Check if image is a URL or storage path
                        const imageUrl = data.product.image.startsWith('http://') || data.product.image.startsWith(
                                'https://') ?
                            data.product.image :
                            `/storage/${data.product.image}`;
                        document.getElementById('edit_current_image_preview').src = imageUrl;

                        // If current image is a URL, pre-fill the URL field and switch to URL mode
                        if (data.product.image.startsWith('http://') || data.product.image.startsWith('https://')) {
                            document.querySelector('input[name="edit_image_source"][value="url"]').checked = true;
                            document.getElementById('edit_image_url').value = data.product.image;
                            toggleEditImageSource();
                            previewEditImageFromUrl(data.product.image);
                        } else {
                            // Reset to file mode for local images
                            document.querySelector('input[name="edit_image_source"][value="file"]').checked = true;
                            toggleEditImageSource();
                        }
                    } else {
                        document.getElementById('edit_current_image').classList.add('hidden');
                        // Reset to file mode
                        document.querySelector('input[name="edit_image_source"][value="file"]').checked = true;
                        toggleEditImageSource();
                    }
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

        function previewEditImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('edit_current_image').classList.remove('hidden');
                    document.getElementById('edit_current_image_preview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function toggleEditImageSource() {
            const source = document.querySelector('input[name="edit_image_source"]:checked').value;
            const fileSection = document.getElementById('edit_fileUploadSection');
            const urlSection = document.getElementById('edit_urlInputSection');
            const fileInput = document.getElementById('edit_image');
            const urlInput = document.getElementById('edit_image_url');

            if (source === 'file') {
                fileSection.classList.remove('hidden');
                urlSection.classList.add('hidden');
                urlInput.value = '';
                urlInput.disabled = true;
                fileInput.disabled = false;
                document.getElementById('edit_urlPreviewContainer').classList.add('hidden');
            } else {
                fileSection.classList.add('hidden');
                urlSection.classList.remove('hidden');
                fileInput.value = '';
                fileInput.disabled = true;
                urlInput.disabled = false;
            }
        }

        function previewEditImageFromUrl(url) {
            const urlPreviewContainer = document.getElementById('edit_urlPreviewContainer');
            const urlPreviewImg = document.getElementById('edit_urlPreviewImg');

            if (url && url.trim() !== '') {
                urlPreviewImg.src = url;
                urlPreviewImg.onerror = function() {
                    urlPreviewContainer.classList.add('hidden');
                    alert('Unable to load image from the provided URL. Please check the URL and try again.');
                };
                urlPreviewImg.onload = function() {
                    urlPreviewContainer.classList.remove('hidden');
                };
            } else {
                urlPreviewContainer.classList.add('hidden');
            }
        }

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
                    document.getElementById('view_quantity').textContent = new Intl.NumberFormat().format(data.quantity);
                    document.getElementById('view_price').textContent = '₱' + new Intl.NumberFormat('en-PH', {
                        minimumFractionDigits: 2
                    }).format(data.price);
                    
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
                        document.getElementById('view_preferred_supplier').textContent = data.preferred_supplier.supplier_name || data.preferred_supplier.name;
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
                        document.getElementById('view_last_supplier').textContent = data.last_supplier.supplier_name || data.last_supplier.name;
                        if (data.last_received_at) {
                            document.getElementById('view_last_received').textContent = 'Last received: ' + new Date(data.last_received_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        }
                        document.getElementById('view_no_supplier').style.display = 'none';
                    } else {
                        document.getElementById('view_last_supplier_container').style.display = 'none';
                    }
                    
                    // Last Purchase Price
                    if (data.last_purchase_price) {
                        document.getElementById('view_last_purchase_price_container').style.display = 'block';
                        document.getElementById('view_last_purchase_price').textContent = '₱' + new Intl.NumberFormat('en-PH', {
                            minimumFractionDigits: 2
                        }).format(data.last_purchase_price);
                        
                        if (data.price > data.last_purchase_price) {
                            const margin = data.price - data.last_purchase_price;
                            document.getElementById('view_price_margin').style.display = 'block';
                            document.getElementById('view_price_margin').textContent = 'Margin: ₱' + new Intl.NumberFormat('en-PH', {
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
                    document.getElementById('view_created_at').textContent = new Date(data.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    document.getElementById('view_updated_at').textContent = new Date(data.updated_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

                    if (data.image) {
                        document.getElementById('view_image_container').classList.remove('hidden');
                        // Check if image is a URL or storage path
                        const imageUrl = data.image.startsWith('http://') || data.image.startsWith('https://') ?
                            data.image :
                            `/storage/${data.image}`;
                        document.getElementById('view_product_image').src = imageUrl;
                        document.getElementById('view_no_image').classList.add('hidden');
                    } else {
                        document.getElementById('view_image_container').classList.add('hidden');
                        document.getElementById('view_no_image').classList.remove('hidden');
                    }

                    if (data.description) {
                        document.getElementById('view_description_container').style.display = 'block';
                        document.getElementById('view_description').textContent = data.description;
                    } else {
                        document.getElementById('view_description_container').style.display = 'none';
                    }

                    let statusHTML = '';
                    if (data.quantity <= 0) {
                        statusHTML =
                            '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800"><span class="w-2 h-2 mr-1 bg-red-500 rounded-full"></span>Out of Stock</span>';
                    } else if (data.quantity <= 10) {
                        statusHTML =
                            '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800"><span class="w-2 h-2 mr-1 bg-yellow-500 rounded-full"></span>Low Stock</span>';
                    } else {
                        statusHTML =
                            '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"><span class="w-2 h-2 mr-1 bg-green-500 rounded-full"></span>In Stock</span>';
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
        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div
            class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-3/4 shadow-lg rounded-md bg-white dark:bg-gray-800">
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="edit_size"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Size <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <input type="text" id="edit_size" name="size"
                                    placeholder="e.g., 42, Large, XL"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="edit_color"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Color <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <input type="text" id="edit_color" name="color"
                                    placeholder="e.g., Black, Red, Blue"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="edit_sku"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">SKU</label>
                                <input type="text" id="edit_sku" name="sku"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="edit_preferred_supplier_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Preferred Supplier <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <select id="edit_preferred_supplier_id" name="preferred_supplier_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select a supplier...</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="edit_price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Price<span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" id="edit_price" name="price" step="0.01"
                                        min="0"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product
                                    Image</label>
                                <div id="edit_current_image" class="mt-2 hidden">
                                    <img id="edit_current_image_preview" src="" alt="Current"
                                        class="h-32 w-32 object-cover rounded-lg border">
                                    <p class="text-xs text-gray-500 mt-1">Current image</p>
                                </div>

                                <!-- Image Source Toggle -->
                                <div class="mt-2 flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="edit_image_source" value="file" checked
                                            onchange="toggleEditImageSource()"
                                            class="form-radio text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Upload File</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="edit_image_source" value="url"
                                            onchange="toggleEditImageSource()"
                                            class="form-radio text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Image URL</span>
                                    </label>
                                </div>

                                <!-- File Upload Section -->
                                <div id="edit_fileUploadSection" class="mt-2">
                                    <input type="file" id="edit_image" name="image" accept="image/*"
                                        onchange="previewEditImage(this)"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>

                                <!-- URL Input Section -->
                                <div id="edit_urlInputSection" class="mt-2 hidden">
                                    <input type="url" id="edit_image_url" name="image_url"
                                        placeholder="https://example.com/image.jpg"
                                        onchange="previewEditImageFromUrl(this.value)"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter a direct image URL
                                    </p>

                                    <!-- URL Image Preview -->
                                    <div id="edit_urlPreviewContainer" class="mt-3 hidden">
                                        <div
                                            class="relative border-2 border-gray-300 border-dashed rounded-md dark:border-gray-600 p-4">
                                            <img id="edit_urlPreviewImg" src="#" alt="URL Preview"
                                                class="max-w-full h-auto object-cover rounded-md" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="edit_description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea id="edit_description" name="description" rows="5"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t dark:border-gray-700">
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

    <!-- View Product Modal -->
    <div id="viewProductModal"
        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div
            class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-3/4 shadow-lg rounded-md bg-white dark:bg-gray-800">
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
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left: Image -->
                        <div class="lg:col-span-1">
                            <div id="view_image_container" class="hidden">
                                <img id="view_product_image" src="" alt="Product"
                                    class="w-full h-auto object-cover rounded-lg border">
                            </div>
                            <div id="view_no_image"
                                class="w-full h-64 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center border hidden">
                                <div class="text-center">
                                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No image available</p>
                                </div>
                            </div>
                        </div>

                        <!-- Middle: Product Details -->
                        <div class="lg:col-span-1 space-y-4">
                            <div id="view_stock_name_container" style="display:none;">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Stock Name (Base Product)</label>
                                <p class="text-lg text-gray-900 dark:text-white" id="view_stock_name"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Product Name</label>
                                <p class="text-lg text-gray-900 dark:text-white" id="view_product_name_detail"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">SKU</label>
                                <p class="text-lg text-gray-900 dark:text-white" id="view_sku"></p>
                            </div>
                            <div id="view_size_container" style="display:none;">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Size</label>
                                <p class="text-lg text-gray-900 dark:text-white" id="view_size"></p>
                            </div>
                            <div id="view_color_container" style="display:none;">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Color</label>
                                <p class="text-lg text-gray-900 dark:text-white" id="view_color"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Brand</label>
                                <p class="text-lg text-gray-900 dark:text-white" id="view_brand"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Category</label>
                                <p class="text-lg text-gray-900 dark:text-white" id="view_category"></p>
                            </div>
                            <div id="view_preferred_supplier_container" style="display:none;">
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Preferred Supplier</label>
                                <p class="text-lg text-gray-900 dark:text-white" id="view_preferred_supplier"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Quantity</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white" id="view_quantity"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Base Price</label>
                                    <p class="text-lg font-semibold text-green-600 dark:text-green-400" id="view_price"></p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Stock Status</label>
                                <div id="view_stock_status"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Inventory Value</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white" id="view_inventory_value"></p>
                            </div>
                        </div>

                        <!-- Right: Supplier & Product Info -->
                        <div class="lg:col-span-1 space-y-6">
                            <!-- Supplier Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Supplier Information</h4>
                                <div class="space-y-3">
                                    <div id="view_last_supplier_container" style="display:none;">
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Last Supplier</label>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white" id="view_last_supplier"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400" id="view_last_received"></p>
                                    </div>
                                    <div id="view_last_purchase_price_container" style="display:none;">
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">Last Purchase Price</label>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white" id="view_last_purchase_price"></p>
                                        <p class="text-xs text-green-600" id="view_price_margin" style="display:none;"></p>
                                    </div>
                                    <div id="view_no_supplier" class="text-sm text-gray-500 dark:text-gray-400">No supplier information available</div>
                                </div>
                            </div>

                            <!-- Product Info -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Product Info</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Product ID</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white" id="view_product_id"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Created</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white" id="view_created_at"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Last Updated</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white" id="view_updated_at"></span>
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
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Edit Product
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
