<x-app-layout>

<div class="py-6">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Transfer Stock Between Products</h2>
                    <p class="text-gray-600 dark:text-gray-400">Transfer</p>
                </div>
                <div class="flex space-x-3 mt-4 sm:mt-0">
                <a href="{{ route('inventory.product_stock_adjustment.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Stock Management
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Transfer Stock Between Products</h3>
                        
                        <a href={{route('inventory.product_stock_adjustment.index')}}>
                            <button class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                                Back to Stock Management
                            </button>
                        </a>
                    </div> --}}

                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative dark:bg-red-900 dark:border-red-700 dark:text-red-100">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('inventory.product_stock_adjustment.transfer.process') }}">
                        @csrf

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Transfer From -->
                            <div class="bg-red-50 dark:bg-red-900/20 p-6 rounded-lg border-2 border-red-200 dark:border-red-800">
                                <h4 class="text-lg font-semibold mb-4 text-red-800 dark:text-red-200 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                    Transfer From
                                </h4>

                                <div class="space-y-4">
                                    <!-- Source Product -->
                                    <div>
                                        <x-input-label for="product_id_from" :value="__('Source Product')" />
                                        <select id="product_id_from" name="product_id_from" 
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                                required>
                                            <option value="">Select product to transfer from...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->product_id }}" 
                                                        data-quantity="{{ $product->quantity }}"
                                                        {{ old('product_id_from') == $product->product_id ? 'selected' : '' }}>
                                                    {{ $product->product_name }} - {{ $product->product_brand }} ({{ number_format($product->quantity) }} available)
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('product_id_from')" />
                                    </div>

                                    <!-- Available Quantity Display -->
                                    <div id="available-quantity" class="bg-red-100 dark:bg-red-900 p-3 rounded-md hidden">
                                        <span class="text-sm font-medium text-red-800 dark:text-red-200">Available: </span>
                                        <span id="available-qty" class="text-sm font-bold text-red-900 dark:text-red-100">0 units</span>
                                    </div>

                                    <!-- Location From -->
                                    <div>
                                        <x-input-label for="location_from" :value="__('Location From (Optional)')" />
                                        <x-text-input id="location_from" name="location_from" type="text" class="mt-1 block w-full" 
                                                     :value="old('location_from')" maxlength="255" 
                                                     placeholder="e.g., Warehouse A, Shelf 1" />
                                        <x-input-error class="mt-2" :messages="$errors->get('location_from')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Transfer To -->
                            <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg border-2 border-green-200 dark:border-green-800">
                                <h4 class="text-lg font-semibold mb-4 text-green-800 dark:text-green-200 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Transfer To
                                </h4>

                                <div class="space-y-4">
                                    <!-- Destination Product -->
                                    <div>
                                        <x-input-label for="product_id_to" :value="__('Destination Product')" />
                                        <select id="product_id_to" name="product_id_to" 
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                                required>
                                            <option value="">Select destination product...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->product_id }}"
                                                        {{ old('product_id_to') == $product->product_id ? 'selected' : '' }}>
                                                    {{ $product->product_name }} - {{ $product->product_brand }} ({{ number_format($product->quantity) }} current)
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('product_id_to')" />
                                    </div>

                                    <!-- Location To -->
                                    <div>
                                        <x-input-label for="location_to" :value="__('Location To (Optional)')" />
                                        <x-text-input id="location_to" name="location_to" type="text" class="mt-1 block w-full" 
                                                     :value="old('location_to')" maxlength="255" 
                                                     placeholder="e.g., Warehouse B, Shelf 2" />
                                        <x-input-error class="mt-2" :messages="$errors->get('location_to')" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transfer Details -->
                        <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg border border-blue-200 dark:border-blue-800">
                            <h4 class="text-lg font-semibold mb-4 text-blue-800 dark:text-blue-200">Transfer Details</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Quantity to Transfer -->
                                <div>
                                    <x-input-label for="quantity" :value="__('Quantity to Transfer')" />
                                    <x-text-input id="quantity" name="quantity" type="number" min="1" class="mt-1 block w-full" 
                                                 :value="old('quantity')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                                </div>

                                <!-- Unit Cost -->
                                <div>
                                    <x-input-label for="unit_cost" :value="__('Unit Cost (Optional)')" />
                                    <x-text-input id="unit_cost" name="unit_cost" type="number" step="0.01" min="0" class="mt-1 block w-full" 
                                                 :value="old('unit_cost')" placeholder="0.00" />
                                    <x-input-error class="mt-2" :messages="$errors->get('unit_cost')" />
                                </div>

                                <!-- Transfer Date -->
                                <div>
                                    <x-input-label for="movement_date" :value="__('Transfer Date')" />
                                    <x-text-input id="movement_date" name="movement_date" type="datetime-local" class="mt-1 block w-full" 
                                                 :value="old('movement_date', now()->format('Y-m-d\TH:i'))" />
                                    <x-input-error class="mt-2" :messages="$errors->get('movement_date')" />
                                </div>
                            </div>
                        </div>

                        <!-- Reason and Notes -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Reason -->
                            <div>
                                <x-input-label for="reason" :value="__('Reason for Transfer')" />
                                <x-text-input id="reason" name="reason" type="text" class="mt-1 block w-full" 
                                             :value="old('reason')" maxlength="500" 
                                             placeholder="e.g., Relocating inventory, Product variant conversion" />
                                <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mt-6">
                            <x-input-label for="notes" :value="__('Additional Notes (Optional)')" />
                            <textarea id="notes" name="notes" rows="3" 
                                     class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                     maxlength="1000" 
                                     placeholder="Any additional details about this transfer...">{{ old('notes') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                        </div>

                        <!-- Transfer Preview -->
                        <div id="transfer-preview" class="mt-8 bg-yellow-50 dark:bg-yellow-900/20 p-6 rounded-lg border border-yellow-200 dark:border-yellow-800 hidden">
                            <h4 class="text-lg font-semibold mb-4 text-yellow-800 dark:text-yellow-200">Transfer Preview</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="font-medium">From:</span>
                                    <div id="preview-from" class="text-gray-600 dark:text-gray-300">-</div>
                                </div>
                                <div>
                                    <span class="font-medium">To:</span>
                                    <div id="preview-to" class="text-gray-600 dark:text-gray-300">-</div>
                                </div>
                                <div>
                                    <span class="font-medium">Quantity:</span>
                                    <div id="preview-quantity" class="text-gray-600 dark:text-gray-300">-</div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex items-center justify-end space-x-4">
                            <button class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <a href="{{ route('inventory.product_stock_adjustment.index') }}">
                                Cancel
                            </a>
                            </button>
                            
                            <button type="submit"  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Process Transfer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productFromSelect = document.getElementById('product_id_from');
            const productToSelect = document.getElementById('product_id_to');
            const quantityInput = document.getElementById('quantity');
            const availableQtyDiv = document.getElementById('available-quantity');
            const availableQtySpan = document.getElementById('available-qty');
            const previewDiv = document.getElementById('transfer-preview');

            // Update available quantity when source product changes
            productFromSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const quantity = selectedOption.dataset.quantity;
                    availableQtySpan.textContent = parseInt(quantity).toLocaleString() + ' units';
                    availableQtyDiv.classList.remove('hidden');
                    quantityInput.max = quantity;
                } else {
                    availableQtyDiv.classList.add('hidden');
                    quantityInput.removeAttribute('max');
                }
                updatePreview();
            });

            // Update preview when inputs change
            function updatePreview() {
                const fromOption = productFromSelect.options[productFromSelect.selectedIndex];
                const toOption = productToSelect.options[productToSelect.selectedIndex];
                const quantity = quantityInput.value;

                if (fromOption.value && toOption.value && quantity) {
                    document.getElementById('preview-from').textContent = fromOption.text.split(' (')[0];
                    document.getElementById('preview-to').textContent = toOption.text.split(' (')[0];
                    document.getElementById('preview-quantity').textContent = parseInt(quantity).toLocaleString() + ' units';
                    previewDiv.classList.remove('hidden');
                } else {
                    previewDiv.classList.add('hidden');
                }
            }

            // Add event listeners for preview updates
            productToSelect.addEventListener('change', updatePreview);
            quantityInput.addEventListener('input', updatePreview);

            // Prevent selecting same product for from and to
            productFromSelect.addEventListener('change', function() {
                const fromValue = this.value;
                Array.from(productToSelect.options).forEach(option => {
                    if (option.value === fromValue && fromValue !== '') {
                        option.disabled = true;
                        option.style.display = 'none';
                    } else {
                        option.disabled = false;
                        option.style.display = 'block';
                    }
                });
                if (productToSelect.value === fromValue) {
                    productToSelect.value = '';
                }
                updatePreview();
            });

            productToSelect.addEventListener('change', function() {
                const toValue = this.value;
                Array.from(productFromSelect.options).forEach(option => {
                    if (option.value === toValue && toValue !== '') {
                        option.disabled = true;
                        option.style.display = 'none';
                    } else {
                        option.disabled = false;
                        option.style.display = 'block';
                    }
                });
                if (productFromSelect.value === toValue) {
                    productFromSelect.value = '';
                }
                updatePreview();
            });
        });
    </script>
</x-app-layout>
