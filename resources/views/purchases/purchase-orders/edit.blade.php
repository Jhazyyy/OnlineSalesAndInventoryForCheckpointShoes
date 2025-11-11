<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Purchase Order
                                {{ $order->order_number }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">Update purchase order information</p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            <a href="{{ route('purchases.purchase-orders.show', $order->order_id) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Order
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
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                    :value="old('order_date', $order->order_date->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('order_date')" class="mt-2" />
                            </div>

                            <!-- Expected Date -->
                            <div>
                                <x-input-label for="expected_date" :value="__('Expected Delivery Date')" />
                                <x-text-input id="expected_date" name="expected_date" type="date"
                                    class="mt-1 block w-full" :value="old(
                                        'expected_date',
                                        $order->expected_date ? $order->expected_date->format('Y-m-d') : '',
                                    )" />
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
                                <select id="payment_method" name="payment_method"
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
                            <div class="md:col-span-2">
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
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
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
                            @foreach ($order->items as $index => $item)
                                <!-- Existing item row -->
                                <div class="item-row border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-4">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
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
                                            <input type="number" name="items[{{ $index }}][quantity_ordered]"
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
                                        <textarea name="items[{{ $index }}][notes]" rows="2"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="Optional notes for this item">{{ old("items.{$index}.notes", $item->notes) }}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <x-input-error :messages="$errors->get('items')" class="mt-2" />
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Summary</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <!-- Shipping Amount -->
                                <div>
                                    <x-input-label for="shipping_amount" :value="__('Shipping Amount')" />
                                    <x-text-input id="shipping_amount" name="shipping_amount" type="number"
                                        step="0.01" class="mt-1 block w-full" :value="old('shipping_amount', $order->shipping_amount)" />
                                    <x-input-error :messages="$errors->get('shipping_amount')" class="mt-2" />
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Order Totals</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span>Subtotal:</span>
                                        <span id="subtotal-display">₱{{ number_format($order->subtotal, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Shipping:</span>
                                        <span
                                            id="shipping-display">₱{{ number_format($order->shipping_amount, 2) }}</span>
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
                </div>

                <!-- Additional Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Additional Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Delivery Address -->
                            <div>
                                <x-input-label for="delivery_address" :value="__('Delivery Address')" />
                                <textarea id="delivery_address" name="delivery_address" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Enter delivery address">{{ old('delivery_address', $order->delivery_address) }}</textarea>
                                <x-input-error :messages="$errors->get('delivery_address')" class="mt-2" />
                            </div>

                            <!-- Billing Address -->
                            <div>
                                <x-input-label for="billing_address" :value="__('Billing Address')" />
                                <textarea id="billing_address" name="billing_address" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Enter billing address">{{ old('billing_address', $order->billing_address) }}</textarea>
                                <x-input-error :messages="$errors->get('billing_address')" class="mt-2" />
                            </div>

                            <!-- Notes -->
                            <div>
                                <x-input-label for="notes" :value="__('Public Notes')" />
                                <textarea id="notes" name="notes" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Notes visible to supplier">{{ old('notes', $order->notes) }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>

                            <!-- Internal Notes -->
                            <div>
                                <x-input-label for="internal_notes" :value="__('Internal Notes')" />
                                <textarea id="internal_notes" name="internal_notes" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Internal notes (not visible to supplier)">{{ old('internal_notes', $order->internal_notes) }}</textarea>
                                <x-input-error :messages="$errors->get('internal_notes')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
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
                    attachItemEvents();
                    itemIndex++;
                });

                // Initial event attachment and calculation
                attachItemEvents();
                updateOrderSummary();

                function createItemRow(index) {
                    const products = @json($products);
                    let productOptions = '<option value="">Select Product</option>';

                    products.forEach(product => {
                        productOptions += `<option value="${product.id}" data-price="${product.price}" data-stock="${product.stock}">
                        ${product.name} (Stock: ${product.stock})
                    </option>`;
                    });

                    return `
                    <div class="item-row border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
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
                                    class="unit-price-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    required>
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

                    // Update summary on additional field changes
                    document.getElementById('shipping_amount').addEventListener('input', updateOrderSummary);
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

                    document.querySelectorAll('.item-row').forEach(row => {
                        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                        const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                        subtotal += (quantity * price);
                    });

                    const shipping = parseFloat(document.getElementById('shipping_amount').value) || 0;

                    const total = subtotal + shipping;

                    document.getElementById('subtotal-display').textContent = '₱' + subtotal.toFixed(2);
                    document.getElementById('shipping-display').textContent = '₱' + shipping.toFixed(2);
                    document.getElementById('total-display').textContent = '₱' + total.toFixed(2);
                }
            });
        </script>
    @endpush
</x-app-layout>
