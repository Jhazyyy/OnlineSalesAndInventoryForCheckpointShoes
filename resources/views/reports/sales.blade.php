<x-app-layout>
    <div class="py-2">
        <div class="w-full mx-auto sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Sales Report</h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            Comprehensive sales analytics and performance metrics
                        </p>
                    </div>

                    <a href="{{ route('reports.index') }}"
                        class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out w-fit">
                        Back to Reports
                    </a>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.sales') }}" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start
                                    Date</label>
                                <input type="date" name="start_date"
                                    value="{{ request('start_date', $filters['start_date'] ?? '') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End
                                    Date</label>
                                <input type="date" name="end_date"
                                    value="{{ request('end_date', $filters['end_date'] ?? '') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <div class="flex flex-wrap gap-2 sm:justify-end sm:items-end">
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Apply Filter
                                </button>
                                <a href="{{ route('reports.sales') }}"
                                    class="inline-flex items-center justify-center px-3 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                @php
                    $cards = [
                        [
                            'label' => 'Total Orders',
                            'color' => 'from-blue-500 to-blue-600',
                            'value' => $report['summary']['total_orders'] ?? 0,
                            'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                            'info' => 'Total number of sales orders in period',
                        ],
                        [
                            'label' => 'Total Revenue',
                            'color' => 'from-green-500 to-green-600',
                            'value' => '₱' . number_format($report['summary']['total_revenue'] ?? 0, 2),
                            'icon' =>
                                'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                            'info' => 'Total amount collected from customers (Net Revenue + Tax)',
                        ],
                        [
                            'label' => 'Gross Revenue',
                            'color' => 'from-teal-500 to-teal-600',
                            'value' => '₱' . number_format($report['summary']['gross_revenue'] ?? 0, 2),
                            'icon' =>
                                'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                            'info' => 'Subtotal before discounts and taxes',
                        ],
                        [
                            'label' => 'Gross Profit',
                            'color' => 'from-purple-500 to-purple-600',
                            'value' => '₱' . number_format($report['summary']['total_profit'] ?? 0, 2),
                            'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                            'info' => 'Net Revenue - Cost of Goods Sold (COGS)',
                        ],
                        [
                            'label' => 'Profit Margin',
                            'color' => 'from-yellow-500 to-yellow-600',
                            'value' => number_format($report['summary']['profit_margin'] ?? 0, 1) . '%',
                            'icon' =>
                                'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                            'info' => '(Gross Profit / Net Revenue) × 100',
                        ],
                    ];
                @endphp

                @foreach ($cards as $card)
                    <div
                        class="bg-gradient-to-br {{ $card['color'] }} text-white rounded-lg shadow-lg p-4 transform hover:scale-[1.03] transition duration-300 ease-in-out relative group">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm opacity-90 mb-1">{{ $card['label'] }}</p>
                                <p class="text-xl sm:text-2xl font-bold">{{ $card['value'] }}</p>
                                <p class="text-xs opacity-75 mt-1 hidden sm:block">{{ $card['info'] }}</p>
                            </div>
                            <svg class="w-8 h-8 opacity-70 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Info Box explaining Revenue vs Profit -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-2">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="text-sm text-blue-900 dark:text-blue-200">
                        <p class="font-semibold mb-2">Revenue & Profit Calculation Formula</p>
                        <div class="space-y-1 font-mono text-xs">
                            <p><strong>Gross Revenue</strong> =
                                ₱{{ number_format($report['summary']['gross_revenue'], 2) }} (Sum of all product line
                                items)</p>
                            <p><strong>- Discounts</strong> =
                                ₱{{ number_format($report['summary']['total_discount'] ?? 0, 2) }}</p>
                            <p class="border-t border-blue-300 dark:border-blue-700 pt-1"><strong>= Net Revenue</strong>
                                = ₱{{ number_format($report['summary']['net_revenue'] ?? 0, 2) }}</p>
                            <p class="mt-2"><strong>+ Tax</strong> =
                                ₱{{ number_format($report['summary']['total_tax'] ?? 0, 2) }}</p>
                            {{-- <p><strong>+ Shipping</strong> = ₱{{ number_format($report['summary']['total_shipping'] ?? 0, 2) }}</p> --}}
                            <p class="border-t border-blue-300 dark:border-blue-700 pt-1"><strong>= Total
                                    Revenue</strong> = ₱{{ number_format($report['summary']['total_revenue'], 2) }}
                                (Customer Pays)</p>
                            <p class="mt-2 text-purple-700 dark:text-purple-300"><strong>Net Revenue</strong> -
                                <strong>COGS (₱{{ number_format($report['summary']['total_cost'], 2) }})</strong> =
                                <strong>Gross Profit
                                    (₱{{ number_format($report['summary']['total_profit'], 2) }})</strong>
                            </p>
                            <p class="text-yellow-700 dark:text-yellow-300"><strong>Profit Margin</strong> = (Gross
                                Profit / Net Revenue) × 100 =
                                <strong>{{ number_format($report['summary']['profit_margin'], 1) }}%</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-4 flex flex-wrap gap-3 sm:gap-4">
                    <button type="button" onclick="openPreviewModal()"
                        class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium rounded-md transition w-fit">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Preview Report
                    </button>

                    <a href="{{ route('reports.export-pdf', ['reportType' => 'sales', 'start_date' => request('start_date', $filters['start_date'] ?? ''), 'end_date' => request('end_date', $filters['end_date'] ?? '')]) }}"
                        target="_blank"
                        class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-medium rounded-md transition w-fit">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export PDF
                    </a>

                    {{-- <a href="{{ route('reports.export-excel', ['reportType' => 'sales', 'start_date' => request('start_date', $filters['start_date'] ?? ''), 'end_date' => request('end_date', $filters['end_date'] ?? '')]) }}"
                       class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-green-600 hover:bg-green-700 text-white text-xs sm:text-sm font-medium rounded-md transition w-fit">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Excel
                    </a> --}}
                </div>
            </div>

            <!-- Charts & Analytics -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">


                        <!-- Charts & Analytics -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                            <div class="p-4 sm:p-6">
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    <!-- Orders by Status -->
                                    {{-- <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                                        <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">
                                            Orders by Status</h3>
                                        <div class="space-y-3">
                                            @forelse($report['sales_by_status'] ?? [] as $status => $data)
                                                <div
                                                    class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded">
                                                    <span
                                                        class="font-medium capitalize text-gray-900 dark:text-white">{{ $status }}</span>
                                                    <span
                                                        class="font-medium capitalize text-gray-900 dark:text-white">Completed</span>
                                                    <div class="text-right">
                                                        <span
                                                            class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full font-bold">{{ $data['count'] }}</span>
                                                        <div class="text-xs text-gray-500">
                                                            ₱{{ number_format($data['amount'], 2) }}</div>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No data
                                                    available</p>
                                            @endforelse
                                        </div>
                                    </div> --}}

                                    <!-- Top 10 Products -->
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                                        <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Top
                                            10 Best Selling Products</h3>
                                        <div class="space-y-2">
                                            @forelse($report['top_products'] ?? [] as $index => $product)
                                                <div
                                                    class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition">
                                                    <div class="flex items-center gap-3">
                                                        <span
                                                            class="font-bold text-lg text-gray-400">#{{ $index + 1 }}</span>
                                                        <div>
                                                            <p class="font-medium text-gray-900 dark:text-white">
                                                                {{ $product->product_name }}</p>
                                                            <p class="text-xs text-gray-500">
                                                                {{ (int) ($product->total_quantity ?? 0) }} units sold
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <span class="text-green-600 dark:text-green-400 font-bold">
                                                        ₱{{ number_format($product->total_revenue ?? 0, 2) }}
                                                    </span>
                                                </div>
                                            @empty
                                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No
                                                    products sold</p>
                                            @endforelse
                                        </div>
                                    </div>

                                    <!-- Top 10 Customers -->
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                                        <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Top
                                            10 Customers</h3>
                                        <div class="space-y-2">
                                            @forelse($report['top_customers'] ?? [] as $index => $customer)
                                                <div
                                                    class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition">
                                                    <div class="flex items-center gap-3">
                                                        <span
                                                            class="font-bold text-lg text-gray-400">#{{ $index + 1 }}</span>
                                                        <div>
                                                            <p class="font-medium text-gray-900 dark:text-white">
                                                                {{ $customer['customer_name'] ?? 'Unknown' }}</p>
                                                            <p class="text-xs text-gray-500">
                                                                {{ $customer['order_count'] ?? 0 }} orders</p>
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="text-green-600 dark:text-green-400 font-bold">₱{{ number_format($customer['total_spent'] ?? 0, 2) }}</span>
                                                </div>
                                            @empty
                                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No
                                                    customer data</p>
                                            @endforelse
                                        </div>
                                    </div>

                                    <!-- Sales by Date -->
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                                        <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">
                                            Daily Sales Summary</h3>
                                        <div class="space-y-2 max-h-96 overflow-y-auto">
                                            @forelse($report['sales_by_date'] ?? [] as $date => $data)
                                                <div
                                                    class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition">
                                                    <span
                                                        class="text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                                                    <div class="text-right">
                                                        <p class="font-bold text-green-600 dark:text-green-400">
                                                            ₱{{ number_format($data['revenue'] ?? 0, 2) }}</p>
                                                        <p class="text-xs text-gray-500">{{ $data['count'] ?? 0 }}
                                                            orders</p>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No sales
                                                    data</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Sales Order Master Table -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-4 sm:p-6">
                            <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Sales Order
                            </h3>
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm sm:text-base">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            @foreach (['SKU', 'Product Name', 'Brand', 'Category', 'Sold Qty', 'Sold Amount', 'Stock'] as $header)
                                                <th
                                                    class="px-4 py-3 sm:px-6 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                                    {{ $header }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse(($report['product_sales'] ?? []) as $row)
                                            <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition">
                                                <td
                                                    class="px-4 py-3 sm:px-6 font-mono text-xs text-gray-900 dark:text-white">
                                                    {{ $row->product_sku ?? 'N/A' }}</td>
                                                <td
                                                    class="px-4 py-3 sm:px-6 font-light text-sm text-gray-900 dark:text-white">
                                                    {{ $row->product_name }}</td>
                                                <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                                    {{ $row->product_brand }}</td>
                                                <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                                    {{ $row->product_category }}</td>
                                                <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                                    {{ (int) ($row->total_quantity ?? 0) }}</td>
                                                <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                                    ₱{{ number_format($row->total_revenue ?? 0, 2) }}</td>
                                                <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                                    {{ (int) ($row->instock_qty ?? 0) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7"
                                                    class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                                    No sales in selected period.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PDF Preview Modal -->
                <div id="pdfPreviewModal"
                    class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div
                        class="relative top-10 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-md bg-white dark:bg-gray-800">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Sales Report Preview</h3>
                            <button onclick="closePreviewModal()"
                                class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4" style="height: 80vh;">
                            <iframe id="pdfPreviewFrame" class="w-full h-full rounded"
                                style="border: none;"></iframe>
                        </div>
                        <div class="mt-4 flex justify-end gap-3">
                            <button onclick="closePreviewModal()"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                                Close
                            </button>
                            <a id="downloadPdfLink" href="#" target="_blank"
                                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium inline-block">
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>

                <script>
                    function openPreviewModal() {
                        const startDate = '{{ request('start_date', $filters['start_date'] ?? '') }}';
                        const endDate = '{{ request('end_date', $filters['end_date'] ?? '') }}';
                        const previewUrl = '{{ route('reports.preview-pdf', ['reportType' => 'sales']) }}?start_date=' + startDate +
                            '&end_date=' + endDate;
                        const downloadUrl = '{{ route('reports.export-pdf', ['reportType' => 'sales']) }}?start_date=' + startDate +
                            '&end_date=' + endDate;

                        document.getElementById('pdfPreviewFrame').src = previewUrl;
                        document.getElementById('downloadPdfLink').href = downloadUrl;
                        document.getElementById('pdfPreviewModal').classList.remove('hidden');
                    }

                    function closePreviewModal() {
                        document.getElementById('pdfPreviewModal').classList.add('hidden');
                        document.getElementById('pdfPreviewFrame').src = '';
                    }

                    // Close modal when clicking outside
                    document.getElementById('pdfPreviewModal').addEventListener('click', function(e) {
                        if (e.target === this) {
                            closePreviewModal();
                        }
                    });

                    // Close modal with Escape key
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            closePreviewModal();
                        }
                    });
                </script>
</x-app-layout>
