<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Tax/Discount</h2>
                            <p class="text-gray-600 dark:text-gray-400">Update tax or discount information</p>
                        </div>
                        <a href="{{ route('master_data.tax_discounts.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('master_data.tax_discounts.update', $taxDiscount) }}" class="space-y-6" id="taxDiscountForm">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <!-- Code -->
                                <div>
                                    <x-input-label for="code" :value="__('Code')" />
                                    <x-text-input id="code" name="code" type="text"
                                        class="mt-1 block w-full" :value="old('code', $taxDiscount->code)" required maxlength="20"
                                        placeholder="e.g., TAX-VAT or DISC-10" />
                                    <x-input-error class="mt-2" :messages="$errors->get('code')" />
                                    <p class="mt-1 text-sm text-gray-500">Unique code. Use letters, numbers, dashes, or underscores.</p>
                                </div>

                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Name')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                        :value="old('name', $taxDiscount->name)" required maxlength="100" placeholder="Enter name" />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <!-- Type -->
                                <div>
                                    <x-input-label for="type" :value="__('Type')" />
                                    <select id="type" name="type" required
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Select type</option>
                                        <option value="tax" {{ old('type', $taxDiscount->type) === 'tax' ? 'selected' : '' }}>Tax</option>
                                        <option value="discount" {{ old('type', $taxDiscount->type) === 'discount' ? 'selected' : '' }}>Discount</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('type')" />
                                </div>

                                <!-- Applicable For -->
                                <div>
                                    <x-input-label for="applicable_for" :value="__('Applicable For')" />
                                    <select id="applicable_for" name="applicable_for" required
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="both" {{ old('applicable_for', $taxDiscount->applicable_for ?? 'both') === 'both' ? 'selected' : '' }}>Both (Supplier & Customer)</option>
                                        <option value="supplier" {{ old('applicable_for', $taxDiscount->applicable_for) === 'supplier' ? 'selected' : '' }}>Supplier (Purchase Orders)</option>
                                        <option value="customer" {{ old('applicable_for', $taxDiscount->applicable_for) === 'customer' ? 'selected' : '' }}>Customer (Sales Orders)</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('applicable_for')" />
                                    <p class="mt-1 text-sm text-gray-500">Choose whether this applies to suppliers, customers, or both</p>
                                </div>

                                <!-- Calculation Method -->
                                <div>
                                    <x-input-label for="calculation_method" :value="__('Calculation Method')" />
                                    <select id="calculation_method" name="calculation_method" required
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="percentage" {{ old('calculation_method', $taxDiscount->calculation_method) === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        <option value="fixed" {{ old('calculation_method', $taxDiscount->calculation_method) === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('calculation_method')" />
                                </div>

                                <!-- Rate (Percentage) -->
                                <div id="rate_field">
                                    <x-input-label for="rate" :value="__('Rate (%)')" />
                                    <x-text-input id="rate" name="rate" type="number" step="0.0001"
                                        class="mt-1 block w-full" :value="old('rate', $taxDiscount->rate)" min="0" max="100"
                                        placeholder="e.g., 12.5" />
                                    <x-input-error class="mt-2" :messages="$errors->get('rate')" />
                                    <p class="mt-1 text-sm text-gray-500">Enter percentage value (e.g., 12.5 for 12.5%)</p>
                                </div>

                                <!-- Fixed Amount -->
                                <div id="fixed_amount_field" style="display: none;">
                                    <x-input-label for="fixed_amount" :value="__('Fixed Amount (₱)')" />
                                    <x-text-input id="fixed_amount" name="fixed_amount" type="number" step="0.01"
                                        class="mt-1 block w-full" :value="old('fixed_amount', $taxDiscount->fixed_amount)" min="0"
                                        placeholder="e.g., 100.00" />
                                    <x-input-error class="mt-2" :messages="$errors->get('fixed_amount')" />
                                    <p class="mt-1 text-sm text-gray-500">Enter fixed amount in pesos</p>
                                </div>

                                <!-- Priority -->
                                <div>
                                    <x-input-label for="priority" :value="__('Priority')" />
                                    <x-text-input id="priority" name="priority" type="number"
                                        class="mt-1 block w-full" :value="old('priority', $taxDiscount->priority)" required min="0"
                                        placeholder="0" />
                                    <x-input-error class="mt-2" :messages="$errors->get('priority')" />
                                    <p class="mt-1 text-sm text-gray-500">Lower numbers are calculated first (0 is highest priority)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                placeholder="Enter description (optional)">{{ old('description', $taxDiscount->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Application Scope -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Application Scope</h3>
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="applies_to" :value="__('Applies To')" />
                                    <select id="applies_to" name="applies_to" required
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="all" {{ old('applies_to', $taxDiscount->applies_to) === 'all' ? 'selected' : '' }}>All Products</option>
                                        <option value="specific" {{ old('applies_to', $taxDiscount->applies_to) === 'specific' ? 'selected' : '' }}>Specific Categories/Products</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('applies_to')" />
                                </div>

                                <div id="specific_fields" style="display: none;">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Categories -->
                                        <div>
                                            <x-input-label for="applicable_categories" :value="__('Applicable Categories')" />
                                            <select id="applicable_categories" name="applicable_categories[]" multiple size="5"
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" 
                                                        {{ in_array($category->id, old('applicable_categories', $taxDiscount->applicable_categories ?? [])) ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <p class="mt-1 text-sm text-gray-500">Hold Ctrl/Cmd to select multiple</p>
                                        </div>

                                        <!-- Products -->
                                        <div>
                                            <x-input-label for="applicable_products" :value="__('Applicable Products')" />
                                            <select id="applicable_products" name="applicable_products[]" multiple size="5"
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ in_array($product->id, old('applicable_products', $taxDiscount->applicable_products ?? [])) ? 'selected' : '' }}>
                                                        {{ $product->product_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <p class="mt-1 text-sm text-gray-500">Hold Ctrl/Cmd to select multiple</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Validity Period -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Validity Period</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="valid_from" :value="__('Valid From')" />
                                    <x-text-input id="valid_from" name="valid_from" type="date"
                                        class="mt-1 block w-full" :value="old('valid_from', $taxDiscount->valid_from ? $taxDiscount->valid_from->format('Y-m-d') : '')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('valid_from')" />
                                    <p class="mt-1 text-sm text-gray-500">Leave blank for no start date</p>
                                </div>

                                <div>
                                    <x-input-label for="valid_to" :value="__('Valid To')" />
                                    <x-text-input id="valid_to" name="valid_to" type="date"
                                        class="mt-1 block w-full" :value="old('valid_to', $taxDiscount->valid_to ? $taxDiscount->valid_to->format('Y-m-d') : '')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('valid_to')" />
                                    <p class="mt-1 text-sm text-gray-500">Leave blank for no end date</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Options -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Additional Options</h3>
                            <div class="space-y-4">
                                <!-- Compound -->
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_compound" name="is_compound" value="1"
                                        {{ old('is_compound', $taxDiscount->is_compound) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="is_compound" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        Compound (Calculate on top of previous taxes/discounts)
                                    </label>
                                </div>

                                <!-- Active Status -->
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                        {{ old('is_active', $taxDiscount->is_active) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div
                            class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('master_data.tax_discounts.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Toggle between percentage and fixed amount fields
        document.getElementById('calculation_method').addEventListener('change', function() {
            const rateField = document.getElementById('rate_field');
            const fixedField = document.getElementById('fixed_amount_field');
            const rateInput = document.getElementById('rate');
            const fixedInput = document.getElementById('fixed_amount');
            
            if (this.value === 'fixed') {
                rateField.style.display = 'none';
                fixedField.style.display = 'block';
                rateInput.removeAttribute('required');
                fixedInput.setAttribute('required', 'required');
            } else {
                rateField.style.display = 'block';
                fixedField.style.display = 'none';
                rateInput.setAttribute('required', 'required');
                fixedInput.removeAttribute('required');
            }
        });

        // Toggle specific fields visibility
        document.getElementById('applies_to').addEventListener('change', function() {
            const specificFields = document.getElementById('specific_fields');
            specificFields.style.display = this.value === 'specific' ? 'block' : 'none';
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('calculation_method').dispatchEvent(new Event('change'));
            document.getElementById('applies_to').dispatchEvent(new Event('change'));
        });
    </script>
</x-app-layout>
