<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Blocked Items</h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            Products that are refurbished, damaged, or completely wasted
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

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.blocked') }}" class="space-y-4">
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
                                    Apply
                                </button>
                                <a href="{{ route('reports.blocked') }}"
                                    class="inline-flex items-center justify-center px-3 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Reset
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
                            'label' => 'Refurbished',
                            'color' => 'from-blue-500 to-blue-600',
                            'value' => $report['summary']['total_refurbished_qty'] ?? 0,
                            'icon' => 'M4 4v6h6M20 20v-6h-6M4 10l6-6m4 16l6-6',
                        ],
                        [
                            'label' => 'Damaged (Shipped)',
                            'color' => 'from-yellow-500 to-yellow-600',
                            'value' => $report['summary']['total_damaged_shipped_qty'] ?? 0,
                            'icon' => 'M9 12h6m2 5H7a2 2 0 01-2-2v-3m14 5a2 2 0 002-2v-3M5 10l-2-2m0 0l2-2m-2 2h6',
                        ],
                        [
                            'label' => 'Damaged (Received)',
                            'color' => 'from-amber-500 to-amber-600',
                            'value' => $report['summary']['total_inbound_damaged_qty'] ?? 0,
                            'icon' => 'M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4',
                        ],
                        [
                            'label' => 'Waste Qty',
                            'color' => 'from-red-500 to-red-600',
                            'value' => $report['summary']['total_waste_qty'] ?? 0,
                            'icon' => 'M19 7l-7 7-4-4',
                        ],
                        [
                            'label' => 'Waste Value',
                            'color' => 'from-rose-500 to-rose-600',
                            'value' => '₱' . number_format($report['summary']['total_waste_value'] ?? 0, 2),
                            'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2',
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
                    <button type="button" onclick="openPurchaseOrderModal()"
                        class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium rounded-md transition w-fit">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View/Preview Report
                    </button>

                    <a href="{{ route('reports.export-pdf', ['reportType' => 'blocked', 'start_date' => request('start_date', $filters['start_date'] ?? ''), 'end_date' => request('end_date', $filters['end_date'] ?? '')]) }}"
                        target="_blank"
                        class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-medium rounded-md transition w-fit">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export PDF
                    </a>

                    {{-- <a href="{{ route('reports.export-excel', ['reportType' => 'blocked', 'start_date' => request('start_date', $filters['start_date'] ?? ''), 'end_date' => request('end_date', $filters['end_date'] ?? '')]) }}"
                       class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-green-600 hover:bg-green-700 text-white text-xs sm:text-sm font-medium rounded-md transition w-fit">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Excel
                    </a> --}}
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Blocked Items by Product
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm sm:text-base">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    @foreach (['Product', 'Brand', 'Category', 'Refurbished', 'Damaged (Shipped)', 'Damaged (Received)', 'Waste Qty', 'Waste Value'] as $header)
                                        <th
                                            class="px-4 py-3 sm:px-6 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                            {{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse(($report['products'] ?? []) as $row)
                                    <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition">
                                        <td class="px-4 py-3 sm:px-6 font-medium text-gray-900 dark:text-white">
                                            {{ $row['product_name'] }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                            {{ $row['product_brand'] ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                            {{ $row['product_category'] ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                            {{ (int) $row['refurbished_qty'] }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                            {{ (int) $row['damaged_shipped_qty'] }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                            {{ (int) $row['inbound_damaged_qty'] }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                            {{ (int) $row['waste_qty'] }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                            ₱{{ number_format($row['waste_value'] ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8"
                                            class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No blocked items found for the selected period.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Purchase Order Master -->
            {{-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">Purchase Order Master
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        SKU</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        Product Name</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        Brand</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        Category</th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        Ordered Qty</th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        Total Cost</th>
                                    <th
                                        class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                        Instock Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                @forelse(($report['purchase_order_master'] ?? []) as $row)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <td class="px-4 py-3 font-mono text-xs text-gray-900 dark:text-white">
                                            {{ $row->product_sku ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                            {{ $row->product_name }}</td>
                                        <td class="px-4 py-3 text-gray-800 dark:text-gray-300">
                                            {{ $row->product_brand }}</td>
                                        <td class="px-4 py-3 text-gray-800 dark:text-gray-300">
                                            {{ $row->product_category }}</td>
                                        <td class="px-4 py-3 text-right text-gray-800 dark:text-gray-300">
                                            {{ number_format($row->total_quantity ?? 0) }}</td>
                                        <td class="px-4 py-3 text-right text-gray-800 dark:text-gray-300">
                                            ₱{{ number_format($row->total_cost ?? 0, 2) }}</td>
                                        <td class="px-4 py-3 text-right text-gray-800 dark:text-gray-300">
                                            {{ number_format($row->instock_qty ?? 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7"
                                            class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No purchase orders found for the selected period.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> --}}

        </div>
    </div>

    <!-- Purchase Order Master Preview Modal -->
    <div id="purchaseOrderModal"
        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div
            class="relative top-10 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Purchase Order Master Preview</h3>
                <button onclick="closePurchaseOrderModal()"
                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4" style="height: 80vh;">
                <iframe id="purchaseOrderFrame" class="w-full h-full rounded" style="border: none;"></iframe>
            </div>
            <div class="mt-4 flex justify-end gap-3">
                <button onclick="closePurchaseOrderModal()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                    Close
                </button>
                <a id="downloadPurchaseOrderPdfLink" href="#" target="_blank"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium inline-block">
                    Download PDF
                </a>
            </div>
        </div>
    </div>

    <script>
        function openPurchaseOrderModal() {
            const startDate = '{{ request('start_date', $filters['start_date'] ?? '') }}';
            const endDate = '{{ request('end_date', $filters['end_date'] ?? '') }}';
            const previewUrl = '{{ route('reports.preview-pdf', ['reportType' => 'blocked']) }}?start_date=' + startDate +
                '&end_date=' + endDate;
            const downloadUrl = '{{ route('reports.export-pdf', ['reportType' => 'blocked']) }}?start_date=' + startDate +
                '&end_date=' + endDate;

            document.getElementById('purchaseOrderFrame').src = previewUrl;
            document.getElementById('downloadPurchaseOrderPdfLink').href = downloadUrl;
            document.getElementById('purchaseOrderModal').classList.remove('hidden');
        }

        function closePurchaseOrderModal() {
            document.getElementById('purchaseOrderModal').classList.add('hidden');
            document.getElementById('purchaseOrderFrame').src = '';
        }

        // Close modal when clicking outside
        document.getElementById('purchaseOrderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePurchaseOrderModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePurchaseOrderModal();
            }
        });
    </script>
</x-app-layout>
