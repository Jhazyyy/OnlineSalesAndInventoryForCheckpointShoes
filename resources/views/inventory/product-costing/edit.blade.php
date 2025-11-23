<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Product Costing</h2>
                            <p class="text-gray-600 dark:text-gray-400">{{ $product->product_name }}</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <a href="{{ route('inventory.product-costing.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded relative"
                    role="alert">
                    <strong class="font-bold">Please correct the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Product Info Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Product Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Product Name</label>
                            <p class="text-gray-900 dark:text-white">{{ $product->product_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Brand</label>
                            <p class="text-gray-900 dark:text-white">{{ $product->product_brand }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</label>
                            <p class="text-gray-900 dark:text-white">{{ $product->product_category }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Price</label>
                            <p class="text-lg font-bold text-green-600 dark:text-green-400">
                                ₱{{ number_format($product->price ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Total
                                Cost</label>
                            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                @if ($product->total_cost)
                                    ₱{{ number_format($product->total_cost ?? 0, 2) }}
                                @else
                                    <span class="text-gray-400">Not Set</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Profit
                                Margin</label>
                            <p
                                class="text-lg font-bold {{ ($product->profit_margin ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                @if ($product->profit_margin !== null)
                                    {{ number_format($product->profit_margin ?? 0, 2) }}%
                                @else
                                    <span class="text-gray-400">Not Set</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Cost Breakdown -->
            @if ($product->total_cost && !empty($breakdown))
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Cost Breakdown</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach ($breakdown as $component => $data)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 capitalize">
                                        {{ str_replace('_', ' ', $component) }}</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        ₱{{ number_format($data['amount'], 2) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $data['percentage'] }}% of
                                        total</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Costing Form -->
            <form method="POST" action="{{ route('inventory.product-costing.update', $product) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Cost Components -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cost Components</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Raw Material Cost -->
                            <div>
                                <label for="raw_material_cost"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Raw Material Cost (₱)
                                </label>
                                <input type="number" name="raw_material_cost" id="raw_material_cost" step="0.01"
                                    min="0" value="{{ old('raw_material_cost', $product->raw_material_cost) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Cost of materials per unit</p>
                            </div>

                            <!-- Labor Cost -->
                            <div>
                                <label for="labor_cost"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Labor Cost (₱)
                                </label>
                                <input type="number" name="labor_cost" id="labor_cost" step="0.01" min="0"
                                    value="{{ old('labor_cost', $product->labor_cost) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Direct labor cost per unit</p>
                            </div>

                            <!-- Overhead Cost -->
                            <div>
                                <label for="overhead_cost"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Overhead Cost (₱)
                                </label>
                                <input type="number" name="overhead_cost" id="overhead_cost" step="0.01"
                                    min="0" value="{{ old('overhead_cost', $product->overhead_cost) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Overhead/indirect costs per
                                    unit</p>
                            </div>

                            <!-- Shipping Cost -->
                            <div>
                                <label for="shipping_cost_per_unit"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Shipping Cost (₱)
                                </label>
                                <input type="number" name="shipping_cost_per_unit" id="shipping_cost_per_unit"
                                    step="0.01" min="0"
                                    value="{{ old('shipping_cost_per_unit', $product->shipping_cost_per_unit) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Shipping/freight cost per unit
                                </p>
                            </div>

                            <!-- Tax Amount -->
                            <div>
                                <label for="tax_amount_per_unit"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tax Amount (₱)
                                </label>
                                <input type="number" name="tax_amount_per_unit" id="tax_amount_per_unit"
                                    step="0.01" min="0"
                                    value="{{ old('tax_amount_per_unit', $product->tax_amount_per_unit) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tax amount per unit</p>
                            </div>

                            <!-- Handling Cost -->
                            <div>
                                <label for="handling_cost"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Handling Cost (₱)
                                </label>
                                <input type="number" name="handling_cost" id="handling_cost" step="0.01"
                                    min="0" value="{{ old('handling_cost', $product->handling_cost) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Handling and packaging cost
                                </p>
                            </div>
                        </div>

                        <!-- Live Calculation Display -->
                        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                            <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-3">Calculated Summary
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <p class="text-blue-700 dark:text-blue-300">Manufacturing Cost</p>
                                    <p class="text-lg font-bold text-blue-900 dark:text-blue-100"
                                        id="calc-manufacturing">₱0.00</p>
                                </div>
                                <div>
                                    <p class="text-blue-700 dark:text-blue-300">Total Cost</p>
                                    <p class="text-lg font-bold text-blue-900 dark:text-blue-100"
                                        id="calc-total-cost">₱0.00</p>
                                </div>
                                <div>
                                    <p class="text-blue-700 dark:text-blue-300">Profit Amount</p>
                                    <p class="text-lg font-bold text-blue-900 dark:text-blue-100" id="calc-profit">
                                        ₱0.00</p>
                                </div>
                                <div>
                                    <p class="text-blue-700 dark:text-blue-300">Profit Margin</p>
                                    <p class="text-lg font-bold text-blue-900 dark:text-blue-100" id="calc-margin">
                                        0.00%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pricing</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Selling Price -->
                            <div>
                                <label for="price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Selling Price (₱) *
                                </label>
                                <input type="number" name="price" id="price" step="0.01" min="0"
                                    required value="{{ old('price', $product->price) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Calculation Method -->
                            {{-- <div>
                                <label for="cost_calculation_method"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Calculation Method
                                </label>
                                <select name="cost_calculation_method" id="cost_calculation_method"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="standard"
                                        {{ old('cost_calculation_method', $product->cost_calculation_method) == 'standard' ? 'selected' : '' }}>
                                        Standard</option>
                                    <option value="average"
                                        {{ old('cost_calculation_method', $product->cost_calculation_method) == 'average' ? 'selected' : '' }}>
                                        Average</option>
                                    <option value="fifo"
                                        {{ old('cost_calculation_method', $product->cost_calculation_method) == 'fifo' ? 'selected' : '' }}>
                                        FIFO</option>
                                    <option value="lifo"
                                        {{ old('cost_calculation_method', $product->cost_calculation_method) == 'lifo' ? 'selected' : '' }}>
                                        LIFO</option>
                                </select>
                            </div> --}}
                        </div>

                        <!-- Price Suggestion -->
                        @if (!empty($pricesuggestion))
                            <div
                                class="mt-4 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg">
                                <h4 class="text-sm font-semibold text-green-900 dark:text-green-200 mb-2">💡 Pricing
                                    Suggestion</h4>
                                <p class="text-sm text-green-800 dark:text-green-300">
                                    For a {{ $pricesuggestion['desired_margin'] ?? 30 }}% profit margin, suggested
                                    price:
                                    <strong
                                        class="text-lg">₱{{ number_format($pricesuggestion['suggested_price'] ?? 0, 2) }}</strong>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Additional Information
                        </h3>

                        <div>
                            <label for="cost_notes"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Cost Notes
                            </label>
                            <textarea name="cost_notes" id="cost_notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Add any notes about costing calculations...">{{ old('cost_notes', $product->cost_notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('inventory.product-costing.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Update Costing
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const inputs = [
                    'raw_material_cost',
                    'labor_cost',
                    'overhead_cost',
                    'shipping_cost_per_unit',
                    'tax_amount_per_unit',
                    'handling_cost',
                    'price'
                ];

                function calculateCosts() {
                    const rawMaterial = parseFloat(document.getElementById('raw_material_cost').value) || 0;
                    const labor = parseFloat(document.getElementById('labor_cost').value) || 0;
                    const overhead = parseFloat(document.getElementById('overhead_cost').value) || 0;
                    const shipping = parseFloat(document.getElementById('shipping_cost_per_unit').value) || 0;
                    const tax = parseFloat(document.getElementById('tax_amount_per_unit').value) || 0;
                    const handling = parseFloat(document.getElementById('handling_cost').value) || 0;
                    const price = parseFloat(document.getElementById('price').value) || 0;

                    const manufacturing = rawMaterial + labor + overhead;
                    const totalCost = manufacturing + shipping + tax + handling;
                    const profit = price - totalCost;
                    const margin = totalCost > 0 ? ((profit / totalCost) * 100) : 0;

                    document.getElementById('calc-manufacturing').textContent = '₱' + manufacturing.toFixed(2);
                    document.getElementById('calc-total-cost').textContent = '₱' + totalCost.toFixed(2);
                    document.getElementById('calc-profit').textContent = '₱' + profit.toFixed(2);
                    document.getElementById('calc-margin').textContent = margin.toFixed(2) + '%';

                    // Update profit color
                    const profitEl = document.getElementById('calc-profit');
                    const marginEl = document.getElementById('calc-margin');

                    if (profit >= 0) {
                        profitEl.classList.remove('text-red-900', 'dark:text-red-100');
                        profitEl.classList.add('text-green-900', 'dark:text-green-100');
                        marginEl.classList.remove('text-red-900', 'dark:text-red-100');
                        marginEl.classList.add('text-green-900', 'dark:text-green-100');
                    } else {
                        profitEl.classList.remove('text-green-900', 'dark:text-green-100');
                        profitEl.classList.add('text-red-900', 'dark:text-red-100');
                        marginEl.classList.remove('text-green-900', 'dark:text-green-100');
                        marginEl.classList.add('text-red-900', 'dark:text-red-100');
                    }
                }

                // Add event listeners
                inputs.forEach(inputId => {
                    const element = document.getElementById(inputId);
                    if (element) {
                        element.addEventListener('input', calculateCosts);
                    }
                });

                // Initial calculation
                calculateCosts();
            });
        </script>
    @endpush
</x-app-layout>
