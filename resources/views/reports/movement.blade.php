<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Movement Report</h2>
                        <p class="text-gray-600 dark:text-gray-400">Track all inventory movements and adjustments</p>
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

            <!-- Date Filter -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.movement') }}" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start
                                    Date</label>
                                <input type="date" name="start_date"
                                    value="{{ request('start_date', $startDate ?? '') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End
                                    Date</label>
                                <input type="date" name="end_date" value="{{ request('end_date', $endDate ?? '') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="flex flex-wrap gap-2 sm:justify-end sm:items-end">
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Apply Filter
                                </button>
                                <a href="{{ route('reports.movement') }}"
                                    class="inline-flex items-center justify-center px-3 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @php
                    $cards = [
                        [
                            'label' => 'Total Movements',
                            'color' => 'from-indigo-500 to-indigo-600',
                            'value' => $report['summary']['total_movements'] ?? 0,
                            'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                        ],
                        [
                            'label' => 'Stock In',
                            'color' => 'from-green-500 to-green-600',
                            'value' => $report['summary']['stock_in'] ?? 0,
                            'icon' => 'M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4',
                        ],
                        [
                            'label' => 'Stock Out',
                            'color' => 'from-red-500 to-red-600',
                            'value' => $report['summary']['stock_out'] ?? 0,
                            'icon' => 'M17 8l4 4m0 0l-4 4m4-4H3',
                        ],
                        [
                            'label' => 'Adjustments',
                            'color' => 'from-yellow-500 to-yellow-600',
                            'value' => $report['summary']['adjustments'] ?? 0,
                            'icon' =>
                                'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
                        ],
                    ];
                @endphp

                @foreach ($cards as $card)
                    <div
                        class="bg-gradient-to-br {{ $card['color'] }} text-white rounded-lg shadow-lg p-5 transform hover:scale-[1.03] transition duration-300 ease-in-out">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs sm:text-sm opacity-90">{{ $card['label'] }}</p>
                                <p class="text-2xl sm:text-3xl font-bold mt-1">{{ $card['value'] }}</p>
                            </div>
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 opacity-70" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Export Buttons -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 flex flex-wrap gap-3 sm:gap-4">
                    <a href="{{ route('reports.export-pdf', ['reportType' => 'movement', 'start_date' => request('start_date', $startDate ?? ''), 'end_date' => request('end_date', $endDate ?? '')]) }}"
                        target="_blank"
                        class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-medium rounded-md transition w-fit">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export PDF
                    </a>

                    <a href="{{ route('reports.export-excel', ['reportType' => 'movement', 'start_date' => request('start_date', $startDate ?? ''), 'end_date' => request('end_date', $endDate ?? '')]) }}"
                        class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-green-600 hover:bg-green-700 text-white text-xs sm:text-sm font-medium rounded-md transition w-fit">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>

            <!-- Main Analytics -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        <!-- Recent Movements Table -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6 lg:col-span-2">
                            <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Recent Stock
                                Movements</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-100 dark:bg-gray-800 text-left">
                                        <tr>
                                            <th class="p-3 rounded-tl-lg">Date</th>
                                            <th class="p-3">Product</th>
                                            <th class="p-3">Type</th>
                                            <th class="p-3">Quantity</th>
                                            <th class="p-3">Reference</th>
                                            <th class="p-3 rounded-tr-lg">Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($report['recent_movements'] ?? [] as $movement)
                                            <tr
                                                class="border-b dark:border-gray-600 hover:bg-white dark:hover:bg-gray-800 transition">
                                                <td class="p-3 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($movement->date ?? now())->format('M d, Y h:i A') }}
                                                </td>
                                                <td class="p-3 font-medium">{{ $movement->product_name ?? 'Unknown' }}
                                                </td>
                                                <td class="p-3">
                                                    <span
                                                        class="px-2 py-1 rounded text-xs font-bold
                                                        @if ($movement->type == 'in') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                                        @elseif($movement->type == 'out') bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                                                        @else bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 @endif">
                                                        {{ strtoupper($movement->type ?? 'N/A') }}
                                                    </span>
                                                </td>
                                                <td
                                                    class="p-3 font-bold {{ $movement->type == 'in' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ $movement->type == 'in' ? '+' : '-' }}{{ $movement->quantity ?? 0 }}
                                                </td>
                                                <td class="p-3 text-xs text-gray-500">
                                                    {{ $movement->reference ?? 'N/A' }}</td>
                                                <td class="p-3 text-xs text-gray-500">
                                                    {{ Str::limit($movement->notes ?? '-', 30) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="p-8 text-center text-gray-500">No movement
                                                    data available</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Sidebar Widgets -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Most Active
                                Products</h3>
                            <div class="space-y-2">
                                @forelse($report['most_active_products'] ?? [] as $index => $product)
                                    <div
                                        class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition">
                                        <div class="flex items-center gap-3">
                                            <span class="font-bold text-lg text-gray-400">#{{ $index + 1 }}</span>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">
                                                    {{ $product->name ?? 'Unknown' }}</p>
                                                <p class="text-xs text-gray-500">{{ $product->movement_count ?? 0 }}
                                                    movements</p>
                                            </div>
                                        </div>
                                        <div class="text-right text-xs">
                                            <p class="text-green-600 dark:text-green-400 font-bold">
                                                +{{ $product->total_in ?? 0 }}</p>
                                            <p class="text-red-600 dark:text-red-400 font-bold">
                                                -{{ $product->total_out ?? 0 }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No product activity
                                    </p>
                                @endforelse
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Movements by
                                Type</h3>
                            <div class="space-y-3">
                                @forelse($report['movements_by_type'] ?? [] as $type)
                                    <div
                                        class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded">
                                        <div>
                                            <p class="font-medium capitalize text-gray-900 dark:text-white">
                                                {{ $type->type ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">{{ $type->total_quantity ?? 0 }} units
                                            </p>
                                        </div>
                                        <span
                                            class="bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-3 py-1 rounded-full font-bold">
                                            {{ $type->count ?? 0 }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No movement type data
                                    </p>
                                @endforelse
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Movement by
                                Product Category</h3>
                            <div class="space-y-3">
                                @forelse($report['movements_by_category'] ?? [] as $category)
                                    <div
                                        class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">
                                                {{ $category->movement_category ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">{{ $category->product_count ?? 0 }}
                                                products</p>
                                        </div>
                                        <span
                                            class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full font-bold">
                                            {{ $category->total_movements ?? 0 }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No category data</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Daily Movement
                                Trend</h3>
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @forelse($report['movements_by_date'] ?? [] as $day)
                                    <div
                                        class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition">
                                        <span
                                            class="text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($day->date ?? now())->format('M d, Y') }}</span>
                                        <div class="flex gap-4 text-xs">
                                            <span class="text-green-600 dark:text-green-400 font-bold">
                                                <span class="text-gray-500">In:</span> {{ $day->in_count ?? 0 }}
                                            </span>
                                            <span class="text-red-600 dark:text-red-400 font-bold">
                                                <span class="text-gray-500">Out:</span> {{ $day->out_count ?? 0 }}
                                            </span>
                                            <span class="text-indigo-600 dark:text-indigo-400 font-bold">
                                                <span class="text-gray-500">Total:</span> {{ $day->total_count ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No daily trend data
                                    </p>
                                @endforelse
                            </div>
                        </div>
                    </div> <!-- End of grid -->
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
