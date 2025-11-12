<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Variant</h2>
                            <p class="text-gray-600 dark:text-gray-400">
                                Update variant for {{ $product->product_name }}
                            </p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            <a href="{{ route('master_data.products.variants.index', $product) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Variants
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('master_data.products.variants.update', [$product, $variant]) }}"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Parent Product Info -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-2">Parent Product</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-blue-700 dark:text-blue-400">Name:</span>
                                    <span class="text-blue-900 dark:text-blue-200 font-medium ml-2">{{ $product->product_name }}</span>
                                </div>
                                <div>
                                    <span class="text-blue-700 dark:text-blue-400">SKU:</span>
                                    <span class="text-blue-900 dark:text-blue-200 font-medium ml-2">{{ $product->sku }}</span>
                                </div>
                                <div>
                                    <span class="text-blue-700 dark:text-blue-400">Base Price:</span>
                                    <span class="text-blue-900 dark:text-blue-200 font-medium ml-2">₱{{ number_format($product->price, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Basic Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Variant Name -->
                                <div>
                                    <label for="variant_name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Variant Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="variant_name" name="variant_name"
                                        value="{{ old('variant_name', $variant->variant_name) }}" required
                                        placeholder="e.g., Black Size 7"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('variant_name') border-red-500 @enderror">
                                    @error('variant_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Variant SKU -->
                                <div>
                                    <label for="variant_sku"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Variant SKU <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="variant_sku" name="variant_sku"
                                        value="{{ old('variant_sku', $variant->variant_sku) }}" required
                                        placeholder="e.g., {{ $product->sku }}-BLK-7"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('variant_sku') border-red-500 @enderror">
                                    @error('variant_sku')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Barcode -->
                            <div>
                                <label for="barcode"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Barcode
                                </label>
                                <input type="text" id="barcode" name="barcode"
                                    value="{{ old('barcode', $variant->barcode) }}"
                                    placeholder="e.g., 1234567890123"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('barcode') border-red-500 @enderror">
                                @error('barcode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Variant Attributes -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Variant Attributes</h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Color -->
                                <div>
                                    <label for="color"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Color
                                    </label>
                                    <input type="text" id="color" name="color"
                                        value="{{ old('color', $variant->color) }}"
                                        placeholder="e.g., Black, Tan, Brown"
                                        list="existingColors"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('color') border-red-500 @enderror">
                                    <datalist id="existingColors">
                                        @foreach($existingColors as $color)
                                            <option value="{{ $color }}">
                                        @endforeach
                                    </datalist>
                                    @error('color')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Size -->
                                <div>
                                    <label for="size"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Size
                                    </label>
                                    <input type="text" id="size" name="size"
                                        value="{{ old('size', $variant->size) }}"
                                        placeholder="e.g., 7, 8, 9, M, L, XL"
                                        list="existingSizes"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('size') border-red-500 @enderror">
                                    <datalist id="existingSizes">
                                        @foreach($existingSizes as $size)
                                            <option value="{{ $size }}">
                                        @endforeach
                                    </datalist>
                                    @error('size')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Material -->
                                <div>
                                    <label for="material"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Material
                                    </label>
                                    <input type="text" id="material" name="material"
                                        value="{{ old('material', $variant->material) }}"
                                        placeholder="e.g., Nappa, Suede, Leather"
                                        list="existingMaterials"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('material') border-red-500 @enderror">
                                    <datalist id="existingMaterials">
                                        @foreach($existingMaterials as $material)
                                            <option value="{{ $material }}">
                                        @endforeach
                                    </datalist>
                                    @error('material')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Inventory -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Pricing & Inventory</h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Price Adjustment -->
                                <div>
                                    <label for="price_adjustment"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Price Adjustment <span class="text-gray-400 text-xs">(+/- from base)</span>
                                    </label>
                                    <input type="number" step="0.01" id="price_adjustment" name="price_adjustment"
                                        value="{{ old('price_adjustment', $variant->price_adjustment) }}"
                                        placeholder="0.00"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('price_adjustment') border-red-500 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">Enter positive for markup, negative for discount</p>
                                    @error('price_adjustment')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <label for="quantity"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Initial Stock Quantity
                                    </label>
                                    <input type="number" id="quantity" name="quantity"
                                        value="{{ old('quantity', $variant->quantity) }}" min="0"
                                        placeholder="0"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('quantity') border-red-500 @enderror">
                                    @error('quantity')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Reorder Level -->
                                <div>
                                    <label for="reorder_level"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Reorder Level
                                    </label>
                                    <input type="number" id="reorder_level" name="reorder_level"
                                        value="{{ old('reorder_level', $variant->reorder_level) }}" min="0"
                                        placeholder="Optional"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('reorder_level') border-red-500 @enderror">
                                    @error('reorder_level')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Critical Level -->
                            <div class="md:w-1/3">
                                <label for="critical_level"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Critical Level
                                </label>
                                <input type="number" id="critical_level" name="critical_level"
                                    value="{{ old('critical_level', $variant->critical_level) }}" min="0"
                                    placeholder="Optional"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('critical_level') border-red-500 @enderror">
                                @error('critical_level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Variant Image</h3>

                            <div>
                                <label for="image"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Upload Image
                                </label>
                                <input type="file" id="image" name="image" accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-indigo-50 file:text-indigo-700
                                        hover:file:bg-indigo-100
                                        dark:file:bg-indigo-900 dark:file:text-indigo-300
                                        @error('image') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500">Accepted formats: JPG, PNG, GIF (Max 2MB)</p>
                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Status & Notes -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Additional Information</h3>

                            <!-- Active Status -->
                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                    {{ old('is_active', $variant->is_active) ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Active (variant is available for sale)
                                </label>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Notes
                                </label>
                                <textarea id="notes" name="notes" rows="3"
                                    placeholder="Any additional notes about this variant..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('notes') border-red-500 @enderror">{{ old('notes', $variant->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('master_data.products.variants.index', $product) }}"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Variant
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
