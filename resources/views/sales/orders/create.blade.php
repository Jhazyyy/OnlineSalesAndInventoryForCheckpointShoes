<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Sales Order</h2>
                            <p class="text-gray-600 dark:text-gray-400">Create a new sales order for a customer</p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            <a href="{{ route('sales.orders.index') }}"
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
            <form method="POST" action="{{ route('sales.orders.store') }}" id="orderForm">
                @csrf

                <!-- Order Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Customer Selection -->
                            <div>
                                <x-input-label for="customer_id" :value="__('Customer')" />
                                <select id="customer_id" name="customer_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer['id'] }}" {{ old('customer_id') == $customer['id'] ? 'selected' : '' }}>
                                            {{ $customer['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                            </div>

                            <!-- Order Date -->
                            <div>
                                <x-input-label for="order_date" :value="__('Order Date')" />
                                <x-text-input id="order_date" name="order_date" type="date" class="mt-1 block w-full"
                                    :value="old('order_date', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('order_date')" class="mt-2" />
                            </div>

                            <!-- Required Date -->
                            <div>
                                <x-input-label for="required_date" :value="__('Required Date')" />
                                <x-text-input id="required_date" name="required_date" type="date"
                                    class="mt-1 block w-full" :value="old('required_date')" />
                                <x-input-error :messages="$errors->get('required_date')" class="mt-2" />
                            </div>

                            <!-- Priority -->
                            <div>
                                <x-input-label for="priority" :value="__('Priority')" />
                                <select id="priority" name="priority"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="normal" {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>
                                        Normal</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
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
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check
                                    </option>
                                </select>
                                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                            </div>

                            <!-- Payment Status -->
                            <div>
                                <x-input-label for="payment_status" :value="__('Payment Status')" />
                                <select id="payment_status" name="payment_status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="pending" {{ old('payment_status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>
                                        Partial</option>
                                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid
                                    </option>
                                </select>
                                <x-input-error :messages="$errors->get('payment_status')" class="mt-2" />
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
                                            @foreach($products as $product)
                                                <option value="{{ $product['id'] }}" data-price="{{ $product['price'] }}"
                                                    data-stock="{{ $product['stock'] }}">
                                                    {{ $product['name'] }} Stock: {{ $product['stock'] }}
                                                    {{-- {{ $product->name }} (SKU: {{ $product->sku }}) - Stock:
                                                    {{ $product->stock_quantity }} --}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                        <input type="number" name="items[0][quantity]"
                                            class="quantity-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            min="1" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit
                                            Price</label>
                                        <input type="number" name="items[0][unit_price]" step="0.01"
                                            class="unit-price-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            required>
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
                            </div>
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
                                <div>
                                    <x-input-label for="tax_amount" :value="__('Tax Amount')" />
                                    <x-text-input id="tax_amount" name="tax_amount" type="number" step="0.01"
                                        class="mt-1 block w-full" :value="old('tax_amount', 0)" />
                                    <x-input-error :messages="$errors->get('tax_amount')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="shipping_amount" :value="__('Shipping Amount')" />
                                    <x-text-input id="shipping_amount" name="shipping_amount" type="number" step="0.01"
                                        class="mt-1 block w-full" :value="old('shipping_amount', 0)" />
                                    <x-input-error :messages="$errors->get('shipping_amount')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="discount_amount" :value="__('Order Discount')" />
                                    <x-text-input id="discount_amount" name="discount_amount" type="number" step="0.01"
                                        class="mt-1 block w-full" :value="old('discount_amount', 0)" />
                                    <x-input-error :messages="$errors->get('discount_amount')" class="mt-2" />
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg dark:text-white">
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span>Subtotal:</span>
                                        <span id="subtotalDisplay">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Tax:</span>
                                        <span id="taxDisplay">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Shipping:</span>
                                        <span id="shippingDisplay">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Discount:</span>
                                        <span id="discountDisplay">₱0.00</span>
                                    </div>
                                    <hr class="border-gray-300 dark:border-gray-600">
                                    <div class="flex justify-between font-bold">
                                        <span>Total:</span>
                                        <span id="totalDisplay">₱0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Addresses and Notes -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Additional Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="shipping_address" :value="__('Shipping Address')" />
                                <textarea id="shipping_address" name="shipping_address" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('shipping_address') }}</textarea>
                                <x-input-error :messages="$errors->get('shipping_address')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="billing_address" :value="__('Billing Address')" />
                                <textarea id="billing_address" name="billing_address" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('billing_address') }}</textarea>
                                <x-input-error :messages="$errors->get('billing_address')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="notes" :value="__('Customer Notes')" />
                                <textarea id="notes" name="notes" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="internal_notes" :value="__('Internal Notes')" />
                                <textarea id="internal_notes" name="internal_notes" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('internal_notes') }}</textarea>
                                <x-input-error :messages="$errors->get('internal_notes')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('sales.orders.index') }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Create Order
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let itemCount = 1;

            // Add new item row
            document.getElementById('addItemBtn').addEventListener('click', function () {
                const itemsContainer = document.getElementById('orderItems');
                const newItem = createItemRow(itemCount);
                itemsContainer.insertAdjacentHTML('beforeend', newItem);
                itemCount++;
            });

            // Event delegation for dynamic elements
            document.getElementById('orderItems').addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-item')) {
                    if (document.querySelectorAll('.item-row').length > 1) {
                        e.target.closest('.item-row').remove();
                        updateCalculations();
                    }
                }
            });

            // Event delegation for input changes
            document.getElementById('orderItems').addEventListener('change', function (e) {
                const row = e.target.closest('.item-row');
                if (!row) return;

                if (e.target.classList.contains('product-select')) {
                    const option = e.target.options[e.target.selectedIndex];
                    if (option.dataset.price) {
                        row.querySelector('.unit-price-input').value = option.dataset.price;
                    }
                    updateLineTotal(row);
                } else if (e.target.classList.contains('quantity-input') ||
                    e.target.classList.contains('unit-price-input') ||
                    e.target.classList.contains('discount-input')) {
                    updateLineTotal(row);
                }
            });

            // Update calculations when summary fields change
            document.querySelectorAll('#tax_amount, #shipping_amount, #discount_amount').forEach(input => {
                input.addEventListener('input', updateCalculations);
            });

            function createItemRow(index) {
                return `
                    <div class="item-row border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                                <select name="items[${index}][product_id]" class="product-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product['id'] }}" data-price="{{ $product['price'] }}" data-stock="{{ $product['stock'] }}">
                                            {{ $product['name'] }} (Stock: {{ $product['stock'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                <input type="number" name="items[${index}][quantity]" class="quantity-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" min="1" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price</label>
                                <input type="number" name="items[${index}][unit_price]" step="0.01" class="unit-price-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Discount</label>
                                <input type="number" name="items[${index}][discount_amount]" step="0.01" class="discount-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Line Total</label>
                                <input type="text" class="line-total mt-1 block w-full rounded-md border-gray-300 bg-gray-50 dark:bg-gray-600 dark:border-gray-600 dark:text-white" readonly>
                            </div>
                            <div>
                                <button type="button" class="remove-item w-full inline-flex justify-center items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }

            function updateLineTotal(row) {
                const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                const discount = parseFloat(row.querySelector('.discount-input').value) || 0;

                const lineTotal = (quantity * unitPrice) - discount;
                row.querySelector('.line-total').value = '₱' + lineTotal.toFixed(2);

                updateCalculations();
            }

            function updateCalculations() {
                let subtotal = 0;

                document.querySelectorAll('.item-row').forEach(row => {
                    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                    const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                    const discount = parseFloat(row.querySelector('.discount-input').value) || 0;

                    subtotal += (quantity * unitPrice) - discount;
                });

                const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
                const shipping = parseFloat(document.getElementById('shipping_amount').value) || 0;
                const orderDiscount = parseFloat(document.getElementById('discount_amount').value) || 0;

                const total = subtotal + tax + shipping - orderDiscount;

                document.getElementById('subtotalDisplay').textContent = '₱' + subtotal.toFixed(2);
                document.getElementById('taxDisplay').textContent = '₱' + tax.toFixed(2);
                document.getElementById('shippingDisplay').textContent = '₱' + shipping.toFixed(2);
                document.getElementById('discountDisplay').textContent = '₱' + orderDiscount.toFixed(2);
                document.getElementById('totalDisplay').textContent = '₱' + total.toFixed(2);
            }

            // Initialize calculations
            updateCalculations();
        });
    </script>
</x-app-layout>