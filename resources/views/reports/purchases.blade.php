<x-app-layout>
    <div class="py-2">
        <div class="w-full mx-auto sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Purchase Report</h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            Procurement analytics and supplier performance overview
                        </p>
                    </div>

                    <a href="{{ route('reports.index') }}"
                        class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out w-fit">
                        Back to Reports
                    </a>
                </div>
            </div>

            <!-- Date Filter Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <!-- Quick Filter Buttons -->
                    <div class="mb-4 flex flex-wrap gap-2">
                        <a href="{{ route('reports.purchases', ['start_date' => now()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
                            class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition">
                            Today
                        </a>
                        <a href="{{ route('reports.purchases', ['start_date' => now()->startOfWeek()->format('Y-m-d'), 'end_date' => now()->endOfWeek()->format('Y-m-d')]) }}"
                            class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition">
                            This Week
                        </a>
                        <a href="{{ route('reports.purchases', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d')]) }}"
                            class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition">
                            This Month
                        </a>
                        <a href="{{ route('reports.purchases', ['start_date' => now()->startOfYear()->format('Y-m-d'), 'end_date' => now()->endOfYear()->format('Y-m-d')]) }}"
                            class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition">
                            This Year
                        </a>
                    </div>

                    <form method="GET" action="{{ route('reports.purchases') }}" class="space-y-4">
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
                                    class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs uppercase rounded-md transition w-fit">
                                    Apply Filter
                                </button>
                                <a href="{{ route('reports.purchases') }}"
                                    class="inline-flex items-center justify-center px-3 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-semibold text-xs uppercase rounded-md transition w-fit">
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 mb-2">
                @php
                    $cards = [
                        [
                            'label' => 'Total Orders',
                            'color' => 'from-purple-500 to-purple-600',
                            'value' => $report['summary']['total_orders'] ?? 0,
                            'icon' =>
                                'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-2.293 2.293A1 1 0 005.414 17H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0z',
                        ],
                        [
                            'label' => 'Total Amount',
                            'color' => 'from-red-500 to-red-600',
                            'value' => '₱' . number_format($report['summary']['total_amount'] ?? 0, 2),
                            'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6m2 4h10a2 2 0 002-2V9H5v6',
                        ],
                        // [
                        //     'label' => 'Total Paid',
                        //     'color' => 'from-green-500 to-green-600',
                        //     'value' => '₱' . number_format($report['summary']['total_paid'] ?? 0, 2),
                        //     'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        // ],
                        // [
                        //     'label' => 'Total Due',
                        //     'color' => 'from-orange-500 to-orange-600',
                        //     'value' => '₱' . number_format($report['summary']['total_due'] ?? 0, 2),
                        //     'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        // ],
                        // [
                        //     'label' => 'Items Purchased',
                        //     'color' => 'from-indigo-500 to-indigo-600',
                        //     'value' => $report['summary']['total_items'] ?? 0,
                        //     'icon' => 'M20 7l-8-4-8 4v10l8 4 8-4V7z',
                        // ],
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-4 flex flex-wrap gap-3 sm:gap-4">
                    <button type="button" onclick="openPurchasePreviewModal()"
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

                    <a href="{{ route('reports.export-pdf', ['reportType' => 'purchases', 'start_date' => request('start_date', $filters['start_date'] ?? ''), 'end_date' => request('end_date', $filters['end_date'] ?? '')]) }}"
                        target="_blank"
                        class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-medium rounded-md transition w-fit">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export PDF
                    </a>

                    {{-- <a href="{{ route('reports.export-excel', ['reportType' => 'purchases', 'start_date' => request('start_date', $filters['start_date'] ?? ''), 'end_date' => request('end_date', $filters['end_date'] ?? '')]) }}"
                        class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-green-600 hover:bg-green-700 text-white text-xs sm:text-sm font-medium rounded-md transition w-fit">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </a> --}}
                </div>
            </div>

            <!-- Product Purchase Details (Purchase Order Master by Product) -->
            @if(!empty($report['product_purchases']) && count($report['product_purchases']) > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Purchase Order (By Product)</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs sm:text-base">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    @foreach (['SKU', 'Product Name', 'Brand', 'Category', 'Qty Ordered', 'Qty Received', 'Avg. Unit Price'] as $header)
                                        <th
                                            class="px-4 py-3 sm:px-6 text-left text-xs font-mono text-gray-700 dark:text-gray-300 uppercase">
                                            {{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($report['product_purchases'] as $product)
                                    <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <td class="px-4 py-3 sm:px-6 font-medium text-gray-900 dark:text-white">
                                            {{ $product->sku ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                            {{ $product->product_name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                            {{ $product->product_brand ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                            {{ $product->product_category ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-left text-gray-800 dark:text-gray-300">
                                            {{ number_format($product->total_ordered ?? 0) }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-left text-gray-800 dark:text-gray-300">
                                            {{ number_format($product->total_received ?? 0) }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-left text-gray-800 dark:text-gray-300">
                                            ₱{{ number_format($product->avg_unit_price ?? 0, 2) }}</td>
                                        {{-- <td class="px-4 py-3 sm:px-6 text-left text-gray-800 dark:text-gray-300">
                                            {{ number_format($product->current_stock ?? 0) }}</td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Purchase Order Master Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-bold mb-4 text-gray-900 dark:text-white">Purchase Orders (By Order)</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs sm:text-base">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    @foreach (['PO Number', 'Supplier', 'Order Date', 'Amount Due', 'Due Date', 'Total Paid', 'Status'] as $header)
                                        <th
                                            class="px-4 py-3 sm:px-6 text-left text-xs font-mono text-gray-700 dark:text-gray-300 uppercase">
                                            {{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse(($report['orders'] ?? []) as $order)
                                    <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <td class="px-4 py-3 sm:px-6 font-medium text-gray-900 dark:text-white">
                                            {{ $order->order_number ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                            {{ $order->supplier->supplier_name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                            {{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-left text-gray-800 dark:text-gray-300">
                                            ₱{{ number_format($order->total_amount ?? 0, 2) }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">
                                            {{ $order->expected_date ? \Carbon\Carbon::parse($order->expected_date)->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 sm:px-6 text-left text-gray-800 dark:text-gray-300">
                                            ₱{{ number_format($order->payments->sum('amount') ?? 0, 2) }}</td>
                                        <td class="px-4 py-3 sm:px-6 text-left text-gray-800 dark:text-gray-300">
                                            @php
                                                $statusColors = [
                                                    'pending' =>
                                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                    'approved' =>
                                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                    'received' =>
                                                        'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                                    'cancelled' =>
                                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                    'completed' =>
                                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                ];
                                                $statusColor =
                                                    $statusColors[strtolower($order->status)] ??
                                                    'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                                            @endphp
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
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
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="purchasePreviewModal"
        class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div
            class="relative top-10 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Purchase Report Preview</h3>
                <button onclick="closePurchasePreviewModal()"
                    class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4" style="height: 80vh;">
                <iframe id="purchasePreviewFrame" class="w-full h-full rounded" style="border: none;"></iframe>
            </div>
            <div class="mt-4 flex justify-end gap-3">
                <button onclick="closePurchasePreviewModal()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                    Close
                </button>
                <a id="downloadPurchasePdfLink" href="#" target="_blank"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium inline-block">
                    Download PDF
                </a>
            </div>
        </div>
    </div>

    <script>
        function openPurchasePreviewModal() {
            const startDate = '{{ request('start_date', $filters['start_date'] ?? '') }}';
            const endDate = '{{ request('end_date', $filters['end_date'] ?? '') }}';
            const previewUrl = '{{ route('reports.preview-pdf', ['reportType' => 'purchases']) }}?start_date=' +
                startDate + '&end_date=' + endDate;
            const downloadUrl = '{{ route('reports.export-pdf', ['reportType' => 'purchases']) }}?start_date=' +
                startDate + '&end_date=' + endDate;

            document.getElementById('purchasePreviewFrame').src = previewUrl;
            document.getElementById('downloadPurchasePdfLink').href = downloadUrl;
            document.getElementById('purchasePreviewModal').classList.remove('hidden');
        }

        function closePurchasePreviewModal() {
            document.getElementById('purchasePreviewModal').classList.add('hidden');
            document.getElementById('purchasePreviewFrame').src = '';
        }

        document.getElementById('purchasePreviewModal').addEventListener('click', function(e) {
            if (e.target === this) closePurchasePreviewModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closePurchasePreviewModal();
        });
    </script>
</x-app-layout>