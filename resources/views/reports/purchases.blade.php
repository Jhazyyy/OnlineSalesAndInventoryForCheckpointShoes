<x-app-layout>
    <div class="w-full h-screen">
        <div :class="navOpen ? 'flex-1' : 'w-full'" class="h-full overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 min-h-full flex flex-col">
                <div class="flex-1 p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-3xl font-bold">Purchase Report</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Procurement analytics and supplier performance</p>
                        </div>
                        <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            ← Back to Reports
                        </a>
                    </div>

                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('reports.purchases') }}" class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Start Date</label>
                                <input type="date" name="start_date" value="{{ request('start_date', $filters['start_date'] ?? '') }}" 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">End Date</label>
                                <input type="date" name="end_date" value="{{ request('end_date', $filters['end_date'] ?? '') }}" 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                            </div>
                            <div class="flex items-end gap-2">
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                                    Apply Filter
                                </button>
                                <a href="{{ route('reports.purchases') }}" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white px-4 py-2 rounded-lg font-medium">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Total Orders</p>
                                    <p class="text-3xl font-bold mt-1">{{ $report['summary']['total_orders'] ?? 0 }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-red-500 to-red-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Total Amount</p>
                                    <p class="text-3xl font-bold mt-1">₱{{ number_format($report['summary']['total_amount'] ?? 0, 2) }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Items Purchased</p>
                                    <p class="text-3xl font-bold mt-1">{{ $report['summary']['total_items'] ?? 0 }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-teal-500 to-teal-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Avg. Order Value</p>
                                    <p class="text-3xl font-bold mt-1">₱{{ number_format($report['summary']['avg_order_value'] ?? 0, 2) }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Export Buttons -->
                    <div class="flex gap-4 mb-6">
                        <a href="{{ route('reports.export-pdf', ['reportType' => 'purchases', 'start_date' => request('start_date', $filters['start_date'] ?? ''), 'end_date' => request('end_date', $filters['end_date'] ?? '')]) }}" target="_blank" class="inline-block">
                            <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export PDF
                            </button>
                        </a>

                        <a href="{{ route('reports.export-excel', ['reportType' => 'purchases', 'start_date' => request('start_date', $filters['start_date'] ?? ''), 'end_date' => request('end_date', $filters['end_date'] ?? '')]) }}" class="inline-block">
                            <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export Excel
                            </button>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Orders by Status -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-xl font-bold mb-4">Orders by Status</h3>
                            <div class="space-y-3">
                                @forelse($report['orders_by_status'] ?? [] as $status)
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded">
                                    <span class="font-medium capitalize">{{ $status->status ?? 'Unknown' }}</span>
                                    <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full font-bold">
                                        {{ $status->count ?? 0 }}
                                    </span>
                                </div>
                                @empty
                                <p class="text-gray-500 text-center py-4">No data available</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Top 10 Suppliers -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-xl font-bold mb-4">Top 10 Suppliers</h3>
                            <div class="space-y-2">
                                @forelse($report['top_suppliers'] ?? [] as $index => $supplier)
                                <div class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition">
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-lg text-gray-400">#{{ $index + 1 }}</span>
                                        <div>
                                            <p class="font-medium">{{ $supplier->name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">{{ $supplier->total_orders ?? 0 }} orders</p>
                                        </div>
                                    </div>
                                    <span class="text-purple-600 dark:text-purple-400 font-bold">
                                        ₱{{ number_format($supplier->total_spent ?? 0, 2) }}
                                    </span>
                                </div>
                                @empty
                                <p class="text-gray-500 text-center py-4">No supplier data</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Most Purchased Products -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-xl font-bold mb-4">Most Purchased Products</h3>
                            <div class="space-y-2">
                                @forelse($report['top_products'] ?? [] as $index => $product)
                                <div class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition">
                                    <div class="flex items-center gap-3">
                                        <span class="font-bold text-lg text-gray-400">#{{ $index + 1 }}</span>
                                        <div>
                                            <p class="font-medium">{{ $product->name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">{{ $product->quantity ?? 0 }} units</p>
                                        </div>
                                    </div>
                                    <span class="text-purple-600 dark:text-purple-400 font-bold">
                                        ₱{{ number_format($product->total_cost ?? 0, 2) }}
                                    </span>
                                </div>
                                @empty
                                <p class="text-gray-500 text-center py-4">No product data</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Purchases by Date -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-xl font-bold mb-4">Daily Purchase Summary</h3>
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @forelse($report['purchases_by_date'] ?? [] as $purchase)
                                <div class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition">
                                    <span class="text-sm">{{ \Carbon\Carbon::parse($purchase->date ?? now())->format('M d, Y') }}</span>
                                    <div class="text-right">
                                        <p class="font-bold text-purple-600 dark:text-purple-400">₱{{ number_format($purchase->amount ?? 0, 2) }}</p>
                                        <p class="text-xs text-gray-500">{{ $purchase->orders ?? 0 }} orders</p>
                                    </div>
                                </div>
                                @empty
                                <p class="text-gray-500 text-center py-4">No purchase data</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
