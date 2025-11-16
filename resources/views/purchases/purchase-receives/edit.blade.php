<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Purchase Receive:
                                {{ $receive->receive_number }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">Update purchase receive details</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('purchases.purchase-receives.show', $receive->receive_id) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Back to Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form -->
            <form action="{{ route('purchases.purchase-receives.update', $receive->receive_id) }}" method="POST"
                id="receiveForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Form Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Vendor Name -->
                                    <div>
                                        <label for="supplier_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Vendor Name <span class="text-red-500">*</span>
                                        </label>
                                        <select id="supplier_id" name="supplier_id" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">Select a vendor</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier['id'] }}"
                                                    {{ old('supplier_id', $receive->supplier_id) == $supplier['id'] ? 'selected' : '' }}>
                                                    {{ $supplier['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Purchase Order -->
                                    <div>
                                        <label for="purchase_order_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Purchase Order# <span class="text-red-500">*</span>
                                        </label>
                                        <select id="purchase_order_id" name="purchase_order_id" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">Select a Purchase Order</option>
                                            @foreach ($purchase_orders as $order)
                                                <option value="{{ $order['id'] }}"
                                                    data-supplier-id="{{ $order['supplier_id'] ?? '' }}"
                                                    {{ old('purchase_order_id', $receive->purchase_order_id) == $order['id'] ? 'selected' : '' }}>
                                                    {{ $order['order_number'] }} - {{ $order['supplier_name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('purchase_order_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <!-- Purchase Receiver Number -->
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Purchase
                                            Receiver#</label>
                                        <input type="text" value="{{ $receive->receive_number }}" readonly
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm dark:bg-gray-600 dark:border-gray-600 dark:text-white">
                                    </div>

                                    <!-- Received Date -->
                                    <div>
                                        <label for="receive_date"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Received Date <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" id="receive_date" name="receive_date"
                                            value="{{ old('receive_date', $receive->receive_date->format('Y-m-d')) }}"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        @error('receive_date')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <!-- Status Display -->
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current
                                            Status</label>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $receive->status_badge_class }}">
                                            {{ ucwords(str_replace('_', ' ', $receive->status)) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <!-- Select or Scan Items Notice -->
                                    <div class="bg-orange-50 border border-orange-200 rounded-md p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-orange-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-orange-700">
                                                    You can reload items from the purchase order or manually adjust
                                                    quantities.
                                                    <button type="button" onclick="loadPurchaseOrderItems()"
                                                        class="text-blue-600 hover:text-blue-800 underline ml-1">
                                                        Reload PO items
                                                    </button>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Items & Description
                                    </h3>
                                    <button type="button" onclick="addItemRow()"
                                        class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Item
                                    </button>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                                        id="itemsTable">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Items & Description</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Ordered</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Received</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    In Transit</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Quantity to Receive</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                                            id="itemsTableBody">
                                            <!-- Items will be populated here -->
                                        </tbody>
                                    </table>
                                </div>

                                <div id="noItemsMessage" class="text-center py-8 text-gray-500 dark:text-gray-400"
                                    style="display: none;">
                                    <p>No items found. Click "Add Item" to add items manually.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Notes (For Internal
                                    Use)</h3>

                                <div class="space-y-4">
                                    <div>
                                        <label for="receiving_notes"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Receiving
                                            Notes</label>
                                        <textarea id="receiving_notes" name="receiving_notes" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="Any notes about the receiving process...">{{ old('receiving_notes', $receive->receiving_notes) }}</textarea>
                                        @error('receiving_notes')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="damage_notes"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Damage
                                            Notes</label>
                                        <textarea id="damage_notes" name="damage_notes" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="Any notes about damaged items...">{{ old('damage_notes', $receive->damage_notes) }}</textarea>
                                        @error('damage_notes')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="receiver_name"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Receiver
                                            Name</label>
                                        <input type="text" id="receiver_name" name="receiver_name"
                                            value="{{ old('receiver_name', $receive->receiver_name) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="Name of person who received the items">
                                        @error('receiver_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Actions -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Actions</h3>

                                <div class="space-y-3">
                                    @if ($receive->canBeEdited())
                                        <button type="submit"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Update Receive
                                        </button>
                                    @else
                                        <div class="text-center py-4">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">This receive cannot be
                                                edited due to its current status.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                            id="summaryCard">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Summary</h3>

                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Items:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white"
                                            id="totalItems">{{ $receive->items->count() }}</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Quantity:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white"
                                            id="totalQuantity">{{ number_format($receive->total_quantity_received) }}</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Value:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white"
                                            id="totalValue">₱{{ number_format($receive->total_amount_received, 2) }}</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Completion:</span>
                                        <span
                                            class="text-sm font-medium text-gray-900 dark:text-white">{{ $receive->completion_percentage }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @php
        $availableProductsList = \App\Models\Product::select('product_id', 'product_name', 'product_brand', 'price')->get();
    @endphp

    <script>
        // Preload datasets via Blade JSON in a safe, isolated block (avoid verbatim here)
        window.availableProducts = @json($availableProductsList);
        window.existingItems = @json($receive->items->load('product')->toArray());
    </script>

    @verbatim
        <script>
            let itemRowCount = 0;
            let availableProducts = window.availableProducts || [];
            let existingItems = window.existingItems || [];

            document.addEventListener('DOMContentLoaded', function() {
                // Load existing items
                existingItems.forEach((item, index) => {
                    addItemRowFromData(item, index);
                });
                itemRowCount = existingItems.length;
                updateNoItemsMessage();
                updateSummary();

                // Purchase order change handler
                document.getElementById('purchase_order_id').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.value) {
                        const supplierId = selectedOption.dataset.supplierId;
                        if (supplierId) {
                            document.getElementById('supplier_id').value = supplierId;
                        }
                    }
                });

                // Supplier change handler (filter purchase orders)
                document.getElementById('supplier_id').addEventListener('change', function() {
                    const selectedSupplierId = this.value;
                    const purchaseOrderSelect = document.getElementById('purchase_order_id');

                    // Show/hide options based on supplier
                    Array.from(purchaseOrderSelect.options).forEach(option => {
                        if (option.value === '') {
                            option.style.display = 'block';
                        } else {
                            const optionSupplierId = option.dataset.supplierId;
                            option.style.display = (selectedSupplierId === '' || optionSupplierId ===
                                selectedSupplierId) ? 'block' : 'none';
                        }
                    });

                    // Reset purchase order selection if current selection doesn't match supplier
                    const currentOption = purchaseOrderSelect.options[purchaseOrderSelect.selectedIndex];
                    if (currentOption.value !== '' && currentOption.dataset.supplierId !== selectedSupplierId) {
                        purchaseOrderSelect.value = '';
                    }
                });
            });

            function addItemRowFromData(item, index) {
                const row = createItemRow(index);

                // Populate with existing data
                // Note: The querySelector syntax with nested brackets like [name$="[product_id]"] is correct CSS selector syntax
                // Some IDEs may show false warnings about "unclosed brackets" - these can be safely ignored
                row.querySelector('select[name$="[product_id]"]').value = item.product_id;
                row.querySelector('input[name$="[purchase_order_item_id]"]').value = item.purchase_order_item_id || '';
                row.querySelector('input[name$="[quantity_expected]"]').value = item.quantity_expected;
                row.querySelector('input[name$="[quantity_received]"]').value = item.quantity_received;
                row.querySelector('input[name$="[quantity_damaged]"]').value = item.quantity_damaged || 0;
                row.querySelector('input[name$="[unit_price]"]').value = item.unit_price;
                row.querySelector('select[name$="[condition]"]').value = item.condition || 'good';
                row.querySelector('textarea[name$="[item_notes]"]').value = item.item_notes || '';

                // Set display values if available
                if (item.purchase_order_item && item.purchase_order_item.quantity_ordered) {
                    row.querySelector('.ordered-qty').textContent = item.purchase_order_item.quantity_ordered;
                    row.querySelector('.received-qty').textContent = item.purchase_order_item.quantity_received || 0;
                    row.querySelector('.in-transit-qty').textContent =
                        (item.purchase_order_item.quantity_ordered - (item.purchase_order_item.quantity_received || 0));
                }

                document.getElementById('itemsTableBody').appendChild(row);
            }

            function loadPurchaseOrderItems() {
                const purchaseOrderId = document.getElementById('purchase_order_id').value;
                if (!purchaseOrderId) {
                    alert('Please select a purchase order first.');
                    return;
                }

                if (confirm('This will replace all current items with items from the selected purchase order. Continue?')) {
                    fetch(`/purchases/purchase-receives/purchase-order/${purchaseOrderId}/items`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Clear existing items
                                document.getElementById('itemsTableBody').innerHTML = '';
                                itemRowCount = 0;

                                // Add each item from the purchase order
                                data.items.forEach(item => {
                                    addItemRowFromPO(item);
                                });

                                updateNoItemsMessage();
                                updateSummary();
                            }
                        })
                        .catch(error => {
                            console.error('Error loading purchase order items:', error);
                            alert('Error loading purchase order items. Please try again.');
                        });
                }
            }

            function addItemRowFromPO(poItem) {
                const row = createItemRow(itemRowCount);

                // Populate with PO data
                row.querySelector('select[name$="[product_id]"]').value = poItem.product_id;
                row.querySelector('input[name$="[purchase_order_item_id]"]').value = poItem.item_id;
                row.querySelector('input[name$="[quantity_expected]"]').value = poItem.quantity_pending;
                row.querySelector('input[name$="[quantity_received]"]').value = poItem.quantity_pending; // Default to full quantity
                row.querySelector('input[name$="[unit_price]"]').value = poItem.unit_price;
                row.querySelector('.ordered-qty').textContent = poItem.quantity_ordered;
                row.querySelector('.received-qty').textContent = poItem.quantity_received;
                row.querySelector('.in-transit-qty').textContent = poItem.quantity_pending;

                document.getElementById('itemsTableBody').appendChild(row);
                itemRowCount++;
            }

            function addItemRow(productData = null) {
                const row = createItemRow(itemRowCount);

                if (productData) {
                    // Populate with provided data
                    row.querySelector('select[name$="[product_id]"]').value = productData.product_id;
                    row.querySelector('input[name$="[quantity_expected]"]').value = productData.quantity_expected || 0;
                    row.querySelector('input[name$="[quantity_received]"]').value = productData.quantity_received || 0;
                    row.querySelector('input[name$="[unit_price]"]').value = productData.unit_price || 0;
                }

                document.getElementById('itemsTableBody').appendChild(row);
                itemRowCount++;
                updateNoItemsMessage();
            }

            function createItemRow(index) {
                const row = document.createElement('tr');

                let productOptions = '<option value="">Select Product</option>';
                availableProducts.forEach(product => {
                    productOptions +=
                        `<option value="${product.product_id}">${product.product_name} - ${product.product_brand}</option>`;
                });

                row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="space-y-2">
                    <select name="items[${index}][product_id]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        ${productOptions}
                    </select>
                    <input type="hidden" name="items[${index}][purchase_order_item_id]" value="">
                    <select name="items[${index}][condition]" class="block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="good">Good</option>
                        <option value="damaged">Damaged</option>
                        <option value="expired">Expired</option>
                        <option value="partial">Partial</option>
                        <option value="shortage">Shortage</option>
                        <option value="excess">Excess</option>
                    </select>
                    <textarea name="items[${index}][item_notes]" rows="1" placeholder="Item notes..." class="block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                <span class="ordered-qty">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                <span class="received-qty">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                <span class="in-transit-qty">-</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <input type="number" name="items[${index}][quantity_expected]" min="0" value="0" class="block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Expected" onchange="updateSummary()">
                    <input type="number" name="items[${index}][quantity_received]" min="0" value="0" required class="block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Received" onchange="updateSummary()">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" name="items[${index}][quantity_damaged]" min="0" value="0" class="block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Damaged" onchange="updateSummary()">
                    <input type="number" name="items[${index}][unit_price]" min="0" step="0.01" value="0" required class="unit-price-input block w-full text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Unit Price" onchange="updateSummary(); checkPriceDifference(this, ${index})" data-product-select="items[${index}][product_id]">
                </div>
                <div class="mt-2 price-update-section" id="priceUpdateSection_${index}" style="display:none;">
                    <label class="flex items-center text-xs">
                        <input type="checkbox" name="items[${index}][update_product_price]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 mr-2">
                        <span class="text-gray-700 dark:text-gray-300">Update product master price</span>
                    </label>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="priceDifferenceInfo_${index}"></div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <button type="button" onclick="removeItemRow(this)" class="text-red-600 hover:text-red-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        `;
                return row;
            }

            function removeItemRow(button) {
                button.closest('tr').remove();
                updateNoItemsMessage();
                updateSummary();
            }

            function updateNoItemsMessage() {
                const tbody = document.getElementById('itemsTableBody');
                const message = document.getElementById('noItemsMessage');

                if (tbody.children.length === 0) {
                    message.style.display = 'block';
                } else {
                    message.style.display = 'none';
                }
            }

            function updateSummary() {
                const rows = document.querySelectorAll('#itemsTableBody tr');
                let totalItems = rows.length;
                let totalQuantity = 0;
                let totalValue = 0;

                rows.forEach(row => {
                    const qtyReceived = parseInt(row.querySelector('input[name$="[quantity_received]"]').value) || 0;
                    const unitPrice = parseFloat(row.querySelector('input[name$="[unit_price]"]').value) || 0;

                    totalQuantity += qtyReceived;
                    totalValue += (qtyReceived * unitPrice);
                });

                document.getElementById('totalItems').textContent = totalItems;
                document.getElementById('totalQuantity').textContent = totalQuantity.toLocaleString();
                document.getElementById('totalValue').textContent = '₱' + totalValue.toFixed(2);
            }

            // Check if unit price differs from product master price
            function checkPriceDifference(priceInput, rowIndex) {
                const row = priceInput.closest('tr');
                const productSelect = row.querySelector('select[name$="[product_id]"]');
                const priceUpdateSection = document.getElementById('priceUpdateSection_' + rowIndex);
                const priceDifferenceInfo = document.getElementById('priceDifferenceInfo_' + rowIndex);
                
                if (!productSelect || !productSelect.value || !priceInput.value) {
                    if (priceUpdateSection) priceUpdateSection.style.display = 'none';
                    return;
                }

                const productId = productSelect.value;
                const unitPrice = parseFloat(priceInput.value);
                
                // Find the product in availableProducts
                const product = availableProducts.find(p => p.product_id == productId);
                
                if (!product) {
                    if (priceUpdateSection) priceUpdateSection.style.display = 'none';
                    return;
                }

                const currentPrice = parseFloat(product.price);
                const lastPurchasePrice = product.last_purchase_price ? parseFloat(product.last_purchase_price) : null;
                
                // Check if there's a price difference (more than 1 cent tolerance)
                const hasDifference = Math.abs(unitPrice - currentPrice) > 0.01;
                
                if (hasDifference && priceUpdateSection) {
                    const difference = unitPrice - currentPrice;
                    const percentChange = ((difference / currentPrice) * 100).toFixed(2);
                    const direction = difference > 0 ? 'higher' : 'lower';
                    const directionClass = difference > 0 ? 'text-green-600' : 'text-red-600';
                    
                    let infoText = `Current: ₱${currentPrice.toFixed(2)} → New: ₱${unitPrice.toFixed(2)}`;
                    infoText += ` <span class="${directionClass}">(${Math.abs(percentChange)}% ${direction})</span>`;
                    
                    if (lastPurchasePrice && Math.abs(unitPrice - lastPurchasePrice) > 0.01) {
                        infoText += `<br>Last purchase: ₱${lastPurchasePrice.toFixed(2)}`;
                    }
                    
                    priceDifferenceInfo.innerHTML = infoText;
                    priceUpdateSection.style.display = 'block';
                } else {
                    if (priceUpdateSection) priceUpdateSection.style.display = 'none';
                }
            }

            // Add event listener to product select to also check price when product changes
            document.addEventListener('change', function(e) {
                if (e.target.matches('select[name$="[product_id]"]')) {
                    const row = e.target.closest('tr');
                    const priceInput = row.querySelector('input[name$="[unit_price]"]');
                    const rowIndex = Array.from(row.parentNode.children).indexOf(row);
                    
                    // Auto-fill price from product data if available
                    const productId = e.target.value;
                    if (productId) {
                        const product = availableProducts.find(p => p.product_id == productId);
                        if (product && priceInput) {
                            priceInput.value = parseFloat(product.price).toFixed(2);
                            checkPriceDifference(priceInput, rowIndex);
                            updateSummary();
                        }
                    }
                }
            });
        </script>
    @endverbatim
</x-app-layout>
