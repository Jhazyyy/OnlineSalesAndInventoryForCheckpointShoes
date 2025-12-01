<x-app-layout>
    <div x-data="posSystem()" x-init="init()">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-2 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                    role="alert">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Header Section -->
            {{-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Sales</h2>
                            <p class="text-gray-600 dark:text-gray-400">Quick in-store purchase</p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            <a href="{{ route('pos.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                Sales List
                            </a>
                        </div>
                    </div>
                </div>
            </div> --}}

            <form method="POST" action="{{ route('pos.store') }}" id="posForm" enctype="multipart/form-data"
                @submit="updateDateTime()">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-2">
                    <!-- Products Section (Left - 2 columns) -->
                    <div class="lg:col-span-2 mt-2">
                        <!-- Product Search & Filter -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-2">
                            <div class="p-4 space-y-3">
                                <!-- Search Input -->
                                <input type="text" x-model="productSearch" @input="filterProducts"
                                    placeholder="Search products by name or SKU..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                                <!-- Category Filter -->
                                <div class="flex items-center gap-2">
                                    {{-- <label class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                        Category:
                                    </label> --}}
                                    <select x-model="selectedCategory" @change="filterProducts"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                        <option value="">All Categories</option>
                                        <template x-for="category in categories" :key="category">
                                            <option :value="category" x-text="category"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Products</h3>
                                    <span class="text-sm text-gray-600 dark:text-gray-400"
                                        x-text="filteredProducts.length + ' items'"></span>
                                </div>

                                <!-- No Products Message -->
                                <div x-show="filteredProducts.length === 0"
                                    class="flex flex-col items-center justify-center py-16 text-center">
                                    <svg class="w-20 h-20 text-gray-400 dark:text-gray-500 mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                        </path>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Products
                                        Found</h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4 max-w-md">
                                        <template x-if="productSearch || selectedCategory">
                                            <span>No products match your current filters. Try adjusting your search or
                                                category selection.</span>
                                        </template>
                                        <template x-if="!productSearch && !selectedCategory">
                                            <span>There are no products available in the inventory at the moment.</span>
                                        </template>
                                    </p>
                                    <button type="button" x-show="productSearch || selectedCategory"
                                        @click="productSearch = ''; selectedCategory = ''; filterProducts();"
                                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                            </path>
                                        </svg>
                                        Clear Filters
                                    </button>
                                </div>

                                <div x-show="filteredProducts.length > 0"
                                    class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 max-h-screen overflow-y-auto">
                                    <template x-for="product in filteredProducts" :key="product.id">
                                        <div @click="addToCart(product.id, product.name, product.price, product.stock, product.image || '')"
                                            class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden cursor-pointer hover:shadow-lg hover:border-blue-500 transition-all"
                                            :class="{ 'opacity-60 cursor-not-allowed': !product.price || product.price === 0 }">

                                            <!-- No Price Warning Badge -->
                                            <template x-if="!product.price || product.price === 0">
                                                <div class="absolute top-2 left-2 z-10">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 text-xs font-bold rounded bg-red-600 text-white">
                                                        No Price
                                                    </span>
                                                </div>
                                            </template>

                                            <!-- Product Image -->
                                            <div class="aspect-square bg-gray-100 dark:bg-gray-600 flex items-center justify-center relative">
                                                <template x-if="product.image">
                                                    <img :src="product.image" :alt="product.name"
                                                        class="w-full h-full object-cover">
                                                </template>
                                            </div>

                                            <!-- Product Information -->
                                            <div class="p-3 space-y-2">
                                                <!-- Product Name & SKU -->
                                                <div>
                                                    <h4 class="font-bold text-sm text-gray-900 dark:text-white line-clamp-2 mb-1"
                                                        x-text="product.name"></h4>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="product.sku"></p>
                                                </div>

                                                <!-- Product Details -->
                                                <div class="space-y-1.5 text-xs border-t border-gray-200 dark:border-gray-700 pt-2">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600 dark:text-gray-400">Category:</span>
                                                        <span class="font-medium text-gray-900 dark:text-white" x-text="product.category"></span>
                                                    </div>
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-gray-600 dark:text-gray-400">Price:</span>
                                                        <template x-if="product.price && product.price > 0">
                                                            <span class="font-bold text-base text-blue-600 dark:text-blue-400"
                                                                x-text="'₱' + parseFloat(product.price).toFixed(2)"></span>
                                                        </template>
                                                        <template x-if="!product.price || product.price === 0">
                                                            <span class="text-xs font-semibold text-red-600 dark:text-red-400">
                                                                No Price Set
                                                            </span>
                                                        </template>
                                                    </div>
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-gray-600 dark:text-gray-400">Stock:</span>
                                                        <span class="font-semibold px-2 py-0.5 rounded"
                                                            :class="{
                                                                'text-green-700 bg-green-100 dark:bg-green-900 dark:text-green-300': product.stock > 10,
                                                                'text-yellow-700 bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-300': product.stock <= 10 && product.stock > 5,
                                                                'text-red-700 bg-red-100 dark:bg-red-900 dark:text-red-300': product.stock <= 5
                                                            }"
                                                            x-text="product.stock + ' ' + product.unit">
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Click to Add Hint -->
                                                <div class="text-center pt-2 border-t border-gray-200 dark:border-gray-700">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 italic">
                                                        Click to add to cart
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cart & Checkout Section (Right - 1 column) -->
                    <div class="lg:col-span-1 mt-2">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                            <div class="p-6">
                                <!-- Customer Selection/Creation -->
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Customer</h3>

                                    <!-- Existing Customer -->
                                    <div x-show="!showNewCustomerForm">
                                        <select x-model="selectedCustomerId" name="customer_id"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white mb-2">
                                            <option value="">Select existing customer...</option>
                                            <template x-for="customer in customers" :key="customer.id">
                                                <option :value="customer.id" x-text="customer.name"></option>
                                            </template>
                                        </select>
                                        <button type="button"
                                            @click="showNewCustomerForm = true; selectedCustomerId = ''"
                                            class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                            Add New Customer
                                        </button>
                                    </div>

                                    <!-- New Customer Form -->
                                    <div x-show="showNewCustomerForm" class="space-y-3">
                                        <div>
                                            <input type="text" name="new_customer_first_name"
                                                x-model="newCustomer.first_name" placeholder="First Name *"
                                                :required="showNewCustomerForm"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                        </div>
                                        <div>
                                            <input type="text" name="new_customer_last_name"
                                                x-model="newCustomer.last_name" placeholder="Last Name"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                        </div>
                                        <div>
                                            <input type="tel" name="new_customer_phone"
                                                x-model="newCustomer.phone" placeholder="Phone Number"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                        </div>
                                        <div>
                                            <input type="email" name="new_customer_email"
                                                x-model="newCustomer.email" placeholder="Email (Optional)"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                        </div>
                                        <button type="button"
                                            @click="showNewCustomerForm = false; clearNewCustomer()"
                                            class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                            Use Existing Customer
                                        </button>
                                    </div>
                                </div>

                                <!-- Cart Items -->
                                <div class="mb-4">
                                    <div class="flex justify-between items-center mb-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Cart</h3>
                                        <button type="button" @click="clearCart()" :disabled="cart.length === 0"
                                            class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 disabled:opacity-50">
                                            Clear
                                        </button>
                                    </div>

                                    <div class="space-y-2 max-h-[300px] overflow-y-auto mb-4 scrollbar-thin">
                                        <p x-show="cart.length === 0" class="text-center text-gray-500 py-8 text-sm">
                                            Cart is empty</p>

                                        <template x-for="(item, index) in cart" :key="index">
                                            <div
                                                class="flex items-center space-x-2 p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white break-words"
                                                        x-text="item.name"></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400"
                                                        x-text="'₱' + parseFloat(item.price).toFixed(2)"></p>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    <button type="button" @click="updateQuantity(index, -1)"
                                                        class="w-7 h-7 flex items-center justify-center bg-gray-200 dark:bg-gray-600 rounded hover:bg-gray-300">
                                                        <span class="text-sm">-</span>
                                                    </button>
                                                    <input type="number" :value="item.quantity"
                                                        @input="setQuantity(index, $event.target.value)"
                                                        @focus="$event.target.select()" min="1"
                                                        :max="item.stock"
                                                        class="w-20 text-center text-sm font-medium border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 py-1">
                                                    <button type="button" @click="updateQuantity(index, 1)"
                                                        class="w-7 h-7 flex items-center justify-center bg-blue-100 dark:bg-blue-900 rounded hover:bg-blue-200">
                                                        <span class="text-sm">+</span>
                                                    </button>
                                                </div>
                                                <button type="button" @click="removeFromCart(index)"
                                                    class="text-red-600 hover:text-red-700">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                                <!-- Hidden inputs for form submission -->
                                                <input type="hidden" :name="'items[' + index + '][product_id]'"
                                                    :value="item.id">
                                                <input type="hidden" :name="'items[' + index + '][quantity]'"
                                                    :value="item.quantity">
                                                <input type="hidden" :name="'items[' + index + '][unit_price]'"
                                                    :value="item.price">
                                                <input type="hidden" :name="'items[' + index + '][discount_amount]'"
                                                    value="0">
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Cart Summary -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-3">
                                    <!-- Customer Tax Dropdown -->
                                    @if (isset($activeTaxes) && $activeTaxes->isNotEmpty())
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                <span class="flex items-center justify-between">
                                                    <span>Tax Rule</span>
                                                    {{-- <span x-show="selectedTaxRule"
                                                        class="text-indigo-600 dark:text-indigo-400"
                                                        x-text="'₱' + taxAmount.toFixed(2)"></span> --}}
                                                </span>
                                            </label>
                                            <select x-model="selectedTaxRule"
                                                class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">-- No Tax --</option>
                                                @foreach ($activeTaxes as $tax)
                                                    <option value="{{ $tax->id }}"
                                                        data-rate="{{ $tax->rate }}"
                                                        data-method="{{ $tax->calculation_method }}"
                                                        data-fixed="{{ $tax->fixed_amount ?? 0 }}">
                                                        {{ $tax->name }} -
                                                        @if ($tax->calculation_method === 'percentage')
                                                            {{ $tax->rate }}%
                                                        @else
                                                            ₱{{ number_format((float) $tax->fixed_amount, 2) }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <!-- Customer Discount Dropdown -->
                                    @if (isset($activeDiscounts) && $activeDiscounts->isNotEmpty())
                                        <div>
                                            <label
                                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                <span class="flex items-center justify-between">
                                                    <span>Discount</span>
                                                    {{-- <span x-show="selectedDiscountRule"
                                                        class="text-green-600 dark:text-green-400"
                                                        x-text="'₱' + discountAmount.toFixed(2)"></span> --}}
                                                </span>
                                            </label>
                                            <select x-model="selectedDiscountRule"
                                                class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">-- No Discount --</option>
                                                @foreach ($activeDiscounts as $discount)
                                                    <option value="{{ $discount->id }}"
                                                        data-rate="{{ $discount->rate }}"
                                                        data-method="{{ $discount->calculation_method }}"
                                                        data-fixed="{{ $discount->fixed_amount ?? 0 }}">
                                                        {{ $discount->name }} -
                                                        @if ($discount->calculation_method === 'percentage')
                                                            {{ $discount->rate }}%
                                                        @else
                                                            ₱{{ number_format((float) $discount->fixed_amount, 2) }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <!-- Order Summary -->
                                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3 space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                            <span class="font-medium text-gray-900 dark:text-white"
                                                x-text="'₱' + subtotal.toFixed(2)"></span>
                                        </div>
                                        <!-- Tax Line - Always Visible -->
                                        <div
                                            class="flex justify-between text-sm border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">Tax</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-500"
                                                    x-show="selectedTaxRule" x-text="getTaxRuleName()"></span>
                                                <span class="text-xs text-gray-400 dark:text-gray-600"
                                                    x-show="!selectedTaxRule">No tax applied</span>
                                            </div>
                                            <span class="font-semibold"
                                                :class="taxAmount > 0 ? 'text-indigo-600 dark:text-indigo-400' :
                                                    'text-gray-500 dark:text-gray-500'"
                                                x-text="'₱' + taxAmount.toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between text-sm" x-show="discountAmount > 0">
                                            <div class="flex flex-col">
                                                <span class="text-gray-600 dark:text-gray-400">Discount</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-500"
                                                    x-show="selectedDiscountRule"
                                                    x-text="getDiscountRuleName()"></span>
                                            </div>
                                            <span class="font-medium text-green-600 dark:text-green-400"
                                                x-text="'-₱' + discountAmount.toFixed(2)"></span>
                                        </div>
                                    </div>

                                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                                        <span class="text-gray-900 dark:text-white">Total</span>
                                        <span class="text-gray-900 dark:text-white"
                                            x-text="'₱' + total.toFixed(2)"></span>
                                    </div>
                                </div>

                                <!-- Payment Method -->
                                <div class="mt-4">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment
                                        Method</label>
                                    <select x-model="paymentMethod" name="payment_method" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="cash">Cash</option>
                                        <option value="gcash">GCash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>
                                </div>

                                <!-- Payment Status -->
                                <div class="mt-4">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment
                                        Status</label>
                                    <select x-model="paymentStatus" name="payment_status" required
                                        :disabled="isPaymentInsufficient"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                                        <option value="paid">Paid</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                    <p x-show="isPaymentInsufficient"
                                        class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                                        Status locked to 'Pending' - insufficient payment amount
                                    </p>
                                </div>

                                <!-- Amount Received (for cash) -->
                                <div class="mt-4" x-show="paymentMethod === 'cash'">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount
                                        Received</label>
                                    <input type="number" x-model.number="amountReceived" name="amount_received"
                                        @input="checkPaymentAmount" step="0.01" min="0"
                                        :placeholder="'₱' + total.toFixed(2)"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <p x-show="change >= 0 && amountReceived > 0"
                                        class="mt-1 text-sm text-green-600 dark:text-green-400">
                                        Change: <span class="font-bold" x-text="'₱' + change.toFixed(2)"></span>
                                    </p>
                                    <p x-show="amountReceived > 0 && change < 0"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        <strong>Insufficient amount!</strong> Short by <span class="font-bold"
                                            x-text="'₱' + Math.abs(change).toFixed(2)"></span>. Status set to Pending.
                                    </p>
                                </div>

                                <!-- Bank Transfer Fields -->
                                <div x-show="paymentMethod === 'bank_transfer'"
                                    class="mt-4 space-y-4 p-4 bg-blue-50 dark:bg-blue-900 rounded-md border border-blue-200 dark:border-blue-700">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-xs text-blue-700 dark:text-blue-300">
                                            Customer will need to upload bank transfer proof after order creation. Order
                                            will be marked as pending until proof is submitted and confirmed by admin.
                                        </p>
                                    </div>

                                    <!-- Bank Name -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Bank Name <span class="text-red-500">*</span>
                                        </label>
                                        <select x-model="bankName" name="bank_name"
                                            :required="paymentMethod === 'bank_transfer'"
                                            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">Select Bank</option>
                                            <option value="BDO">BDO (Banco de Oro)</option>
                                            <option value="BPI">BPI (Bank of the Philippine Islands)</option>
                                            <option value="Metrobank">Metrobank</option>
                                            <option value="UnionBank">UnionBank</option>
                                            <option value="PNB">PNB (Philippine National Bank)</option>
                                            <option value="Landbank">Landbank</option>
                                            <option value="Security Bank">Security Bank</option>
                                            <option value="RCBC">RCBC</option>
                                            <option value="Chinabank">Chinabank</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <!-- Reference Number -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Reference Number <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" x-model="referenceNo" name="reference_no"
                                            :required="paymentMethod === 'bank_transfer'"
                                            placeholder="Enter transaction reference"
                                            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>

                                    <!-- Proof Upload -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Proof of Payment <span class="text-red-500">*</span>
                                        </label>
                                        <input type="file" name="payment_proof"
                                            :required="paymentMethod === 'bank_transfer'" accept="image/*,.pdf"
                                            @change="handleProofUpload($event)"
                                            class="w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Upload bank transfer receipt (JPG, PNG, PDF, max 5MB)
                                        </p>
                                        <div x-show="proofFileName"
                                            class="mt-2 text-xs text-green-600 dark:text-green-400">
                                            File selected: <span x-text="proofFileName"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- GCash Scan-to-Pay -->
                                <div x-show="paymentMethod === 'gcash'" class="mt-4">
                                    <button type="button" @click="showGcashModal = true"
                                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                            </path>
                                        </svg>
                                        Show GCash QR Code
                                    </button>

                                    <!-- Show reference number if entered -->
                                    <div x-show="gcashReferenceNo"
                                        class="mt-3 p-3 bg-green-50 dark:bg-green-900 rounded-md border border-green-200 dark:border-green-700">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-green-800 dark:text-green-200">GCash
                                                    Reference Number Entered</p>
                                                <p class="text-xs text-green-600 dark:text-green-400"
                                                    x-text="gcashReferenceNo"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden input for GCash reference -->
                                <input type="hidden" name="gcash_reference_no" :value="gcashReferenceNo">


                                <!-- Hidden Fields -->
                                <!-- Order date will be updated to real-time on form submission -->
                                <input type="hidden" name="order_date" id="order_date_input" :value="currentDateTime">
                                <input type="hidden" name="tax_rule_id"
                                    :value="selectedTaxRule ? selectedTaxRule : ''">
                                <input type="hidden" name="tax_amount"
                                    :value="selectedTaxRule ? taxAmount.toFixed(2) : 0">
                                <input type="hidden" name="discount_rule_id"
                                    :value="selectedDiscountRule ? selectedDiscountRule : ''">
                                <input type="hidden" name="discount_amount"
                                    :value="selectedDiscountRule ? discountAmount.toFixed(2) : 0">
                                <input type="hidden" name="subtotal_amount" :value="subtotal.toFixed(2)">
                                <input type="hidden" name="total_amount" :value="total.toFixed(2)">

                                <!-- Action Buttons -->
                                <div class="mt-6 space-y-2">
                                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        <span class="font-semibold">Note:</span> You cannot modify the cart or
                                        customer details after confirming the sale.

                                    </div>
                                    <button type="submit"
                                        :disabled="cart.length === 0 || (!selectedCustomerId && !newCustomer.first_name) || (
                                            paymentMethod === 'cash' && (!amountReceived || amountReceived <= 0))"
                                        @click="validatePayment($event)"
                                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Confirm Sale
                                    </button>
                                    <button type="button" @click="clearAll()"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 transition">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GCash Scan-to-Pay Modal -->
                <div x-show="showGcashModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
                    aria-labelledby="modal-title" role="dialog" aria-modal="true"
                    @keydown.escape.window="showGcashModal = false">
                    <div
                        class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Background overlay -->
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                            @click="showGcashModal = false" aria-hidden="true"></div>

                        <!-- Modal panel -->
                        <div
                            class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <!-- Header -->
                            <div class="bg-blue-600 px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-white mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                            </path>
                                        </svg>
                                        <h3 class="text-xl font-bold text-white" id="modal-title">
                                            GCash Scan-to-Pay
                                        </h3>
                                    </div>
                                    <button type="button" @click="showGcashModal = false"
                                        class="text-white hover:text-gray-200">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Body -->
                            <div class="bg-white dark:bg-gray-800 px-6 py-6">
                                <!-- Instructions -->
                                <div
                                    class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg border border-blue-200 dark:border-blue-700">
                                    <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">How to Pay:</h4>
                                    <ol
                                        class="text-sm text-blue-700 dark:text-blue-300 space-y-1 list-decimal list-inside">
                                        <li>Open your GCash app</li>
                                        <li>Tap "Scan QR" on your app</li>
                                        <li>Scan the QR code below</li>
                                        <li>Confirm payment of <strong x-text="'₱' + total.toFixed(2)"></strong></li>
                                        <li>Enter the reference number from your GCash app below</li>
                                    </ol>
                                </div>

                                <!-- Amount Display -->
                                <div class="mb-6 text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Amount to Pay</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-white"
                                        x-text="'₱' + total.toFixed(2)"></p>
                                </div>

                                <!-- QR Code Display -->
                                <div class="mb-6 flex justify-center">
                                    <div
                                        class="p-4 bg-white dark:bg-gray-900 rounded-lg shadow-inner border-2 border-dashed border-gray-300 dark:border-gray-600">
                                        <!-- Replace this with your actual QR code image -->
                                        <img src="{{ asset('gcash_qr.jpg') }}" alt="GCash QR Code"
                                            class="w-64 h-64 object-contain"
                                            onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'256\' height=\'256\' viewBox=\'0 0 256 256\'%3E%3Crect width=\'256\' height=\'256\' fill=\'%23f3f4f6\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'monospace\' font-size=\'16\' fill=\'%236b7280\'%3EGCASH QR%3C/text%3E%3C/svg%3E'">
                                        <p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            Scan with GCash app
                                        </p>
                                    </div>
                                </div>

                                <!-- Reference Number Input -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        GCash Reference Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" x-model="gcashReferenceNo"
                                        placeholder="Enter 13-digit reference number" maxlength="20"
                                        class="w-full px-4 py-3 text-lg font-mono border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        You can find this in your GCash app after completing the payment
                                    </p>
                                </div>

                                <!-- Payment Verification Notice -->
                                <div
                                    class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900 rounded-md border border-yellow-200 dark:border-yellow-700">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2 mt-0.5"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-xs text-yellow-700 dark:text-yellow-300">
                                            The sale will be marked as "Paid via GCash" once you confirm. Please ensure
                                            the reference number is correct.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex space-x-3">
                                <button type="button" @click="showGcashModal = false"
                                    class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 transition">
                                    Cancel
                                </button>
                                <button type="button" @click="confirmGcashPayment()"
                                    :disabled="!gcashReferenceNo || gcashReferenceNo.length < 10"
                                    class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Confirm Payment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Toast Notification -->
            <div x-show="toast.show" x-cloak x-transition:enter="transform ease-out duration-300 transition"
                x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed top-4 right-4 z-50 max-w-sm w-full shadow-lg rounded-lg pointer-events-auto overflow-hidden"
                :class="{
                    'bg-green-50 dark:bg-green-900': toast.type === 'success',
                    'bg-red-50 dark:bg-red-900': toast.type === 'error',
                    'bg-yellow-50 dark:bg-yellow-900': toast.type === 'warning',
                    'bg-blue-50 dark:bg-blue-900': toast.type === 'info'
                }">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <template x-if="toast.type === 'success'">
                                <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                            <template x-if="toast.type === 'error'">
                                <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                            <template x-if="toast.type === 'warning'">
                                <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </template>
                            <template x-if="toast.type === 'info'">
                                <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium"
                                :class="{
                                    'text-green-800 dark:text-green-200': toast.type === 'success',
                                    'text-red-800 dark:text-red-200': toast.type === 'error',
                                    'text-yellow-800 dark:text-yellow-200': toast.type === 'warning',
                                    'text-blue-800 dark:text-blue-200': toast.type === 'info'
                                }"
                                x-text="toast.message"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="toast.show = false"
                                class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
                                :class="{
                                    'text-green-500 hover:text-green-600 focus:ring-green-500': toast
                                        .type === 'success',
                                    'text-red-500 hover:text-red-600 focus:ring-red-500': toast.type === 'error',
                                    'text-yellow-500 hover:text-yellow-600 focus:ring-yellow-500': toast
                                        .type === 'warning',
                                    'text-blue-500 hover:text-blue-600 focus:ring-blue-500': toast.type === 'info'
                                }">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
                selectedCategory: '',
                categories: [],
                cart: [],
                selectedCustomerId: '',
                selectedTaxRule: '{{ $defaultTax ? $defaultTax->id : '' }}',
                selectedDiscountRule: '',
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
                currentDateTime: '',

                // Bank Transfer fields
                bankName: '',
                referenceNo: '',
                proofFileName: '',

                // GCash fields
                showGcashModal: false,
                gcashReferenceNo: '',

                // Toast notification
                toast: {
                    show: false,
                    message: '',
                    type: 'info' // success, error, warning, info
                },

                // Computed
                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },

                get taxAmount() {
                    if (!this.selectedTaxRule) return 0;

                    const select = document.querySelector('select[x-model="selectedTaxRule"]');
                    if (!select) return 0;

                    const selectedOption = select.options[select.selectedIndex];
                    const method = selectedOption.getAttribute('data-method');
                    const rate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
                    const fixedAmount = parseFloat(selectedOption.getAttribute('data-fixed')) || 0;

                    if (method === 'percentage') {
                        return (this.subtotal * rate) / 100;
                    }
                    return fixedAmount;
                },

                get discountAmount() {
                    if (!this.selectedDiscountRule) return 0;

                    const select = document.querySelector('select[x-model="selectedDiscountRule"]');
                    if (!select) return 0;

                    const selectedOption = select.options[select.selectedIndex];
                    const method = selectedOption.getAttribute('data-method');
                    const rate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
                    const fixedAmount = parseFloat(selectedOption.getAttribute('data-fixed')) || 0;

                    if (method === 'percentage') {
                        return (this.subtotal * rate) / 100;
                    }
                    return fixedAmount;
                },

                get total() {
                    return this.subtotal + this.taxAmount - this.discountAmount;
                },

                get change() {
                    return this.amountReceived - this.total;
                },

                get isPaymentInsufficient() {
                    // For cash payments, check if amount received is less than total
                    if (this.paymentMethod === 'cash' && this.amountReceived > 0 && this.amountReceived < this.total) {
                        return true;
                    }
                    return false;
                },

                // Methods
                init() {
                    // Extract unique categories from products
                    this.categories = [...new Set(this.products.map(p => p.category))].sort();

                    this.filteredProducts = this.products;
                    
                    // Initialize current date time
                    this.updateDateTime();
                    
                    // Watch for payment amount changes
                    this.$watch('amountReceived', () => this.checkPaymentAmount());
                    this.$watch('total', () => this.checkPaymentAmount());
                    this.$watch('paymentMethod', () => this.checkPaymentAmount());
                    this.$watch('selectedCustomerId', () => this.checkPaymentAmount());
                    this.$watch('newCustomer.first_name', () => this.checkPaymentAmount());
                },

                updateDateTime() {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    this.currentDateTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    
                    // Also directly set the input value to ensure it's updated before form submission
                    const input = document.getElementById('order_date_input');
                    if (input) {
                        input.value = this.currentDateTime;
                    }
                },

                filterProducts() {
                    const search = this.productSearch.toLowerCase();

                    this.filteredProducts = this.products.filter(product => {
                        // Filter by search term
                        const matchesSearch = !search ||
                            product.name.toLowerCase().includes(search) ||
                            product.sku.toLowerCase().includes(search);

                        // Filter by category
                        const matchesCategory = !this.selectedCategory ||
                            product.category === this.selectedCategory;

                        return matchesSearch && matchesCategory;
                    });
                },

                checkPaymentAmount() {
                    // Set payment status to pending if:
                    // 1. No customer selected
                    // 2. Cash payment with no amount received or amount is 0
                    // 3. Cash payment with insufficient amount
                    if (!this.selectedCustomerId && !this.newCustomer.first_name) {
                        this.paymentStatus = 'pending';
                        return;
                    }

                    if (this.paymentMethod === 'cash') {
                        if (!this.amountReceived || this.amountReceived <= 0) {
                            this.paymentStatus = 'pending';
                        } else if (this.amountReceived < this.total) {
                            this.paymentStatus = 'pending';
                        } else if (this.amountReceived >= this.total) {
                            // Only set to paid if amount is sufficient
                            this.paymentStatus = 'paid';
                        }
                    }
                },

                addToCart(id, name, price, stock, image = '') {
                    // Validate if product has a price
                    if (!price || price === 0 || price === '0' || price === null || price === '') {
                        this.showToast('Cannot add product: Price not set for "' + name +
                            '". Please update the product price first.', 'error');
                        return;
                    }

                    const existing = this.cart.find(item => item.id === id);
                    if (existing) {
                        if (existing.quantity < stock) {
                            existing.quantity++;
                        } else {
                            this.showToast('Cannot add more. Stock limit reached.', 'warning');
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
                            this.showToast('Product added to cart', 'success');
                        } else {
                            this.showToast('Product is out of stock', 'error');
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

                setQuantity(index, value) {
                    const item = this.cart[index];
                    const newQuantity = parseInt(value) || 0;

                    if (newQuantity <= 0) {
                        this.removeFromCart(index);
                        return;
                    }

                    if (newQuantity > item.stock) {
                        alert('Cannot exceed available stock of ' + item.stock);
                        item.quantity = item.stock;
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
                },

                validatePayment(event) {
                    // Update order_date to real-time timestamp before submission
                    this.updateDateTime();
                    
                    // Debug: Show the timestamp being sent
                    console.log('=== POS TIMESTAMP DEBUG ===');
                    console.log('Current DateTime:', this.currentDateTime);
                    console.log('JavaScript Date:', new Date().toLocaleString());
                    console.log('==========================');

                    // Validate cart is not empty
                    if (this.cart.length === 0) {
                        event.preventDefault();
                        this.showToast('Cannot create sale: Cart is empty', 'error');
                        return false;
                    }

                    // Validate customer is selected
                    if (!this.selectedCustomerId && !this.newCustomer.first_name) {
                        event.preventDefault();
                        this.showToast('Please select a customer or enter new customer details', 'error');
                        return false;
                    }

                    // Validate all products have valid prices
                    const invalidProducts = this.cart.filter(item => !item.price || item.price <= 0);
                    if (invalidProducts.length > 0) {
                        event.preventDefault();
                        this.showToast('Some products have invalid prices. Please remove them from cart.', 'error');
                        return false;
                    }

                    // Prevent form submission if payment is insufficient but marked as paid
                    if (this.paymentMethod === 'cash' && this.paymentStatus === 'paid' && this.amountReceived > 0 && this
                        .amountReceived < this.total) {
                        event.preventDefault();
                        this.showToast('Cannot complete as PAID with insufficient payment amount. Please enter the full amount or mark as PENDING.', 'error');
                        return false;
                    }

                    // Validate cash payment has amount received
                    if (this.paymentMethod === 'cash' && (!this.amountReceived || this.amountReceived <= 0)) {
                        event.preventDefault();
                        this.showToast('Please enter the amount received for cash payment', 'error');
                        return false;
                    }

                    // Validate bank transfer fields
                    if (this.paymentMethod === 'bank_transfer') {
                        if (!this.bankName || !this.referenceNo || !this.proofFileName) {
                            event.preventDefault();
                            this.showToast('Please fill in all bank transfer details: Bank Name, Reference Number, and upload Proof of Payment.', 'error');
                            return false;
                        }
                    }

                    // Validate GCash reference number
                    if (this.paymentMethod === 'gcash') {
                        if (!this.gcashReferenceNo || this.gcashReferenceNo.length < 10) {
                            event.preventDefault();
                            this.showToast('Please click "Show GCash QR Code" and enter the reference number after payment.', 'error');
                            return false;
                        }
                    }

                    // Log submission for debugging
                    console.log('Submitting POS form at:', this.currentDateTime, {
                        cart: this.cart,
                        customerId: this.selectedCustomerId,
                        newCustomer: this.newCustomer,
                        paymentStatus: this.paymentStatus,
                        paymentMethod: this.paymentMethod,
                        amountReceived: this.amountReceived,
                        bankName: this.bankName,
                        referenceNo: this.referenceNo,
                        gcashReferenceNo: this.gcashReferenceNo,
                        total: this.total,
                        orderDate: this.currentDateTime
                    });

                    return true;
                },

                confirmGcashPayment() {
                    if (!this.gcashReferenceNo || this.gcashReferenceNo.length < 10) {
                        alert('Please enter a valid GCash reference number (at least 10 characters)');
                        return;
                    }

                    // Set payment status to paid for GCash
                    this.paymentStatus = 'paid';

                    // Close the modal
                    this.showGcashModal = false;

                    // Show success message
                    alert('GCash payment confirmed! Reference: ' + this.gcashReferenceNo);
                },

                handleProofUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        // Validate file size (5MB max)
                        if (file.size > 5 * 1024 * 1024) {
                            alert('File size must be less than 5MB');
                            event.target.value = '';
                            this.proofFileName = '';
                            return;
                        }

                        // Validate file type
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                        if (!allowedTypes.includes(file.type)) {
                            alert('Only JPG, PNG, and PDF files are allowed');
                            event.target.value = '';
                            this.proofFileName = '';
                            return;
                        }

                        this.proofFileName = file.name;
                    } else {
                        this.proofFileName = '';
                    }
                },

                getTaxRuleName() {
                    if (!this.selectedTaxRule) return '';
                    const select = document.querySelector('select[x-model="selectedTaxRule"]');
                    if (!select) return '';
                    const selectedOption = select.options[select.selectedIndex];
                    return selectedOption ? selectedOption.text : '';
                },

                getDiscountRuleName() {
                    if (!this.selectedDiscountRule) return '';
                    const select = document.querySelector('select[x-model="selectedDiscountRule"]');
                    if (!select) return '';
                    const selectedOption = select.options[select.selectedIndex];
                    return selectedOption ? selectedOption.text : '';
                },

                showToast(message, type = 'info') {
                    this.toast.message = message;
                    this.toast.type = type;
                    this.toast.show = true;

                    // Auto-hide after 5 seconds
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 5000);
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

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
