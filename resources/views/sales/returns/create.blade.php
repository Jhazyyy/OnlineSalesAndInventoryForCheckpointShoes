<x-app-layout>
    <div class="py-2">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Return</h2>
                            <p class="text-gray-600 dark:text-gray-400">Add a new product return to the system</p>
                        </div>
                        <div>
                            <a href="{{ route('sales.returns.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Back to Return List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Return Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('sales.returns.store') }}" class="space-y-6">
                        @csrf

                        <!-- Sales Order Selection Section -->
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                            <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-4">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Link to Sales Order (Optional)
                            </h3>
                            <p class="text-sm text-blue-700 dark:text-blue-300 mb-4">
                                Select a sales order to automatically populate customer and product details
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Sales Order Search -->
                                <div>
                                    <label for="sales_order_search"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Search Sales Order
                                    </label>
                                    <input type="text" id="sales_order_search"
                                        placeholder="Search by order number or customer..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        autocomplete="on">
                                    <!-- Dropdown for search results -->
                                    <div id="sales_order_results"
                                        class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    </div>
                                </div>

                                <!-- Selected Sales Order Display -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Selected Order
                                    </label>
                                    <input type="text" id="sales_order_display"
                                        value="{{ $salesOrder->order_number ?? 'No sales order linked' }}" readonly
                                        placeholder="No sales order selected"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 dark:bg-gray-600 dark:border-gray-600 dark:text-white">
                                    <input type="hidden" id="sales_order_id" name="sales_order_id"
                                        value="{{ old('sales_order_id', $salesOrder->order_id ?? '') }}">
                                </div>
                            </div>

                            <!-- Display sales order items if selected -->
                            <div id="sales_order_items" class="mt-4 hidden">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order Items:</h4>
                                <div id="items_list" class="space-y-2"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <!-- Customer Selection (Auto-populated from sales order) -->
                            <div>
                                <label for="customer_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Customer
                                </label>
                                <select id="customer_id" name="customer_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select a customer...</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->customer_id }}"
                                            {{ old('customer_id', $salesOrder->customer_id ?? '') == $customer->customer_id ? 'selected' : '' }}>
                                            {{ $customer->display_name }} - {{ $customer->email ?? $customer->phone }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <!-- Product Selection -->
                            <div>
                                <label for="product_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Product <span class="text-red-500">*</span>
                                </label>
                                <select id="product_id" name="product_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select a product...</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->product_id }}"
                                            data-price="{{ $product->selling_price }}"
                                            data-stock="{{ $product->stock_quantity }}"
                                            {{ old('product_id') == $product->product_id ? 'selected' : '' }}>
                                            {{ $product->product_name }} (SKU: {{ $product->sku }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    You can return products even if they are currently out of stock. The returned items
                                    will be added back to inventory.
                                </p>
                                @error('product_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Return Date -->
                            <div>
                                <label for="return_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Return Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="return_date" name="return_date"
                                    value="{{ old('return_date', now()->toDateString()) }}" required
                                    max="{{ now()->toDateString() }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    min="{{ date('Y-m-d') }}">
                                @error('return_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label for="quantity"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Quantity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}"
                                    required min="1"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500" id="stock-info"></p>
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Unit Price <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" id="price" name="price" value="{{ old('price') }}"
                                        required min="0" step="0.01"
                                        class="mt-1 block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Return Status -->
                            <div>
                                <label for="return_status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select id="return_status" name="return_status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}"
                                            {{ old('return_status', 'pending') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('return_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Amount (calculated) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Total Amount
                                </label>
                                <div
                                    class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-300 dark:border-gray-600">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white"
                                        id="total-amount">₱0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Return Reason (Optional)
                            </label>
                            <textarea id="reason" name="reason" rows="3" placeholder="Enter the reason for this return..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div
                            class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('sales.returns.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Create Return
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for dynamic calculations and sales order search -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productSelect = document.getElementById('product_id');
            const priceInput = document.getElementById('price');
            const quantityInput = document.getElementById('quantity');
            const totalAmountSpan = document.getElementById('total-amount');
            const stockInfo = document.getElementById('stock-info');
            const salesOrderSearch = document.getElementById('sales_order_search');
            const salesOrderResults = document.getElementById('sales_order_results');
            const salesOrderDisplay = document.getElementById('sales_order_display');
            const salesOrderIdInput = document.getElementById('sales_order_id');
            const salesOrderItemsDiv = document.getElementById('sales_order_items');
            const itemsList = document.getElementById('items_list');
            const customerSelect = document.getElementById('customer_id');

            let searchTimeout;
            let selectedSalesOrder = null;

            // Sales Order Search
            salesOrderSearch.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = this.value.trim();

                if (searchTerm.length < 2) {
                    salesOrderResults.classList.add('hidden');
                    return;
                }

                searchTimeout = setTimeout(function() {
                    fetch(
                            `{{ route('sales.returns.search.sales-orders') }}?search=${encodeURIComponent(searchTerm)}`
                        )
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.sales_orders.length > 0) {
                                displaySalesOrderResults(data.sales_orders);
                            } else {
                                salesOrderResults.innerHTML =
                                    '<div class="p-3 text-sm text-gray-500 dark:text-gray-400">No sales orders found</div>';
                                salesOrderResults.classList.remove('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Error searching sales orders:', error);
                            salesOrderResults.classList.add('hidden');
                        });
                }, 300);
            });

            function displaySalesOrderResults(salesOrders) {
                salesOrderResults.innerHTML = '';
                salesOrders.forEach(order => {
                    const orderItem = document.createElement('div');
                    orderItem.className =
                        'p-3 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-200 dark:border-gray-600';
                    orderItem.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">${order.order_number}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">${order.customer_name}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">${order.order_date} • Status: ${order.status}</div>
                            </div>
                            <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">₱${parseFloat(order.total_amount).toFixed(2)}</div>
                        </div>
                    `;
                    orderItem.addEventListener('click', function() {
                        selectSalesOrder(order);
                    });
                    salesOrderResults.appendChild(orderItem);
                });
                salesOrderResults.classList.remove('hidden');
            }

            function selectSalesOrder(order) {
                selectedSalesOrder = order;
                salesOrderIdInput.value = order.sales_order_id;
                salesOrderDisplay.value = order.order_number;
                salesOrderSearch.value = order.order_number;
                salesOrderResults.classList.add('hidden');

                // Auto-populate customer
                if (order.customer_id) {
                    customerSelect.value = order.customer_id;
                }

                // Display order items
                displayOrderItems(order.items);
            }

            function displayOrderItems(items) {
                if (!items || items.length === 0) {
                    salesOrderItemsDiv.classList.add('hidden');
                    return;
                }

                itemsList.innerHTML = '';
                items.forEach(item => {
                    const itemDiv = document.createElement('div');
                    itemDiv.className =
                        'flex justify-between items-center p-3 bg-white dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer';
                    itemDiv.innerHTML = `
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 dark:text-white">${item.product_name}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Qty: ${item.quantity} • Price: ₱${parseFloat(item.unit_price).toFixed(2)}</div>
                        </div>
                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">₱${(parseFloat(item.quantity) * parseFloat(item.unit_price)).toFixed(2)}</div>
                        <button type="button" class="ml-3 px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                            Select
                        </button>
                    `;

                    // When item is clicked, populate the return form
                    itemDiv.querySelector('button').addEventListener('click', function() {
                        productSelect.value = item.product_id;
                        priceInput.value = parseFloat(item.unit_price).toFixed(2);
                        quantityInput.value = 1; // Default to 1, user can change
                        quantityInput.max = item.quantity; // Max is what was ordered
                        quantityInput.setAttribute('data-max-quantity', item.quantity);
                        
                        // Update stock info to show max returnable
                        stockInfo.innerHTML = `<span class="text-blue-600 dark:text-blue-400">Maximum returnable: ${item.quantity} units (from this order)</span>`;
                        
                        updateStockInfo();
                        calculateTotal();

                        // Scroll to the product selection
                        productSelect.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    });

                    itemsList.appendChild(itemDiv);
                });

                salesOrderItemsDiv.classList.remove('hidden');
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!salesOrderSearch.contains(event.target) && !salesOrderResults.contains(event.target)) {
                    salesOrderResults.classList.add('hidden');
                }
            });

            function updatePrice() {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                if (selectedOption && selectedOption.dataset.price) {
                    priceInput.value = parseFloat(selectedOption.dataset.price).toFixed(2);
                    updateStockInfo();
                } else {
                    priceInput.value = '';
                    stockInfo.textContent = '';
                }
                calculateTotal();
            }

            function updateStockInfo() {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                if (selectedOption && selectedOption.dataset.stock !== undefined) {
                    const stock = parseInt(selectedOption.dataset.stock);
                    if (stock <= 0) {
                        stockInfo.innerHTML =
                            '<span class="text-orange-600 dark:text-orange-400 font-medium">⚠️ Product is currently OUT OF STOCK (will be added back when return is approved)</span>';
                    } else {
                        // stockInfo.innerHTML = `<span class="text-green-600 dark:text-green-400">Current stock: ${quantity} units</span>`;
                    }
                    // Don't set max limit on returns - customer can return any quantity they purchased
                } else {
                    stockInfo.textContent = '';
                }
            }

            function calculateTotal() {
                const price = parseFloat(priceInput.value) || 0;
                const quantity = parseInt(quantityInput.value) || 0;
                const total = price * quantity;
                totalAmountSpan.textContent = '₱' + total.toFixed(2);
            }

            // Event listeners
            productSelect.addEventListener('change', updatePrice);
            priceInput.addEventListener('input', calculateTotal);
            quantityInput.addEventListener('input', calculateTotal);

            // Add validation for max quantity
            quantityInput.addEventListener('input', function() {
                const maxQty = parseInt(quantityInput.getAttribute('max'));
                const currentQty = parseInt(quantityInput.value);
                
                if (maxQty && currentQty > maxQty) {
                    quantityInput.setCustomValidity('Quantity cannot exceed ' + maxQty + ' units');
                    quantityInput.reportValidity();
                } else {
                    quantityInput.setCustomValidity('');
                }
            });

            // Initial calculation
            if (productSelect.value) {
                updatePrice();
            }

            // Load sales order data if provided via URL
            @if ($salesOrder)
                selectSalesOrder({
                    sales_order_id: {{ $salesOrder->order_id }},
                    order_number: '{{ $salesOrder->order_number }}',
                    customer_id: {{ $salesOrder->customer_id ?? 'null' }},
                    customer_name: '{{ $salesOrder->customer->display_name ?? 'N/A' }}',
                    order_date: '{{ $salesOrder->order_date?->format('M d, Y') ?? '' }}',
                    status: '{{ $salesOrder->status }}',
                    total_amount: {{ $salesOrder->total_amount }},
                    items: {!! json_encode(
                        $salesOrder->items->map(function ($item) {
                            return [
                                'product_id' => $item->product_id,
                                'product_name' => $item->product->product_name ?? 'Unknown',
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                                'total_price' => $item->total_price,
                            ];
                        }),
                    ) !!}
                });
            @endif
        });
    </script>
</x-app-layout>
