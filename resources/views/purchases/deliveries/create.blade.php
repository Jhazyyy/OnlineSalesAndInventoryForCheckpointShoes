<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Delivery</h2>
                    <p class="text-gray-600 dark:text-gray-400">Create delivery from purchase order</p>
                </div>
            </div>

            <form id="deliveryForm" action="{{ route('purchases.deliveries.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Purchase Order -->
                                    <div>
                                        <label for="purchase_order_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Purchase
                                            Order *</label>
                                        <select id="purchase_order_id" name="purchase_order_id" required
                                            data-supplier-id="{{ old('supplier_id') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">Select Purchase Order</option>
                                            @foreach ($purchase_orders as $order)
                                                <option value="{{ $order['id'] }}"
                                                    data-supplier-id="{{ $order['supplier_id'] }}"
                                                    {{ old('purchase_order_id', request('purchase_order_id')) == $order['id'] ? 'selected' : '' }}>
                                                    {{ $order['order_number'] }} - {{ $order['supplier_name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('purchase_order_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Supplier (Auto-filled) -->
                                    <div>
                                        <label for="supplier_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                                        <select id="supplier_id" name="supplier_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white bg-gray-50"
                                            disabled>
                                            <option value="">Auto-filled from PO</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier['id'] }}"
                                                    {{ old('supplier_id') == $supplier['id'] ? 'selected' : '' }}>
                                                    {{ $supplier['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Delivery Date -->
                                    <div>
                                        <label for="delivery_date"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Date
                                            *</label>
                                        <input type="date" id="delivery_date" name="delivery_date"
                                            value="{{ old('delivery_date', now()->format('Y-m-d')) }}" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        @error('delivery_date')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Scheduled Delivery Date -->
                                    <div>
                                        <label for="scheduled_delivery_date"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scheduled
                                            Delivery</label>
                                        <input type="date" id="scheduled_delivery_date" name="scheduled_delivery_date"
                                            value="{{ old('scheduled_delivery_date') }} required"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <label for="status"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <select id="status" name="status"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="scheduled"
                                                {{ old('status', 'scheduled') == 'scheduled' ? 'selected' : '' }}>
                                                Scheduled</option>
                                            <option value="in_transit"
                                                {{ old('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                            <option value="out_for_delivery"
                                                {{ old('status') == 'out_for_delivery' ? 'selected' : '' }}>Out for
                                                Delivery</option>
                                            <option value="delivered"
                                                {{ old('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        </select>
                                    </div>

                                    <!-- Priority -->
                                    <div>
                                        <label for="priority"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                        <select id="priority" name="priority"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low
                                            </option>
                                            <option value="normal"
                                                {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}>Normal
                                            </option>
                                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                                High</option>
                                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>
                                                Urgent</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Information -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Shipping Information
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Carrier -->
                                    <div>
                                        <label for="carrier"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Carrier</label>
                                        <select id="carrier" name="carrier"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">Select Carrier</option>
                                            @foreach ($carriers as $carrier)
                                                <option value="{{ $carrier['id'] }}"
                                                    {{ old('carrier') == $carrier['id'] ? 'selected' : '' }}>
                                                    {{ $carrier['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Tracking Number -->
                                    <div>
                                        <label for="tracking_number"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tracking
                                            Number</label>
                                        <input type="text" id="tracking_number" name="tracking_number"
                                            value="{{ old('tracking_number') }}"
                                            placeholder="Auto-generated if left blank"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Leave blank to auto-generate a tracking number (Format: TRK-YYYYMMDD-XXXX)
                                        </p>
                                    </div>

                                    <!-- Recipient Name -->
                                    <div>
                                        <label for="recipient_name"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recipient
                                            Name</label>
                                        <input type="text" id="recipient_name" name="recipient_name"
                                            value="{{ old('recipient_name') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>

                                    <!-- Recipient Phone -->
                                    <div>
                                        <label for="recipient_phone"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recipient
                                            Phone</label>
                                        <input type="text" id="recipient_phone" name="recipient_phone"
                                            value="{{ old('recipient_phone') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>

                                    <!-- Shipping Cost -->
                                    <div>
                                        <label for="shipping_cost"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipping
                                            Cost</label>
                                        <input type="number" id="shipping_cost" name="shipping_cost" step="0.01" min="0"
                                            value="{{ old('shipping_cost', 0) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="0.00">
                                        @error('shipping_cost')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Delivery Address -->
                                    <div class="md:col-span-2">
                                        <label for="delivery_address"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery
                                            Address</label>
                                        <textarea id="delivery_address" name="delivery_address" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('delivery_address') }}</textarea>
                                    </div>

                                    <!-- Delivery Notes -->
                                    <div class="md:col-span-2">
                                        <label for="delivery_notes"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery
                                            Notes</label>
                                        <textarea id="delivery_notes" name="delivery_notes" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="Any special delivery instructions...">{{ old('delivery_notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Delivery Items</h3>
                                    <div id="loadItemsSection" style="display: none;">
                                        <button type="button" id="loadItemsBtn" onclick="loadPurchaseOrderItems()"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12">
                                                </path>
                                            </svg>
                                            Load Items from PO
                                        </button>
                                        <span id="poItemsLoadedMessage"
                                            class="ml-2 text-sm text-green-600 dark:text-green-400"
                                            style="display: none;">Items loaded</span>
                                    </div>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Product</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Ordered</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Received</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Pending</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Expected</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    To Deliver</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Unit Price</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Line Total</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTableBody"
                                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            <!-- Items will be added here dynamically -->
                                        </tbody>
                                    </table>
                                </div>

                                <div id="noItemsMessage" class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No items added</h3>
                                    <p class="mt-1 text-sm text-gray-500">Select a purchase order to load items.</p>
                                </div>

                                {{-- <div class="mt-4">
                                    <button type="button" onclick="addItemRow()"
                                        class="inline-flex items-center px-3 py-1.5 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Item Manually
                                    </button>
                                </div> --}}
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
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Create Delivery
                                    </button>

                                    <a href="{{ route('purchases.deliveries.index') }}"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                            id="summaryCard" style="display: none;">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Summary</h3>

                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Items:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white"
                                            id="totalItems">0</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Quantity:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white"
                                            id="totalQuantity">0</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Value:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white"
                                            id="totalValue">₱0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemRowCount = 0;
        let availableProducts = [];

        @php
            $products = \App\Models\Product::select(['product_id', 'product_name', 'product_brand', 'price', 'sku'])
                ->get()
                ->map(function ($p) {
                    return [
                        'product_id' => $p->product_id,
                        'product_name' => $p->product_name,
                        'product_brand' => $p->product_brand ?? '',
                        'price' => $p->price,
                        'sku' => $p->sku ?? '',
                    ];
                });
        @endphp

        availableProducts = @json($products);

        document.addEventListener('DOMContentLoaded', function() {
            // Purchase order change handler
            document.getElementById('purchase_order_id').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const loadItemsSection = document.getElementById('loadItemsSection');

                if (selectedOption.value) {
                    const supplierId = selectedOption.dataset.supplierId;
                    if (supplierId) {
                        const supplierSelect = document.getElementById('supplier_id');
                        supplierSelect.value = supplierId;
                    }
                    loadItemsSection.style.display = 'block';
                    // Auto-load items
                    loadPurchaseOrderItems();
                } else {
                    loadItemsSection.style.display = 'none';
                    document.getElementById('itemsTableBody').innerHTML = '';
                    itemRowCount = 0;
                    updateNoItemsMessage();
                    updateSummary();
                }
            });

            // If preselected PO
            const preselectedPO = document.getElementById('purchase_order_id').value;
            if (preselectedPO) {
                const selectedOption = document.getElementById('purchase_order_id').options[document
                    .getElementById('purchase_order_id').selectedIndex];
                const supplierId = selectedOption ? selectedOption.dataset.supplierId : null;
                if (supplierId) {
                    document.getElementById('supplier_id').value = supplierId;
                }
                document.getElementById('loadItemsSection').style.display = 'block';
                loadPurchaseOrderItems();
            }
        });

        function loadPurchaseOrderItems() {
            const purchaseOrderId = document.getElementById('purchase_order_id').value;
            if (!purchaseOrderId) {
                alert('Please select a purchase order first.');
                return;
            }

            const loadBtn = document.getElementById('loadItemsBtn');
            const originalBtnText = loadBtn.innerHTML;
            loadBtn.disabled = true;
            loadBtn.innerHTML =
                '<svg class="animate-spin h-4 w-4 mr-1 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading...';

            fetch(`/purchases/deliveries/purchase-order/${purchaseOrderId}/items`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('itemsTableBody').innerHTML = '';
                        itemRowCount = 0;

                        data.items.forEach(item => {
                            addItemRowFromPO(item);
                        });

                        updateNoItemsMessage();
                        updateSummary();

                        document.getElementById('poItemsLoadedMessage').style.display = 'inline';
                    } else {
                        throw new Error(data.message || 'Failed to load items');
                    }
                })
                .catch(error => {
                    console.error('Error loading purchase order items:', error);
                    alert('Error loading purchase order items: ' + error.message);
                })
                .finally(() => {
                    loadBtn.disabled = false;
                    loadBtn.innerHTML = originalBtnText;
                });
        }

        function addItemRowFromPO(poItem) {
            const row = createItemRow(itemRowCount);

            row.querySelector('select[name$="[product_id]"]').value = poItem.product_id;
            row.querySelector('input[name$="[purchase_order_item_id]"]').value = poItem.item_id;
            row.querySelector('input[name$="[quantity_expected]"]').value = poItem.quantity_remaining;
            row.querySelector('input[name$="[quantity_delivered]"]').value = poItem.quantity_remaining;
            row.querySelector('input[name$="[unit_price]"]').value = poItem.unit_price;
            row.querySelector('.ordered-qty').textContent = poItem.quantity_ordered;
            row.querySelector('.received-qty').textContent = poItem.quantity_received;
            row.querySelector('.pending-qty').textContent = poItem.quantity_remaining;

            // Update line total
            updateLineTotal(row);

            document.getElementById('itemsTableBody').appendChild(row);
            itemRowCount++;
        }

        function addItemRow(productData = null) {
            const row = createItemRow(itemRowCount);

            if (productData) {
                row.querySelector('select[name$="[product_id]"]').value = productData.product_id;
                row.querySelector('input[name$="[quantity_expected]"]').value = productData.quantity_expected || 0;
                row.querySelector('input[name$="[quantity_delivered]"]').value = productData.quantity_delivered || 0;
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
                const brandInfo = product.product_brand ? ' - ' + product.product_brand : '';
                const skuInfo = product.sku ? ' [' + product.sku + ']' : '';
                productOptions += `<option value="${product.product_id}">${product.product_name}${brandInfo}${skuInfo}</option>`;
            });

            row.innerHTML = `
                <td class="px-6 py-4">
                    <select name="items[${index}][product_id]" required onchange="updateLineTotal(this.closest('tr'))"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                        ${productOptions}
                    </select>
                    <input type="hidden" name="items[${index}][purchase_order_item_id]" value="">
                    <input type="hidden" name="items[${index}][condition]" value="good">
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                    <span class="ordered-qty">-</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                    <span class="received-qty">-</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                    <span class="pending-qty">-</span>
                </td>
                <td class="px-6 py-4">
                    <input type="number" name="items[${index}][quantity_expected]" min="0" value="0" required
                        oninput="updateLineTotal(this.closest('tr'))"
                        class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                </td>
                <td class="px-6 py-4">
                    <input type="number" name="items[${index}][quantity_delivered]" min="0" value="0" required
                        oninput="updateLineTotal(this.closest('tr'))"
                        class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                </td>
                <td class="px-6 py-4">
                    <input type="number" name="items[${index}][unit_price]" min="0" step="0.01" value="0" required
                        oninput="updateLineTotal(this.closest('tr'))"
                        class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                    <span class="line-total">₱0.00</span>
                </td>
                <td class="px-6 py-4">
                    <button type="button" onclick="removeItemRow(this)" class="text-red-600 hover:text-red-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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

        function updateLineTotal(row) {
            const quantity = parseFloat(row.querySelector('input[name$="[quantity_delivered]"]').value) || 0;
            const price = parseFloat(row.querySelector('input[name$="[unit_price]"]').value) || 0;
            const total = quantity * price;

            row.querySelector('.line-total').textContent = '₱' + total.toFixed(2);
            updateSummary();
        }

        function updateNoItemsMessage() {
            const tbody = document.getElementById('itemsTableBody');
            const noItemsMessage = document.getElementById('noItemsMessage');

            if (tbody.children.length === 0) {
                noItemsMessage.style.display = 'block';
            } else {
                noItemsMessage.style.display = 'none';
            }
        }

        function updateSummary() {
            const tbody = document.getElementById('itemsTableBody');
            const rows = tbody.getElementsByTagName('tr');

            let totalItems = rows.length;
            let totalQuantity = 0;
            let totalValue = 0;

            for (let row of rows) {
                const quantity = parseFloat(row.querySelector('input[name$="[quantity_delivered]"]').value) || 0;
                const price = parseFloat(row.querySelector('input[name$="[unit_price]"]').value) || 0;

                totalQuantity += quantity;
                totalValue += quantity * price;
            }

            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('totalQuantity').textContent = totalQuantity;
            document.getElementById('totalValue').textContent = '₱' + totalValue.toFixed(2);

            const summaryCard = document.getElementById('summaryCard');
            if (totalItems > 0) {
                summaryCard.style.display = 'block';
            } else {
                summaryCard.style.display = 'none';
            }
        }
    </script>
</x-app-layout>
