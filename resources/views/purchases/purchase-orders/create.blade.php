<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Purchase Order</h2>
                            <p class="text-gray-600 dark:text-gray-400">Create a new purchase order from a supplier</p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            <a href="{{ route('purchases.purchase-orders.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <form method="POST" action="{{ route('purchases.purchase-orders.store') }}" id="orderForm">
                @csrf

                <!-- Order Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Supplier Selection -->
                            <div>
                                <x-input-label for="supplier_id" :value="__('Supplier')" />
                                <select id="supplier_id" name="supplier_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier['id'] }}"
                                            {{ old('supplier_id') == $supplier['id'] ? 'selected' : '' }}>
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
                                    :value="old('order_date', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('order_date')" class="mt-2" />
                            </div>

                            <!-- Expected Date -->
                            <div>
                                <x-input-label for="expected_date" :value="__('Expected Delivery Date')" />
                                <x-text-input id="expected_date" name="expected_date" type="date"
                                    class="mt-1 block w-full" :value="old('expected_date')" />
                                <x-input-error :messages="$errors->get('expected_date')" class="mt-2" />
                            </div>

                            <!-- Priority -->
                            <div>
                                <x-input-label for="priority" :value="__('Priority')" />
                                <select id="priority" name="priority"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="normal"
                                        {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>
                                        Normal</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High
                                    </option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent
                                    </option>
                                </select>
                                <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <x-input-label for="payment_method" :value="__('Payment Method')" />
                                <select id="payment_method" name="payment_method"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Payment Method</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash
                                    </option>
                                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card
                                    </option>
                                    <option value="bank_transfer"
                                        {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer
                                    </option>
                                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>
                                        Check
                                    </option>
                                    <option value="credit" {{ old('payment_method') == 'credit' ? 'selected' : '' }}>
                                        Credit</option>
                                </select>
                                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                            </div>

                            <!-- Reference Number -->
                            <div>
                                <x-input-label for="reference_number" :value="__('Reference Number')" />
                                <x-text-input id="reference_number" name="reference_number" type="text"
                                    class="mt-1 block w-full" :value="old('reference_number')"
                                    placeholder="Optional reference number" />
                                <x-input-error :messages="$errors->get('reference_number')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
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

                        <div id="orderItems">
                            <!-- Initial item row -->
                            <div class="item-row border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                                    <div class="md:col-span-2">
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                                        <select name="items[0][product_id]"
                                            class="product-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            required>
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product['id'] }}"
                                                    data-price="{{ $product['price'] }}"
                                                    data-stock="{{ $product['stock'] }}">
                                                    {{ $product['product_name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                        <input type="number" name="items[0][quantity_ordered]"
                                            class="quantity-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            min="1" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit
                                            Price</label>
                                        <input type="number" name="items[0][unit_price]" step="0.01"
                                            class="unit-price-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Discount</label>
                                        <input type="number" name="items[0][discount_amount]" step="0.01"
                                            class="discount-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            value="0">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Line
                                            Total</label>
                                        <input type="text"
                                            class="line-total mt-1 block w-full rounded-md border-gray-300 bg-gray-50 dark:bg-gray-600 dark:border-gray-600 dark:text-white"
                                            readonly>
                                    </div>
                                    <div>
                                        <button type="button"
                                            class="remove-item w-full inline-flex justify-center items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                                    <textarea name="items[0][notes]" rows="2"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Optional notes for this item"></textarea>
                                </div>
                            </div>
                        </div>

                        <x-input-error :messages="$errors->get('items')" class="mt-2" />
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Summary</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <!-- Supplier Tax -->
                                @if(isset($activeTaxes) && $activeTaxes->isNotEmpty())
                                <div>
                                    <x-input-label for="tax_rule_id" :value="__('Supplier Tax')" />
                                    <select id="tax_rule_id" name="tax_rule_id" onchange="applyTaxRule(this)"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">-- No Tax --</option>
                                        @foreach($activeTaxes as $tax)
                                            <option value="{{ $tax->id }}" 
                                                data-rate="{{ $tax->rate }}"
                                                data-method="{{ $tax->calculation_method }}"
                                                data-fixed="{{ $tax->fixed_amount ?? 0 }}"
                                                {{ old('tax_rule_id') == $tax->id ? 'selected' : '' }}>
                                                {{ $tax->name }} - 
                                                @if($tax->calculation_method === 'percentage')
                                                    {{ $tax->rate }}%
                                                @else
                                                    ₱{{ number_format((float)$tax->fixed_amount, 2) }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Select a supplier tax to apply automatically based on order subtotal
                                    </p>
                                </div>
                                @endif

                                <!-- Supplier Discount -->
                                @if(isset($activeDiscounts) && $activeDiscounts->isNotEmpty())
                                <div>
                                    <x-input-label for="discount_rule_id" :value="__('Supplier Discount')" />
                                    <select id="discount_rule_id" name="discount_rule_id" onchange="applyDiscountRule(this)"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">-- No Discount --</option>
                                        @foreach($activeDiscounts as $discount)
                                            <option value="{{ $discount->id }}" 
                                                data-rate="{{ $discount->rate }}"
                                                data-method="{{ $discount->calculation_method }}"
                                                data-fixed="{{ $discount->fixed_amount ?? 0 }}"
                                                {{ old('discount_rule_id') == $discount->id ? 'selected' : '' }}>
                                                {{ $discount->name }} - 
                                                @if($discount->calculation_method === 'percentage')
                                                    {{ $discount->rate }}%
                                                @else
                                                    ₱{{ number_format((float)$discount->fixed_amount, 2) }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Select a supplier discount to apply automatically based on order subtotal
                                    </p>
                                </div>
                                @endif

                                <!-- Tax Amount -->
                                <div>
                                    <x-input-label for="tax_amount" :value="__('Tax Amount (Calculated)')" />
                                    <x-text-input id="tax_amount" name="tax_amount" type="number" step="0.01"
                                        class="mt-1 block w-full bg-gray-100 dark:bg-gray-600" 
                                        :value="old('tax_amount', '0.00')" readonly />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        This will be calculated automatically based on the selected tax rule
                                    </p>
                                    <x-input-error :messages="$errors->get('tax_amount')" class="mt-2" />
                                </div>

                                <!-- Shipping Amount -->
                                <div>
                                    <x-input-label for="shipping_amount" :value="__('Shipping Amount')" />
                                    <x-text-input id="shipping_amount" name="shipping_amount" type="number"
                                        step="0.01" class="mt-1 block w-full" :value="old('shipping_amount', '0.00')" />
                                    <x-input-error :messages="$errors->get('shipping_amount')" class="mt-2" />
                                </div>

                                <!-- Discount Amount -->
                                <div>
                                    <x-input-label for="discount_amount" :value="__('Discount Amount (Calculated)')" />
                                    <x-text-input id="discount_amount" name="discount_amount" type="number"
                                        step="0.01" class="mt-1 block w-full bg-gray-100 dark:bg-gray-600" 
                                        :value="old('discount_amount', '0.00')" readonly />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        This will be calculated automatically based on the selected discount rule
                                    </p>
                                    <x-input-error :messages="$errors->get('discount_amount')" class="mt-2" />
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Order Totals</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span>Subtotal:</span>
                                        <span id="subtotal-display">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Tax:</span>
                                        <span id="tax-display">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Shipping:</span>
                                        <span id="shipping-display">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Discount:</span>
                                        <span id="discount-display">₱0.00</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="flex justify-between font-bold text-lg">
                                        <span>Total:</span>
                                        <span id="total-display">₱0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Additional Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Delivery Address -->
                            <div>
                                <x-input-label for="delivery_address" :value="__('Delivery Address')" />
                                <textarea id="delivery_address" name="delivery_address" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Enter delivery address">{{ old('delivery_address') }}</textarea>
                                <x-input-error :messages="$errors->get('delivery_address')" class="mt-2" />
                            </div>

                            <!-- Billing Address -->
                            <div>
                                <x-input-label for="billing_address" :value="__('Billing Address')" />
                                <textarea id="billing_address" name="billing_address" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Enter billing address">{{ old('billing_address') }}</textarea>
                                <x-input-error :messages="$errors->get('billing_address')" class="mt-2" />
                            </div>

                            <!-- Notes -->
                            <div>
                                <x-input-label for="notes" :value="__('Public Notes')" />
                                <textarea id="notes" name="notes" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Notes visible to supplier">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>

                            <!-- Internal Notes -->
                            <div>
                                <x-input-label for="internal_notes" :value="__('Internal Notes')" />
                                <textarea id="internal_notes" name="internal_notes" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Internal notes (not visible to supplier)">{{ old('internal_notes') }}</textarea>
                                <x-input-error :messages="$errors->get('internal_notes')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('purchases.purchase-orders.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Create Purchase
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('page-scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let itemIndex = 1;

                // Use event delegation for better dynamic element handling
                const orderItemsContainer = document.getElementById('orderItems');

                // Event delegation for product selection
                orderItemsContainer.addEventListener('change', function(e) {
                    if (e.target.classList.contains('product-select')) {
                        const option = e.target.selectedOptions[0];
                        const price = option.dataset.price || '';
                        const row = e.target.closest('.item-row');
                        const priceInput = row.querySelector('.unit-price-input');
                        priceInput.value = price;
                        calculateLineTotal(row);
                    }
                });

                // Event delegation for quantity, price, and discount inputs
                orderItemsContainer.addEventListener('input', function(e) {
                    if (e.target.classList.contains('quantity-input') ||
                        e.target.classList.contains('unit-price-input') ||
                        e.target.classList.contains('discount-input')) {
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

                // Update summary on additional field changes
                document.querySelectorAll('#tax_amount, #shipping_amount, #discount_amount').forEach(input => {
                    input.addEventListener('input', updateOrderSummary);
                });

                // Add Item Button
                document.getElementById('addItemBtn').addEventListener('click', function() {
                    const itemsContainer = document.getElementById('orderItems');
                    const newItem = createItemRow(itemIndex);
                    itemsContainer.insertAdjacentHTML('beforeend', newItem);

                    // New: Focus on the product select in the newly added row to prompt adding a new product
                    const newRow = itemsContainer.lastElementChild; // Get the newly added row
                    const productSelect = newRow.querySelector('.product-select');
                    if (productSelect) {
                        productSelect.focus(); // Automatically focus on the product dropdown
                    }

                    itemIndex++;
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
                            <div class="item-row border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
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
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Discount</label>
                                        <input type="number" name="items[${index}][discount_amount]" step="0.01"
                                            class="discount-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            value="0">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Line Total</label>
                                        <input type="text"
                                            class="line-total mt-1 block w-full rounded-md border-gray-300 bg-gray-50 dark:bg-gray-600 dark:border-gray-600 dark:text-white"
                                            readonly>
                                    </div>
                                    <div>
                                        <button type="button"
                                            class="remove-item w-full inline-flex justify-center items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                                    <textarea name="items[${index}][notes]" rows="2"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Optional notes for this item"></textarea>
                                </div>
                            </div>
                        `;
                }

                function calculateLineTotal(row) {
                    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                    const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                    const discount = parseFloat(row.querySelector('.discount-input').value) || 0;

                    const lineTotal = (quantity * price) - discount;
                    row.querySelector('.line-total').value = '₱' + lineTotal.toFixed(2);

                    updateOrderSummary();
                }

                function updateOrderSummary() {
                    let subtotal = 0;

                    document.querySelectorAll('.item-row').forEach(row => {
                        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                        const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                        const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
                        subtotal += (quantity * price) - discount;
                    });

                    // Recalculate tax if a tax rule is selected, otherwise set to 0
                    const taxSelect = document.getElementById('tax_rule_id');
                    if (taxSelect && taxSelect.value) {
                        const selectedOption = taxSelect.options[taxSelect.selectedIndex];
                        const method = selectedOption.getAttribute('data-method');
                        const rate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
                        const fixedAmount = parseFloat(selectedOption.getAttribute('data-fixed')) || 0;
                        
                        let taxAmount = 0;
                        if (method === 'percentage') {
                            taxAmount = (subtotal * rate) / 100;
                        } else {
                            taxAmount = fixedAmount;
                        }
                        document.getElementById('tax_amount').value = taxAmount.toFixed(2);
                    } else if (taxSelect) {
                        // No tax rule selected, clear the amount
                        document.getElementById('tax_amount').value = '0.00';
                    }

                    // Recalculate discount if a discount rule is selected, otherwise set to 0
                    const discountSelect = document.getElementById('discount_rule_id');
                    if (discountSelect && discountSelect.value) {
                        const selectedOption = discountSelect.options[discountSelect.selectedIndex];
                        const method = selectedOption.getAttribute('data-method');
                        const rate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
                        const fixedAmount = parseFloat(selectedOption.getAttribute('data-fixed')) || 0;
                        
                        let discountAmount = 0;
                        if (method === 'percentage') {
                            discountAmount = (subtotal * rate) / 100;
                        } else {
                            discountAmount = fixedAmount;
                        }
                        document.getElementById('discount_amount').value = discountAmount.toFixed(2);
                    } else if (discountSelect) {
                        // No discount rule selected, clear the amount
                        document.getElementById('discount_amount').value = '0.00';
                    }

                    const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
                    const shipping = parseFloat(document.getElementById('shipping_amount').value) || 0;
                    const orderDiscount = parseFloat(document.getElementById('discount_amount').value) || 0;

                    const total = subtotal + tax + shipping - orderDiscount;

                    document.getElementById('subtotal-display').textContent = '₱' + subtotal.toFixed(2);
                    document.getElementById('tax-display').textContent = '₱' + tax.toFixed(2);
                    document.getElementById('shipping-display').textContent = '₱' + shipping.toFixed(2);
                    document.getElementById('discount-display').textContent = '₱' + orderDiscount.toFixed(2);
                    document.getElementById('total-display').textContent = '₱' + total.toFixed(2);
                }

                // Apply selected tax rule to calculate tax amount
                function applyTaxRule(selectElement) {
                    updateOrderSummary();
                }

                // Apply selected discount rule to calculate discount amount
                function applyDiscountRule(selectElement) {
                    updateOrderSummary();
                }
            });
        </script>
    @endpush
</x-app-layout>
