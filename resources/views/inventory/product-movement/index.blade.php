<x-app-layout>
    <div class="w-full h-screen">
        <div :class="navOpen ? 'flex-1' : 'w-full'" class="h-full overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 min-h-full flex flex-col">
                <div class="flex-1 p-6 text-gray-900 dark:text-gray-100">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-3xl font-bold">Product Movement Analysis</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Track fast, slow, and non-moving products</p>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('inventory.product-movement.calculate-all') }}" class="inline">
                                @csrf
                                <select name="days" class="border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600">
                                    <option value="30">Last 30 Days</option>
                                    <option value="60">Last 60 Days</option>
                                    <option value="90" selected>Last 90 Days</option>
                                    <option value="180">Last 180 Days</option>
                                </select>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Calculate Movement
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <!-- Fast Moving -->
                        <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-600 dark:text-green-400">Fast Moving</p>
                                    <p class="text-2xl font-semibold text-green-900 dark:text-green-100">
                                        {{ $stats['fast_moving'] }}
                                    </p>
                                    <p class="text-xs text-green-500 dark:text-green-300 mt-1">
                                        {{ $stats['fast_moving_percentage'] }}% of total
                                    </p>
                                </div>
                                <div class="p-3 bg-green-500 rounded-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                            </div>
                            <a href="{{ route('inventory.product-movement.fast-moving') }}" class="text-green-600 dark:text-green-400 text-sm mt-2 inline-block hover:underline">View Products →</a>
                        </div>

                        <!-- Slow Moving -->
                        <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Slow Moving</p>
                                    <p class="text-2xl font-semibold text-yellow-900 dark:text-yellow-100">
                                        {{ $stats['slow_moving'] }}
                                    </p>
                                    <p class="text-xs text-yellow-500 dark:text-yellow-300 mt-1">
                                        {{ $stats['slow_moving_percentage'] }}% of total
                                    </p>
                                </div>
                                <div class="p-3 bg-yellow-500 rounded-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                    </svg>
                                </div>
                            </div>
                            <a href="{{ route('inventory.product-movement.slow-moving') }}" class="text-yellow-600 dark:text-yellow-400 text-sm mt-2 inline-block hover:underline">View Products →</a>
                        </div>

                        <!-- Non-Moving -->
                        <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-red-600 dark:text-red-400">Non-Moving</p>
                                    <p class="text-2xl font-semibold text-red-900 dark:text-red-100">
                                        {{ $stats['non_moving'] }}
                                    </p>
                                    <p class="text-xs text-red-500 dark:text-red-300 mt-1">
                                        {{ $stats['non_moving_percentage'] }}% of total
                                    </p>
                                </div>
                                <div class="p-3 bg-red-500 rounded-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </div>
                            <a href="{{ route('inventory.product-movement.non-moving') }}" class="text-red-600 dark:text-red-400 text-sm mt-2 inline-block hover:underline">View Products →</a>
                        </div>

                        <!-- Promotional -->
                        <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Promotional</p>
                                    <p class="text-2xl font-semibold text-purple-900 dark:text-purple-100">
                                        {{ $stats['promotional_products'] }}
                                    </p>
                                    <p class="text-xs text-purple-500 dark:text-purple-300 mt-1">
                                        Products on promotion
                                    </p>
                                </div>
                                <div class="p-3 bg-purple-500 rounded-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                            </div>
                            <a href="{{ route('inventory.product-movement.promotional') }}" class="text-purple-600 dark:text-purple-400 text-sm mt-2 inline-block hover:underline">View Products →</a>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 p-4 rounded-lg mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm text-blue-700 dark:text-blue-300">
                                <p><strong>Movement Thresholds:</strong></p>
                                <ul class="list-disc list-inside mt-1 space-y-1">
                                    <li><strong>Fast Moving:</strong> {{ $stats['thresholds']['fast_moving_threshold'] }}</li>
                                    <li><strong>Slow Moving:</strong> {{ $stats['thresholds']['slow_moving_threshold'] }}</li>
                                    <li><strong>Non-Moving:</strong> No sales in {{ $stats['thresholds']['non_moving_days'] }}</li>
                                </ul>
                                <p class="mt-2"><strong>Last Analysis:</strong> {{ $stats['last_analysis'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filter and Search -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6">
                        <form method="GET" class="flex gap-4 items-end">
                            <div class="flex-1">
                                <label class="block text-sm font-medium mb-1">Search Products</label>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                    placeholder="Search by name, brand, or category..."
                                    class="w-full border rounded px-3 py-2 dark:bg-gray-600 dark:border-gray-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Category</label>
                                <select name="category" class="border rounded px-3 py-2 dark:bg-gray-600 dark:border-gray-500">
                                    <option value="all" {{ $category === 'all' ? 'selected' : '' }}>All Products</option>
                                    <option value="fast" {{ $category === 'fast' ? 'selected' : '' }}>Fast Moving</option>
                                    <option value="slow" {{ $category === 'slow' ? 'selected' : '' }}>Slow Moving</option>
                                    <option value="non-moving" {{ $category === 'non-moving' ? 'selected' : '' }}>Non-Moving</option>
                                </select>
                            </div>
                            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                Filter
                            </button>
                            <a href="{{ route('inventory.product-movement.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">
                                Clear
                            </a>
                        </form>
                    </div>

                    <!-- Products Table -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Movement</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Velocity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Last Sale</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($products as $product)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $product->product_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $product->product_brand }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $product->product_category ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            {{ $product->quantity }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($product->movement_category === 'fast')
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Fast Moving
                                                </span>
                                            @elseif($product->movement_category === 'slow')
                                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    Slow Moving
                                                </span>
                                            @elseif($product->movement_category === 'non-moving')
                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Non-Moving
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                    Uncategorized
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            {{ $product->movement_velocity ? number_format($product->movement_velocity, 2) . ' units/day' : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            @if($product->last_sale_date)
                                                {{ \Carbon\Carbon::parse($product->last_sale_date)->format('M d, Y') }}
                                                <div class="text-xs">{{ $product->days_since_last_sale }} days ago</div>
                                            @else
                                                <span class="text-gray-400">Never</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($product->is_promotional)
                                                <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                    Promotional
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('inventory.products.show', $product->product_id) }}" 
                                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                                    View
                                                </a>
                                                <form method="POST" action="{{ route('inventory.product-movement.calculate-single', $product->product_id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400">
                                                        Recalculate
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No products found. Try adjusting your filters or run movement calculation first.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="mt-6">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
