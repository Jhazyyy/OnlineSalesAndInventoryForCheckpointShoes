<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Exchange</h2>
                                <p class="text-gray-600 dark:text-gray-400">Add a new product exchange to the system</p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('sales.exchanges.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Exchanges
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Exchange Form -->
            <form method="POST" action="{{ route('sales.exchanges.store') }}" class="space-y-6">
                @csrf

                <!-- Basic Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Exchange Information</h3>
                        
                        <!-- Sales Order Search Section -->
                        <div class="mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border-2 border-blue-200 dark:border-blue-700">
                            <div class="flex items-center mb-4">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Link to Sales Order</h4>
                            </div>
                            <p class="text-sm text-blue-700 dark:text-blue-300 mb-4">Search for the sales order that contains the item to exchange (Optional but recommended)</p>
                            
                            <div class="relative">
                                <input type="text" 
                                       id="salesOrderSearch" 
                                       placeholder="Search by order number, tracking number, or customer name..."
                                       class="w-full px-4 py-3 border-2 border-blue-300 dark:border-blue-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-base"
                                       autocomplete="off">
                                <div id="salesOrderResults" class="hidden absolute z-10 w-full mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-xl border-2 border-blue-200 dark:border-blue-700 max-h-96 overflow-y-auto"></div>
                            </div>
                            
                            <input type="hidden" id="sales_order_id" name="sales_order_id" value="{{ old('sales_order_id', $salesOrder->order_id ?? '') }}">
                            
                            <div id="selectedOrderInfo" class="hidden mt-4 p-4 bg-white dark:bg-gray-700 rounded-lg border-2 border-green-400 dark:border-green-600">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Selected Order:</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white" id="selectedOrderNumber"></p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400" id="selectedOrderCustomer"></p>
                                    </div>
                                    <button type="button" onclick="clearSelectedOrder()" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Customer Selection -->
                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Customer <span class="text-red-500">*</span>
                                </label>
                                <select id="customer_id" name="customer_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select a customer...</option>
                                    @foreach($customers as $customer)
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

                            <!-- Exchange Type -->
                            <div>
                                <label for="exchange_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Exchange Type <span class="text-red-500">*</span>
                                </label>
                                <select id="exchange_type" name="exchange_type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" 
                                                {{ old('exchange_type', 'product_exchange') == $type ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('exchange_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Exchange Date -->
                            <div>
                                <label for="exchange_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Exchange Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="exchange_date" name="exchange_date" 
                                       value="{{ old('exchange_date', now()->toDateString()) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('exchange_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Requested Completion Date -->
                            <div>
                                <label for="requested_completion_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Requested Completion Date
                                </label>
                                <input type="date" id="requested_completion_date" name="requested_completion_date" 
                                       value="{{ old('requested_completion_date') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('requested_completion_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select id="status" name="status" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" 
                                                {{ old('status', 'pending') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Reason and Notes -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <!-- Reason -->
                            <div>
                                <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Exchange Reason
                                </label>
                                <textarea id="reason" name="reason" rows="3" 
                                          placeholder="Enter the reason for this exchange..."
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Public Notes
                                </label>
                                <textarea id="notes" name="notes" rows="3" 
                                          placeholder="Enter public notes for this exchange..."
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Internal Notes -->
                        <div class="mt-6">
                            <label for="internal_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Internal Notes
                            </label>
                            <textarea id="internal_notes" name="internal_notes" rows="3" 
                                      placeholder="Enter internal notes (not visible to customer)..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('internal_notes') }}</textarea>
                            @error('internal_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Original Items (Items being returned) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Original Items (Being Returned)</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Items that the customer is returning</p>
                            </div>
                            <button type="button" id="add-original-item" 
                                    class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Item
                            </button>
                        </div>

                        <div id="original-items-container">
                            <!-- Original item template will be added here by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- New Items (Items being given) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">New Items (Being Given)</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Items that the customer will receive (optional for refund exchanges)</p>
                            </div>
                            <button type="button" id="add-new-item" 
                                    class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Item
                            </button>
                        </div>

                        <div id="new-items-container">
                            <!-- New item template will be added here by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Exchange Summary -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Exchange Summary</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-red-800 dark:text-red-200">Original Total</h4>
                                <p class="text-2xl font-bold text-red-900 dark:text-red-100" id="original-total">₱0.00</p>
                            </div>
                            
                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-green-800 dark:text-green-200">New Total</h4>
                                <p class="text-2xl font-bold text-green-900 dark:text-green-100" id="new-total">₱0.00</p>
                            </div>
                            
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Difference</h4>
                                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100" id="difference-amount">₱0.00</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" id="difference-note">No difference</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('sales.exchanges.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Create Exchange
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for dynamic functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const products = @json($products);
            let originalItemIndex = 0;
            let newItemIndex = 0;

            // Add original item functionality
            document.getElementById('add-original-item').addEventListener('click', function() {
                addOriginalItem();
            });

            // Add new item functionality
            document.getElementById('add-new-item').addEventListener('click', function() {
                addNewItem();
            });

            function addOriginalItem() {
                const container = document.getElementById('original-items-container');
                const itemHtml = `
                    <div class="original-item-row border-b border-gray-200 dark:border-gray-700 pb-6 mb-6 last:border-b-0">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                                <select name="original_items[${originalItemIndex}][product_id]" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white product-select"
                                        onchange="updateOriginalItemPrice(this, ${originalItemIndex})">
                                    <option value="">Select product...</option>
                                    ${products.map(product => 
                                        `<option value="${product.product_id}" data-price="${product.selling_price}">${product.product_name} (${product.quantity})</option>`
                                    ).join('')}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                <input type="number" name="original_items[${originalItemIndex}][quantity]" required min="1"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white quantity-input"
                                       onchange="calculateTotals()">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" name="original_items[${originalItemIndex}][unit_price]" required min="0" step="0.01"
                                           class="mt-1 block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white price-input"
                                           onchange="calculateTotals()">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Condition</label>
                                <select name="original_items[${originalItemIndex}][condition]"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select condition...</option>
                                    <option value="new">New</option>
                                    <option value="good">Good</option>
                                    <option value="fair">Fair</option>
                                    <option value="poor">Poor</option>
                                    <option value="damaged">Damaged</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="button" onclick="removeOriginalItem(this)"
                                        class="w-full inline-flex justify-center items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Remove
                                </button>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                            <textarea name="original_items[${originalItemIndex}][notes]" rows="2"
                                      placeholder="Additional notes about this item..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', itemHtml);
                originalItemIndex++;
            }

            function addNewItem() {
                const container = document.getElementById('new-items-container');
                const itemHtml = `
                    <div class="new-item-row border-b border-gray-200 dark:border-gray-700 pb-6 mb-6 last:border-b-0">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                                <select name="new_items[${newItemIndex}][product_id]" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white product-select"
                                        onchange="updateNewItemPrice(this, ${newItemIndex})">
                                    <option value="">Select product...</option>
                                    ${products.map(product => 
                                        `<option value="${product.product_id}" data-price="${product.selling_price}">${product.product_name} (${product.quantity})</option>`
                                    ).join('')}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                <input type="number" name="new_items[${newItemIndex}][quantity]" required min="1"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white quantity-input"
                                       onchange="calculateTotals()">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" name="new_items[${newItemIndex}][unit_price]" required min="0" step="0.01"
                                           class="mt-1 block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white price-input"
                                           onchange="calculateTotals()">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Condition</label>
                                <select name="new_items[${newItemIndex}][condition]"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="new">New</option>
                                    <option value="refurbished">Refurbished</option>
                                    <option value="open_box">Open Box</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="button" onclick="removeNewItem(this)"
                                        class="w-full inline-flex justify-center items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Remove
                                </button>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                            <textarea name="new_items[${newItemIndex}][notes]" rows="2"
                                      placeholder="Additional notes about this item..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', itemHtml);
                newItemIndex++;
            }

            // Global functions for the dynamically created elements
            window.updateOriginalItemPrice = function(selectElement, index) {
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                if (selectedOption && selectedOption.dataset.price) {
                    const priceInput = selectElement.closest('.original-item-row').querySelector('.price-input');
                    priceInput.value = parseFloat(selectedOption.dataset.price).toFixed(2);
                    calculateTotals();
                }
            };

            window.updateNewItemPrice = function(selectElement, index) {
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                if (selectedOption && selectedOption.dataset.price) {
                    const priceInput = selectElement.closest('.new-item-row').querySelector('.price-input');
                    priceInput.value = parseFloat(selectedOption.dataset.price).toFixed(2);
                    calculateTotals();
                }
            };

            window.removeOriginalItem = function(button) {
                button.closest('.original-item-row').remove();
                calculateTotals();
            };

            window.removeNewItem = function(button) {
                button.closest('.new-item-row').remove();
                calculateTotals();
            };

            window.calculateTotals = function() {
                let originalTotal = 0;
                let newTotal = 0;

                // Calculate original items total
                document.querySelectorAll('.original-item-row').forEach(row => {
                    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                    const price = parseFloat(row.querySelector('.price-input').value) || 0;
                    originalTotal += quantity * price;
                });

                // Calculate new items total
                document.querySelectorAll('.new-item-row').forEach(row => {
                    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                    const price = parseFloat(row.querySelector('.price-input').value) || 0;
                    newTotal += quantity * price;
                });

                const difference = newTotal - originalTotal;

                // Update display
                document.getElementById('original-total').textContent = '₱' + originalTotal.toFixed(2);
                document.getElementById('new-total').textContent = '₱' + newTotal.toFixed(2);
                document.getElementById('difference-amount').textContent = '₱' + Math.abs(difference).toFixed(2);

                const differenceNote = document.getElementById('difference-note');
                if (difference > 0) {
                    differenceNote.textContent = 'Customer owes additional payment';
                    differenceNote.className = 'text-sm text-red-600 dark:text-red-400 mt-1';
                } else if (difference < 0) {
                    differenceNote.textContent = 'Customer receives refund';
                    differenceNote.className = 'text-sm text-green-600 dark:text-green-400 mt-1';
                } else {
                    differenceNote.textContent = 'No difference';
                    differenceNote.className = 'text-sm text-gray-600 dark:text-gray-400 mt-1';
                }
            };

            // Add initial items
            addOriginalItem();
            
            // Sales Order Search Functionality
            const salesOrderSearch = document.getElementById('salesOrderSearch');
            const salesOrderResults = document.getElementById('salesOrderResults');
            const salesOrderIdInput = document.getElementById('sales_order_id');
            const selectedOrderInfo = document.getElementById('selectedOrderInfo');
            const customerSelect = document.getElementById('customer_id');
            let searchTimeout;

            salesOrderSearch.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = this.value.trim();

                if (searchTerm.length < 2) {
                    salesOrderResults.classList.add('hidden');
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('sales.exchanges.search.sales-orders') }}?search=${encodeURIComponent(searchTerm)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length === 0) {
                                salesOrderResults.innerHTML = '<div class="p-4 text-gray-500 dark:text-gray-400">No sales orders found</div>';
                            } else {
                                salesOrderResults.innerHTML = data.map(order => `
                                    <div class="p-4 hover:bg-blue-50 dark:hover:bg-blue-900/30 cursor-pointer border-b dark:border-gray-700 last:border-b-0"
                                         onclick="selectSalesOrder(${order.order_id}, '${order.order_number}', '${order.customer_name}', ${order.customer_id}, ${JSON.stringify(order.items).replace(/"/g, '&quot;')})">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900 dark:text-white">${order.order_number}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">${order.customer_name}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Order Date: ${order.created_at}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-blue-600 dark:text-blue-400">₱${parseFloat(order.total_amount).toFixed(2)}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-500">${order.items.length} item(s)</p>
                                            </div>
                                        </div>
                                        <div class="mt-2 space-y-1">
                                            ${order.items.map(item => `
                                                <div class="text-xs text-gray-600 dark:text-gray-400 flex justify-between">
                                                    <span>• ${item.product_name}</span>
                                                    <span>Qty: ${item.quantity} @ ₱${parseFloat(item.price).toFixed(2)}</span>
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                `).join('');
                            }
                            salesOrderResults.classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error('Error fetching sales orders:', error);
                            salesOrderResults.innerHTML = '<div class="p-4 text-red-500">Error loading sales orders. Please try again.</div>';
                            salesOrderResults.classList.remove('hidden');
                        });
                }, 300);
            });

            // Global function to select a sales order
            window.selectSalesOrder = function(orderId, orderNumber, customerName, customerId, items) {
                salesOrderIdInput.value = orderId;
                salesOrderSearch.value = '';
                salesOrderResults.classList.add('hidden');
                
                // Update selected order display
                document.getElementById('selectedOrderNumber').textContent = orderNumber;
                document.getElementById('selectedOrderCustomer').textContent = customerName;
                selectedOrderInfo.classList.remove('hidden');
                
                // Auto-select customer
                customerSelect.value = customerId;
            };

            // Global function to clear selected order
            window.clearSelectedOrder = function() {
                salesOrderIdInput.value = '';
                selectedOrderInfo.classList.add('hidden');
                salesOrderSearch.value = '';
            };

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!salesOrderSearch.contains(event.target) && !salesOrderResults.contains(event.target)) {
                    salesOrderResults.classList.add('hidden');
                }
            });
            
            // If there's a pre-selected sales order (from URL parameter), show the info
            @if(isset($salesOrder))
                document.getElementById('selectedOrderNumber').textContent = '{{ $salesOrder->order_number ?? $salesOrder->tracking_number ?? "N/A" }}';
                document.getElementById('selectedOrderCustomer').textContent = '{{ $salesOrder->customer->display_name ?? "N/A" }}';
                selectedOrderInfo.classList.remove('hidden');
            @endif
        });
    </script>
</x-app-layout>
