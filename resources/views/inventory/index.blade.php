<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Current Inventory</h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Monitor and manage your inventory levels</p>
                    </div>

                    <!-- Search Form -->
                    <form method="GET" class="flex items-center gap-2 mt-4 sm:mt-0">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search products..." 
                               class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white w-64" />
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150">
                            Search
                        </button>
                    </form>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Stock</th>
                                {{-- <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reserved</th> --}}
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Available</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reorder Level</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($inventories as $row)
                                @php
                                    $totalStock = (int) ($row->total_stock ?? 0);
                                    $reserved = (int) ($row->reserved ?? 0);
                                    $available = max(0, $totalStock - $reserved);
                                    $reorderLevel = (int) ($row->reorder_level ?? 0);

                                    if ($reorderLevel > 0) {
                                        if ($available <= $reorderLevel / 2) {
                                            $status = 'Critical';
                                            $statusClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                                        } elseif ($available <= $reorderLevel) {
                                            $status = 'Low';
                                            $statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                                        } else {
                                            $status = 'Good';
                                            $statusClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                        }

                                        $percentage = min(100, ($available / ($reorderLevel * 2)) * 100);
                                    } else {
                                        $status = 'Good';
                                        $statusClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                                        $percentage = 100;
                                    }
                                @endphp

                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $row->product_name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $row->sku ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $row->product_category ?? 'Uncategorized' }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ number_format($totalStock) }}
                                    </td>
                                    {{-- <td class="px-6 py-4 text-center text-sm text-gray-600 dark:text-gray-400">
                                        {{ number_format($reserved) }}
                                    </td> --}}
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ number_format($available) }}
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="h-2 rounded-full transition-all duration-300 
                                                    {{ $status === 'Critical' ? 'bg-red-500' : ($status === 'Low' ? 'bg-yellow-500' : 'bg-blue-500') }}" 
                                                    style="width: {{ $percentage }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-900 dark:text-gray-100">
                                        {{ $reorderLevel > 0 ? number_format($reorderLevel) : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No inventory records found</p>
                                            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Start by adding products to your inventory</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($inventories->hasPages())
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                        {{ $inventories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
