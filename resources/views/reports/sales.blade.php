<x-app-layout>
    <div class="w-full h-screen">
        <div :class="navOpen ? 'flex-1' : 'w-full'" class="h-full overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 min-h-full flex flex-col">
                <div class="flex-1 p-6 text-gray-900 dark:text-gray-100">
                    
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-3xl font-bold">Sales Report</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Comprehensive sales analytics and performance metrics</p>
                        </div>
                        <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                            ← Back to Reports
                        </a>
                    </div>

                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('reports.sales') }}" class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
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
                                <a href="{{ route('reports.sales') }}" class="bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white px-4 py-2 rounded-lg font-medium">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
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

                        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Total Revenue</p>
                                    <p class="text-3xl font-bold mt-1">₱{{ number_format($report['summary']['total_revenue'] ?? 0, 2) }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Total Profit</p>
                                    <p class="text-3xl font-bold mt-1">₱{{ number_format($report['summary']['total_profit'] ?? 0, 2) }}</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-lg shadow-lg p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Avg. Profit Margin</p>
                                    <p class="text-3xl font-bold mt-1">{{ number_format($report['summary']['profit_margin'] ?? 0, 1) }}%</p>
                                </div>
                                <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Export Buttons -->
                    <div class="flex gap-4 mb-6">
                        <button type="button" onclick="openPreviewModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View/Preview Report
                        </button>

                        <a href="{{ route('reports.export-pdf', ['reportType' => 'sales', 'start_date' => request('start_date', $filters['start_date'] ?? ''), 'end_date' => request('end_date', $filters['end_date'] ?? '')]) }}" target="_blank" class="inline-block">
                            <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export PDF
                            </button>
                        </a>

                        <a href="{{ route('reports.export-excel', ['reportType' => 'sales', 'start_date' => request('start_date', $filters['start_date'] ?? ''), 'end_date' => request('end_date', $filters['end_date'] ?? '')]) }}" class="inline-block">
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
                                @forelse($report['sales_by_status'] ?? [] as $status => $data)
                                    <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded">
                                        <span class="font-medium capitalize">{{ $status }}</span>
                                        <div class="text-right">
                                            <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full font-bold">{{ $data['count'] }}</span>
                                            <div class="text-xs text-gray-500">₱{{ number_format($data['amount'], 2) }}</div>
                                        </div>
                                    </div>
                                @empty
                                <p class="text-gray-500 text-center py-4">No data available</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Top 10 Products -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-xl font-bold mb-4">Top 10 Best Selling Products</h3>
                            <div class="space-y-2">
                                @forelse($report['top_products'] ?? [] as $index => $product)
                                    <div class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition">
                                        <div class="flex items-center gap-3">
                                            <span class="font-bold text-lg text-gray-400">#{{ $index + 1 }}</span>
                                            <div>
                                                <p class="font-medium">{{ $product->product_name }}</p>
                                                <p class="text-xs text-gray-500">{{ (int)($product->total_quantity ?? 0) }} units sold</p>
                                            </div>
                                        </div>
                                        <span class="text-green-600 dark:text-green-400 font-bold">
                                            ₱{{ number_format($product->total_revenue ?? 0, 2) }}
                                        </span>
                                    </div>
                                @empty
                                <p class="text-gray-500 text-center py-4">No products sold</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Top 10 Customers -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-xl font-bold mb-4">Top 10 Customers</h3>
                            <div class="space-y-2">
                                @forelse($report['top_customers'] ?? [] as $index => $customer)
                                    <div class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition">
                                        <div class="flex items-center gap-3">
                                            <span class="font-bold text-lg text-gray-400">#{{ $index + 1 }}</span>
                                            <div>
                                                <p class="font-medium">{{ $customer['customer_name'] ?? 'Unknown' }}</p>
                                                <p class="text-xs text-gray-500">{{ $customer['order_count'] ?? 0 }} orders</p>
                                            </div>
                                        </div>
                                        <span class="text-green-600 dark:text-green-400 font-bold">₱{{ number_format($customer['total_spent'] ?? 0, 2) }}</span>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">No customer data</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Sales by Date -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-6">
                            <h3 class="text-xl font-bold mb-4">Daily Sales Summary</h3>
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @forelse($report['sales_by_date'] ?? [] as $date => $data)
                                    <div class="flex items-center justify-between p-2 hover:bg-white dark:hover:bg-gray-800 rounded transition">
                                        <span class="text-sm">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span>
                                        <div class="text-right">
                                            <p class="font-bold text-green-600 dark:text-green-400">₱{{ number_format($data['revenue'] ?? 0, 2) }}</p>
                                            <p class="text-xs text-gray-500">{{ $data['count'] ?? 0 }} orders</p>
                                        </div>
                                    </div>
                                @empty
                                <p class="text-gray-500 text-center py-4">No sales data</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Product Sales (Sales Order Master) -->
                    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold">Sales Order Master</h3>
                            <div class="flex gap-2">
                                <a href="{{ route('reports.export-pdf', ['reportType' => 'sales', 'start_date' => request('start_date', $filters['start_date'] ?? ''), 'end_date' => request('end_date', $filters['end_date'] ?? '')]) }}" target="_blank" class="px-3 py-2 rounded bg-red-600 text-white text-sm">PDF</a>
                                <a href="{{ route('reports.export-excel', ['reportType' => 'sales', 'start_date' => request('start_date', $filters['start_date'] ?? ''), 'end_date' => request('end_date', $filters['end_date'] ?? '')]) }}" class="px-3 py-2 rounded bg-green-600 text-white text-sm">XLS</a>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-2 pr-4">SKU</th>
                                        <th class="py-2 pr-4">Product Name</th>
                                        <th class="py-2 pr-4">Brand</th>
                                        <th class="py-2 pr-4">Category</th>
                                        <th class="py-2 pr-4 text-right">Sold Qty</th>
                                        <th class="py-2 pr-4 text-right">Sold Amount</th>
                                        <th class="py-2 pr-4 text-right">Instock Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($report['product_sales'] ?? []) as $row)
                                        <tr class="border-b border-gray-100 dark:border-gray-700">
                                            <td class="py-2 pr-4">{{ $row->product_sku ?? 'N/A' }}</td>
                                            <td class="py-2 pr-4">{{ $row->product_name }}</td>
                                            <td class="py-2 pr-4">{{ $row->product_brand }}</td>
                                            <td class="py-2 pr-4">{{ $row->product_category }}</td>
                                            <td class="py-2 pr-4 text-right">{{ (int)($row->total_quantity ?? 0) }}</td>
                                            <td class="py-2 pr-4 text-right">₱{{ number_format($row->total_revenue ?? 0, 2) }}</td>
                                            <td class="py-2 pr-4 text-right">{{ (int)($row->instock_qty ?? 0) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="py-6 text-center text-gray-500">No sales in selected period.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Preview Modal -->
    <div id="pdfPreviewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Sales Report Preview</h3>
                <button onclick="closePreviewModal()" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4" style="height: 80vh;">
                <iframe id="pdfPreviewFrame" class="w-full h-full rounded" style="border: none;"></iframe>
            </div>
            <div class="mt-4 flex justify-end gap-3">
                <button onclick="closePreviewModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                    Close
                </button>
                <a id="downloadPdfLink" href="#" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium inline-block">
                    Download PDF
                </a>
            </div>
        </div>
    </div>

    <script>
        function openPreviewModal() {
            const startDate = '{{ request('start_date', $filters['start_date'] ?? '') }}';
            const endDate = '{{ request('end_date', $filters['end_date'] ?? '') }}';
            const previewUrl = '{{ route('reports.preview-pdf', ['reportType' => 'sales']) }}?start_date=' + startDate + '&end_date=' + endDate;
            const downloadUrl = '{{ route('reports.export-pdf', ['reportType' => 'sales']) }}?start_date=' + startDate + '&end_date=' + endDate;
            
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
