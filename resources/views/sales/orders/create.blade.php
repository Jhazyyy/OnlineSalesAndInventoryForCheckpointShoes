<x-app-layout>
    <div class="py-6" 
         x-data="salesOrderCreate()" 
         x-init="window.addEventListener('clear-cart', () => { cart = []; showToast('Cart cleared', 'info'); })">
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
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4" id="productsGrid">
                                @foreach($products as $product)
                                <div class="relative" 
                                     x-data="{ showTooltip: false }"
                                     @mouseenter="showTooltip = true" 
                                     @mouseleave="showTooltip = false">
                                    <div @click="addToCart({{ $product['id'] }}, '{{ addslashes($product['name']) }}', {{ $product['price'] }}, {{ $product['stock'] }}, '{{ $product['image'] ?? '' }}')"
                                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden cursor-pointer hover:shadow-lg transition-shadow product-card">
                                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                            @if(!empty($product['image']))
                                            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="w-full h-full object-cover">
                                            @else
                                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                            <div class="flex items-baseline gap-2">
                                                <p class="text-lg font-bold text-gray-900 dark:text-white">₱{{ number_format($product['price'], 2) }}</p>
                                                @if($product['markup_percentage'])
                                                    <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-900 px-2 py-0.5 rounded">+{{ $product['markup_percentage'] }}%</span>
                                                @endif
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
                                         @click="addToCart({{ $product['id'] }}, '{{ addslashes($product['name']) }}', {{ $product['price'] }}, {{ $product['stock'] }}, '{{ $product['image'] ?? '' }}'); showTooltip = false">
                                        <div class="space-y-2 h-full flex flex-col">
                                            <!-- Product Image -->
                                            @if(!empty($product['image']))
                                            <div class="w-full h-28 bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden flex-shrink-0">
                                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="w-full h-full object-cover">
                                            </div>
                                            @endif
                                            
                                            <!-- Product Name -->
                                            <div class="flex-shrink-0">
                                                <h4 class="font-bold text-sm text-gray-900 dark:text-white mb-0.5 line-clamp-2">
                                                    {{ $product['name'] }}
                                                </h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    SKU: {{ $product['sku'] ?? 'N/A' }}
                                                </p>
                                            </div>
                                            
                                            <!-- Product Details -->
                                            <div class="space-y-1.5 text-xs border-t border-gray-200 dark:border-gray-700 pt-2 flex-1">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Category:</span>
                                                    <span class="font-medium text-gray-900 dark:text-white truncate ml-2">{{ $product['category'] ?? 'N/A' }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Brand:</span>
                                                    <span class="font-medium text-gray-900 dark:text-white truncate ml-2">{{ $product['brand'] ?? 'N/A' }}</span>
                                                </div>
                                                <div class="flex justify-between bg-indigo-50 dark:bg-indigo-900/30 -mx-2 px-2 py-1 rounded">
                                                    <span class="text-gray-600 dark:text-gray-400">Price:</span>
                                                    <span class="font-bold text-base text-indigo-600 dark:text-indigo-400">₱{{ number_format($product['price'], 2) }}</span>
                                                </div>
                                                @if($product['markup_percentage'])
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Markup:</span>
                                                    <span class="font-semibold text-indigo-600 dark:text-indigo-400">+{{ $product['markup_percentage'] }}%</span>
                                                </div>
                                                @endif
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Stock:</span>
                                                    <span class="font-semibold" 
                                                          :class="{ 
                                                              'text-green-600 dark:text-green-400': {{ $product['stock'] }} > 10,
                                                              'text-yellow-600 dark:text-yellow-400': {{ $product['stock'] }} <= 10 && {{ $product['stock'] }} > 0,
                                                              'text-red-600 dark:text-red-400': {{ $product['stock'] }} === 0
                                                          }">
                                                        {{ $product['stock'] }} {{ $product['unit'] ?? 'pcs' }}
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
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Section (Right Side - 1 column) -->
                <div class="lg:col-span-1">
                    <!-- Cart Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <form method="POST" action="{{ route('sales.orders.store') }}" id="orderForm">
                            @csrf
                            
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Cart</h3>
                                    <div class="flex items-center gap-2">
                                        <span x-show="cart.length > 0" 
                                              class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                              x-text="cart.length + ' item' + (cart.length !== 1 ? 's' : '')"></span>
                                        <button type="button" 
                                                @click="confirmClearCart()" 
                                                :disabled="cart.length === 0"
                                                class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 disabled:opacity-50 disabled:cursor-not-allowed">
                                            Clear
                                        </button>
                                    </div>
                                </div>

                                <!-- Cart Items -->
                                <div class="mb-4 space-y-3" id="cartItems">
                                    <p x-show="cart.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-8">Cart is empty</p>
                                    
                                    <template x-for="(item, index) in cart" :key="item.id">
                                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div class="flex-shrink-0 w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-lg overflow-hidden flex items-center justify-center">
                                                <template x-if="item.image">
                                                    <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!item.image">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                    </svg>
                                                </template>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="item.name"></p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Qty: <span x-text="item.quantity"></span> <span x-text="item.stock > 0 ? '(' + item.stock + ' in stock)' : ''"></span>
                                                </p>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <button type="button" @click="updateQuantity(index, -1)" class="w-6 h-6 flex items-center justify-center bg-gray-200 dark:bg-gray-600 rounded hover:bg-gray-300 dark:hover:bg-gray-500">
                                                        <span class="text-gray-700 dark:text-gray-200">-</span>
                                                    </button>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="item.quantity"></span>
                                                    <button type="button" @click="updateQuantity(index, 1)" class="w-6 h-6 flex items-center justify-center bg-blue-100 dark:bg-blue-900 rounded hover:bg-blue-200 dark:hover:bg-blue-800">
                                                        <span class="text-blue-700 dark:text-blue-300">+</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-base font-bold text-gray-900 dark:text-white" x-text="'₱' + (item.price * item.quantity).toFixed(2)"></p>
                                                <button type="button" @click="removeFromCart(index)" class="mt-1 text-red-600 hover:text-red-700 dark:text-red-400">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Cart Summary -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-3">
                                    <!-- Customer Tax Dropdown -->
                                    @if(isset($activeTaxes) && $activeTaxes->isNotEmpty())
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            <span class="flex items-center justify-between">
                                                <span>Tax Rule</span>
                                                <span x-show="selectedTaxRule" class="text-indigo-600 dark:text-indigo-400" x-text="'₱' + tax.toFixed(2)"></span>
                                            </span>
                                        </label>
                                        <select x-model="selectedTaxRule" 
                                            class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- No Tax --</option>
                                            @foreach($activeTaxes as $tax)
                                                <option value="{{ $tax->id }}" 
                                                    data-rate="{{ $tax->rate }}"
                                                    data-method="{{ $tax->calculation_method }}"
                                                    data-fixed="{{ $tax->fixed_amount ?? 0 }}">
                                                    {{ $tax->name }} - 
                                                    @if($tax->calculation_method === 'percentage')
                                                        {{ $tax->rate }}%
                                                    @else
                                                        ₱{{ number_format((float)$tax->fixed_amount, 2) }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif

                                    <!-- Customer Discount Dropdown -->
                                    @if(isset($activeDiscounts) && $activeDiscounts->isNotEmpty())
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            <span class="flex items-center justify-between">
                                                <span>Discount Rule</span>
                                                <span x-show="selectedDiscountRule" class="text-indigo-600 dark:text-indigo-400" x-text="'₱' + discount.toFixed(2)"></span>
                                            </span>
                                        </label>
                                        <select x-model="selectedDiscountRule" 
                                            class="w-full text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">-- No Discount --</option>
                                            @foreach($activeDiscounts as $discount)
                                                <option value="{{ $discount->id }}" 
                                                    data-rate="{{ $discount->rate }}"
                                                    data-method="{{ $discount->calculation_method }}"
                                                    data-fixed="{{ $discount->fixed_amount ?? 0 }}">
                                                    {{ $discount->name }} - 
                                                    @if($discount->calculation_method === 'percentage')
                                                        {{ $discount->rate }}%
                                                    @else
                                                        ₱{{ number_format((float)$discount->fixed_amount, 2) }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif

                                    <!-- Order Summary -->
                                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3 space-y-2 text-sm">
                                        <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                            <span>Subtotal</span>
                                            <span x-text="'₱' + subtotal.toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between text-gray-700 dark:text-gray-300" x-show="tax > 0">
                                            <span>Tax</span>
                                            <span class="text-indigo-600 dark:text-indigo-400" x-text="'₱' + tax.toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between text-gray-700 dark:text-gray-300" x-show="discount > 0">
                                            <span>Discount</span>
                                            <span class="text-green-600 dark:text-green-400" x-text="'-₱' + discount.toFixed(2)"></span>
                                        </div>
                                    </div>

                                    <!-- Total -->
                                    <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
                                            <span class="text-2xl font-bold text-gray-900 dark:text-white" x-text="'₱' + total.toFixed(2)"></span>
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

                                    <!-- Purchase Type -->
                                    <div>
                                        <x-input-label for="purchase_type" :value="__('Purchase Type')" />
                                        <select id="purchase_type" name="purchase_type"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="in_store" {{ old('purchase_type', 'in_store') == 'in_store' ? 'selected' : '' }}>In-Store Purchase</option>
                                            <option value="online" {{ old('purchase_type') == 'online' ? 'selected' : '' }}>Online Purchase</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            <span id="purchase_type_help">In-Store: Customer receives product immediately upon payment</span>
                                        </p>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="flex space-x-2 pt-4">
                                        <button type="submit" @click.prevent="submitOrder" :disabled="cart.length === 0"
                                            class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Create Order
                                        </button>
                                    </div>

                                    <!-- Hidden Fields -->
                                    <input type="hidden" name="order_date" value="{{ date('Y-m-d') }}">
                                    <input type="hidden" name="tax_rule_id" x-model="selectedTaxRule">
                                    <input type="hidden" name="tax_amount" x-model="tax">
                                    <input type="hidden" name="discount_rule_id" x-model="selectedDiscountRule">
                                    <input type="hidden" name="discount_amount" x-model="discount">
                                    <input type="hidden" name="discount_type" x-model="discountType">
                                    <input type="hidden" name="custom_discount" x-model="customDiscount">
                                    
                                    <!-- Cart Items Hidden Fields -->
                                    <template x-for="(item, index) in cart" :key="item.id">
                                        <div>
                                            <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.id">
                                            <input type="hidden" :name="'items[' + index + '][quantity]'" :value="item.quantity">
                                            <input type="hidden" :name="'items[' + index + '][unit_price]'" :value="item.price">
                                            <input type="hidden" :name="'items[' + index + '][discount_amount]'" value="0">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Modal -->
    <div x-show="alert.show" 
         x-cloak
         @click.self="alert.show = false"
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="alert.show" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 aria-hidden="true"></div>

            <!-- Modal panel -->
            <div x-show="alert.show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-middle bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                             :class="{
                                'bg-red-100 dark:bg-red-900': alert.type === 'error',
                                'bg-green-100 dark:bg-green-900': alert.type === 'success',
                                'bg-yellow-100 dark:bg-yellow-900': alert.type === 'warning'
                             }">
                            <template x-if="alert.type === 'error'">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </template>
                            <template x-if="alert.type === 'success'">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </template>
                            <template x-if="alert.type === 'warning'">
                                <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </template>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" x-text="alert.title"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="alert.message"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" 
                            @click="alert.show = false" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Cart Confirmation Modal (Plain JS like purchase receives) -->
    <div id="clearCartModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Confirm Clear Cart
                    </h3>
                    <button onclick="closeClearCartModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="mt-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Are you sure you want to clear all items from the cart? This action cannot be undone.
                    </p>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t dark:border-gray-700">
                    <button type="button" onclick="closeClearCartModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="button" onclick="executeClearCart()"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Clear Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="toast.show" 
         x-cloak
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
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
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                               'text-green-500 hover:text-green-600 focus:ring-green-500': toast.type === 'success',
                               'text-red-500 hover:text-red-600 focus:ring-red-500': toast.type === 'error',
                               'text-yellow-500 hover:text-yellow-600 focus:ring-yellow-500': toast.type === 'warning',
                               'text-blue-500 hover:text-blue-600 focus:ring-blue-500': toast.type === 'info'
                            }">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function salesOrderCreate() {
            return {
                cart: [],
                selectedTaxRule: '{{ $defaultTax ? $defaultTax->id : "" }}',
                customTax: 0,
                selectedDiscountRule: '',
                discountType: 'percentage',
                customDiscount: 0,
                alert: {
                    show: false,
                    type: 'info',
                    title: 'Alert',
                    message: ''
                },
                confirmation: {
                    show: false,
                    title: 'Confirm Action',
                    message: '',
                    callback: () => {}
                },
                toast: {
                    show: false,
                    type: 'info',
                    message: ''
                },
                
                // Initialize watchers for real-time updates
                init() {
                    // Watch subtotal changes to recalculate tax and discount
                    this.$watch('subtotal', () => {
                        if (this.selectedTaxRule) {
                            this.applyCustomerTax();
                        }
                        if (this.selectedDiscountRule) {
                            this.applyCustomerDiscount();
                        }
                    });
                    
                    // Watch tax rule changes
                    this.$watch('selectedTaxRule', () => {
                        this.applyCustomerTax();
                    });
                    
                    // Watch discount rule changes
                    this.$watch('selectedDiscountRule', () => {
                        this.applyCustomerDiscount();
                    });
                },
                
                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },
                
                get tax() {
                    return this.customTax || 0;
                },
                
                get discount() {
                    if (this.discountType === 'percentage') {
                        return this.subtotal * (this.customDiscount / 100);
                    }
                    return this.customDiscount;
                },
                
                applyCustomerTax() {
                    if (!this.selectedTaxRule) {
                        this.customTax = 0;
                        return;
                    }
                    
                    const select = document.querySelector('select[x-model="selectedTaxRule"]');
                    const selectedOption = select.options[select.selectedIndex];
                    const method = selectedOption.getAttribute('data-method');
                    const rate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
                    const fixedAmount = parseFloat(selectedOption.getAttribute('data-fixed')) || 0;
                    
                    if (method === 'percentage') {
                        this.customTax = (this.subtotal * rate) / 100;
                    } else {
                        this.customTax = fixedAmount;
                    }
                },
                
                get total() {
                    return this.subtotal + this.tax - this.discount;
                },
                
                applyCustomerDiscount() {
                    if (!this.selectedDiscountRule) {
                        this.discountType = 'percentage';
                        this.customDiscount = 0;
                        return;
                    }
                    
                    const select = document.querySelector('select[x-model="selectedDiscountRule"]');
                    const selectedOption = select.options[select.selectedIndex];
                    const method = selectedOption.getAttribute('data-method');
                    const rate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
                    const fixedAmount = parseFloat(selectedOption.getAttribute('data-fixed')) || 0;
                    
                    if (method === 'percentage') {
                        this.discountType = 'percentage';
                        this.customDiscount = rate;
                    } else {
                        this.discountType = 'fixed';
                        this.customDiscount = fixedAmount;
                    }
                },
                
                showAlert(message, type = 'info', title = null) {
                    this.alert.message = message;
                    this.alert.type = type;
                    this.alert.title = title || (type === 'error' ? 'Error' : type === 'success' ? 'Success' : type === 'warning' ? 'Warning' : 'Information');
                    this.alert.show = true;
                },
                
                showToast(message, type = 'info') {
                    this.toast.message = message;
                    this.toast.type = type;
                    this.toast.show = true;
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 3000);
                },
                
                showConfirmation(message, callback, title = 'Confirm Action') {
                    this.confirmation.message = message;
                    this.confirmation.title = title;
                    this.confirmation.callback = callback;
                    this.confirmation.show = true;
                },
                
                addToCart(id, name, price, stock, image = '') {
                    const existingItem = this.cart.find(item => item.id === id);
                    
                    if (existingItem) {
                        if (existingItem.quantity < stock) {
                            existingItem.quantity++;
                            this.showToast(`${name} quantity updated to ${existingItem.quantity}`, 'success');
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
                            this.showToast(`${name} added to cart`, 'success');
                        } else {
                            this.showAlert('Product is out of stock', 'error');
                        }
                    }
                },
                
                removeFromCart(index) {
                    this.cart.splice(index, 1);
                    this.showToast('Item removed from cart', 'info');
                },
                
                updateQuantity(index, change) {
                    const item = this.cart[index];
                    const newQuantity = item.quantity + change;

                    if (newQuantity <= 0) {
                        this.removeFromCart(index);
                        return;
                    }

                    if (newQuantity > item.stock) {
                        this.showToast('Cannot exceed available stock quantity', 'warning');
                        return;
                    }

                    item.quantity = newQuantity;
                },
                
                clearCart() {
                    this.cart = [];
                    this.showToast('Cart cleared', 'info');
                },
                
                confirmClearCart() {
                    if (this.cart.length === 0) {
                        this.showAlert('Cart is already empty', 'warning');
                        return;
                    }
                    
                    // Show modal using plain JavaScript like in purchase receives
                    const modal = document.getElementById('clearCartModal');
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                },
                
                closeClearCartModal() {
                    const modal = document.getElementById('clearCartModal');
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                },
                
                executeClearCart() {
                    this.cart = [];
                    this.closeClearCartModal();
                    this.showToast('Cart cleared', 'info');
                },
                
                submitOrder() {
                    if (this.cart.length === 0) {
                        this.showAlert('Please add items to cart before submitting', 'warning');
                        return;
                    }
                    
                    // Submit the form
                    document.getElementById('orderForm').submit();
                }
            }
        }

        // Global functions for Clear Cart Modal (outside Alpine.js scope)
        function closeClearCartModal() {
            const modal = document.getElementById('clearCartModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function executeClearCart() {
            // Dispatch custom event that Alpine.js will handle
            window.dispatchEvent(new CustomEvent('clear-cart'));
            closeClearCartModal();
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('clearCartModal');
                if (!modal.classList.contains('hidden')) {
                    closeClearCartModal();
                }
            }
        });

        // Handle Purchase Type and Payment Status interactions
        document.addEventListener('DOMContentLoaded', function() {
            const paymentStatusSelect = document.getElementById('payment_status');
            const purchaseTypeSelect = document.getElementById('purchase_type');
            const purchaseTypeHelp = document.getElementById('purchase_type_help');

            function updatePurchaseTypeHelp() {
                const purchaseType = purchaseTypeSelect.value;
                const paymentStatus = paymentStatusSelect.value;

                if (purchaseType === 'in_store') {
                    if (paymentStatus === 'paid') {
                        purchaseTypeHelp.textContent = '✓ In-Store + Paid: Order will be marked as delivered (customer receives product immediately)';
                        purchaseTypeHelp.classList.remove('text-gray-500');
                        purchaseTypeHelp.classList.add('text-green-600', 'dark:text-green-400', 'font-medium');
                    } else {
                        purchaseTypeHelp.textContent = 'In-Store: Customer receives product immediately upon payment';
                        purchaseTypeHelp.classList.remove('text-green-600', 'dark:text-green-400', 'font-medium');
                        purchaseTypeHelp.classList.add('text-gray-500', 'dark:text-gray-400');
                    }
                } else {
                    purchaseTypeHelp.textContent = 'Online: Order will be processed and shipped to customer';
                    purchaseTypeHelp.classList.remove('text-green-600', 'dark:text-green-400', 'font-medium');
                    purchaseTypeHelp.classList.add('text-gray-500', 'dark:text-gray-400');
                }
            }

            // Update help text when either field changes
            paymentStatusSelect.addEventListener('change', updatePurchaseTypeHelp);
            purchaseTypeSelect.addEventListener('change', updatePurchaseTypeHelp);

            // Initial update
            updatePurchaseTypeHelp();
        });
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