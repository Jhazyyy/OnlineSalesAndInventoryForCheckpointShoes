<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Reorder Items</h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            Products at or below reorder level
                        </p>
                    </div>

                    <a href="{{ route('reports.index') }}"
                       class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out w-fit">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Reports
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded-md">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded-md">
                    <p class="font-semibold">Please correct the following errors:</p>
                    <ul class="list-disc list-inside mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filter Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.reorder') }}" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Product</label>
                                <input type="text" name="q" placeholder="Name, brand, or category"
                                       value="{{ $filters['q'] ?? '' }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                <input type="text" name="category" placeholder="Optional category filter"
                                       value="{{ $filters['category'] ?? '' }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="flex flex-wrap gap-2 sm:justify-end sm:items-end">
                                <button type="submit"
                                        class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Search
                                </button>
                                <a href="{{ route('reports.reorder') }}"
                                   class="inline-flex items-center justify-center px-3 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-semibold text-xs uppercase rounded-md transition w-auto">
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-2">
                @php
                    $cards = [
                        ['label' => 'Total Products', 'color' => 'from-blue-500 to-blue-600', 'value' => $report['summary']['total_products'] ?? 0, 'icon' => 'M3 12h18M9 18l-6-6 6-6'],
                        ['label' => 'Out of Stock', 'color' => 'from-red-500 to-red-600', 'value' => $report['summary']['out_of_stock'] ?? 0, 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label' => 'Critical', 'color' => 'from-yellow-500 to-yellow-600', 'value' => $report['summary']['critical'] ?? 0, 'icon' => 'M12 9v2m0 4h.01']
                    ];
                @endphp

                @foreach($cards as $card)
                    <div class="bg-gradient-to-br {{ $card['color'] }} text-white rounded-lg shadow-lg p-5 transform hover:scale-[1.03] transition duration-300 ease-in-out">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs sm:text-sm opacity-90">{{ $card['label'] }}</p>
                                <p class="text-2xl sm:text-3xl font-bold mt-1">{{ $card['value'] }}</p>
                            </div>
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Bulk Reorder Action Bar -->
            <div id="bulkActionBar" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4 hidden">
                <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            <span id="selectedCount">0</span> item(s) selected
                        </span>
                        <span id="supplierWarning" class="ml-4 text-xs text-red-600 dark:text-red-400 hidden">
                            ⚠️ All selected items must have the same supplier
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="clearSelection()" 
                                class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded text-sm transition">
                            Clear Selection
                        </button>
                        <button type="button" id="bulkReorderBtn" disabled
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Bulk Reorder Selected Items
                        </button>
                    </div>
                </div>
            </div>

            <!-- Reorder Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">Products Needing Reorder</h3>
                        @if(count($report['products'] ?? []) > 0)
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Select All</span>
                            </label>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm sm:text-base">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase w-12">
                                        <span class="sr-only">Select</span>
                                    </th>
                                    @foreach(['SKU / Product', 'Category', 'Current Qty', 'Suggested', 'Supplier', 'Reorder Action'] as $header)
                                        <th class="px-4 py-3 sm:px-6 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse(($report['products'] ?? []) as $product)
                                    <tr class="hover:bg-blue-50 dark:hover:bg-gray-700 transition" 
                                        data-product-id="{{ $product->product_id }}"
                                        data-supplier-id="{{ $product->preferred_supplier_id ?? '' }}"
                                        data-supplier-name="{{ $product->preferredSupplier->supplier_name ?? 'No Supplier' }}"
                                        data-suggested-qty="{{ $product->suggested_order_qty ?? 1 }}">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" 
                                                   class="product-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                                   data-product-id="{{ $product->product_id }}"
                                                   data-supplier-id="{{ $product->preferred_supplier_id ?? '' }}"
                                                   {{ empty($product->preferred_supplier_id) ? 'disabled title="No supplier assigned"' : '' }}>
                                        </td>
                                        <td class="px-4 py-3 sm:px-6">
                                            <div class="font-bold text-base text-gray-900 dark:text-white">{{ $product->sku }}</div>
                                            <div class="text-sm text-gray-700 dark:text-gray-300">{{ $product->product_name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $product->product_brand }}</div>
                                        </td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">{{ $product->product_category }}</td>
                                        <td class="px-4 py-3 sm:px-6">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $product->quantity <= 0 ? 'bg-red-100 text-red-800 dark:bg-red-200 dark:text-red-900' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-200 dark:text-yellow-900' }}">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 sm:px-6 text-gray-800 dark:text-gray-300">{{ $product->suggested_order_qty ?? 1 }}</td>
                                        <td class="px-4 py-3 sm:px-6">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                                {{ $product->preferredSupplier->supplier_name ?? 'No Supplier' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 sm:px-6">
                                            <form method="POST" action="{{ route('reports.reorder.create') }}" class="flex flex-wrap gap-2 items-center">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                                                <input type="number" 
                                                       name="quantity" 
                                                       min="1" 
                                                       value="{{ max(1, (int)($product->suggested_order_qty ?? 1)) }}"
                                                       class="quantity-input w-20 rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-1 focus:ring-indigo-500"
                                                       data-product-id="{{ $product->product_id }}" />
                                                <select name="supplier_id"
                                                        class="rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-1 focus:ring-indigo-500">
                                                    <option value="">Select supplier</option>
                                                    @foreach($suppliers as $s)
                                                        <option value="{{ $s->supplier_id }}" {{ $product->preferred_supplier_id == $s->supplier_id ? 'selected' : '' }}>
                                                            {{ $s->supplier_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit"
                                                        class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs sm:text-sm transition">
                                                    Reorder
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No products found that need reordering.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bulk Reorder Form (hidden, submitted via JS) -->
            <form id="bulkReorderForm" method="POST" action="{{ route('reports.reorder.bulk') }}" class="hidden">
                @csrf
                <input type="hidden" name="supplier_id" id="bulkSupplierId">
                <div id="bulkProductsContainer"></div>
            </form>

            <script>
                let selectedProducts = new Map();

                // Select All functionality
                document.getElementById('selectAll')?.addEventListener('change', function(e) {
                    const checkboxes = document.querySelectorAll('.product-checkbox:not([disabled])');
                    checkboxes.forEach(cb => {
                        cb.checked = e.target.checked;
                        handleCheckboxChange({target: cb});
                    });
                });

                // Handle individual checkbox changes
                document.querySelectorAll('.product-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', handleCheckboxChange);
                });

                // Update quantity in selected products when input changes
                document.querySelectorAll('.quantity-input').forEach(input => {
                    input.addEventListener('change', function() {
                        const productId = this.dataset.productId;
                        if (selectedProducts.has(productId)) {
                            const data = selectedProducts.get(productId);
                            data.quantity = this.value;
                            selectedProducts.set(productId, data);
                        }
                    });
                });

                function handleCheckboxChange(e) {
                    const checkbox = e.target;
                    const productId = checkbox.dataset.productId;
                    const supplierId = checkbox.dataset.supplierId;
                    const row = checkbox.closest('tr');
                    
                    console.log('Checkbox changed:', {
                        productId,
                        supplierId,
                        checked: checkbox.checked
                    });
                    
                    if (checkbox.checked) {
                        // Get the quantity from the input field
                        const qtyInput = row.querySelector('.quantity-input');
                        const quantity = qtyInput ? qtyInput.value : row.dataset.suggestedQty;
                        
                        selectedProducts.set(productId, {
                            supplierId: supplierId,
                            supplierName: row.dataset.supplierName,
                            suggestedQty: row.dataset.suggestedQty,
                            quantity: quantity
                        });
                        
                        // Highlight selected row
                        row.classList.add('bg-blue-100', 'dark:bg-blue-900');
                        
                        console.log('Product added to selection:', selectedProducts.get(productId));
                    } else {
                        selectedProducts.delete(productId);
                        
                        // Remove highlight from row
                        row.classList.remove('bg-blue-100', 'dark:bg-blue-900');
                        
                        console.log('Product removed from selection');
                    }
                    
                    updateBulkActionBar();
                }

                function updateBulkActionBar() {
                    const count = selectedProducts.size;
                    const bulkActionBar = document.getElementById('bulkActionBar');
                    const selectedCountSpan = document.getElementById('selectedCount');
                    const bulkReorderBtn = document.getElementById('bulkReorderBtn');
                    const supplierWarning = document.getElementById('supplierWarning');
                    
                    console.log('Updating bulk action bar, selected count:', count);
                    
                    selectedCountSpan.textContent = count;
                    
                    if (count === 0) {
                        bulkActionBar.classList.add('hidden');
                        bulkReorderBtn.disabled = true;
                        return;
                    }
                    
                    bulkActionBar.classList.remove('hidden');
                    
                    // Check if all selected products have the same supplier
                    const supplierIds = Array.from(selectedProducts.values()).map(p => p.supplierId);
                    const uniqueSuppliers = [...new Set(supplierIds)];
                    
                    console.log('Supplier IDs:', supplierIds);
                    console.log('Unique suppliers:', uniqueSuppliers);
                    
                    if (uniqueSuppliers.length > 1 || uniqueSuppliers[0] === '') {
                        bulkReorderBtn.disabled = true;
                        supplierWarning.classList.remove('hidden');
                        console.log('Multiple suppliers or no supplier - button disabled');
                    } else {
                        bulkReorderBtn.disabled = false;
                        supplierWarning.classList.add('hidden');
                        console.log('Same supplier for all - button enabled');
                    }
                }

                function clearSelection() {
                    selectedProducts.clear();
                    document.querySelectorAll('.product-checkbox').forEach(cb => {
                        cb.checked = false;
                        // Remove highlight from all rows
                        const row = cb.closest('tr');
                        if (row) {
                            row.classList.remove('bg-blue-100', 'dark:bg-blue-900');
                        }
                    });
                    const selectAllCheckbox = document.getElementById('selectAll');
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = false;
                    }
                    updateBulkActionBar();
                }

                // Bulk reorder button click
                document.getElementById('bulkReorderBtn')?.addEventListener('click', function() {
                    console.log('Bulk reorder button clicked');
                    
                    if (selectedProducts.size === 0) {
                        console.log('No products selected');
                        return;
                    }
                    
                    console.log('Selected products:', selectedProducts);
                    
                    // Get supplier ID (all should be the same)
                    const firstProduct = Array.from(selectedProducts.values())[0];
                    const supplierId = firstProduct.supplierId;
                    const supplierName = firstProduct.supplierName;
                    
                    console.log('Supplier ID:', supplierId);
                    console.log('Supplier Name:', supplierName);
                    
                    if (!supplierId) {
                        alert('Please ensure all selected products have a supplier assigned.');
                        return;
                    }
                    
                    // Confirm action
                    const productCount = selectedProducts.size;
                    if (!confirm(`Create a purchase order for ${productCount} product(s) from ${supplierName}?`)) {
                        console.log('User cancelled');
                        return;
                    }
                    
                    // Update quantities from input fields before submitting
                    selectedProducts.forEach((data, productId) => {
                        const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                        if (row) {
                            const qtyInput = row.querySelector('.quantity-input');
                            if (qtyInput) {
                                data.quantity = qtyInput.value;
                                console.log(`Product ${productId} quantity: ${data.quantity}`);
                            }
                        }
                    });
                    
                    // Populate form
                    document.getElementById('bulkSupplierId').value = supplierId;
                    
                    const container = document.getElementById('bulkProductsContainer');
                    container.innerHTML = '';
                    
                    selectedProducts.forEach((data, productId) => {
                        const productInput = document.createElement('input');
                        productInput.type = 'hidden';
                        productInput.name = 'products[]';
                        productInput.value = productId;
                        container.appendChild(productInput);
                        
                        const qtyInput = document.createElement('input');
                        qtyInput.type = 'hidden';
                        qtyInput.name = `quantities[${productId}]`;
                        qtyInput.value = data.quantity || data.suggestedQty;
                        container.appendChild(qtyInput);
                        
                        console.log(`Added to form - Product: ${productId}, Qty: ${qtyInput.value}`);
                    });
                    
                    console.log('Form data prepared, submitting...');
                    console.log('Form HTML:', document.getElementById('bulkReorderForm').innerHTML);
                    
                    // Submit form
                    document.getElementById('bulkReorderForm').submit();
                });
            </script>

        </div>
    </div>
</x-app-layout>
