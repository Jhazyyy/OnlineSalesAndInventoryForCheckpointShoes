<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-red-700 dark:text-red-300 flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Record Waste or Damaged Inventory
                        </h3>
                        <a href="{{ route('inventory.stock.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                     <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                            Back to Stock Management
                        </a>
                    </div>

                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4 rounded-lg mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Important Notice</h4>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                    This will permanently reduce the inventory quantities. Please ensure all details are accurate before proceeding.
                                    Common reasons include damaged goods, expired products, theft, or quality control issues.
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative dark:bg-red-900 dark:border-red-700 dark:text-red-100">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('inventory.stock.waste.process') }}">
                        @csrf

                        <!-- Product Selection -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg mb-6">
                            <h4 class="text-md font-semibold mb-4">Product Information</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Product Selection -->
                                <div>
                                    <x-input-label for="product_id" :value="__('Product')" />
                                    <select id="product_id" name="product_id" 
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                            required>
                                        <option value="">Select a product...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->product_id }}" 
                                                    data-quantity="{{ $product->quantity }}"
                                                    data-name="{{ $product->product_name }}"
                                                    data-brand="{{ $product->product_brand }}"
                                                    {{ old('product_id') == $product->product_id ? 'selected' : '' }}>
                                                {{ $product->product_name }} - {{ $product->product_brand }} ({{ number_format($product->quantity) }} available)
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('product_id')" />
                                </div>

                                <!-- Current Stock Display -->
                                <div id="current-stock-display" class="hidden">
                                    <x-input-label :value="__('Current Stock')" />
                                    <div class="mt-1 p-3 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-800 rounded-md">
                                        <div class="text-lg font-semibold text-blue-900 dark:text-blue-100">
                                            <span id="current-stock-quantity">0</span> units available
                                        </div>
                                        <div class="text-sm text-blue-700 dark:text-blue-300">
                                            <span id="current-stock-product">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Waste/Damage Details -->
                        <div class="bg-red-50 dark:bg-red-900/20 p-6 rounded-lg border border-red-200 dark:border-red-800 mb-6">
                            <h4 class="text-md font-semibold mb-4 text-red-800 dark:text-red-200">Waste/Damage Details</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Quantity to Remove -->
                                <div>
                                    <x-input-label for="quantity" :value="__('Quantity to Remove')" />
                                    <x-text-input id="quantity" name="quantity" type="number" min="1" class="mt-1 block w-full" 
                                                 :value="old('quantity')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Enter the number of units to remove from inventory</p>
                                </div>

                                <!-- Unit Cost -->
                                <div>
                                    <x-input-label for="unit_cost" :value="__('Unit Cost (Optional)')" />
                                    <x-text-input id="unit_cost" name="unit_cost" type="number" step="0.01" min="0" class="mt-1 block w-full" 
                                                 :value="old('unit_cost')" placeholder="0.00" />
                                    <x-input-error class="mt-2" :messages="$errors->get('unit_cost')" />
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Cost per unit for loss calculation</p>
                                </div>

                                <!-- Reason -->
                                <div class="md:col-span-2">
                                    <x-input-label for="reason" :value="__('Reason for Waste/Damage')" />
                                    <select id="reason" name="reason" 
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                                            required>
                                        <option value="">Select a reason...</option>
                                        <option value="Damaged during handling" {{ old('reason') == 'Damaged during handling' ? 'selected' : '' }}>Damaged during handling</option>
                                        <option value="Expired products" {{ old('reason') == 'Expired products' ? 'selected' : '' }}>Expired products</option>
                                        <option value="Quality control failure" {{ old('reason') == 'Quality control failure' ? 'selected' : '' }}>Quality control failure</option>
                                        <option value="Water damage" {{ old('reason') == 'Water damage' ? 'selected' : '' }}>Water damage</option>
                                        <option value="Fire damage" {{ old('reason') == 'Fire damage' ? 'selected' : '' }}>Fire damage</option>
                                        <option value="Theft" {{ old('reason') == 'Theft' ? 'selected' : '' }}>Theft</option>
                                        <option value="Manufacturing defect" {{ old('reason') == 'Manufacturing defect' ? 'selected' : '' }}>Manufacturing defect</option>
                                        <option value="Customer return - damaged" {{ old('reason') == 'Customer return - damaged' ? 'selected' : '' }}>Customer return - damaged</option>
                                        <option value="Obsolete inventory" {{ old('reason') == 'Obsolete inventory' ? 'selected' : '' }}>Obsolete inventory</option>
                                        <option value="Other" {{ old('reason') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                                </div>

                                <!-- Movement Date -->
                                <div>
                                    <x-input-label for="movement_date" :value="__('Date of Incident')" />
                                    <x-text-input id="movement_date" name="movement_date" type="datetime-local" class="mt-1 block w-full" 
                                                 :value="old('movement_date', now()->format('Y-m-d\TH:i'))" />
                                    <x-input-error class="mt-2" :messages="$errors->get('movement_date')" />
                                </div>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div class="mb-6">
                            <x-input-label for="notes" :value="__('Additional Notes')" />
                            <textarea id="notes" name="notes" rows="4" 
                                     class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                     maxlength="1000" 
                                     placeholder="Provide detailed information about the circumstances, location, responsible party, corrective actions taken, etc.">{{ old('notes') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Detailed description will help with future prevention and insurance claims</p>
        </div>

        <!-- Impact Summary -->
        <div id="impact-summary" class="bg-orange-50 dark:bg-orange-900/20 p-6 rounded-lg border border-orange-200 dark:border-orange-800 mb-6 hidden">
            <h4 class="text-md font-semibold mb-4 text-orange-800 dark:text-orange-200">Impact Summary</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <span class="text-sm font-medium text-orange-700 dark:text-orange-300">Product:</span>
                    <div id="impact-product" class="text-orange-900 dark:text-orange-100">-</div>
                </div>
                <div>
                    <span class="text-sm font-medium text-orange-700 dark:text-orange-300">Quantity Lost:</span>
                    <div id="impact-quantity" class="text-orange-900 dark:text-orange-100">-</div>
                </div>
                <div>
                    <span class="text-sm font-medium text-orange-700 dark:text-orange-300">Financial Impact:</span>
                    <div id="impact-value" class="text-orange-900 dark:text-orange-100">-</div>
                </div>
            </div>
            
            <div class="mt-4">
                <span class="text-sm font-medium text-orange-700 dark:text-orange-300">New Stock Level:</span>
                <div class="mt-1">
                    <span id="impact-before" class="text-orange-900 dark:text-orange-100">-</span>
                    <svg class="inline w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                    <span id="impact-after" class="text-orange-900 dark:text-orange-100 font-semibold">-</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4">
            <button class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <a href="{{ route('inventory.stock.index') }}">
                Cancel
            </a>
            </button>

            <x-red-button type="submit" 
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md text-sm font-medium flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Record Waste/Damage
            </x-red-button>
        </div>
    </form>
</div>
</div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const unitCostInput = document.getElementById('unit_cost');
    const currentStockDisplay = document.getElementById('current-stock-display');
    const currentStockQuantity = document.getElementById('current-stock-quantity');
    const currentStockProduct = document.getElementById('current-stock-product');
    const impactSummary = document.getElementById('impact-summary');

    // Update current stock display when product changes
    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const quantity = selectedOption.dataset.quantity;
            const name = selectedOption.dataset.name;
            const brand = selectedOption.dataset.brand;
            
            currentStockQuantity.textContent = parseInt(quantity).toLocaleString();
            currentStockProduct.textContent = `${name} - ${brand}`;
            currentStockDisplay.classList.remove('hidden');
            
            quantityInput.max = quantity;
            updateImpactSummary();
        } else {
            currentStockDisplay.classList.add('hidden');
            impactSummary.classList.add('hidden');
            quantityInput.removeAttribute('max');
        }
    });

    // Update impact summary when inputs change
    function updateImpactSummary() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const quantity = parseInt(quantityInput.value) || 0;
        const unitCost = parseFloat(unitCostInput.value) || 0;

        if (selectedOption.value && quantity > 0) {
            const currentStock = parseInt(selectedOption.dataset.quantity);
            const name = selectedOption.dataset.name;
            const brand = selectedOption.dataset.brand;
            const newStock = Math.max(0, currentStock - quantity);
            const financialImpact = quantity * unitCost;

            document.getElementById('impact-product').textContent = `${name} - ${brand}`;
            document.getElementById('impact-quantity').textContent = `${quantity.toLocaleString()} units`;
            document.getElementById('impact-value').textContent = financialImpact > 0 ? `$${financialImpact.toFixed(2)}` : 'Not calculated';
            document.getElementById('impact-before').textContent = `${currentStock.toLocaleString()} units`;
            document.getElementById('impact-after').textContent = `${newStock.toLocaleString()} units`;
            
            // Warning if trying to remove more than available
            if (quantity > currentStock) {
                quantityInput.classList.add('border-red-500');
                quantityInput.classList.remove('border-gray-300');
            } else {
                quantityInput.classList.remove('border-red-500');
                quantityInput.classList.add('border-gray-300');
            }
            
            impactSummary.classList.remove('hidden');
        } else {
            impactSummary.classList.add('hidden');
        }
    }

    // Add event listeners for impact summary updates
    quantityInput.addEventListener('input', updateImpactSummary);
    unitCostInput.addEventListener('input', updateImpactSummary);

    // Initialize if product is pre-selected
    if (productSelect.value) {
        productSelect.dispatchEvent(new Event('change'));
    }
});
</script>
</x-app-layout>
