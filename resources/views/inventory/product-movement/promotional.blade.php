<x-app-layout>
    <div class="w-full h-screen">
        <div :class="navOpen ? 'flex-1' : 'w-full'" class="h-full overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 min-h-full flex flex-col">
                <div class="flex-1 p-6 text-gray-900 dark:text-gray-100">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-3xl font-bold">Promotional Products</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Products marked for promotion and discounts</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('inventory.product-movement.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                ← Back to Movement
                            </a>
                            <button onclick="showMarkPromotionModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                                Mark Products for Promotion
                            </button>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Statistics -->
                    <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg mb-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $stats['promotional_products'] }} Products on Promotion</p>
                                <p class="text-sm text-purple-600 dark:text-purple-400 mt-1">Consider applying discounts or special offers to these products</p>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" id="selectAll" class="rounded" onclick="toggleSelectAll(this)">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Movement</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Reason</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($products as $product)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="product_ids[]" value="{{ $product->product_id }}" class="rounded product-checkbox">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $product->product_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $product->product_brand }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $product->quantity }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                            ₱{{ number_format($product->price, 2) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($product->movement_category === 'fast')
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Fast</span>
                                            @elseif($product->movement_category === 'slow')
                                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Slow</span>
                                            @elseif($product->movement_category === 'non-moving')
                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Non-Moving</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $product->promotional_reason ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form method="POST" action="{{ route('inventory.product-movement.unmark-from-promotion') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="product_ids[]" value="{{ $product->product_id }}">
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400" 
                                                        onclick="return confirm('Remove this product from promotions?')">
                                                    Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No promotional products found. Click "Mark Products for Promotion" to add products.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Bulk Actions -->
                    <div id="bulkActions" class="hidden mt-4 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <form method="POST" action="{{ route('inventory.product-movement.unmark-from-promotion') }}" id="bulkForm">
                            @csrf
                            <div class="flex justify-between items-center">
                                <span class="text-sm"><span id="selectedCount">0</span> products selected</span>
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded" 
                                        onclick="return confirm('Remove selected products from promotions?')">
                                    Remove from Promotions
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="mt-6">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Mark for Promotion Modal -->
    <div id="markPromotionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Mark Products for Promotion</h3>
                <form method="POST" action="{{ route('inventory.product-movement.mark-for-promotion') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select criteria to auto-mark products:
                            </label>
                            <label class="flex items-center mb-2">
                                <input type="checkbox" name="criteria[movement_category][]" value="slow" class="rounded mr-2">
                                <span class="text-sm">Slow Moving Products</span>
                            </label>
                            <label class="flex items-center mb-2">
                                <input type="checkbox" name="criteria[movement_category][]" value="non-moving" class="rounded mr-2" checked>
                                <span class="text-sm">Non-Moving Products</span>
                            </label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Minimum Stock Level
                            </label>
                            <input type="number" name="criteria[min_stock]" value="10" min="0" 
                                   class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600">
                            <p class="text-xs text-gray-500 mt-1">Only mark products with at least this many units in stock</p>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-6">
                        <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                            Mark Products
                        </button>
                        <button type="button" onclick="hideMarkPromotionModal()" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.product-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateBulkActions();
        }

        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActions);
        });

        function updateBulkActions() {
            const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
            const bulkActions = document.getElementById('bulkActions');
            const bulkForm = document.getElementById('bulkForm');
            const selectedCount = document.getElementById('selectedCount');
            
            selectedCount.textContent = checkedBoxes.length;
            
            if (checkedBoxes.length > 0) {
                bulkActions.classList.remove('hidden');
                // Clear existing hidden inputs
                bulkForm.querySelectorAll('input[name="product_ids[]"]').forEach(input => input.remove());
                // Add new hidden inputs
                checkedBoxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'product_ids[]';
                    input.value = checkbox.value;
                    bulkForm.appendChild(input);
                });
            } else {
                bulkActions.classList.add('hidden');
            }
        }

        function showMarkPromotionModal() {
            document.getElementById('markPromotionModal').classList.remove('hidden');
        }

        function hideMarkPromotionModal() {
            document.getElementById('markPromotionModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
