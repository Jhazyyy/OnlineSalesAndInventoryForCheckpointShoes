<x-app-layout>
<div class="py-6">
    <div class="w-full mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Product Threshold Management</h2>
                        <p class="text-gray-600 dark:text-gray-400">Manage inventory thresholds for products</p>
                    </div>
                    <div class="flex space-x-3 mt-4 sm:mt-0">
                        <a href="{{ route('inventory.thresholds.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" id="bulkUpdateBtn">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Bulk Update
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('inventory.thresholds.products') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search Product</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" 
                               placeholder="Search by name or SKU..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">All Products</option>
                            <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Low Stock</option>
                            <option value="critical" {{ request('status') === 'critical' ? 'selected' : '' }}>Critical Stock</option>
                            <option value="good" {{ request('status') === 'good' ? 'selected' : '' }}>Good Stock</option>
                            <option value="no_threshold" {{ request('status') === 'no_threshold' ? 'selected' : '' }}>No Threshold Set</option>
                        </select>
                    </div>

                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort By</label>
                        <select id="sort" name="sort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Product Name</option>
                            <option value="quantity" {{ request('sort') === 'quantity' ? 'selected' : '' }}>Current Quantity</option>
                            <option value="reorder_level" {{ request('sort') === 'reorder_level' ? 'selected' : '' }}>Reorder Level</option>
                            <option value="updated_at" {{ request('sort') === 'updated_at' ? 'selected' : '' }}>Last Updated</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Filter
                        </button>
                    </div>

                    <div class="flex items-end">
                        <a href="{{ route('inventory.thresholds.products') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Current Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reorder Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Critical Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Max Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($products as $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="product_ids[]" value="{{ $product->product_id }}" class="product-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $product->sku ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-700 dark:text-gray-300">{{ $product->product_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $product->quantity }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" name="reorder_level_{{ $product->product_id }}" 
                                               value="{{ $product->reorder_level }}" 
                                               min="0" 
                                               class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 text-sm"
                                               onchange="updateThreshold({{ $product->product_id }}, 'reorder_level', this.value)">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" name="critical_level_{{ $product->product_id }}" 
                                               value="{{ $product->critical_level }}" 
                                               min="0" 
                                               class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 text-sm"
                                               onchange="updateThreshold({{ $product->product_id }}, 'critical_level', this.value)">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" name="max_stock_{{ $product->product_id }}" 
                                               value="{{ $product->max_stock }}" 
                                               min="0" 
                                               class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 text-sm"
                                               onchange="updateThreshold({{ $product->product_id }}, 'max_stock', this.value)">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusArray = $product->getStockStatus();
                                            $primaryStatus = !empty($statusArray) ? $statusArray[0]['type'] : 'good';
                                            $statusColors = [
                                                'critical_stock' => 'bg-red-100 text-red-800',
                                                'low_stock' => 'bg-yellow-100 text-yellow-800',
                                                'out_of_stock' => 'bg-red-100 text-red-800',
                                                'overstock' => 'bg-blue-100 text-blue-800',
                                                'good' => 'bg-green-100 text-green-800'
                                            ];
                                            $statusLabel = match($primaryStatus) {
                                                'critical_stock' => 'Critical',
                                                'low_stock' => 'Low Stock',
                                                'out_of_stock' => 'Out of Stock',
                                                'overstock' => 'Overstock',
                                                default => 'Good'
                                            };
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$primaryStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            @if($product->quantity <= ($product->reorder_level ?? 0))
                                            <button type="button" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" 
                                                    onclick="generatePurchaseOrder({{ $product->product_id }})" 
                                                    title="Generate Purchase Order">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                            @endif
                                            <button type="button" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" 
                                                    onclick="viewProductHistory({{ $product->product_id }})" 
                                                    title="View History">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Select all checkbox functionality
    $('#selectAll').change(function() {
        $('.product-checkbox').prop('checked', $(this).prop('checked'));
    });

    // Update individual checkbox state
    $('.product-checkbox').change(function() {
        var totalCheckboxes = $('.product-checkbox').length;
        var checkedCheckboxes = $('.product-checkbox:checked').length;
        
        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes);
    });

    // Bulk update button
    $('#bulkUpdateBtn').click(function() {
        var selectedIds = $('.product-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            alert('Please select at least one product to update.');
            return;
        }

        alert('Bulk update functionality for ' + selectedIds.length + ' products coming soon!');
    });
});

// Update threshold function
function updateThreshold(productId, field, value) {
    $.ajax({
        url: '/inventory/thresholds/products/' + productId + '/update-threshold',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            field: field,
            value: value
        },
        success: function(response) {
            if (response.success) {
                // Show success indicator
                var input = $('input[name="' + field + '_' + productId + '"]');
                input.addClass('border-green-500').removeClass('border-gray-300');
                setTimeout(function() {
                    input.removeClass('border-green-500').addClass('border-gray-300');
                }, 2000);
            } else {
                alert('Error updating threshold: ' + response.message);
            }
        },
        error: function() {
            alert('Failed to update threshold');
        }
    });
}

// Generate purchase order function
function generatePurchaseOrder(productId) {
    alert('Generate purchase order for product ID ' + productId + ' - functionality coming soon!');
}

// View product history function
function viewProductHistory(productId) {
    alert('View history for product ID ' + productId + ' - functionality coming soon!');
}
</script>
@endpush

</x-app-layout>