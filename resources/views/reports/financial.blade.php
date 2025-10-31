<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Financial Report</h2>
                        <p class="text-gray-600 dark:text-gray-400">Comprehensive profit & loss analysis</p>
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

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.financial') }}" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start
                                    Date</label>
                                <input type="date" name="start_date"
                                    value="{{ request('start_date', $startDate ?? '') }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End
                                    Date</label>
                                <input type="date" name="end_date" value="{{ request('end_date', $endDate ?? '') }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="flex flex-wrap gap-2 sm:justify-end sm:items-end">
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Apply Filter
                                </button>
                                <a href="{{ route('reports.financial') }}"
                                    class="inline-flex items-center justify-center px-3 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Profit & Loss Summary -->
            <div
                class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800 rounded-lg shadow-lg p-6 mb-6">
                <h3 class="text-xl font-bold mb-4 text-yellow-900 dark:text-yellow-100">Profit & Loss Summary</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-1">Total Revenue</p>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                            ₱{{ number_format($report['summary']['total_revenue'] ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-1">Total Cost</p>
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400">
                            ₱{{ number_format($report['summary']['total_cost'] ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-1">Net Profit</p>
                        <p
                            class="text-3xl font-bold {{ ($report['summary']['net_profit'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            ₱{{ number_format($report['summary']['net_profit'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-1">Profit Margin</p>
                        <p
                            class="text-3xl font-bold {{ ($report['summary']['profit_margin'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ number_format($report['summary']['profit_margin'] ?? 0, 1) }}%
                        </p>
                    </div>
                </div>
            </div>

            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @php
                    $cards = [
                        [
                            'label' => 'Total Orders',
                            'color' => 'from-blue-500 to-blue-600',
                            'value' => $report['summary']['total_orders'] ?? 0,
                            'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                        ],
                        [
                            'label' => 'Total Payments',
                            'color' => 'from-purple-500 to-purple-600',
                            'value' => $report['summary']['total_payments'] ?? 0,
                            'icon' =>
                                'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                        ],
                        [
                            'label' => 'Total Invoices',
                            'color' => 'from-indigo-500 to-indigo-600',
                            'value' => $report['summary']['total_invoices'] ?? 0,
                            'icon' =>
                                'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        ],
                        [
                            'label' => 'Avg Order Value',
                            'color' => 'from-teal-500 to-teal-600',
                            'value' => '₱' . number_format($report['summary']['avg_order_value'] ?? 0, 2),
                            'icon' =>
                                'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
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
            <div class="flex flex-wrap gap-4 mb-6">
                <a href="{{ route('reports.export-pdf', ['reportType' => 'financial', 'start_date' => request('start_date', $startDate ?? ''), 'end_date' => request('end_date', $endDate ?? '')]) }}"
                    target="_blank"
                    class="inline-flex items-center px-5 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586l5.414 5.414V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </a>

                <a href="{{ route('reports.export-excel', ['reportType' => 'financial', 'start_date' => request('start_date', $startDate ?? ''), 'end_date' => request('end_date', $endDate ?? '')]) }}"
                    class="inline-flex items-center px-5 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586l5.414 5.414V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </a>
            </div>

            <!-- Financial Details -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Monthly Breakdown -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 lg:col-span-2">
                    <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Monthly Performance</h3>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($report['monthly_breakdown'] ?? [] as $month)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-lg">
                                        {{ \Carbon\Carbon::parse($month->month ?? now())->format('F Y') }}</h4>
                                    <span
                                        class="text-sm font-bold {{ ($month->profit ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ ($month->profit ?? 0) >= 0 ? '+' : '' }}₱{{ number_format($month->profit ?? 0, 2) }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500 text-xs">Revenue</p>
                                        <p class="font-bold text-green-600 dark:text-green-400">
                                            ₱{{ number_format($month->revenue ?? 0, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs">Cost</p>
                                        <p class="font-bold text-red-600 dark:text-red-400">
                                            ₱{{ number_format($month->cost ?? 0, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs">Orders</p>
                                        <p class="font-bold">{{ $month->orders ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No monthly data available</p>
                        @endforelse
                    </div>
                </div>

                <!-- Revenue by Payment Status -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Revenue by Payment Status</h3>
                    <div class="space-y-3">
                        @forelse($report['revenue_by_payment_status'] ?? [] as $status)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                <span class="font-medium capitalize">{{ $status->status ?? 'Unknown' }}</span>
                                <span
                                    class="text-green-600 dark:text-green-400 font-bold">₱{{ number_format($status->total ?? 0, 2) }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No payment data</p>
                        @endforelse
                    </div>
                </div>

                <!-- Revenue by Invoice Status -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Revenue by Invoice Status</h3>
                    <div class="space-y-3">
                        @forelse($report['revenue_by_invoice_status'] ?? [] as $status)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                <span class="font-medium capitalize">{{ $status->status ?? 'Unknown' }}</span>
                                <span
                                    class="text-green-600 dark:text-green-400 font-bold">₱{{ number_format($status->total ?? 0, 2) }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No invoice data</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>