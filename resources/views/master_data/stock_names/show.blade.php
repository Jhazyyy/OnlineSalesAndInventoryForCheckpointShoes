<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div
                class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Name Details</h2>
                            <p class="text-gray-600 dark:text-gray-400">View stock name information</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('master_data.stock_names.edit', $stockName) }}"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Edit
                            </a>
                            <a href="{{ route('master_data.stock_names.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Name Information -->
            <div
                class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Stock
                                        Code:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $stockName->stock_code }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Stock
                                        Name:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $stockName->name }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</span>
                                    @if ($stockName->is_active)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            Active
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistics</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total
                                        Products:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $productsCount }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Created:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $stockName->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Last
                                        Updated:</span>
                                    <p class="text-gray-900 dark:text-white">{{ $stockName->updated_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($stockName->description)
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Description</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $stockName->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Products List -->
            @if ($productsCount > 0)
                <div
                    class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Products
                                ({{ $productsCount }})</h3>
                            <a href="{{ route('master_data.products.index', ['stock_name' => $stockName->name]) }}"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                View All Products →
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Product Name
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Size
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Color
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Price
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Stock
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($stockName->products()->limit(10)->get() as $product)
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                <a href="{{ route('master_data.products.show', $product) }}"
                                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                    {{ $product->product_name }}
                                                </a>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $product->size ?? 'N/A' }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $product->color ?? 'N/A' }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                ₱{{ number_format($product->price, 2) }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $product->quantity }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if ($product->quantity > 10)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                        In Stock
                                                    </span>
                                                @elseif ($product->quantity > 0)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                        Low Stock
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                        Out of Stock
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div
                    class="bg-white dark:bg-gray-800 border dark:border-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-gray-500 dark:text-gray-400 mb-4">
                            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8v2m4 6h.01">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">No Products</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">This stock name doesn't have any products yet.</p>
                        <a href="{{ route('master_data.products.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Add Product
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
