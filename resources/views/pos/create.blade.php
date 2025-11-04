<x-app-layout>
    <div class="py-6" x-data="posSystem()" x-init="init()">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Point of Sale</h2>
                            <p class="text-gray-600 dark:text-gray-400">Quick in-store purchase</p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            <a href="{{ route('pos.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Sales History
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('pos.store') }}" id="posForm">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Products Section (Left - 2 columns) -->
                    <div class="lg:col-span-2">
                        <!-- Product Search & Filter -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                            <div class="p-4">
                                <input type="text" 
                                       x-model="productSearch" 
                                       @input="filterProducts"
                                       placeholder="Search products by name or SKU..." 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Products</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 max-h-[600px] overflow-y-auto">
                                    <template x-for="product in filteredProducts" :key="product.id">
                                        <div class="relative" 
                                             x-data="{ showTooltip: false }"
                                             @mouseenter="showTooltip = true" 
                                             @mouseleave="showTooltip = false">
                                            <div @click="addToCart(product.id, product.name, product.price, product.stock, product.image || '')" 
                                                 class="bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden cursor-pointer hover:shadow-lg hover:border-blue-500 transition-all">
                                                <div class="aspect-square bg-gray-100 dark:bg-gray-600 flex items-center justify-center">
                                                    <template x-if="product.image">
                                                        <img :src="product.image" :alt="product.name" class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!product.image">
                                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                        </svg>
                                                    </template>
                                                </div>
                                                <div class="p-3">
                                                    <div class="flex items-start justify-between mb-2">
                                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded"
                                                              :class="{
                                                                  'text-green-700 bg-green-100 dark:bg-green-900 dark:text-green-300': product.stock > 10,
                                                                  'text-yellow-700 bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-300': product.stock <= 10 && product.stock > 0,
                                                                  'text-red-700 bg-red-100 dark:bg-red-900 dark:text-red-300': product.stock === 0
                                                              }"
                                                              x-text="product.stock + ' ' + product.unit"></span>
                                                    </div>
                                                    <h4 class="font-semibold text-sm text-gray-900 dark:text-white truncate mb-1" x-text="product.name" :title="product.name"></h4>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2" x-text="product.sku"></p>
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-lg font-bold text-gray-900 dark:text-white" x-text="'₱' + parseFloat(product.price).toFixed(2)"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Hover Tooltip -->
                                            <div x-show="showTooltip"
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-150"
                                                 x-transition:leave-start="opacity-100 scale-100"
                                                 x-transition:leave-end="opacity-0 scale-95"
                                                 class="absolute z-50 inset-0 bg-white dark:bg-gray-800 border-2 border-blue-500 dark:border-blue-400 rounded-lg shadow-2xl p-3 overflow-y-auto cursor-pointer"
                                                 style="display: none;"
                                                 @click="addToCart(product.id, product.name, product.price, product.stock, product.image || ''); showTooltip = false">
                                                <div class="space-y-2 h-full flex flex-col">
                                                    <!-- Product Image -->
                                                    <template x-if="product.image">
                                                        <div class="w-full h-28 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden flex-shrink-0">
                                                            <img :src="product.image" :alt="product.name" class="w-full h-full object-cover">
                                                        </div>
                                                    </template>
                                                    
                                                    <!-- Product Name -->
                                                    <div class="flex-shrink-0">
                                                        <h4 class="font-bold text-sm text-gray-900 dark:text-white mb-0.5 line-clamp-2" x-text="product.name"></h4>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            SKU: <span x-text="product.sku"></span>
                                                        </p>
                                                    </div>
                                                    
                                                    <!-- Product Details -->
                                                    <div class="space-y-1.5 text-xs border-t border-gray-200 dark:border-gray-700 pt-2 flex-1">
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600 dark:text-gray-400">Category:</span>
                                                            <span class="font-medium text-gray-900 dark:text-white truncate ml-2" x-text="product.category"></span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600 dark:text-gray-400">Price:</span>
                                                            <span class="font-bold text-base text-blue-600 dark:text-blue-400" x-text="'₱' + parseFloat(product.price).toFixed(2)"></span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600 dark:text-gray-400">Stock:</span>
                                                            <span class="font-semibold" 
                                                                  :class="{ 
                                                                      'text-green-600 dark:text-green-400': product.stock > 10,
                                                                      'text-yellow-600 dark:text-yellow-400': product.stock <= 10 && product.stock > 0,
                                                                      'text-red-600 dark:text-red-400': product.stock === 0
                                                                  }"
                                                                  x-text="product.stock + ' ' + product.unit">
                                                            </span>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Click to Add Hint -->
                                                    <div class="text-center pt-1.5 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 italic">
                                                            Click to add to cart
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cart & Checkout Section (Right - 1 column) -->
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <!-- Customer Selection/Creation -->
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Customer</h3>
                                    
                                    <!-- Existing Customer -->
                                    <div x-show="!showNewCustomerForm">
                                        <select x-model="selectedCustomerId" 
                                                name="customer_id"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white mb-2">
                                            <option value="">Select existing customer...</option>
                                            <template x-for="customer in customers" :key="customer.id">
                                                <option :value="customer.id" x-text="customer.name + ' (' + customer.phone + ')'"></option>
                                            </template>
                                        </select>
                                        <button type="button" 
                                                @click="showNewCustomerForm = true; selectedCustomerId = ''"
                                                class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                            + Add New Customer
                                        </button>
                                    </div>

                                    <!-- New Customer Form -->
                                    <div x-show="showNewCustomerForm" class="space-y-3">
                                        <div>
                                            <input type="text" 
                                                   name="new_customer_first_name"
                                                   x-model="newCustomer.first_name"
                                                   placeholder="First Name *" 
                                                   :required="showNewCustomerForm"
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                        </div>
                                        <div>
                                            <input type="text" 
                                                   name="new_customer_last_name"
                                                   x-model="newCustomer.last_name"
                                                   placeholder="Last Name" 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                        </div>
                                        <div>
                                            <input type="tel" 
                                                   name="new_customer_phone"
                                                   x-model="newCustomer.phone"
                                                   placeholder="Phone Number" 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                        </div>
                                        <div>
                                            <input type="email" 
                                                   name="new_customer_email"
                                                   x-model="newCustomer.email"
                                                   placeholder="Email (Optional)" 
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                        </div>
                                        <button type="button" 
                                                @click="showNewCustomerForm = false; clearNewCustomer()"
                                                class="text-sm text-gray-600 hover:text-gray-700 dark:text-gray-400">
                                            ← Use Existing Customer
                                        </button>
                                    </div>
                                </div>

                                <!-- Cart Items -->
                                <div class="mb-4">
                                    <div class="flex justify-between items-center mb-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Cart</h3>
                                        <button type="button" 
                                                @click="clearCart()" 
                                                :disabled="cart.length === 0"
                                                class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 disabled:opacity-50">
                                            Clear
                                        </button>
                                    </div>

                                    <div class="space-y-2 max-h-[300px] overflow-y-auto mb-4 scrollbar-thin">
                                        <p x-show="cart.length === 0" class="text-center text-gray-500 py-8 text-sm">Cart is empty</p>
                                        
                                        <template x-for="(item, index) in cart" :key="index">
                                            <div class="flex items-center space-x-2 p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="item.name"></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'₱' + parseFloat(item.price).toFixed(2)"></p>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    <button type="button" @click="updateQuantity(index, -1)" class="w-7 h-7 flex items-center justify-center bg-gray-200 dark:bg-gray-600 rounded hover:bg-gray-300">
                                                        <span class="text-sm">-</span>
                                                    </button>
                                                    <span class="w-8 text-center text-sm font-medium" x-text="item.quantity"></span>
                                                    <button type="button" @click="updateQuantity(index, 1)" class="w-7 h-7 flex items-center justify-center bg-blue-100 dark:bg-blue-900 rounded hover:bg-blue-200">
                                                        <span class="text-sm">+</span>
                                                    </button>
                                                </div>
                                                <button type="button" @click="removeFromCart(index)" class="text-red-600 hover:text-red-700">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                                <!-- Hidden inputs for form submission -->
                                                <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.id">
                                                <input type="hidden" :name="'items[' + index + '][quantity]'" :value="item.quantity">
                                                <input type="hidden" :name="'items[' + index + '][unit_price]'" :value="item.price">
                                                <input type="hidden" :name="'items[' + index + '][discount_amount]'" value="0">
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Cart Summary -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                        <span class="font-medium text-gray-900 dark:text-white" x-text="'₱' + total.toFixed(2)"></span>
                                    </div>
                                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                                        <span class="text-gray-900 dark:text-white">Total</span>
                                        <span class="text-gray-900 dark:text-white" x-text="'₱' + total.toFixed(2)"></span>
                                    </div>
                                </div>

                                <!-- Payment Method -->
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                                    <select x-model="paymentMethod" 
                                            name="payment_method" 
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <!-- Payment Status -->
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Status</label>
                                    <select x-model="paymentStatus" 
                                            name="payment_status" 
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="paid">Paid</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>

                                <!-- Amount Received (for cash) -->
                                <div class="mt-4" x-show="paymentMethod === 'cash' && paymentStatus === 'paid'">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount Received</label>
                                    <input type="number" 
                                           x-model.number="amountReceived" 
                                           name="amount_received"
                                           step="0.01" 
                                           min="0"
                                           :placeholder="'₱' + total.toFixed(2)"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <p x-show="change > 0" class="mt-1 text-sm text-green-600 dark:text-green-400">
                                        Change: <span class="font-bold" x-text="'₱' + change.toFixed(2)"></span>
                                    </p>
                                    <p x-show="amountReceived > 0 && change < 0" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        Insufficient amount
                                    </p>
                                </div>

                                <!-- Hidden Fields -->
                                <input type="hidden" name="order_date" :value="new Date().toISOString().split('T')[0]">

                                <!-- Action Buttons -->
                                <div class="mt-6 space-y-2">
                                    <button type="submit" 
                                            :disabled="cart.length === 0 || (!selectedCustomerId && !newCustomer.first_name)"
                                            @click="console.log('Submitting POS form', { cart: cart, customerId: selectedCustomerId, newCustomer: newCustomer })"
                                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Complete Sale
                                    </button>
                                    <button type="button" 
                                            @click="clearAll()"
                                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 transition">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function posSystem() {
            return {
                // Data
                products: @json($products),
                customers: @json($customers),
                filteredProducts: [],
                productSearch: '',
                cart: [],
                selectedCustomerId: '',
                showNewCustomerForm: false,
                newCustomer: {
                    first_name: '',
                    last_name: '',
                    phone: '',
                    email: ''
                },
                paymentMethod: 'cash',
                paymentStatus: 'paid',
                amountReceived: 0,

                // Computed
                get total() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },

                get change() {
                    return this.amountReceived - this.total;
                },

                // Methods
                init() {
                    this.filteredProducts = this.products;
                },

                filterProducts() {
                    const search = this.productSearch.toLowerCase();
                    if (!search) {
                        this.filteredProducts = this.products;
                        return;
                    }
                    this.filteredProducts = this.products.filter(product => 
                        product.name.toLowerCase().includes(search) ||
                        product.sku.toLowerCase().includes(search)
                    );
                },

                addToCart(id, name, price, stock, image = '') {
                    const existing = this.cart.find(item => item.id === id);
                    if (existing) {
                        if (existing.quantity < stock) {
                            existing.quantity++;
                        } else {
                            alert('Cannot add more. Stock limit reached.');
                        }
                    } else {
                        if (stock > 0) {
                            this.cart.push({
                                id: id,
                                name: name,
                                price: price,
                                quantity: 1,
                                stock: stock,
                                image: image
                            });
                        } else {
                            alert('Product is out of stock');
                        }
                    }
                },

                updateQuantity(index, change) {
                    const item = this.cart[index];
                    const newQuantity = item.quantity + change;
                    
                    if (newQuantity <= 0) {
                        this.removeFromCart(index);
                        return;
                    }
                    
                    if (newQuantity > item.stock) {
                        alert('Cannot exceed available stock');
                        return;
                    }
                    
                    item.quantity = newQuantity;
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                clearCart() {
                    if (confirm('Clear all items from cart?')) {
                        this.cart = [];
                    }
                },

                clearNewCustomer() {
                    this.newCustomer = {
                        first_name: '',
                        last_name: '',
                        phone: '',
                        email: ''
                    };
                },

                clearAll() {
                    if (confirm('Cancel this sale and clear all data?')) {
                        this.cart = [];
                        this.selectedCustomerId = '';
                        this.showNewCustomerForm = false;
                        this.clearNewCustomer();
                        this.amountReceived = 0;
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Enhanced scrollbar for cart items */
        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
        }
        
        .scrollbar-thin::-webkit-scrollbar {
            width: 8px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-track {
            background: rgba(229, 231, 235, 0.3);
            border-radius: 4px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.6);
            border-radius: 4px;
            border: 2px solid transparent;
            background-clip: content-box;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.9);
        }
        
        .dark .scrollbar-thin {
            scrollbar-color: rgba(75, 85, 99, 0.5) transparent;
        }
        
        .dark .scrollbar-thin::-webkit-scrollbar-track {
            background: rgba(55, 65, 81, 0.3);
        }
        
        .dark .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: rgba(75, 85, 99, 0.6);
        }
        
        .dark .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background-color: rgba(75, 85, 99, 0.9);
        }
    </style>
</x-app-layout>
