<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <header class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Stock Adjustment</h1>
                        <p class="text-gray-600 dark:text-gray-400">Adjust product stock quantities</p>
                    </div>
                    <nav class="mt-4 sm:mt-0 flex space-x-3" aria-label="Secondary navigation">
                        <a href="{{ route('inventory.product_stock_adjustment.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true" focusable="false">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Adjustments
                        </a>
                    </nav>
                </div>
            </header>

            <!-- Alerts -->
            @if (session('success'))
                <div role="alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6"
                    tabindex="0">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div role="alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6"
                    tabindex="0">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Stock Adjustment Form -->
            <section aria-labelledby="form-title"
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('inventory.product_stock_adjustment.store') }}"
                        class="grid grid-cols-1 md:grid-cols-3 gap-6" novalidate>
                        @csrf

                        <!-- Product Selection -->
                        <div class="col-span-1 md:col-span-1">
                            <label for="product_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select Product <span aria-hidden="true" class="text-red-500">*</span>
                            </label>
                            <select name="product_id" id="product_id" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                onchange="updateCurrentStock()" aria-describedby="product-error product-help">
                                <option value="" disabled selected>Choose a product...</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->product_id }}"
                                        data-current-stock="{{ $product->quantity }}"
                                        {{ old('product_id') == $product->product_id ? 'selected' : '' }}>
                                        {{ $product->product_name }}: {{ number_format($product->quantity) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <p id="product-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Quantity -->
                        <div class="col-span-1">
                            <label for="new_quantity"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                New Stock Quantity <span aria-hidden="true" class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="new_quantity" id="new_quantity"
                                    value="{{ old('new_quantity') }}" min="0" step="1" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-16"
                                    placeholder="Enter new stock quantity..." aria-describedby="new-qty-error"
                                    onchange="calculateStockChange()">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">units</span>
                                </div>
                            </div>
                            @error('new_quantity')
                                <p id="new-qty-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unit Cost -->
                        <div class="col-span-1">
                            <label for="unit_cost"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Unit Cost (Optional)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" name="unit_cost" id="unit_cost" value="{{ old('unit_cost') }}"
                                    min="0" step="0.01"
                                    class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="0.00" aria-describedby="unit-cost-help">
                            </div>
                            <p id="unit-cost-help" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Used to calculate the total value of this adjustment
                            </p>
                            @error('unit_cost')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reason -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="reason"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Reason for Adjustment
                            </label>
                            <select name="reason" id="reason"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                aria-describedby="reason-help">
                                <option value="" disabled selected>Select a reason...</option>
                                @php
                                    $reasons = [
                                        'Physical count adjustment',
                                        'Damaged goods removal',
                                        'System error correction',
                                        'Theft or Loss',
                                        'Initial stock entry',
                                        'Promotional samples',
                                        'Quality control testing',
                                        'Other',
                                    ];
                                @endphp
                                @foreach ($reasons as $reason)
                                    <option value="{{ $reason }}"
                                        {{ old('reason') === $reason ? 'selected' : '' }}>{{ $reason }}
                                    </option>
                                @endforeach
                            </select>
                            <p id="reason-help" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Select the reason for this stock adjustment.
                            </p>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Movement Date -->
                        <div class="col-span-1">
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

                        <!-- Notes -->
                        <div class="col-span-1 md:col-span-3">
                            <label for="notes"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Additional Notes
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Additional details about this stock adjustment...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock Displays (Not Inputs) -->
                        <div class="col-span-1 md:col-span-3 space-y-4">
                            <!-- Current Stock Display -->
                            <div id="current-stock-display"
                                class="hidden bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4"
                                role="region" aria-live="polite" aria-atomic="true">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-blue-800 dark:text-blue-200">
                                        Current Stock: <span id="current-stock-amount" class="font-semibold">0</span>
                                        units
                                    </span>
                                </div>
                            </div>

                            <!-- Stock Change Display -->
                            <div id="stock-change-display"
                                class="hidden bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4"
                                role="region" aria-live="polite" aria-atomic="true">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-2" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                    <span class="text-gray-700 dark:text-gray-300">
                                        Stock Change: <span id="stock-change-amount" class="font-semibold">0</span>
                                        units
                                        <span id="stock-change-type" class="ml-1"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="col-span-1 md:col-span-3 flex justify-end space-x-3 pt-4">
                            <a href="{{ route('inventory.product_stock_adjustment.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>

                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Create
                            </button>
                        </div>
                    </form>

                </div>
            </section>
        </div>
    </div>

    <!-- Scripts -->
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

                // Reset change
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

        document.addEventListener('DOMContentLoaded', () => {
            updateCurrentStock();
            calculateStockChange();
        });
    </script>
</x-app-layout>
