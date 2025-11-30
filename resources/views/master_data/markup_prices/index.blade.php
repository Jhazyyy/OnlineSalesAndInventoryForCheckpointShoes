<x-app-layout>
    <div class="py-2">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Markup Price Management</h2>
                            <p class="text-gray-600 dark:text-gray-400">Manage product markup percentages and pricing
                                sources</p>
                        </div>
                        <div class="mt-4 sm:mt-0 flex space-x-2">
                            <button onclick="openBulkUpdateModal()"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Bulk Update
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <form method="GET" action="{{ route('master_data.markup_prices.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <!-- Search -->
                            <div>
                                <label for="search"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    placeholder="Product name, SKU..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Brand Filter -->
                            <div>
                                <label for="brand"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Brand</label>
                                <select id="brand" name="brand"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Brands</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand }}"
                                            {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Category Filter -->
                            <div>
                                <label for="category"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                <select id="category" name="category"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}"
                                            {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Price Source Filter -->
                            <div>
                                <label for="price_source"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price
                                    Source</label>
                                <select id="price_source" name="price_source"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Sources</option>
                                    <option value="manual" {{ request('price_source') == 'manual' ? 'selected' : '' }}>
                                        Manual</option>
                                    <option value="markup" {{ request('price_source') == 'markup' ? 'selected' : '' }}>
                                        Markup</option>
                                    <option value="costing"
                                        {{ request('price_source') == 'costing' ? 'selected' : '' }}>Costing</option>
                                </select>
                            </div>

                            <!-- Markup Status Filter -->
                            <div>
                                <label for="markup_status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Markup
                                    Status</label>
                                <select id="markup_status" name="markup_status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">All Products</option>
                                    <option value="with_markup"
                                        {{ request('markup_status') == 'with_markup' ? 'selected' : '' }}>With Markup
                                    </option>
                                    <option value="without_markup"
                                        {{ request('markup_status') == 'without_markup' ? 'selected' : '' }}>Without
                                        Markup</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-start space-x-2">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Filter
                            </button>
                            <a href="{{ route('master_data.markup_prices.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <input type="checkbox" id="selectAll"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Product
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total Cost
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider truncate">
                                        Markup %
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Markup Price
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Selling Price
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Price Source
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($products as $product)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox"
                                                class="product-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                value="{{ $product->product_id }}">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white break-words">
                                                {{ $product->product_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $product->product_brand }} - {{ $product->product_category }}
                                            </div>
                                            @if ($product->sku)
                                                <div class="text-xs text-gray-400">SKU: {{ $product->sku }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            @if ($product->total_cost)
                                                ₱{{ number_format($product->total_cost, 2) }}
                                            @else
                                                <span class="text-gray-400">Not set</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            @if ($product->markup_percentage)
                                                {{ number_format($product->markup_percentage, 2) }}%
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            @if ($product->markup_price)
                                                ₱{{ number_format($product->markup_price, 2) }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            @if ($product->price)
                                                ₱{{ number_format($product->price, 2) }}
                                            @else
                                                <span class="text-red-500">No price</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $sourceColors = [
                                                    'manual' =>
                                                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                    'markup' =>
                                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                    'costing' =>
                                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                ];
                                            @endphp
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sourceColors[$product->price_source] ?? $sourceColors['manual'] }}">
                                                {{ ucfirst($product->price_source) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="openEditModal({{ $product->product_id }})"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8"
                                            class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No products found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="editForm">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4" id="modal-title">
                            Edit Markup Price
                        </h3>

                        <div class="space-y-4">
                            <div id="productInfo" class="bg-gray-50 dark:bg-gray-700 p-3 rounded-md">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    <div><strong>Product:</strong> <span id="productName"></span></div>
                                    <div><strong>Current Price:</strong> ₱<span id="currentPrice"></span></div>
                                    <div><strong>Total Cost:</strong> ₱<span id="totalCost"></span></div>
                                </div>
                            </div>

                            <!-- Price Source -->
                            <div>
                                <label for="price_source_edit"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Price Source
                                </label>
                                <select id="price_source_edit" name="price_source" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="manual">Manual - Set Price Manually</option>
                                    <option value="markup">Markup - Calculate From Cost + Markup %</option>
                                    <option value="costing">Costing - Calculate From Cost + Profit Margin</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Choose how the selling price
                                    is determined</p>
                            </div>

                            <!-- Manual Price Input -->
                            <div id="manualPriceDiv">
                                <label for="manual_price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Manual Price (₱)
                                </label>
                                <input type="number" id="manual_price" name="manual_price" step="0.01"
                                    min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Markup Percentage Input -->
                            <div id="markupPercentageDiv">
                                <label for="markup_percentage_edit"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Markup Percentage (%)
                                </label>
                                <input type="number" id="markup_percentage_edit" name="markup_percentage"
                                    step="0.01" min="0" max="1000"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Example: 20% markup on ₱100
                                    cost = ₱120 selling price</p>
                            </div>

                            <!-- Preview -->
                            <div id="pricePreview" class="bg-blue-50 dark:bg-blue-900 p-3 rounded-md hidden">
                                <div class="text-sm text-blue-700 dark:text-blue-300">
                                    <div><strong>New Selling Price:</strong> ₱<span id="previewPrice"></span></div>
                                    <div><strong>Price Change:</strong> <span id="priceChange"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Save Changes
                        </button>
                        <button type="button" onclick="closeEditModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Update Modal -->
    <div id="bulkModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="bulkForm">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                            Bulk Update Markup Prices
                        </h3>

                        <div class="space-y-4">
                            <div class="bg-yellow-50 dark:bg-yellow-900 p-3 rounded-md">
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    <span id="selectedCount">0</span> products selected
                                </p>
                            </div>

                            <!-- Price Source -->
                            <div>
                                <label for="price_source_bulk"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Price Source
                                </label>
                                <select id="price_source_bulk" name="price_source" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="manual">Manual</option>
                                    <option value="markup">Markup</option>
                                    <option value="costing">Costing</option>
                                </select>
                            </div>

                            <!-- Markup Percentage -->
                            <div id="markupPercentageBulkDiv">
                                <label for="markup_percentage_bulk"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Markup Percentage (%)
                                </label>
                                <input type="number" id="markup_percentage_bulk" name="markup_percentage"
                                    step="0.01" min="0" max="1000"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Update Selected
                        </button>
                        <button type="button" onclick="closeBulkModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentProductId = null;
            let productsData = @json($products->items());

            // Select All functionality
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.product-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedCount();
            });

            // Update selected count
            document.querySelectorAll('.product-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            function updateSelectedCount() {
                const selected = document.querySelectorAll('.product-checkbox:checked').length;
                document.getElementById('selectedCount').textContent = selected;
            }

            // Price Source change handler
            document.getElementById('price_source_edit').addEventListener('change', function() {
                const manualDiv = document.getElementById('manualPriceDiv');
                const markupDiv = document.getElementById('markupPercentageDiv');

                if (this.value === 'manual') {
                    manualDiv.style.display = 'block';
                    markupDiv.style.display = 'none';
                } else if (this.value === 'markup') {
                    manualDiv.style.display = 'none';
                    markupDiv.style.display = 'block';
                } else {
                    manualDiv.style.display = 'none';
                    markupDiv.style.display = 'none';
                }
            });

            document.getElementById('price_source_bulk').addEventListener('change', function() {
                const markupDiv = document.getElementById('markupPercentageBulkDiv');
                markupDiv.style.display = (this.value === 'markup') ? 'block' : 'none';
            });

            // Open Edit Modal
            function openEditModal(productId) {
                currentProductId = productId;
                const product = productsData.find(p => p.product_id === productId);

                if (!product) return;

                document.getElementById('productName').textContent = product.product_name;
                document.getElementById('currentPrice').textContent = product.price ? parseFloat(product.price).toFixed(2) :
                    '0.00';
                document.getElementById('totalCost').textContent = product.total_cost ? parseFloat(product.total_cost).toFixed(
                    2) : '0.00';

                document.getElementById('price_source_edit').value = product.price_source || 'manual';
                document.getElementById('manual_price').value = product.price || '';
                document.getElementById('markup_percentage_edit').value = product.markup_percentage || '';

                // Trigger price source change to show/hide appropriate fields
                document.getElementById('price_source_edit').dispatchEvent(new Event('change'));

                document.getElementById('editModal').classList.remove('hidden');
            }

            function closeEditModal() {
                document.getElementById('editModal').classList.add('hidden');
                currentProductId = null;
            }

            function openBulkUpdateModal() {
                const selected = document.querySelectorAll('.product-checkbox:checked').length;
                if (selected === 0) {
                    alert('Please select at least one product');
                    return;
                }
                document.getElementById('bulkModal').classList.remove('hidden');
            }

            function closeBulkModal() {
                document.getElementById('bulkModal').classList.add('hidden');
            }

            // Edit Form Submit
            document.getElementById('editForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                // Validate based on price source
                if (data.price_source === 'markup' && (!data.markup_percentage || data.markup_percentage <= 0)) {
                    alert('Please enter a markup percentage greater than 0');
                    return;
                }

                if (data.price_source === 'manual' && (!data.manual_price || data.manual_price <= 0)) {
                    alert('Please enter a manual price greater than 0');
                    return;
                }

                try {
                    const response = await fetch(`/master_data/markup_prices/${currentProductId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        alert('Markup price updated successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + (result.message || 'Failed to update'));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to update markup price. Please check console for details.');
                }
            });

            // Bulk Form Submit
            document.getElementById('bulkForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                const selectedIds = Array.from(document.querySelectorAll('.product-checkbox:checked'))
                    .map(cb => parseInt(cb.value));

                if (selectedIds.length === 0) {
                    alert('No products selected');
                    return;
                }

                const formData = new FormData(this);
                const priceSource = formData.get('price_source');
                const markupPercentage = formData.get('markup_percentage');

                // Validate based on price source
                if (priceSource === 'markup' && (!markupPercentage || markupPercentage <= 0)) {
                    alert('Please enter a markup percentage greater than 0 for markup pricing');
                    return;
                }

                const data = {
                    product_ids: selectedIds,
                    price_source: priceSource,
                    markup_percentage: markupPercentage || null
                };

                try {
                    const response = await fetch('/master_data/markup_prices/bulk-update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert(result.message);
                        location.reload();
                    } else {
                        alert('Error: ' + (result.message || 'Failed to update'));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to update products');
                }
            });

            // Markup percentage input handler for preview
            document.getElementById('markup_percentage_edit').addEventListener('input', async function() {
                if (!currentProductId || !this.value) {
                    document.getElementById('pricePreview').classList.add('hidden');
                    return;
                }

                const priceSource = document.getElementById('price_source_edit').value;

                try {
                    const response = await fetch(
                        `/master_data/markup_prices/${currentProductId}/preview?markup_percentage=${this.value}&price_source=${priceSource}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                    const preview = await response.json();

                    if (preview.new_selling_price) {
                        document.getElementById('previewPrice').textContent = preview.new_selling_price.toFixed(2);

                        const change = preview.price_change || 0;
                        const changeText = change >= 0 ? `+₱${change.toFixed(2)}` :
                            `-₱${Math.abs(change).toFixed(2)}`;
                        const changeColor = change >= 0 ? 'text-green-600' : 'text-red-600';
                        document.getElementById('priceChange').innerHTML =
                            `<span class="${changeColor}">${changeText}</span>`;

                        document.getElementById('pricePreview').classList.remove('hidden');
                    } else {
                        document.getElementById('pricePreview').classList.add('hidden');
                    }
                } catch (error) {
                    console.error('Error fetching preview:', error);
                }
            });
        </script>
    @endpush
</x-app-layout>
