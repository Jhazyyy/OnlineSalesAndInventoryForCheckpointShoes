<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Invoice</h2>
                            <p class="text-gray-600 dark:text-gray-400">Create a new invoice for your customer</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('sales.invoices.index') }}" 
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('sales.invoices.store') }}" id="invoiceForm">
                        @csrf

                        <!-- Basic Invoice Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Customer -->
                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer *</label>
                                <select id="customer_id" name="customer_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer['id'] }}" {{ ($salesOrder && $salesOrder->customer_id == $customer['id']) || old('customer_id') == $customer['id'] ? 'selected' : '' }}>
                                            {{ $customer['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sales Order (if applicable) -->
                            @if($salesOrder)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Based on Sales Order</label>
                                    <div class="mt-1 p-2 bg-blue-50 dark:bg-blue-900 rounded-md">
                                        <p class="text-sm text-blue-700 dark:text-blue-300">{{ $salesOrder->order_number }}</p>
                                    </div>
                                    <input type="hidden" name="sales_order_id" value="{{ $salesOrder->order_id }}">
                                </div>
                            @endif

                            <!-- Invoice Date -->
                            <div>
                                <label for="invoice_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Invoice Date *</label>
                                <input type="date" id="invoice_date" name="invoice_date" required
                                       value="{{ old('invoice_date', date('Y-m-d')) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('invoice_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
                                <input type="date" id="due_date" name="due_date"
                                       value="{{ old('due_date') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Terms -->
                            <div>
                                <label for="payment_terms" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Terms (Days)</label>
                                <input type="number" id="payment_terms" name="payment_terms" min="0" max="365"
                                       value="{{ old('payment_terms', 30) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('payment_terms')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Invoice Items -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Invoice Items</h3>
                                <button type="button" id="addItem" 
                                    class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Item
                                </button>
                            </div>

                            <div id="itemsContainer">
                                @if($salesOrder && $salesOrder->items->count() > 0)
                                    @foreach($salesOrder->items as $index => $item)
                                        <div class="item-row border rounded-lg p-4 mb-4 bg-gray-50 dark:bg-gray-700">
                                            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                                <!-- Product -->
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                                                    <select name="items[{{ $index }}][product_id]" class="product-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product['id'] }}" data-price="{{ $product['price'] }}" {{ $item->product_id == $product['id'] ? 'selected' : '' }}>
                                                                {{ $product['name'] }} - ₱{{ number_format($product['price'], 2) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Quantity -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                                    <input type="number" name="items[{{ $index }}][quantity]" min="1" 
                                                           value="{{ $item->quantity }}"
                                                           class="quantity-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                                </div>

                                                <!-- Unit Price -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price</label>
                                                    <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" min="0"
                                                           value="{{ $item->unit_price }}"
                                                           class="unit-price-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                </div>

                                                <!-- Discount -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Discount</label>
                                                    <input type="number" step="0.01" name="items[{{ $index }}][discount_amount]" min="0"
                                                           value="{{ $item->discount_amount }}"
                                                           class="discount-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                </div>

                                                <!-- Line Total -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Total</label>
                                                    <input type="text" class="line-total mt-1 block w-full rounded-md border-gray-300 bg-gray-50 dark:bg-gray-600 dark:border-gray-600 dark:text-white" readonly>
                                                </div>

                                                <!-- Remove Button -->
                                                <div class="flex items-end">
                                                    <button type="button" class="remove-item text-red-600 hover:text-red-800">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- Item Notes -->
                                            <div class="mt-3">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                                                <input type="text" name="items[{{ $index }}][notes]" 
                                                       value="{{ $item->notes }}"
                                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="item-row border rounded-lg p-4 mb-4 bg-gray-50 dark:bg-gray-700">
                                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                            <!-- Product -->
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                                                <select name="items[0][product_id]" class="product-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product['id'] }}" data-price="{{ $product['price'] }}">
                                                            {{ $product['name'] }} - ₱{{ number_format($product['price'], 2) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Quantity -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                                <input type="number" name="items[0][quantity]" min="1" value="1"
                                                       class="quantity-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                            </div>

                                            <!-- Unit Price -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price</label>
                                                <input type="number" step="0.01" name="items[0][unit_price]" min="0"
                                                       class="unit-price-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            </div>

                                            <!-- Discount -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Discount</label>
                                                <input type="number" step="0.01" name="items[0][discount_amount]" min="0" value="0"
                                                       class="discount-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            </div>

                                            <!-- Line Total -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total</label>
                                                <input type="text" class="line-total mt-1 block w-full rounded-md border-gray-300 bg-gray-100 dark:bg-gray-600 text-gray-500" readonly>
                                            </div>

                                            <!-- Remove Button -->
                                            <div class="flex items-end">
                                                <button type="button" class="remove-item text-red-600 hover:text-red-800">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Item Notes -->
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                                            <input type="text" name="items[0][notes]" 
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @error('items')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Invoice Totals -->
                        <div class="mb-6">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="tax_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tax Amount</label>
                                        <input type="number" step="0.01" name="tax_amount" id="tax_amount" min="0"
                                               value="{{ old('tax_amount', 0) }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    
                                    <div>
                                        <label for="discount_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Discount Amount</label>
                                        <input type="number" step="0.01" name="discount_amount" id="discount_amount" min="0"
                                               value="{{ old('discount_amount', 0) }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>

                                    <div class="bg-white dark:bg-gray-800 rounded p-3">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Subtotal: <span id="subtotal">₱0.00</span></div>
                                        <div class="text-lg font-semibold text-gray-900 dark:text-white">Total: <span id="total">₱0.00</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="billing_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Billing Address</label>
                                <textarea id="billing_address" name="billing_address" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('billing_address', $salesOrder ? $salesOrder->billing_address : '') }}</textarea>
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                                <textarea id="notes" name="notes" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="terms_conditions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Terms & Conditions</label>
                            <textarea id="terms_conditions" name="terms_conditions" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('terms_conditions') }}</textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('sales.invoices.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Create Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let itemIndex = {{ $salesOrder && $salesOrder->items->count() > 0 ? $salesOrder->items->count() : 1 }};

        // Add new item
        document.getElementById('addItem').addEventListener('click', function() {
            const container = document.getElementById('itemsContainer');
            const newItem = createItemRow(itemIndex);
            container.appendChild(newItem);
            itemIndex++;
            calculateTotals();
        });

        function createItemRow(index) {
            const div = document.createElement('div');
            div.className = 'item-row border rounded-lg p-4 mb-4 bg-gray-50 dark:bg-gray-700';
            div.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                        <select name="items[${index}][product_id]" class="product-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product['id'] }}" data-price="{{ $product['price'] }}">{{ $product['name'] }} - ₱{{ number_format($product['price'], 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                        <input type="number" name="items[${index}][quantity]" min="1" value="1" class="quantity-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price</label>
                        <input type="number" step="0.01" name="items[${index}][unit_price]" min="0" class="unit-price-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Discount</label>
                        <input type="number" step="0.01" name="items[${index}][discount_amount]" min="0" value="0" class="discount-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total</label>
                        <input type="text" class="line-total mt-1 block w-full rounded-md border-gray-300 bg-gray-100 dark:bg-gray-600 text-gray-500" readonly>
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="remove-item text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                    <input type="text" name="items[${index}][notes]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            `;

            // Add event listeners to new item
            addItemEventListeners(div);
            return div;
        }

        function addItemEventListeners(itemRow) {
            // Product selection
            const productSelect = itemRow.querySelector('.product-select');
            productSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                const unitPriceInput = itemRow.querySelector('.unit-price-input');
                if (price) {
                    unitPriceInput.value = price;
                }
                calculateLineTotal(itemRow);
            });

            // Quantity, price, discount changes
            const inputs = itemRow.querySelectorAll('.quantity-input, .unit-price-input, .discount-input');
            inputs.forEach(input => {
                input.addEventListener('input', () => calculateLineTotal(itemRow));
            });

            // Remove item
            const removeBtn = itemRow.querySelector('.remove-item');
            removeBtn.addEventListener('click', function() {
                if (document.querySelectorAll('.item-row').length > 1) {
                    itemRow.remove();
                    calculateTotals();
                } else {
                    alert('At least one item is required.');
                }
            });
        }

        function calculateLineTotal(itemRow) {
            const quantity = parseFloat(itemRow.querySelector('.quantity-input').value) || 0;
            const unitPrice = parseFloat(itemRow.querySelector('.unit-price-input').value) || 0;
            const discount = parseFloat(itemRow.querySelector('.discount-input').value) || 0;
            
            const lineTotal = (quantity * unitPrice) - discount;
            itemRow.querySelector('.line-total').value = '₱' + lineTotal.toFixed(2);
            
            calculateTotals();
        }

        function calculateTotals() {
            let subtotal = 0;
            
            document.querySelectorAll('.item-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
                
                subtotal += (quantity * unitPrice) - discount;
            });

            const taxAmount = parseFloat(document.getElementById('tax_amount').value) || 0;
            const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
            const total = subtotal + taxAmount - discountAmount;

            document.getElementById('subtotal').textContent = '₱' + subtotal.toFixed(2);
            document.getElementById('total').textContent = '₱' + total.toFixed(2);
        }

        // Add event listeners to existing items
        document.querySelectorAll('.item-row').forEach(addItemEventListeners);

        // Add listeners to tax and discount inputs
        document.getElementById('tax_amount').addEventListener('input', calculateTotals);
        document.getElementById('discount_amount').addEventListener('input', calculateTotals);

        // Update due date when payment terms change
        document.getElementById('payment_terms').addEventListener('change', function() {
            const invoiceDate = document.getElementById('invoice_date').value;
            const paymentTerms = this.value;
            
            if (invoiceDate && paymentTerms) {
                const dueDate = new Date(invoiceDate);
                dueDate.setDate(dueDate.getDate() + parseInt(paymentTerms));
                document.getElementById('due_date').value = dueDate.toISOString().split('T')[0];
            }
        });

        // Calculate initial totals
        calculateTotals();
    </script>
</x-app-layout>
