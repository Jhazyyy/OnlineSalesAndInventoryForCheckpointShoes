<x-app-layout>
    <div class="w-full h-screen">
        <div :class="navOpen ? 'flex-1' : 'w-full'" class="h-full overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 min-h-full flex flex-col">
                <div class="flex-1 p-6 text-gray-900 dark:text-gray-100">
                    <!-- Header -->
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-3xl font-bold">Reorder Items</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Products at or below reorder level</p>
                        </div>
                        <a href="{{ route('reports.index') }}" class="text-sm text-blue-600 hover:underline">Back to Reports</a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-2 rounded">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-2 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filters/Search -->
                    <form method="GET" action="{{ route('reports.reorder') }}" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Search product</label>
                                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, brand, or category" class="w-full px-3 py-2 rounded border dark:bg-gray-700 dark:border-gray-600" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Category</label>
                                <input type="text" name="category" value="{{ $filters['category'] ?? '' }}" placeholder="Optional category filter" class="w-full px-3 py-2 rounded border dark:bg-gray-700 dark:border-gray-600" />
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Apply Filters</button>
                            </div>
                        </div>
                    </form>

                    <!-- Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded p-4">
                            <div class="text-sm text-gray-500">Total candidates</div>
                            <div class="text-2xl font-bold">{{ $report['summary']['total_candidates'] ?? 0 }}</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded p-4">
                            <div class="text-sm text-gray-500">Out of stock</div>
                            <div class="text-2xl font-bold">{{ $report['summary']['out_of_stock'] ?? 0 }}</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded p-4">
                            <div class="text-sm text-gray-500">Critical</div>
                            <div class="text-2xl font-bold">{{ $report['summary']['critical'] ?? 0 }}</div>
                        </div>
                    </div>

                    <!-- Reorder Table -->
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Product</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Category</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Qty</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Reorder level</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Suggested</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Supplier</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse(($report['products'] ?? []) as $product)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-medium">{{ $product->product_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $product->product_brand }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">{{ $product->product_category }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $product->quantity <= 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">{{ $product->reorder_level ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm">{{ $product->suggested_order_qty ?? 1 }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <form method="POST" action="{{ route('reports.reorder.create') }}" class="flex items-center gap-2">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->product_id }}" />
                                                <input type="number" name="quantity" min="1" value="{{ max(1, (int)($product->suggested_order_qty ?? 1)) }}" class="w-20 px-2 py-1 rounded border dark:bg-gray-700 dark:border-gray-600" />
                                                <select name="supplier_id" class="px-2 py-1 rounded border dark:bg-gray-700 dark:border-gray-600">
                                                    <option value="">Select supplier</option>
                                                    @foreach($suppliers as $s)
                                                        <option value="{{ $s->supplier_id }}" {{ $product->preferred_supplier_id == $s->supplier_id ? 'selected' : '' }}>
                                                            {{ $s->supplier_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                                <button type="submit" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-sm">Reorder</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">No products found that need reordering.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
