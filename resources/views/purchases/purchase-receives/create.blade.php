<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">New Purchase Receive</h2>
                            <p class="text-gray-600 dark:text-gray-400">Create a new purchase receive record</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('purchases.purchase-receives.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Form -->
            <form action="{{ route('purchases.purchase-receives.store') }}" method="POST" id="receiveForm">
                @csrf

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
                                            Supplier/Vendor Name <span class="text-red-500">*</span>
                                        </label>
                                        <select id="supplier_id" name="supplier_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">Select a vendor</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier['id'] }}"
                                                    {{ old('supplier_id') == $supplier['id'] ? 'selected' : '' }}>
                                                    {{ $supplier['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Auto-filled when PO is
                                            selected</p>
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
                                                    {{ old('purchase_order_id', request('purchase_order_id')) == $order['id'] ? 'selected' : '' }}>
                                                    {{ $order['order_number'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('purchase_order_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Related Delivery (Auto-assigned from PO) -->
                                    <div class="md:col-span-2">
                                        <label for="delivery_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Related Delivery
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <select id="delivery_id" name="delivery_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">Select a delivery...</option>
                                            {{-- Deliveries will be populated via JavaScript based on selected PO --}}
                                        </select>
                                        @error('delivery_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="deliveryHelpText">
                                            All purchase orders require delivery confirmation before receiving items.
                                            The most recent delivered shipment will be auto-assigned.
                                        </p>
                                        <p class="mt-1 text-xs text-orange-600 dark:text-orange-400"
                                            id="deliveryWarning" style="display:none;">
                                            ⚠️ This PO has no delivered shipments yet. Please create and mark a delivery
                                            as delivered first.
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <!-- Purchase Receiver Number (Auto-generated, display only) -->
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Purchase
                                            Receiver#</label>
                                        <input type="text" value="Auto-generated" readonly
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm dark:bg-gray-600 dark:border-gray-600 dark:text-white">
                                    </div>

                                    <!-- Received Date -->
                                    <div>
                                        <label for="receive_date"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Received Date <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" id="receive_date" name="receive_date"
                                            value="{{ old('receive_date', date('Y-m-d')) }}" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        @error('receive_date')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Load Items from PO Button -->
                                <div class="mt-4" id="loadItemsSection" style="display: none;">
                                    <div
                                        class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                <p class="text-sm text-blue-800 dark:text-blue-300">
                                                    <span id="poItemsLoadedMessage" style="display: none;">Items loaded
                                                        from Purchase Order. You can adjust quantities or add more
                                                        items.</span>
                                                    <span id="poItemsNotLoadedMessage">Click the button to load items
                                                        from the selected Purchase Order.</span>
                                                </p>
                                            </div>
                                            <button type="button" onclick="loadPurchaseOrderItems()" id="loadItemsBtn"
                                                class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                    </path>
                                                </svg>
                                                Load Items
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Items and Description
                                    </h3>
                                    {{-- <button type="button" onclick="addItemRow()"
                                        class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Item
                                    </button> --}}
                                </div>

                                <div class="overflow-x-auto -mx-6 sm:mx-0">
                                    <div class="inline-block min-w-full align-middle">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                                            id="itemsTable">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th
                                                        class="px-3 sm:px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-full max-w-xs">
                                                        Items & Description</th>
                                                    <th
                                                        class="px-3 sm:px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                                        Ordered</th>
                                                    <th
                                                        class="px-3 sm:px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                                        Received</th>
                                                    <th
                                                        class="px-3 sm:px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                                        In Transit</th>
                                                    <th
                                                        class="px-3 sm:px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                                        Quantity to Receive</th>
                                                    <th
                                                        class="px-3 sm:px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider whitespace-nowrap">
                                                        Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                                                id="itemsTableBody">
                                                <!-- Items will be added here dynamically -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div id="noItemsMessage" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <p>Select a purchase order to load items or click "Add Item" to manually add items.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Notes (For Internal
                                    Use)</h3>

                                <div>
                                    <label for="receiving_notes"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Receiving
                                        Notes</label>
                                    <textarea id="receiving_notes" name="receiving_notes" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Any notes about the receiving process...">{{ old('receiving_notes') }}</textarea>
                                    @error('receiving_notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
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
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Save as Received
                                    </button>

                                    <button type="button" onclick="document.getElementById('receiveForm').reset()"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                            </path>
                                        </svg>
                                        Reset Form
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Summary (will be populated by JavaScript) -->
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
        // Preload datasets via Blade JSON in a safe, isolated block
        @php
            $products = \App\Models\Product::select(['product_id', 'product_name', 'product_brand', 'price'])
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
        window.availableProducts = @json($products);
        window.deliveriesData = @json($deliveries ?? []);
    </script>

    @verbatim
        <script>
            let itemRowCount = 0;
            // Datasets populated above
            let availableProducts = window.availableProducts || [];
            const deliveriesData = window.deliveriesData || [];

            document.addEventListener('DOMContentLoaded', function() {
                // Purchase order change handler - Auto-load items when PO is selected
                document.getElementById('purchase_order_id').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const loadItemsSection = document.getElementById('loadItemsSection');
                    const selectedPOId = selectedOption.value;

                    if (selectedPOId) {
                        const supplierId = selectedOption.dataset.supplierId;
                        if (supplierId) {
                            document.getElementById('supplier_id').value = supplierId;
                        }

                        // Filter and populate deliveries for this PO
                        updateDeliveriesDropdown(selectedPOId);

                        // Show the load items section
                        loadItemsSection.style.display = 'block';
                        // Automatically load items from the selected purchase order
                        loadPurchaseOrderItems();
                    } else {
                        // Hide the load items section
                        loadItemsSection.style.display = 'none';
                        // Clear deliveries
                        updateDeliveriesDropdown(null);
                        // Clear items if no PO is selected
                        document.getElementById('itemsTableBody').innerHTML = '';
                        itemRowCount = 0;
                        updateNoItemsMessage();
                        updateSummary();
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
                        // Clear items when PO is cleared
                        document.getElementById('itemsTableBody').innerHTML = '';
                        itemRowCount = 0;
                        updateNoItemsMessage();
                        updateSummary();
                        // Hide load items section
                        document.getElementById('loadItemsSection').style.display = 'none';
                    }
                });

                // If a purchase order is preselected (e.g., coming from Purchase Order page),
                // auto-set the supplier and load the PO items into the Goods Receipt form.
                const preselectedPO = document.getElementById('purchase_order_id').value;
                if (preselectedPO) {
                    const selectedOption = document.getElementById('purchase_order_id').options[document.getElementById(
                        'purchase_order_id').selectedIndex];
                    const supplierId = selectedOption ? selectedOption.dataset.supplierId : null;
                    if (supplierId) {
                        document.getElementById('supplier_id').value = supplierId;
                    }
                    // Show load items section
                    document.getElementById('loadItemsSection').style.display = 'block';
                    // Load items from the selected Purchase Order
                    loadPurchaseOrderItems();
                }
            });

            function loadPurchaseOrderItems() {
                const purchaseOrderId = document.getElementById('purchase_order_id').value;
                if (!purchaseOrderId) {
                    alert('Please select a purchase order first.');
                    return;
                }

                // Show loading state
                const loadBtn = document.getElementById('loadItemsBtn');
                const originalBtnText = loadBtn.innerHTML;
                loadBtn.disabled = true;
                loadBtn.innerHTML =
                    '<svg class="animate-spin h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Loading...';

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

                            // Show success message
                            document.getElementById('poItemsLoadedMessage').style.display = 'inline';
                            document.getElementById('poItemsNotLoadedMessage').style.display = 'none';

                            // Show order info if available
                            if (data.order) {
                                console.log('Loaded PO:', data.order.order_number);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error loading purchase order items:', error);
                        alert('Error loading purchase order items. Please try again.');
                    })
                    .finally(() => {
                        // Restore button state
                        loadBtn.disabled = false;
                        loadBtn.innerHTML = originalBtnText;
                    });
            }

            function addItemRowFromPO(poItem) {
                const row = createItemRow(itemRowCount);

                // Populate with PO data
                row.querySelector('select[name$="[product_id]"]').value = poItem.product_id;
                row.querySelector('input[name$="[purchase_order_item_id]"]').value = poItem.item_id;
                row.querySelector('input[name$="[quantity_expected]"]').value = poItem.quantity_pending;
                row.querySelector('input[name$="[quantity_received]"]').value = poItem
                    .quantity_pending; // Default to full quantity
                row.querySelector('input[name$="[unit_price]"]').value = poItem.unit_price;
                row.querySelector('.ordered-qty').textContent = poItem.quantity_ordered;
                row.querySelector('.received-qty').textContent = poItem.quantity_received;
                row.querySelector('.in-transit-qty').textContent = poItem.quantity_pending;

                // Add product details to item notes if available
                const itemNotesInput = row.querySelector('input[name$="[item_notes]"]');
                let detailsText = [];
                if (poItem.product_sku && poItem.product_sku !== 'N/A') {
                    detailsText.push('SKU: ' + poItem.product_sku);
                }
                if (poItem.product_brand && poItem.product_brand !== 'N/A') {
                    detailsText.push('Brand: ' + poItem.product_brand);
                }
                if (poItem.product_category && poItem.product_category !== 'N/A') {
                    detailsText.push('Category: ' + poItem.product_category);
                }
                if (detailsText.length > 0) {
                    itemNotesInput.value = detailsText.join(' | ');
                }

                document.getElementById('itemsTableBody').appendChild(row);

                // Check for price difference after adding the row
                const priceInput = row.querySelector('input[name$="[unit_price]"]');
                if (priceInput) {
                    checkPriceDifference(priceInput, itemRowCount);
                }

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
                    const brandInfo = product.product_brand ? ' - ' + product.product_brand : '';
                    const skuInfo = product.sku ? ' [' + product.sku + ']' : '';
                    productOptions += '<option value="' + product.product_id + '">' +
                        product.product_name + brandInfo + skuInfo + '</option>';
                });

                row.innerHTML =
                    '<td class="px-3 sm:px-4 lg:px-6 py-4">' +
                    '<div class="space-y-2">' +
                    '<select name="items[' + index +
                    '][product_id]" required class="block w-full min-w-[200px] rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">' +
                    productOptions +
                    '</select>' +
                    '<input type="hidden" name="items[' + index + '][purchase_order_item_id]" value="">' +
                    '<input type="hidden" name="items[' + index + '][condition]" value="good">' +
                    '<input type="text" name="items[' + index +
                    '][item_notes]" placeholder="Item description, notes..." class="block w-full min-w-[200px] text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">' +
                    '</div>' +
                    '</td>' +
                    '<td class="px-3 sm:px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">' +
                    '<span class="ordered-qty text-blue-600 dark:text-blue-400 font-medium">-</span>' +
                    '</td>' +
                    '<td class="px-3 sm:px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">' +
                    '<span class="received-qty text-green-600 dark:text-green-400 font-medium">-</span>' +
                    '</td>' +
                    '<td class="px-3 sm:px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-center">' +
                    '<span class="in-transit-qty text-orange-600 dark:text-orange-400 font-medium">-</span>' +
                    '</td>' +
                    '<td class="px-3 sm:px-4 py-4 whitespace-nowrap">' +
                    '<div class="grid grid-cols-2 gap-2 min-w-[180px]">' +
                    '<div>' +
                    '<label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Expected</label>' +
                    '<input type="number" name="items[' + index +
                    '][quantity_expected]" min="0" value="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" onchange="updateSummary()">' +
                    '</div>' +
                    '<div>' +
                    '<label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Received</label>' +
                    '<input type="number" name="items[' + index +
                    '][quantity_received]" min="0" value="0" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" onchange="updateSummary()">' +
                    '</div>' +
                    '</div>' +
                    '<div class="mt-2">' +
                    '<label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Unit Price</label>' +
                    '<input type="number" name="items[' + index +
                    '][unit_price]" min="0" step="0.01" value="0" required class="unit-price-input block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm" placeholder="0.00" onchange="updateSummary(); checkPriceDifference(this, ' +
                    index + ')" data-product-select="items[' + index + '][product_id]">' +
                    '</div>' +
                    '<div class="mt-2 price-update-section" id="priceUpdateSection_' + index + '" style="display:none;">' +
                    '<label class="flex items-center text-xs">' +
                    '<input type="checkbox" name="items[' + index +
                    '][update_product_price]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 mr-2">' +
                    '<span class="text-gray-700 dark:text-gray-300">Update product master price</span>' +
                    '</label>' +
                    '<div class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="priceDifferenceInfo_' + index + '"></div>' +
                    '</div>' +
                    '</td>' +
                    '<td class="px-3 sm:px-4 py-4 whitespace-nowrap text-center">' +
                    '<button type="button" onclick="removeItemRow(this)" class="inline-flex items-center justify-center text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">' +
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>' +
                    '</svg>' +
                    '</button>' +
                    '</td>';

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

                // Show/hide summary card
                const summaryCard = document.getElementById('summaryCard');
                if (totalItems > 0) {
                    summaryCard.style.display = 'block';
                } else {
                    summaryCard.style.display = 'none';
                }
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


            // Update deliveries dropdown based on selected purchase order
            function updateDeliveriesDropdown(purchaseOrderId) {
                const deliverySelect = document.getElementById('delivery_id');
                const deliveryWarning = document.getElementById('deliveryWarning');

                // Clear existing options and show default
                deliverySelect.innerHTML = '<option value="">Select a delivery...</option>';

                // Always show warning by default when no PO selected
                if (deliveryWarning) deliveryWarning.style.display = 'none';

                if (!purchaseOrderId) {
                    return;
                }

                // Filter deliveries for this PO
                const relatedDeliveries = deliveriesData.filter(d => d.purchase_order_id == purchaseOrderId);

                // Always show warning if no deliveries exist or none are delivered
                if (relatedDeliveries.length === 0) {
                    if (deliveryWarning) {
                        deliveryWarning.style.display = 'block';
                        deliveryWarning.innerHTML =
                            '⚠️ This PO has no deliveries. Please create a delivery first before receiving items.';
                    }
                    return;
                }

                // Find delivered deliveries
                const deliveredDeliveries = relatedDeliveries.filter(d => d.status === 'delivered');

                // Show warning if there are deliveries but none are delivered
                if (deliveredDeliveries.length === 0) {
                    if (deliveryWarning) {
                        deliveryWarning.style.display = 'block';
                        deliveryWarning.innerHTML =
                            '⚠️ This PO has no delivered shipments yet. Please mark a delivery as delivered first.';
                    }
                }

                relatedDeliveries.forEach(delivery => {
                    const option = document.createElement('option');
                    option.value = delivery.id;
                    option.textContent =
                        `${delivery.delivery_number} - ${delivery.carrier || 'N/A'}${delivery.tracking_number ? ' (' + delivery.tracking_number + ')' : ''} [${delivery.status}]`;
                    // Disable non-delivered options
                    if (delivery.status !== 'delivered') {
                        option.disabled = true;
                        option.style.color = '#999';
                    }
                    deliverySelect.appendChild(option);
                });

                // Auto-select the most recent delivered delivery
                if (deliveredDeliveries.length > 0) {
                    // Sort by actual_delivery_date or delivery_date, most recent first
                    deliveredDeliveries.sort((a, b) => {
                        const dateA = new Date(a.actual_delivery_date || a.delivery_date);
                        const dateB = new Date(b.actual_delivery_date || b.delivery_date);
                        return dateB - dateA;
                    });

                    deliverySelect.value = deliveredDeliveries[0].id;

                    // Hide warning if we have a delivered delivery
                    if (deliveryWarning) deliveryWarning.style.display = 'none';
                }
            }
        </script>
    @endverbatim
</x-app-layout>
