<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Critical Level Items</h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            Products at or below critical stock level - Immediate attention required
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

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.critical') }}" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Product</label>
                                <input type="text" name="q" placeholder="Name, brand, or category"
                                       value="{{ $filters['q'] ?? '' }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                <input type="text" name="category" placeholder="Optional category filter"
                                       value="{{ $filters['category'] ?? '' }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="flex flex-wrap gap-2 sm:justify-end sm:items-end">
                                <button type="submit"
                                        class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Apply Filters
                                </button>
                                <a href="{{ route('reports.critical') }}"
                                   class="inline-flex items-center justify-center px-3 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @php
                    $cards = [
                        ['label' => 'Total Critical Items', 'color' => 'from-orange-500 to-orange-600', 'value' => $report['summary']['total_items'] ?? 0, 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                        ['label' => 'Urgent (Out of Stock)', 'color' => 'from-red-500 to-red-600', 'value' => $report['summary']['urgent'] ?? 0, 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Critical Level', 'color' => 'from-yellow-500 to-yellow-600', 'value' => $report['summary']['critical'] ?? 0, 'icon' => 'M12 9v2m0 4h.01']
                    ];
                @endphp

                @foreach($cards as $card)
                    <div class="bg-gradient-to-br {{ $card['color'] }} text-white rounded-lg shadow-lg p-5 transform hover:scale-[1.03] transition duration-300 ease-in-out">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs sm:text-sm opacity-90">{{ $card['label'] }}</p>
                                <p class="text-2xl sm:text-3xl font-bold mt-1">{{ $card['value'] }}</p>
                            </div>
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Critical Items Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">Critical Level Items</h3>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-semibold">{{ $report['products']->count() }}</span> items found
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-3 py-3 sm:px-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Item Code</th>
                                    <th class="px-3 py-3 sm:px-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Item Name</th>
                                    <th class="px-3 py-3 sm:px-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Category / Brand</th>
                                    <th class="px-3 py-3 sm:px-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Current Stock</th>
                                    <th class="px-3 py-3 sm:px-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Reorder Level</th>
                                    <th class="px-3 py-3 sm:px-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Shortage</th>
                                    <th class="px-3 py-3 sm:px-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Supplier</th>
                                    <th class="px-3 py-3 sm:px-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Last Purchase</th>
                                    <th class="px-3 py-3 sm:px-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Avg Sales</th>
                                    <th class="px-3 py-3 sm:px-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Priority</th>
                                    <th class="px-3 py-3 sm:px-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Action Needed</th>
                                    <th class="px-3 py-3 sm:px-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                @forelse(($report['products'] ?? []) as $product)
                                    <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition">
                                        <!-- Item Code / SKU -->
                                        <td class="px-3 py-3 sm:px-4 text-gray-900 dark:text-white font-mono text-xs">
                                            {{ $product->sku ?? $product->product_id }}
                                        </td>
                                        
                                        <!-- Item Name / Description -->
                                        <td class="px-3 py-3 sm:px-4">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $product->product_name }}</div>
                                            @if($product->property_name && $product->property_value)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $product->property_name }}: {{ $product->property_value }}</div>
                                            @endif
                                        </td>
                                        
                                        <!-- Category / Brand -->
                                        <td class="px-3 py-3 sm:px-4">
                                            <div class="text-gray-800 dark:text-gray-300">{{ $product->product_category }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $product->product_brand }}</div>
                                        </td>
                                        
                                        <!-- Current Stock Quantity -->
                                        <td class="px-3 py-3 sm:px-4 text-center">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold
                                                {{ $product->quantity <= 0 ? 'bg-red-100 text-red-800 dark:bg-red-200 dark:text-red-900' : 
                                                   ($product->quantity <= ($product->critical_level * 0.5) ? 'bg-orange-100 text-orange-800 dark:bg-orange-200 dark:text-orange-900' : 
                                                   'bg-yellow-100 text-yellow-800 dark:bg-yellow-200 dark:text-yellow-900') }}">
                                                {{ number_format($product->quantity) }} pcs
                                            </span>
                                        </td>
                                        
                                        <!-- Reorder Level (Threshold) -->
                                        <td class="px-3 py-3 sm:px-4 text-center text-gray-800 dark:text-gray-300">
                                            {{ $product->reorder_level ? number_format($product->reorder_level) . ' pcs' : '-' }}
                                        </td>
                                        
                                        <!-- Difference / Shortage -->
                                        <td class="px-3 py-3 sm:px-4 text-center">
                                            <span class="text-red-600 dark:text-red-400 font-semibold">
                                                -{{ number_format($product->shortage) }} pcs
                                            </span>
                                        </td>
                                        
                                        <!-- Supplier / Vendor -->
                                        <td class="px-3 py-3 sm:px-4 text-gray-800 dark:text-gray-300">
                                            {{ $product->preferredSupplier->supplier_name ?? 'No supplier set' }}
                                        </td>
                                        
                                        <!-- Last Purchase Date -->
                                        <td class="px-3 py-3 sm:px-4 text-center text-gray-800 dark:text-gray-300">
                                            @if($product->last_purchase_date)
                                                {{ \Carbon\Carbon::parse($product->last_purchase_date)->format('M d, Y') }}
                                            @else
                                                <span class="text-gray-400">Never</span>
                                            @endif
                                        </td>
                                        
                                        <!-- Average Daily / Monthly Sales -->
                                        <td class="px-3 py-3 sm:px-4 text-center">
                                            <div class="text-gray-800 dark:text-gray-300">{{ number_format($product->avg_daily_sales, 1) }}/day</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($product->avg_monthly_sales) }}/mo</div>
                                        </td>
                                        
                                        <!-- Status / Priority Level -->
                                        <td class="px-3 py-3 sm:px-4 text-center">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $product->priority_level === 'urgent' ? 'bg-red-100 text-red-800' : 
                                                   ($product->priority_level === 'critical' ? 'bg-orange-100 text-orange-800' : 
                                                   'bg-yellow-100 text-yellow-800') }}">
                                                {{ $product->priority_label }}
                                            </span>
                                        </td>
                                        
                                        <!-- Remarks / Action Needed -->
                                        <td class="px-3 py-3 sm:px-4 text-gray-700 dark:text-gray-300 text-xs max-w-xs">
                                            {{ $product->action_needed }}
                                        </td>
                                        
                                        <!-- Action -->
                                        <td class="px-3 py-3 sm:px-4">
                                            <form method="POST" action="{{ route('reports.reorder.create') }}" class="flex flex-col gap-2">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                                                <input type="number" name="quantity" min="1" value="{{ max(1, (int)($product->shortage ?? 1)) }}"
                                                       class="w-20 rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-1 focus:ring-indigo-500 text-xs" 
                                                       placeholder="Qty" />
                                                <select name="supplier_id"
                                                        class="rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-1 focus:ring-indigo-500 text-xs">
                                                    <option value="">Select supplier</option>
                                                    @foreach($suppliers as $s)
                                                        <option value="{{ $s->supplier_id }}" {{ $product->preferred_supplier_id == $s->supplier_id ? 'selected' : '' }}>
                                                            {{ $s->supplier_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit"
                                                        class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs transition">
                                                    Create PO
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <p class="text-lg font-semibold">Great! No critical items found.</p>
                                                <p class="text-sm">All products are above critical stock levels.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Export Options -->
            @if($report['products']->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Export Options</h4>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('reports.export-pdf', 'critical') }}?{{ http_build_query($filters) }}" 
                               class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-xs transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Export PDF
                            </a>
                            <a href="{{ route('reports.export-excel', 'critical') }}?{{ http_build_query($filters) }}" 
                               class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-xs transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export Excel
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
