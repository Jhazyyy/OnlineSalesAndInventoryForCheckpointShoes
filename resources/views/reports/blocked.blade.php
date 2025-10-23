<x-app-layout>
    <div class="w-full h-screen">
        <div :class="navOpen ? 'flex-1' : 'w-full'" class="h-full overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 min-h-full flex flex-col">
                <div class="flex-1 p-6 text-gray-900 dark:text-gray-100">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-3xl font-bold">Blocked Items</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Products that are refurbished, damaged, or completely wasted</p>
                        </div>
                        <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            ← Back to Reports
                        </a>
                    </div>

                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('reports.blocked') }}" class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
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
                                <a href="{{ route('reports.blocked') }}" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white px-4 py-2 rounded-lg font-medium">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Refurbished</p>
                                    <p class="text-3xl font-bold mt-1">{{ $report['summary']['total_refurbished_qty'] ?? 0 }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M4 10l6-6m4 16l6-6" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Damaged (Shipped)</p>
                                    <p class="text-3xl font-bold mt-1">{{ $report['summary']['total_damaged_shipped_qty'] ?? 0 }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 5H7a2 2 0 01-2-2v-3m14 5a2 2 0 002-2v-3M5 10l-2-2m0 0l2-2m-2 2h6" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Damaged (Received)</p>
                                    <p class="text-3xl font-bold mt-1">{{ $report['summary']['total_inbound_damaged_qty'] ?? 0 }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-red-500 to-red-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Waste Qty</p>
                                    <p class="text-3xl font-bold mt-1">{{ $report['summary']['total_waste_qty'] ?? 0 }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-7 7-4-4" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-rose-500 to-rose-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Waste Value</p>
                                    <p class="text-3xl font-bold mt-1">₱{{ number_format($report['summary']['total_waste_value'] ?? 0, 2) }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Export Buttons -->
                    <div class="flex gap-4 mb-6">
                        <a href="{{ route('reports.export-pdf', ['reportType' => 'blocked', 'start_date' => request('start_date', $filters['start_date'] ?? ''), 'end_date' => request('end_date', $filters['end_date'] ?? '')]) }}" target="_blank" class="inline-block">
                            <span class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export PDF
                            </span>
                        </a>

                        <a href="{{ route('reports.export-excel', ['reportType' => 'blocked', 'start_date' => request('start_date', $filters['start_date'] ?? ''), 'end_date' => request('end_date', $filters['end_date'] ?? '')]) }}" class="inline-block">
                            <span class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export Excel
                            </span>
                        </a>
                    </div>

                    <!-- Products Table -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                        <h3 class="text-xl font-bold mb-4">Blocked Items by Product</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-800 text-left">
                                    <tr>
                                        <th class="p-3 rounded-tl-lg">Product</th>
                                        <th class="p-3">Brand</th>
                                        <th class="p-3">Category</th>
                                        <th class="p-3">Refurbished</th>
                                        <th class="p-3">Damaged (Shipped)</th>
                                        <th class="p-3">Damaged (Received)</th>
                                        <th class="p-3">Waste Qty</th>
                                        <th class="p-3 rounded-tr-lg">Waste Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($report['products'] ?? []) as $row)
                                        <tr class="border-b dark:border-gray-600 hover:bg-white dark:hover:bg-gray-800 transition">
                                            <td class="p-3 font-medium">{{ $row['product_name'] }}</td>
                                            <td class="p-3">{{ $row['product_brand'] ?? 'N/A' }}</td>
                                            <td class="p-3">{{ $row['product_category'] ?? 'N/A' }}</td>
                                            <td class="p-3">{{ (int) $row['refurbished_qty'] }}</td>
                                            <td class="p-3">{{ (int) $row['damaged_shipped_qty'] }}</td>
                                            <td class="p-3">{{ (int) $row['inbound_damaged_qty'] }}</td>
                                            <td class="p-3">{{ (int) $row['waste_qty'] }}</td>
                                            <td class="p-3">₱{{ number_format($row['waste_value'] ?? 0, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="p-8 text-center text-gray-500">No blocked items found for the selected period</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
