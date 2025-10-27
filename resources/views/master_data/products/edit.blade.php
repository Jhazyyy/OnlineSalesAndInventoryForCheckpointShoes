<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Product</h2>
                            <p class="text-gray-600 dark:text-gray-400">Update product information</p>
                        </div>
                        <div>
                            <a href="{{ route('master_data.products.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Form Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('master_data.products.update', $product) }}"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Product Name -->
                        <div>
                            <label for="product_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="product_name" name="product_name"
                                value="{{ old('product_name', $product->product_name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('product_name') border-red-500 @enderror">
                            @error('product_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- SKU, Barcode Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- SKU -->
                            <div>
                                <label for="sku"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    SKU
                                </label>
                                <input type="text" id="sku" name="sku"
                                    value="{{ old('sku', $product->sku) }}"
                                    placeholder="e.g., SHOE-001"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('sku') border-red-500 @enderror">
                                @error('sku')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Barcode -->
                            <div>
                                <label for="barcode"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Barcode
                                </label>
                                <input type="text" id="barcode" name="barcode"
                                    value="{{ old('barcode', $product->barcode) }}"
                                    placeholder="e.g., 1234567890123"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('barcode') border-red-500 @enderror">
                                @error('barcode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Property Name and Value Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Property Name -->
                            <div>
                                <label for="property_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Property Name <span class="text-gray-400 text-xs">(Optional, e.g., Size, Color)</span>
                                </label>
                                <input type="text" id="property_name" name="property_name"
                                    value="{{ old('property_name', $product->property_name) }}"
                                    placeholder="e.g., Size"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('property_name') border-red-500 @enderror">
                                @error('property_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Property Value -->
                            <div>
                                <label for="property_value"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Property Value <span class="text-gray-400 text-xs">(Optional, e.g., 42, Red)</span>
                                </label>
                                <input type="text" id="property_value" name="property_value"
                                    value="{{ old('property_value', $product->property_value) }}"
                                    placeholder="e.g., 42"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('property_value') border-red-500 @enderror">
                                @error('property_value')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Brand and Category Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Product Brand -->
                            <div>
                                <label for="product_brand"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Brand <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 flex">
                                    <select id="product_brand" name="product_brand" required
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('product_brand') border-red-500 @enderror">
                                        <option value="">Select a brand...</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand }}"
                                                {{ old('product_brand', $product->product_brand) == $brand ? 'selected' : '' }}>
                                                {{ $brand }}
                                            </option>
                                        @endforeach
                                        <option value="custom"
                                            {{ !in_array(old('product_brand', $product->product_brand), $brands->toArray()) && old('product_brand', $product->product_brand) ? 'selected' : '' }}>
                                            + Add New Brand</option>
                                    </select>
                                    <a href="{{ route('master_data.brands.create') }}"
                                        class="ml-2 inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        title="Add New Brand">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </a>
                                </div>
                                <input type="text" id="custom_brand" name="custom_brand"
                                    value="{{ !in_array(old('product_brand', $product->product_brand), $brands->toArray()) && old('product_brand', $product->product_brand) ? old('product_brand', $product->product_brand) : '' }}"
                                    placeholder="Enter new brand name..."
                                    style="display: {{ !in_array(old('product_brand', $product->product_brand), $brands->toArray()) && old('product_brand', $product->product_brand) ? 'block' : 'none' }};"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('product_brand')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Product Category -->
                            <div>
                                <label for="product_category"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 flex">
                                    <select id="product_category" name="product_category" required
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('product_category') border-red-500 @enderror">
                                        <option value="">Select a category...</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category }}"
                                                {{ old('product_category', $product->product_category) == $category ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                        <option value="custom"
                                            {{ !in_array(old('product_category', $product->product_category), $categories->toArray()) && old('product_category', $product->product_category) ? 'selected' : '' }}>
                                            + Add New Category</option>
                                    </select>
                                    <a href="{{ route('master_data.categories.create') }}"
                                        class="ml-2 inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        title="Add New Category">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </a>
                                </div>
                                <input type="text" id="custom_category" name="custom_category"
                                    value="{{ !in_array(old('product_category', $product->product_category), $categories->toArray()) && old('product_category', $product->product_category) ? old('product_category', $product->product_category) : '' }}"
                                    placeholder="Enter new category name..."
                                    style="display: {{ !in_array(old('product_category', $product->product_category), $categories->toArray()) && old('product_category', $product->product_category) ? 'block' : 'none' }};"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('product_category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Price and Current Stock Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Price -->
                            <div>
                                <label for="price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Price (Peso) <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" id="price" name="price"
                                        value="{{ old('price', $product->price) }}" step="0.01" min="0"
                                        required
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('price') border-red-500 @enderror">
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Current Stock (Read-only Display) -->
                            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Current Stock Quantity</label>
                                        <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                                            {{ number_format($product->quantity) }}
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Managed via <a href="{{ route('inventory.product_stock_adjustment.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Stock Movements</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-rows-1 gap-6">
                            <!-- Current Image Display -->
                            @if ($product->image)
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Current
                                        Image</label>
                                    <div class="mt-1">
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->product_name }}"
                                            class="w-48 h-auto object-cover rounded-lg border">
                                    </div>
                                </div>
                            @endif

                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-4 hidden flex-col items-center">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Image
                                    Preview</label>
                                <img id="previewImg" src="#" alt="Preview"
                                    class="w-48 h-auto object-cover rounded-lg border">
                            </div>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Product Image Upload -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $product->image ? 'Update Image' : 'Product Image' }}
                            </label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md dark:border-gray-600">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="image"
                                            class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>{{ $product->image ? 'Replace image' : 'Upload a file' }}</span>
                                            <input id="image" name="image" type="file" class="sr-only"
                                                accept="image/*" onchange="previewImage(this)">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Description
                            </label>
                            <textarea id="description" name="description" rows="4" placeholder="Enter product description..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('master_data.products.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle custom brand selection
        document.addEventListener('DOMContentLoaded', function() {
            const brandSelect = document.getElementById('product_brand');
            const customBrandInput = document.getElementById('custom_brand');
            const categorySelect = document.getElementById('product_category');
            const customCategoryInput = document.getElementById('custom_category');

            brandSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customBrandInput.style.display = 'block';
                    customBrandInput.required = true;
                    this.required = false;
                } else {
                    customBrandInput.style.display = 'none';
                    customBrandInput.required = false;
                    this.required = true;
                }
            });

            categorySelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customCategoryInput.style.display = 'block';
                    customCategoryInput.required = true;
                    this.required = false;
                } else {
                    customCategoryInput.style.display = 'none';
                    customCategoryInput.required = false;
                    this.required = true;
                }
            });

            // Form submission handler
            document.querySelector('form').addEventListener('submit', function(e) {
                if (brandSelect.value === 'custom') {
                    if (!customBrandInput.value.trim()) {
                        e.preventDefault();
                        alert('Please enter a custom brand name.');
                        return false;
                    }
                    // Replace the select value with the custom input value
                    brandSelect.value = customBrandInput.value.trim();
                }
                if (categorySelect.value === 'custom') {
                    if (!customCategoryInput.value.trim()) {
                        e.preventDefault();
                        alert('Please enter a custom category name.');
                        return false;
                    }
                    // Replace the select value with the custom input value
                    categorySelect.value = customCategoryInput.value.trim();
                }
            });
        });

        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
