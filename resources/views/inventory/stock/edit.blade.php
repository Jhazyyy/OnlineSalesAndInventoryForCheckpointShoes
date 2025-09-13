<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Stock Movement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Edit Movement #{{ $stock->movement_id }}</h3>
                        <x-gray-button onclick="window.location.href='{{ route('inventory.stock.index') }}'">
                            Back to List
                        </x-gray-button>
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

                    <form method="POST" action="{{ route('inventory.stock.update', $stock) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Product Information (Read-only) -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h4 class="text-md font-semibold mb-3">Product Information</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                                        <div class="mt-1 p-2 bg-gray-100 dark:bg-gray-600 rounded-md text-gray-800 dark:text-gray-200">
                                            {{ $stock->product->product_name ?? 'N/A' }} - {{ $stock->product->product_brand ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Stock</label>
                                        <div class="mt-1 p-2 bg-gray-100 dark:bg-gray-600 rounded-md text-gray-800 dark:text-gray-200">
                                            {{ number_format($stock->product->quantity ?? 0) }} units
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Movement Details -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h4 class="text-md font-semibold mb-3">Movement Details</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Movement Type</label>
                                        <div class="mt-1 p-2 bg-gray-100 dark:bg-gray-600 rounded-md text-gray-800 dark:text-gray-200">
                                            {{ $stock->movement_type_label }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity Before</label>
                                        <div class="mt-1 p-2 bg-gray-100 dark:bg-gray-600 rounded-md text-gray-800 dark:text-gray-200">
                                            {{ number_format($stock->quantity_before) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Editable Fields -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Quantity Change -->
                            <div>
                                <x-input-label for="quantity_change" :value="__('Quantity Change')" />
                                <x-text-input id="quantity_change" name="quantity_change" type="number" class="mt-1 block w-full" 
                                             :value="old('quantity_change', $stock->quantity_change)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('quantity_change')" />
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Positive for increase, negative for decrease</p>
                            </div>

                            <!-- Unit Cost -->
                            <div>
                                <x-input-label for="unit_cost" :value="__('Unit Cost')" />
                                <x-text-input id="unit_cost" name="unit_cost" type="number" step="0.01" class="mt-1 block w-full" 
                                             :value="old('unit_cost', $stock->unit_cost)" />
                                <x-input-error class="mt-2" :messages="$errors->get('unit_cost')" />
                            </div>

                            <!-- Movement Date -->
                            <div>
                                <x-date-picker 
                                    name="movement_date" 
                                    label="Movement Date" 
                                    :value="old('movement_date', $stock->movement_date->format('Y-m-d\TH:i'))"
                                    placeholder="Select movement date"
                                    :showAge="false"
                                    :includeTime="true"
                                    timeLabel="Time"
                                    maxDate="{{ date('Y-m-d') }}"
                                    minDate="2020-01-01" />
                                <x-input-error class="mt-2" :messages="$errors->get('movement_date')" />
                            </div>

                            <!-- Reason -->
                            <div>
                                <x-input-label for="reason" :value="__('Reason')" />
                                <x-text-input id="reason" name="reason" type="text" class="mt-1 block w-full" 
                                             :value="old('reason', $stock->reason)" maxlength="500" />
                                <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mt-6">
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="3" 
                                     class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                     maxlength="1000">{{ old('notes', $stock->notes) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                        </div>

                        <!-- Calculated Fields Display -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                                <h4 class="text-md font-semibold mb-2 text-blue-800 dark:text-blue-200">New Quantity After</h4>
                                <div id="quantity-after" class="text-lg font-bold text-blue-900 dark:text-blue-100">
                                    {{ number_format($stock->quantity_before) }}
                                </div>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                                <h4 class="text-md font-semibold mb-2 text-green-800 dark:text-green-200">Total Value</h4>
                                <div id="total-value" class="text-lg font-bold text-green-900 dark:text-green-100">
                                    {{ $stock->total_value ? '$' . number_format($stock->total_value, 2) : '$0.00' }}
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex items-center justify-end space-x-4">
                            <x-gray-button onclick="window.location.href='{{ route('inventory.stock.show', $stock) }}'">
                                Cancel
                            </x-gray-button>
                            <x-primary-button>
                                {{ __('Update Movement') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-calculate quantity after and total value
        function updateCalculations() {
            const quantityBefore = {{ $stock->quantity_before }};
            const quantityChange = parseInt(document.getElementById('quantity_change').value) || 0;
            const unitCost = parseFloat(document.getElementById('unit_cost').value) || 0;
            
            const quantityAfter = quantityBefore + quantityChange;
            const totalValue = Math.abs(quantityChange) * unitCost;
            
            document.getElementById('quantity-after').textContent = quantityAfter.toLocaleString();
            document.getElementById('total-value').textContent = '$' + totalValue.toFixed(2);
            
            // Update color based on positive/negative change
            const quantityAfterEl = document.getElementById('quantity-after');
            if (quantityChange > 0) {
                quantityAfterEl.className = 'text-lg font-bold text-green-900 dark:text-green-100';
            } else if (quantityChange < 0) {
                quantityAfterEl.className = 'text-lg font-bold text-red-900 dark:text-red-100';
            } else {
                quantityAfterEl.className = 'text-lg font-bold text-blue-900 dark:text-blue-100';
            }
        }
        
        // Add event listeners
        document.getElementById('quantity_change').addEventListener('input', updateCalculations);
        document.getElementById('unit_cost').addEventListener('input', updateCalculations);
        
        // Initial calculation
        updateCalculations();
    </script>
</x-app-layout>
