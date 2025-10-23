<x-app-layout>
    <div class="w-full h-screen">
        <div :class="navOpen ? 'flex-1' : 'w-full'" class="h-full overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 min-h-full flex flex-col">
                <div class="flex-1 p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-3xl font-bold">Financial Report</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Comprehensive profit & loss analysis</p>
                        </div>
                        <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            ← Back to Reports
                        </a>
                    </div>

                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('reports.financial') }}" class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Start Date</label>
                                <input type="date" name="start_date" value="{{ request('start_date', $startDate ?? '') }}" 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">End Date</label>
                                <input type="date" name="end_date" value="{{ request('end_date', $endDate ?? '') }}" 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                            </div>
                            <div class="flex items-end gap-2">
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                                    Apply Filter
                                </button>
                                <a href="{{ route('reports.financial') }}" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white px-4 py-2 rounded-lg font-medium">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- P&L Summary -->
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800 rounded-lg shadow-lg p-8 mb-6">
                        <h3 class="text-2xl font-bold mb-6 text-yellow-900 dark:text-yellow-100">Profit & Loss Statement</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-1">Total Revenue</p>
                                <p class="text-3xl font-bold text-green-600 dark:text-green-400">₱{{ number_format($report['summary']['total_revenue'] ?? 0, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-1">Total Cost</p>
                                <p class="text-3xl font-bold text-red-600 dark:text-red-400">₱{{ number_format($report['summary']['total_cost'] ?? 0, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-1">Net Profit</p>
                                <p class="text-3xl font-bold {{ ($report['summary']['net_profit'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    ₱{{ number_format($report['summary']['net_profit'] ?? 0, 2) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-1">Profit Margin</p>
                                <p class="text-3xl font-bold {{ ($report['summary']['profit_margin'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ number_format($report['summary']['profit_margin'] ?? 0, 1) }}%
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Metrics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Total Orders</p>
                                    <p class="text-3xl font-bold mt-1">{{ $report['summary']['total_orders'] ?? 0 }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Total Payments</p>
                                    <p class="text-3xl font-bold mt-1">{{ $report['summary']['total_payments'] ?? 0 }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Total Invoices</p>
                                    <p class="text-3xl font-bold mt-1">{{ $report['summary']['total_invoices'] ?? 0 }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-teal-500 to-teal-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Avg Order Value</p>
                                    <p class="text-2xl font-bold mt-1">₱{{ number_format($report['summary']['avg_order_value'] ?? 0, 2) }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Export Buttons -->
                    <div class="flex gap-4 mb-6">
                        <a href="{{ route('reports.export-pdf', ['reportType' => 'financial', 'start_date' => request('start_date', $startDate ?? ''), 'end_date' => request('end_date', $endDate ?? '')]) }}" target="_blank" class="inline-block">
                            <span class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export PDF
                            </span>
                        </a>

                        <a href="{{ route('reports.export-excel', ['reportType' => 'financial', 'start_date' => request('start_date', $startDate ?? ''), 'end_date' => request('end_date', $endDate ?? '')]) }}" class="inline-block">
                            <span class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export Excel
                            </span>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Monthly Breakdown -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6 lg:col-span-2">
                            <h3 class="text-xl font-bold mb-4">Monthly Performance</h3>
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @forelse($report['monthly_breakdown'] ?? [] as $month)
                                <div class="bg-white dark:bg-gray-800 rounded p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-lg">{{ \Carbon\Carbon::parse($month->month ?? now())->format('F Y') }}</h4>
                                        <span class="text-sm {{ ($month->profit ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-bold">
                                            {{ ($month->profit ?? 0) >= 0 ? '+' : '' }}₱{{ number_format($month->profit ?? 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 text-xs">Revenue</p>
                                            <p class="font-bold text-green-600 dark:text-green-400">₱{{ number_format($month->revenue ?? 0, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 text-xs">Cost</p>
                                            <p class="font-bold text-red-600 dark:text-red-400">₱{{ number_format($month->cost ?? 0, 2) }}</p>
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
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-xl font-bold mb-4">Revenue by Payment Status</h3>
                            <div class="space-y-3">
                                @forelse($report['revenue_by_payment_status'] ?? [] as $status)
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded">
                                    <span class="font-medium capitalize">{{ $status->status ?? 'Unknown' }}</span>
                                    <span class="text-green-600 dark:text-green-400 font-bold">
                                        ₱{{ number_format($status->total ?? 0, 2) }}
                                    </span>
                                </div>
                                @empty
                                <p class="text-gray-500 text-center py-4">No payment data</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Revenue by Invoice Status -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-xl font-bold mb-4">Revenue by Invoice Status</h3>
                            <div class="space-y-3">
                                @forelse($report['revenue_by_invoice_status'] ?? [] as $status)
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded">
                                    <span class="font-medium capitalize">{{ $status->status ?? 'Unknown' }}</span>
                                    <span class="text-green-600 dark:text-green-400 font-bold">
                                        ₱{{ number_format($status->total ?? 0, 2) }}
                                    </span>
                                </div>
                                @empty
                                <p class="text-gray-500 text-center py-4">No invoice data</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
