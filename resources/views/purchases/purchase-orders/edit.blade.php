<x-app-layout>
    <div class="py-2">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Purchase Order: {{ $order->order_number }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">Update purchase order information</p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            <a href="{{ route('purchases.purchase-orders.show', $order->order_id) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <form method="POST" action="{{ route('purchases.purchase-orders.update', $order->order_id) }}"
                id="orderForm">
                @csrf
                @method('PUT')

                <!-- Order Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <!-- Order Number (Read-only) -->
                            <div>
                                <x-input-label for="order_number" :value="__('Order Number')" />
                                <x-text-input id="order_number" name="order_number" type="text"
                                    class="mt-1 block w-full bg-gray-50" :value="$order->order_number" readonly />
                            </div>

                            <!-- Supplier Selection -->
                            <div>
                                <x-input-label for="supplier_id" :value="__('Supplier')" />
                                <select id="supplier_id" name="supplier_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier['id'] }}"
                                            {{ old('supplier_id', $order->supplier_id) == $supplier['id'] ? 'selected' : '' }}>
                                            {{ $supplier['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                            </div>

                            <!-- Order Date -->
                            <div>
                                <x-input-label for="order_date" :value="__('Order Date')" />
                                <x-text-input id="order_date" name="order_date" type="date" class="mt-1 block w-full"
                                    :value="old('order_date', $order->order_date->format('Y-m-d'))" min="{{ date('Y-m-d') }}" required />
                                <x-input-error :messages="$errors->get('order_date')" class="mt-2" />
                            </div>

                            <!-- Expected Date -->
                            <div>
                                <x-input-label for="expected_date" :value="__('Expected Delivery Date')" />
                                <x-text-input id="expected_date" name="expected_date" type="date"
                                    class="mt-1 block w-full" :value="old(
                                        'expected_date',
                                        $order->expected_date ? $order->expected_date->format('Y-m-d') : '',
                                    )" min="{{ date('Y-m-d') }}"/>
                                <x-input-error :messages="$errors->get('expected_date')" class="mt-2" />
                            </div>

                            <!-- Priority -->
                            <div>
                                <x-input-label for="priority" :value="__('Priority')" />
                                <select id="priority" name="priority"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="low"
                                        {{ old('priority', $order->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="normal"
                                        {{ old('priority', $order->priority) == 'normal' ? 'selected' : '' }}>Normal
                                    </option>
                                    <option value="high"
                                        {{ old('priority', $order->priority) == 'high' ? 'selected' : '' }}>High
                                    </option>
                                    <option value="urgent"
                                        {{ old('priority', $order->priority) == 'urgent' ? 'selected' : '' }}>Urgent
                                    </option>
                                </select>
                                <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <x-input-label for="payment_method" :value="__('Payment Method')" />
                                <select id="payment_method" name="payment_method" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Payment Method</option>
                                    <option value="cash"
                                        {{ old('payment_method', $order->payment_method) == 'cash' ? 'selected' : '' }}>
                                        Cash</option>
                                    {{-- <option value="card"
                                        {{ old('payment_method', $order->payment_method) == 'card' ? 'selected' : '' }}>
                                        Card</option> --}}
                                    <option value="bank_transfer"
                                        {{ old('payment_method', $order->payment_method) == 'bank_transfer' ? 'selected' : '' }}>
                                        Bank Transfer</option>
                                    {{-- <option value="check"
                                        {{ old('payment_method', $order->payment_method) == 'check' ? 'selected' : '' }}>
                                        Check</option>
                                    <option value="credit"
                                        {{ old('payment_method', $order->payment_method) == 'credit' ? 'selected' : '' }}>
                                        Credit</option> --}}
                                </select>
                                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                            </div>

                            <!-- Reference Number -->
                            <div class="md:col-span-1">
                                <x-input-label for="reference_number" :value="__('Reference Number')" />
                                <x-text-input id="reference_number" name="reference_number" type="text"
                                    class="mt-1 block w-full" :value="old('reference_number', $order->reference_number)"
                                    placeholder="Optional reference number" />
                                <x-input-error :messages="$errors->get('reference_number')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Items</h3>
                            <button type="button" id="addItemBtn"
                                class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Item
                            </button>
                        </div>

                        <div id="orderItems" class="max-h-[600px] overflow-y-auto pr-2"
                            style="scrollbar-width: thin; scrollbar-color: #9ca3af #f3f4f6;">
                            @foreach ($order->items as $index => $item)
                                <!-- Existing item row -->
                                <div class="item-row border border-gray-200 dark:border-gray-600 rounded-lg mb-4">
                                    <!-- Toggle Header -->
                                    <div
                                        class="toggle-item-header flex items-center justify-between p-4 cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center space-x-3">
                                            <svg class="toggle-item-icon w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform duration-300"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                            <span
                                                class="item-product-name font-medium text-gray-700 dark:text-gray-300">
                                                {{ $item->product->product_name ?? 'Not selected' }}
                                            </span>
                                            <span class="item-summary text-sm text-gray-500 dark:text-gray-400"
                                                style="display: none;"></span>
                                        </div>
                                    </div>

                                    <!-- Item Content (Collapsible) -->
                                    <div class="item-content p-4"
                                        style="max-height: 1000px; opacity: 1; overflow: hidden; transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;">
                                        <div class="grid grid-cols-1 md:grid-cols-5 gap-2 items-end">
                                            <div class="md:col-span-2">
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                                                <select name="items[{{ $index }}][product_id]"
                                                    class="product-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                    required>
                                                    <option value="">Select Product</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product['id'] }}"
                                                            data-price="{{ $product['price'] }}"
                                                            data-stock="{{ $product['stock'] }}"
                                                            {{ old("items.{$index}.product_id", $item->product_id) == $product['id'] ? 'selected' : '' }}>
                                                            {{ $product['product_name'] }} (Stock:
                                                            {{ $product['stock'] }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                                <input type="number"
                                                    name="items[{{ $index }}][quantity_ordered]"
                                                    class="quantity-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                    min="1"
                                                    value="{{ old("items.{$index}.quantity_ordered", $item->quantity_ordered) }}"
                                                    required>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit
                                                    Price</label>
                                                <input type="number" name="items[{{ $index }}][unit_price]"
                                                    step="0.01"
                                                    class="unit-price-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                    value="{{ old("items.{$index}.unit_price", $item->unit_price) }}"
                                                    required>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Line
                                                    Total</label>
                                                <input type="text"
                                                    class="line-total mt-1 block w-full rounded-md border-gray-300 bg-gray-50 dark:bg-gray-600 dark:border-gray-600 dark:text-white"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                                            <textarea name="items[{{ $index }}][notes]" rows="2"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                placeholder="Optional notes for this item">{{ old("items.{$index}.notes", $item->notes) }}</textarea>
                                            <button type="button"
                                                class="remove-item mt-2 w-full inline-flex justify-center items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <x-input-error :messages="$errors->get('items')" class="mt-2" />
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Summary</h3>

                        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Selected Items</h4>
                                <div id="summary-items" class="space-y-2 mb-4">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No items selected yet</p>
                                </div>
                                <hr class="my-3 border-gray-300 dark:border-gray-600">
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Order Totals</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span>Subtotal:</span>
                                        <span id="subtotal-display">₱{{ number_format($order->subtotal, 2) }}</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="flex justify-between font-bold text-lg">
                                        <span>Total:</span>
                                        <span id="total-display">₱{{ number_format($order->total_amount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('purchases.purchase-orders.show', $order->order_id) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <x-primary-button type="submit">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let itemIndex = {{ $order->items->count() }};

                // Add Item Button
                document.getElementById('addItemBtn').addEventListener('click', function() {
                    const itemsContainer = document.getElementById('orderItems');
                    const newItem = createItemRow(itemIndex);
                    itemsContainer.insertAdjacentHTML('beforeend', newItem);

                    // Focus on the product select in the newly added row
                    const newRow = itemsContainer.lastElementChild;
                    const productSelect = newRow.querySelector('.product-select');
                    if (productSelect) {
                        productSelect.focus();
                    }

                    itemIndex++;
                });

                // Use event delegation for better performance
                const orderItemsContainer = document.getElementById('orderItems');

                // Event delegation for individual item toggle
                orderItemsContainer.addEventListener('click', function(e) {
                    const header = e.target.closest('.toggle-item-header');
                    if (header) {
                        const itemRow = header.closest('.item-row');
                        const itemContent = itemRow.querySelector('.item-content');
                        const toggleIcon = itemRow.querySelector('.toggle-item-icon');
                        const itemSummary = itemRow.querySelector('.item-summary');

                        if (itemContent.style.maxHeight && itemContent.style.maxHeight !== '0px') {
                            // Collapse
                            itemContent.style.maxHeight = '0px';
                            itemContent.style.opacity = '0';
                            toggleIcon.style.transform = 'rotate(-90deg)';

                            // Show summary
                            const productName = itemRow.querySelector('.item-product-name').textContent ||
                                'Not selected';
                            const quantity = itemRow.querySelector('.quantity-input').value || '0';
                            const unitPrice = itemRow.querySelector('.unit-price-input').value || '0';
                            const lineTotal = (parseFloat(quantity) * parseFloat(unitPrice)).toFixed(2);
                            itemSummary.textContent =
                                `Qty: ${quantity} × ₱${parseFloat(unitPrice).toFixed(2)} = ₱${lineTotal}`;
                            itemSummary.style.display = 'inline';
                        } else {
                            // Expand
                            itemContent.style.maxHeight = itemContent.scrollHeight + 'px';
                            itemContent.style.opacity = '1';
                            toggleIcon.style.transform = 'rotate(0deg)';
                            itemSummary.style.display = 'none';
                        }
                    }
                });

                // Event delegation for product selection
                orderItemsContainer.addEventListener('change', function(e) {
                    if (e.target.classList.contains('product-select')) {
                        const option = e.target.selectedOptions[0];
                        const price = option.dataset.price || '';
                        const productName = option.text || 'Not selected';
                        const row = e.target.closest('.item-row');
                        const priceInput = row.querySelector('.unit-price-input');
                        const productNameDisplay = row.querySelector('.item-product-name');

                        priceInput.value = price;
                        if (productNameDisplay) {
                            productNameDisplay.textContent = productName;
                        }
                        calculateLineTotal(row);
                    }
                });

                // Event delegation for quantity, price inputs
                orderItemsContainer.addEventListener('input', function(e) {
                    if (e.target.classList.contains('quantity-input') ||
                        e.target.classList.contains('unit-price-input')) {
                        calculateLineTotal(e.target.closest('.item-row'));
                    }
                });

                // Event delegation for remove button
                orderItemsContainer.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
                        const button = e.target.classList.contains('remove-item') ? e.target : e.target.closest(
                            '.remove-item');
                        const itemRows = document.querySelectorAll('.item-row');
                        if (itemRows.length > 1) {
                            button.closest('.item-row').remove();
                            updateOrderSummary();
                        } else {
                            alert('At least one item is required.');
                        }
                    }
                });

                // Calculate line totals for existing items on page load
                document.querySelectorAll('.item-row').forEach(row => {
                    calculateLineTotal(row);
                });

                // Initial calculation
                updateOrderSummary();

                function createItemRow(index) {
                    const products = @json($products);
                    let productOptions = '<option value="">Select Product</option>';

                    products.forEach(product => {
                        productOptions += `<option value="${product.id}" data-price="${product.price}" data-stock="${product.stock}">
                        ${product.product_name} (Stock: ${product.stock})
                    </option>`;
                    });

                    return `
                    <div class="item-row border border-gray-200 dark:border-gray-600 rounded-lg mb-4">
                        <!-- Toggle Header -->
                        <div class="toggle-item-header flex items-center justify-between p-4 cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <div class="flex items-center space-x-3">
                                <svg class="toggle-item-icon w-5 h-5 text-gray-500 dark:text-gray-400 transition-transform duration-300" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                <span class="item-product-name font-medium text-gray-700 dark:text-gray-300">Not selected</span>
                                <span class="item-summary text-sm text-gray-500 dark:text-gray-400" style="display: none;"></span>
                            </div>
                        </div>
                        
                        <!-- Item Content (Collapsible) -->
                        <div class="item-content p-4" style="max-height: 1000px; opacity: 1; overflow: hidden; transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-2 items-end">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                                    <select name="items[${index}][product_id]"
                                        class="product-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        required>
                                        ${productOptions}
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                    <input type="number" name="items[${index}][quantity_ordered]"
                                        class="quantity-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        min="1" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price</label>
                                    <input type="number" name="items[${index}][unit_price]" step="0.01"
                                        class="unit-price-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Line Total</label>
                                    <input type="text"
                                        class="line-total mt-1 block w-full rounded-md border-gray-300 bg-gray-50 dark:bg-gray-600 dark:border-gray-600 dark:text-white"
                                        readonly>
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                                <textarea name="items[${index}][notes]" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Optional notes for this item"></textarea>
                                <button type="button"
                                    class="remove-item mt-2 w-full inline-flex justify-center items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                }

                function attachItemEvents() {
                    // Product selection change
                    document.querySelectorAll('.product-select').forEach(select => {
                        select.addEventListener('change', function() {
                            const option = this.selectedOptions[0];
                            const price = option.dataset.price || '';
                            const row = this.closest('.item-row');
                            const priceInput = row.querySelector('.unit-price-input');
                            priceInput.value = price;
                            calculateLineTotal(row);
                        });
                    });

                    // Calculate line total on input change
                    document.querySelectorAll('.quantity-input, .unit-price-input, .discount-input').forEach(input => {
                        input.addEventListener('input', function() {
                            calculateLineTotal(this.closest('.item-row'));
                        });
                    });

                    // Remove item
                    document.querySelectorAll('.remove-item').forEach(button => {
                        button.addEventListener('click', function() {
                            const itemRows = document.querySelectorAll('.item-row');
                            if (itemRows.length > 1) {
                                this.closest('.item-row').remove();
                                updateOrderSummary();
                            } else {
                                alert('At least one item is required.');
                            }
                        });
                    });
                }

                function calculateLineTotal(row) {
                    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                    const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;

                    const lineTotal = (quantity * price);
                    row.querySelector('.line-total').value = '₱' + lineTotal.toFixed(2);

                    updateOrderSummary();
                }

                function updateOrderSummary() {
                    let subtotal = 0;
                    const summaryItemsContainer = document.getElementById('summary-items');
                    let itemsHtml = '';

                    document.querySelectorAll('.item-row').forEach((row, index) => {
                        const productSelect = row.querySelector('.product-select');
                        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                        const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                        const lineTotal = quantity * price;
                        subtotal += lineTotal;

                        // Get selected product name
                        const productName = productSelect.selectedOptions[0]?.text || 'Not selected';

                        if (quantity > 0 && price > 0 && productSelect.value) {
                            itemsHtml += `
                                <div class="flex justify-between items-start text-sm">
                                    <div class="flex-1">
                                        <span class="font-medium text-gray-700 dark:text-gray-300">${productName}</span>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Qty: ${quantity} × ₱${price.toFixed(2)}</div>
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-white">₱${lineTotal.toFixed(2)}</span>
                                </div>
                            `;
                        }
                    });

                    // Update summary items display
                    if (itemsHtml) {
                        summaryItemsContainer.innerHTML = itemsHtml;
                    } else {
                        summaryItemsContainer.innerHTML =
                            '<p class="text-sm text-gray-500 dark:text-gray-400">No items selected yet</p>';
                    }

                    const total = subtotal;

                    document.getElementById('subtotal-display').textContent = '₱' + subtotal.toFixed(2);
                    document.getElementById('total-display').textContent = '₱' + total.toFixed(2);
                }
            });
        </script>
    @endpush
</x-app-layout>
