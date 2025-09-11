<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Add Stock Adjustment</h2>
                            <p class="text-gray-600 dark:text-gray-400">Adjust product stock quantities</p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            <a href="{{ route('inventory.stock.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Stock Movements
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Stock Adjustment Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('inventory.stock.store') }}" class="space-y-6">
                        @csrf

                        <!-- Product Selection -->
                        <div>
                            <label for="product_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select Product <span class="text-red-500">*</span>
                            </label>
                            <select name="product_id" id="product_id" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                onchange="updateCurrentStock()">
                                <option value="">Choose a product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->product_id }}" data-current-stock="{{ $product->quantity }}"
                                        {{ old('product_id') == $product->product_id ? 'selected' : '' }}>
                                        {{ $product->product_name }} - {{ $product->product_brand }} (Current:
                                        {{ $product->quantity }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Stock Display -->
                        <div id="current-stock-display"
                            class="hidden bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-blue-800 dark:text-blue-200">
                                    Current Stock: <span id="current-stock-amount" class="font-semibold">0</span> units
                                </span>
                            </div>
                        </div>

                        <!-- New Quantity -->
                        <div>
                            <label for="new_quantity"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                New Stock Quantity <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="new_quantity" id="new_quantity"
                                    value="{{ old('new_quantity') }}" min="0" step="1" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-16"
                                    placeholder="Enter new stock quantity..." onchange="calculateStockChange()">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 sm:text-sm">units</span>
                                </div>
                            </div>
                            @error('new_quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock Change Display -->
                        <div id="stock-change-display"
                            class="hidden bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-2" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">
                                    Stock Change: <span id="stock-change-amount" class="font-semibold">0</span> units
                                    <span id="stock-change-type" class="ml-1"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Unit Cost (Optional) -->
                        <div>
                            <label for="unit_cost"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Unit Cost (Optional)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" name="unit_cost" id="unit_cost" value="{{ old('unit_cost') }}"
                                    min="0" step="0.01"
                                    class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="0.00">
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Used to calculate the total value
                                of this adjustment</p>
                            @error('unit_cost')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reason -->
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Reason for Adjustment
                            </label>
                            <select name="reason" id="reason"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select a reason...</option>
                                <option value="Physical count adjustment" {{ old('reason') == 'Physical count adjustment' ? 'selected' : '' }}>Physical count adjustment</option>
                                <option value="Damaged goods removal" {{ old('reason') == 'Damaged goods removal' ? 'selected' : '' }}>Damaged goods removal</option>
                                <option value="Expired products" {{ old('reason') == 'Expired products' ? 'selected' : '' }}>Expired products</option>
                                <option value="System error correction" {{ old('reason') == 'System error correction' ? 'selected' : '' }}>System error correction</option>
                                <option value="Theft or loss" {{ old('reason') == 'Theft or loss' ? 'selected' : '' }}>
                                    Theft or loss</option>
                                <option value="Initial stock entry" {{ old('reason') == 'Initial stock entry' ? 'selected' : '' }}>Initial stock entry</option>
                                <option value="Promotional samples" {{ old('reason') == 'Promotional samples' ? 'selected' : '' }}>Promotional samples</option>
                                <option value="Quality control testing" {{ old('reason') == 'Quality control testing' ? 'selected' : '' }}>Quality control testing</option>
                                <option value="Other" {{ old('reason') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Additional Notes
                            </label>
                            <textarea name="notes" id="notes" rows="4"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Additional details about this stock adjustment...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Movement Date -->
                        <div>
                            <label for="movement_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Movement Date
                            </label>
                            <input type="datetime-local" name="movement_date" id="movement_date"
                                value="{{ old('movement_date', now()->format('Y-m-d\TH:i')) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('movement_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-6">
                            <a href="{{ route('inventory.stock.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>

                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Create Stock Adjustment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateCurrentStock() {
            const productSelect = document.getElementById('product_id');
            const currentStockDisplay = document.getElementById('current-stock-display');
            const currentStockAmount = document.getElementById('current-stock-amount');

            if (productSelect.value) {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const currentStock = selectedOption.getAttribute('data-current-stock');

                currentStockAmount.textContent = parseInt(currentStock).toLocaleString();
                currentStockDisplay.classList.remove('hidden');

                // Reset new quantity and stock change display
                document.getElementById('new_quantity').value = '';
                document.getElementById('stock-change-display').classList.add('hidden');
            } else {
                currentStockDisplay.classList.add('hidden');
                document.getElementById('stock-change-display').classList.add('hidden');
            }
        }

        function calculateStockChange() {
            const productSelect = document.getElementById('product_id');
            const newQuantityInput = document.getElementById('new_quantity');
            const stockChangeDisplay = document.getElementById('stock-change-display');
            const stockChangeAmount = document.getElementById('stock-change-amount');
            const stockChangeType = document.getElementById('stock-change-type');

            if (productSelect.value && newQuantityInput.value !== '') {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const currentStock = parseInt(selectedOption.getAttribute('data-current-stock'));
                const newStock = parseInt(newQuantityInput.value);
                const change = newStock - currentStock;

                stockChangeAmount.textContent = Math.abs(change).toLocaleString();

                if (change > 0) {
                    stockChangeAmount.className = 'font-semibold text-green-600';
                    stockChangeType.textContent = '(Increase)';
                    stockChangeType.className = 'ml-1 text-green-600';
                } else if (change < 0) {
                    stockChangeAmount.className = 'font-semibold text-red-600';
                    stockChangeType.textContent = '(Decrease)';
                    stockChangeType.className = 'ml-1 text-red-600';
                } else {
                    stockChangeAmount.className = 'font-semibold text-gray-600';
                    stockChangeType.textContent = '(No Change)';
                    stockChangeType.className = 'ml-1 text-gray-600';
                }

                stockChangeDisplay.classList.remove('hidden');
            } else {
                stockChangeDisplay.classList.add('hidden');
            }
        }

        // Initialize on page load if values are pre-filled
        document.addEventListener('DOMContentLoaded', function () {
            updateCurrentStock();
            calculateStockChange();
        });
    </script>
</x-app-layout>