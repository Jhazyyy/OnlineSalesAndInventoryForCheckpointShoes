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

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Products Section (Left Side - 2 columns) -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Available Products</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4" id="productsGrid">
                                @foreach($products as $product)
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden cursor-pointer hover:shadow-lg transition-shadow product-card"
                                    data-id="{{ $product['id'] }}"
                                    data-name="{{ $product['name'] }}"
                                    data-price="{{ $product['price'] }}"
                                    data-stock="{{ $product['stock'] }}">
                                    <div class="aspect-square bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        @if(!empty($product['image']))
                                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="w-full h-full object-cover">
                                        @else
                                        <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <div class="flex items-start justify-between mb-2">
                                            <span class="inline-block px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 dark:bg-green-900 dark:text-green-300 rounded">
                                                {{ $product['stock'] }} {{ $product['unit'] ?? 'pcs' }}
                                            </span>
                                        </div>
                                        <h3 class="font-medium text-gray-900 dark:text-white text-sm mb-1 truncate" title="{{ $product['name'] }}">
                                            {{ $product['name'] }}
                                        </h3>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">₱{{ number_format($product['price'], 2) }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Section (Right Side - 1 column) -->
                <div class="lg:col-span-1">
                    <form method="POST" action="{{ route('sales.orders.store') }}" id="orderForm">
                        @csrf

                        <!-- Cart Card -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Cart</h3>
                                    <button type="button" id="clearCartBtn" class="text-sm text-red-600 hover:text-red-700 dark:text-red-400">
                                        Clear(<span id="cartCount">0</span>)
                                    </button>
                                </div>

                                <!-- Cart Items -->
                                <div class="mb-4 max-h-96 overflow-y-auto space-y-3" id="cartItems">
                                    <p class="text-center text-gray-500 dark:text-gray-400 py-8" id="emptyCartMessage">Cart is empty</p>
                                </div>

                                <!-- Cart Summary -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-3">
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                            <span>Subtotal</span>
                                            <span id="subtotalDisplay">₱0.00</span>
                                        </div>
                                        <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                            <span>Tax (0%)</span>
                                            <span id="taxDisplay">₱0.00</span>
                                        </div>
                                        <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                            <span>Discount</span>
                                            <span id="discountDisplay">₱0.00</span>
                                        </div>
                                    </div>

                                    <!-- Custom Discount -->
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Discount Type</label>
                                            <select id="discount_type" name="discount_type" class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="percentage">Percentage</option>
                                                <option value="fixed">Fixed</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Amount</label>
                                            <input type="number" id="custom_discount" name="custom_discount" step="0.01" min="0" value="0"
                                                class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    </div>

                                    <!-- Total -->
                                    <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
                                            <span class="text-2xl font-bold text-gray-900 dark:text-white" id="totalDisplay">₱0.00</span>
                                        </div>
                                    </div>

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

                                    <!-- Payment Method -->
                                    <div>
                                        <x-input-label for="payment_method" :value="__('Payment Method')" />
                                        <select id="payment_method" name="payment_method"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="cash" {{ old('payment_method', 'cash') == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        </select>
                                    </div>

                                    <!-- Payment Status -->
                                    <div>
                                        <x-input-label for="payment_status" :value="__('Payment Status')" />
                                        <select id="payment_status" name="payment_status"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="pending" {{ old('payment_status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                            <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                        </select>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="flex space-x-2 pt-4">
                                        <button type="submit" id="submitBtn"
                                            class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Create Order
                                        </button>
                                    </div>

                                    <!-- Hidden Fields -->
                                    <input type="hidden" name="order_date" value="{{ date('Y-m-d') }}">
                                    <input type="hidden" name="tax_amount" id="tax_amount" value="0">
                                    <input type="hidden" name="discount_amount" id="discount_amount_hidden" value="0">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Modal -->
    <div id="alertModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <!-- Modal panel -->
            <div class="inline-block align-middle bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div id="alertIcon" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <!-- Icon will be inserted here -->
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Alert
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400" id="alertMessage">
                                    Message
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="closeAlertBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let cart = [];

            // Alert Modal Functions
            function showAlert(message, type = 'error') {
                const modal = document.getElementById('alertModal');
                const alertIcon = document.getElementById('alertIcon');
                const alertMessage = document.getElementById('alertMessage');
                const modalTitle = document.getElementById('modal-title');

                // Set message
                alertMessage.textContent = message;

                // Set icon and colors based on type
                if (type === 'error') {
                    modalTitle.textContent = 'Error';
                    alertIcon.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10';
                    alertIcon.innerHTML = `
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    `;
                } else if (type === 'success') {
                    modalTitle.textContent = 'Success';
                    alertIcon.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10';
                    alertIcon.innerHTML = `
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    `;
                } else if (type === 'warning') {
                    modalTitle.textContent = 'Warning';
                    alertIcon.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900 sm:mx-0 sm:h-10 sm:w-10';
                    alertIcon.innerHTML = `
                        <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    `;
                }

                // Show modal
                modal.classList.remove('hidden');
            }

            function hideAlert() {
                const modal = document.getElementById('alertModal');
                modal.classList.add('hidden');
            }

            // Close alert button
            document.getElementById('closeAlertBtn').addEventListener('click', hideAlert);

            // Close on background click
            document.getElementById('alertModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideAlert();
                }
            });

            // Product card click handler
            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    const productName = this.dataset.name;
                    const productPrice = parseFloat(this.dataset.price);
                    const productStock = parseInt(this.dataset.stock);

                    addToCart(productId, productName, productPrice, productStock);
                });
            });

            // Clear cart button
            document.getElementById('clearCartBtn').addEventListener('click', function() {
                if (cart.length === 0) {
                    showAlert('Cart is already empty', 'warning');
                    return;
                }
                
                // Create a custom confirmation modal
                const modal = document.getElementById('alertModal');
                const alertIcon = document.getElementById('alertIcon');
                const alertMessage = document.getElementById('alertMessage');
                const modalTitle = document.getElementById('modal-title');
                const closeBtn = document.getElementById('closeAlertBtn');

                modalTitle.textContent = 'Confirm Clear Cart';
                alertMessage.textContent = 'Are you sure you want to clear all items from the cart?';
                alertIcon.className = 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900 sm:mx-0 sm:h-10 sm:w-10';
                alertIcon.innerHTML = `
                    <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                `;

                // Update buttons for confirmation
                closeBtn.outerHTML = `
                    <button type="button" id="confirmClearBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Clear Cart
                    </button>
                    <button type="button" id="cancelClearBtn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                `;

                modal.classList.remove('hidden');

                // Confirm clear
                document.getElementById('confirmClearBtn').addEventListener('click', function() {
                    cart = [];
                    renderCart();
                    hideAlert();
                    // Restore original close button
                    document.querySelector('.bg-gray-50.dark\\:bg-gray-700').innerHTML = `
                        <button type="button" id="closeAlertBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            OK
                        </button>
                    `;
                    document.getElementById('closeAlertBtn').addEventListener('click', hideAlert);
                });

                // Cancel
                document.getElementById('cancelClearBtn').addEventListener('click', function() {
                    hideAlert();
                    // Restore original close button
                    document.querySelector('.bg-gray-50.dark\\:bg-gray-700').innerHTML = `
                        <button type="button" id="closeAlertBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            OK
                        </button>
                    `;
                    document.getElementById('closeAlertBtn').addEventListener('click', hideAlert);
                });
            });

            // Custom discount change
            document.getElementById('custom_discount').addEventListener('input', updateCalculations);
            document.getElementById('discount_type').addEventListener('change', updateCalculations);

            // Form submission
            document.getElementById('orderForm').addEventListener('submit', function(e) {
                if (cart.length === 0) {
                    e.preventDefault();
                    showAlert('Please add items to cart before submitting', 'warning');
                    return;
                }

                // Prepare items data
                const itemsData = cart.map((item, index) => ({
                    product_id: item.id,
                    quantity: item.quantity,
                    unit_price: item.price,
                    discount_amount: 0
                }));

                // Create hidden inputs for items
                itemsData.forEach((item, index) => {
                    const container = document.createElement('div');
                    container.innerHTML = `
                        <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                        <input type="hidden" name="items[${index}][unit_price]" value="${item.unit_price}">
                        <input type="hidden" name="items[${index}][discount_amount]" value="${item.discount_amount}">
                    `;
                    this.appendChild(container);
                });
            });

            function addToCart(id, name, price, stock) {
                // Check if product already exists in cart
                const existingItem = cart.find(item => item.id === id);
                
                if (existingItem) {
                    if (existingItem.quantity < stock) {
                        existingItem.quantity++;
                        showAlert(`${name} quantity updated to ${existingItem.quantity}`, 'success');
                    } else {
                        showAlert('Cannot add more. Stock limit reached.', 'warning');
                        return;
                    }
                } else {
                    if (stock > 0) {
                        cart.push({
                            id: id,
                            name: name,
                            price: price,
                            quantity: 1,
                            stock: stock
                        });
                        showAlert(`${name} added to cart`, 'success');
                    } else {
                        showAlert('Product is out of stock', 'error');
                        return;
                    }
                }

                renderCart();
            }

            function removeFromCart(index) {
                cart.splice(index, 1);
                renderCart();
            }

            function updateQuantity(index, change) {
                const item = cart[index];
                const newQuantity = item.quantity + change;

                if (newQuantity <= 0) {
                    removeFromCart(index);
                    return;
                }

                if (newQuantity > item.stock) {
                    showAlert('Cannot exceed available stock quantity', 'warning');
                    return;
                }

                item.quantity = newQuantity;
                renderCart();
            }

            function renderCart() {
                const cartContainer = document.getElementById('cartItems');
                const emptyMessage = document.getElementById('emptyCartMessage');
                const cartCount = document.getElementById('cartCount');

                cartCount.textContent = cart.length;

                if (cart.length === 0) {
                    emptyMessage.style.display = 'block';
                    cartContainer.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 py-8" id="emptyCartMessage">Cart is empty</p>';
                    updateCalculations();
                    return;
                }

                emptyMessage.style.display = 'none';
                
                cartContainer.innerHTML = cart.map((item, index) => `
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${item.name}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Q: ${item.quantity} ${item.stock > 0 ? '(' + item.stock + ' in stock)' : ''}</p>
                            <div class="flex items-center space-x-2 mt-1">
                                <button type="button" onclick="updateQuantity(${index}, -1)" class="w-6 h-6 flex items-center justify-center bg-gray-200 dark:bg-gray-600 rounded hover:bg-gray-300 dark:hover:bg-gray-500">
                                    <span class="text-gray-700 dark:text-gray-200">-</span>
                                </button>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">${item.quantity}.00</span>
                                <button type="button" onclick="updateQuantity(${index}, 1)" class="w-6 h-6 flex items-center justify-center bg-blue-100 dark:bg-blue-900 rounded hover:bg-blue-200 dark:hover:bg-blue-800">
                                    <span class="text-blue-700 dark:text-blue-300">+</span>
                                </button>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-base font-bold text-gray-900 dark:text-white">₱${(item.price * item.quantity).toFixed(2)}</p>
                            <button type="button" onclick="removeFromCart(${index})" class="mt-1 text-red-600 hover:text-red-700 dark:text-red-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                `).join('');

                updateCalculations();
            }

            function updateCalculations() {
                const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                const taxRate = 0; // 0%
                const tax = subtotal * taxRate;

                const customDiscount = parseFloat(document.getElementById('custom_discount').value) || 0;
                const discountType = document.getElementById('discount_type').value;
                
                let discount = 0;
                if (discountType === 'percentage') {
                    discount = subtotal * (customDiscount / 100);
                } else {
                    discount = customDiscount;
                }

                const total = subtotal + tax - discount;

                document.getElementById('subtotalDisplay').textContent = '₱' + subtotal.toFixed(2);
                document.getElementById('taxDisplay').textContent = '₱' + tax.toFixed(2);
                document.getElementById('discountDisplay').textContent = '₱' + discount.toFixed(2);
                document.getElementById('totalDisplay').textContent = '₱' + total.toFixed(2);

                // Update hidden fields
                document.getElementById('tax_amount').value = tax.toFixed(2);
                document.getElementById('discount_amount_hidden').value = discount.toFixed(2);

                // Enable/disable submit button
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = cart.length === 0;
            }

            // Make functions global
            window.addToCart = addToCart;
            window.removeFromCart = removeFromCart;
            window.updateQuantity = updateQuantity;

            // Initialize
            renderCart();
        });
    </script>
</x-app-layout>