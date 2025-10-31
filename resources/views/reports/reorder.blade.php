<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Reorder Items</h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            Products at or below reorder level
                        </p>
                    </div>

                    <a href="{{ route('reports.index') }}"
                       class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out w-fit">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Reports
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.reorder') }}" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Product</label>
                                <input type="text" name="q" placeholder="Name, brand, or category"
                                       value="{{ $filters['q'] ?? '' }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                <input type="text" name="category" placeholder="Optional category filter"
                                       value="{{ $filters['category'] ?? '' }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="flex flex-wrap gap-2 sm:justify-end sm:items-end">
                                <button type="submit"
                                        class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Apply Filters
                                </button>
                                <a href="{{ route('reports.reorder') }}"
                                   class="inline-flex items-center justify-center px-3 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @php
                    $cards = [
                        ['label' => 'Total Candidates', 'color' => 'from-blue-500 to-blue-600', 'value' => $report['summary']['total_candidates'] ?? 0, 'icon' => 'M3 12h18M9 18l-6-6 6-6'],
                        ['label' => 'Out of Stock', 'color' => 'from-red-500 to-red-600', 'value' => $report['summary']['out_of_stock'] ?? 0, 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Critical', 'color' => 'from-yellow-500 to-yellow-600', 'value' => $report['summary']['critical'] ?? 0, 'icon' => 'M12 9v2m0 4h.01']
                    ];
                @endphp

                @foreach($cards as $card)
                    <div class="bg-gradient-to-br {{ $card['color'] }} text-white rounded-lg shadow-lg p-5 transform hover:scale-[1.03] transition duration-300 ease-in-out">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs sm:text-sm opacity-90">{{ $card['label'] }}</p>
                                <p class="text-2xl sm:text-3xl font-bold mt-1">{{ $card['value'] }}</p>
                            </div>
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Reorder Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Products Needing Reorder</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm sm:text-base">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    @foreach(['Product', 'Category', 'Qty', 'Reorder Level', 'Suggested', 'Supplier', 'Action'] as $header)
                                        <th class="px-4 py-3 sm:px-6 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse(($report['products'] ?? []) as $product)
                                    <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition">
                                        <td class="px-4 py-3 sm:px-6">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $product->product_name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $product->product_brand }}</div>
                                        </td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">{{ $product->product_category }}</td>
                                        <td class="px-4 py-3 sm:px-6">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $product->quantity <= 0 ? 'bg-red-100 text-red-800 dark:bg-red-200 dark:text-red-900' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-200 dark:text-yellow-900' }}">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">{{ $product->reorder_level ?? '-' }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">{{ $product->suggested_order_qty ?? 1 }}</td>
                                        <td class="px-4 py-3 sm:px-6">
                                            <form method="POST" action="{{ route('reports.reorder.create') }}" class="flex flex-wrap gap-2 items-center">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                                                <input type="number" name="quantity" min="1" value="{{ max(1, (int)($product->suggested_order_qty ?? 1)) }}"
                                                       class="w-20 rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-1 focus:ring-indigo-500" />
                                                <select name="supplier_id"
                                                        class="rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-1 focus:ring-indigo-500">
                                                    <option value="">Select supplier</option>
                                                    @foreach($suppliers as $s)
                                                        <option value="{{ $s->supplier_id }}" {{ $product->preferred_supplier_id == $s->supplier_id ? 'selected' : '' }}>
                                                            {{ $s->supplier_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                        </td>
                                        <td class="px-4 py-3 sm:px-6 text-right">
                                                <button type="submit"
                                                        class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs sm:text-sm transition">
                                                    Reorder
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No products found that need reordering.
                                        </td>
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
