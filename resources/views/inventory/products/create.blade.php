<x-app-layout>
    <div class="py-2">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Product</h2>
                            <p class="text-gray-600 dark:text-gray-400">Create a new product to add in your inventory</p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            <a href="{{ route('inventory.products.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('inventory.products.store') }}"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Stock Name (Parent Product) -->
                            <div>
                                <label for="stock_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Stock Name (Base Product) <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <div class="mt-1 flex">
                                    <select id="stock_name" name="stock_name"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('stock_name') border-red-500 @enderror">
                                        <option value="">Select a stock name...</option>
                                        @foreach ($stockNames as $stockName)
                                            <option value="{{ $stockName }}"
                                                {{ old('stock_name') == $stockName ? 'selected' : '' }}>
                                                {{ $stockName }}
                                            </option>
                                        @endforeach
                                        <option value="custom">+ Add New Stock Name</option>
                                    </select>
                                </div>
                                <input type="text" id="custom_stock_name" name="custom_stock_name"
                                    value="{{ old('custom_stock_name') }}" placeholder="Enter new stock name..."
                                    style="display: none;"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">The parent/base product name that variants are associated with</p>
                                @error('stock_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Product Name -->
                            <div>
                                <label for="product_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Product Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="product_name" name="product_name"
                                    value="{{ old('product_name') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('product_name') border-red-500 @enderror">
                                @error('product_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Size -->
                            <div>
                                <label for="size"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Size <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <input type="text" id="size" name="size"
                                    value="{{ old('size') }}"
                                    placeholder="e.g., 42, Large, XL"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('size') border-red-500 @enderror">
                                @error('size')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Color -->
                            <div>
                                <label for="color"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Color <span class="text-gray-400 text-xs"></span>
                                </label>
                                <input type="text" id="color" name="color"
                                    value="{{ old('color') }}"
                                    placeholder="e.g., Black, Red, Blue"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('color') border-red-500 @enderror">
                                @error('color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- SKU -->
                            <div>
                                <label for="sku"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    SKU <span class="text-gray-400 text-xs">(Optional - auto-generated if empty)</span>
                                </label>
                                <input type="text" id="sku" name="sku" value="{{ old('sku') }}"
                                    placeholder="e.g., SHOE-001"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('sku') border-red-500 @enderror">
                                @error('sku')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Product Brand and Category Row -->
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
                                                {{ old('product_brand') == $brand ? 'selected' : '' }}>
                                                {{ $brand }}
                                            </option>
                                        @endforeach
                                        <option value="custom">+ Add New Brand</option>
                                    </select>
                                    {{-- <a href="{{ route('master_data.brands.create') }}"
                                        class="ml-2 inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        title="Add New Brand">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </a> --}}
                                </div>
                                <input type="text" id="custom_brand" name="custom_brand"
                                    value="{{ old('custom_brand') }}" placeholder="Enter new brand name..."
                                    style="display: none;"
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
                                                {{ old('product_category') == $category ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                        <option value="custom">+ Add New Category</option>
                                    </select>
                                    {{-- <a href="{{ route('master_data.categories.create') }}"
                                        class="ml-2 inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        title="Add New Category">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </a> --}}
                                </div>
                                <input type="text" id="custom_category" name="custom_category"
                                    value="{{ old('custom_category') }}" placeholder="Enter new category name..."
                                    style="display: none;"
                                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('product_category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Preferred Supplier -->
                            <div>
                                <label for="preferred_supplier_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Preferred Supplier <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <select id="preferred_supplier_id" name="preferred_supplier_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select a supplier...</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_id }}" {{ old('preferred_supplier_id') == $supplier->supplier_id ? 'selected' : '' }}>
                                            {{ $supplier->supplier_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('preferred_supplier_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Pricing Method and Markup Price Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Pricing Method -->
                            <div>
                                <label for="pricing_method"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Pricing Method<span class="text-red-500">*</span>
                                </label>
                                <select id="pricing_method" name="pricing_method" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('pricing_method') border-red-500 @enderror">
                                    <option value="manual" {{ old('pricing_method', 'manual') == 'manual' ? 'selected' : '' }}>Manual Price</option>
                                    <option value="markup" {{ old('pricing_method') == 'markup' ? 'selected' : '' }}>Markup Price</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    Manual: Fixed price | Markup: Applied from markup configuration
                                </p>
                                @error('pricing_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Markup Price (only shown when markup is selected) -->
                            <div id="markup_price_field" style="display: none;">
                                <label for="markup_price_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Markup Price Configuration<span class="text-red-500">*</span>
                                </label>
                                <select id="markup_price_id" name="markup_price_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('markup_price_id') border-red-500 @enderror">
                                    <option value="">Select markup configuration...</option>
                                    @foreach ($markupPrices as $markup)
                                        <option value="{{ $markup->id }}" {{ old('markup_price_id') == $markup->id ? 'selected' : '' }}>
                                            {{ $markup->name }} ({{ $markup->markup_percentage }}%)
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    Selling price will be calculated from total cost + markup percentage
                                </p>
                                @error('markup_price_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Price and Image Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Price -->
                            <div id="manual_price_field">
                                <label for="price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Price<span class="text-red-500" id="price_required">*</span>
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" id="price" name="price" value="{{ old('price') }}"
                                        step="0.01" min="0" required
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('price') border-red-500 @enderror">
                                </div>
                                <p class="mt-1 text-xs text-gray-500" id="price_helper">
                                    Base price for the product
                                </p>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <!-- Barcode -->
                            {{-- <div>
                                <label for="barcode"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Barcode <span class="text-gray-400 text-xs">(Optional)</span>
                                </label>
                                <input type="text" id="barcode" name="barcode" value="{{ old('barcode') }}"
                                    placeholder="e.g., 1234567890123"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('barcode') border-red-500 @enderror">
                                @error('barcode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div> --}}
                        </div>

                        {{-- Product Image and Description --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Product Image -->
                            <div>
                                <label for="image"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Product Image
                                </label>

                                <!-- Image Source Toggle -->
                                <div class="mt-2 flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="image_source" value="file" checked
                                            onchange="toggleImageSource()"
                                            class="form-radio text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Upload File</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="image_source" value="url"
                                            onchange="toggleImageSource()"
                                            class="form-radio text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Image URL</span>
                                    </label>
                                </div>

                                <!-- File Upload Section -->
                                <div id="fileUploadSection" class="mt-2">
                                    <div class="relative flex justify-center items-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md dark:border-gray-600 overflow-hidden cursor-pointer"
                                        onclick="document.getElementById('image').click()">

                                        <!-- Upload placeholder -->
                                        <div id="uploadPlaceholder" class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                fill="none" viewBox="0 0 48 48">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                                <span
                                                    class="relative bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                                    Click to upload
                                                </span>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 2MB</p>
                                        </div>

                                        <!-- Hidden File Input -->
                                        <input id="image" name="image" type="file" class="sr-only"
                                            accept="image/*" onchange="previewImage(this)">

                                        <!-- Image Preview (inside box) -->
                                        <img id="previewImg" src="#" alt="Preview"
                                            class="inset-0 max-w-fit h-auto object-cover rounded-md hidden" />
                                    </div>
                                </div>

                                <!-- URL Input Section -->
                                <div id="urlInputSection" class="mt-2 hidden">
                                    <input type="url" id="image_url" name="image_url"
                                        value="{{ old('image_url') }}"
                                        placeholder="https://example.com/image.jpg"
                                        onchange="previewImageFromUrl(this.value)"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter a direct image URL (must end with .jpg, .jpeg, .png, or .gif)</p>
                                    
                                    <!-- URL Image Preview -->
                                    <div id="urlPreviewContainer" class="mt-3 hidden">
                                        <div class="relative border-2 border-gray-300 border-dashed rounded-md dark:border-gray-600 p-4">
                                            <img id="urlPreviewImg" src="#" alt="URL Preview"
                                                class="max-w-full h-auto object-cover rounded-md" />
                                        </div>
                                    </div>
                                </div>

                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('image_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Description
                                </label>
                                <textarea id="description" name="description" rows="5" placeholder="Enter product description..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
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
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Create Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle custom brand/category/stock name selection
        document.addEventListener('DOMContentLoaded', function() {
            const stockNameSelect = document.getElementById('stock_name');
            const customStockNameInput = document.getElementById('custom_stock_name');
            const brandSelect = document.getElementById('product_brand');
            const customBrandInput = document.getElementById('custom_brand');
            const categorySelect = document.getElementById('product_category');
            const customCategoryInput = document.getElementById('custom_category');

            stockNameSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customStockNameInput.style.display = 'block';
                    customStockNameInput.required = false; // Stock name is optional
                } else {
                    customStockNameInput.style.display = 'none';
                    customStockNameInput.required = false;
                }
            });

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
                if (stockNameSelect.value === 'custom') {
                    if (!customStockNameInput.value.trim()) {
                        e.preventDefault();
                        alert('Please enter a custom stock name.');
                        return false;
                    }
                }
                if (brandSelect.value === 'custom') {
                    if (!customBrandInput.value.trim()) {
                        e.preventDefault();
                        alert('Please enter a custom brand name.');
                        return false;
                    }
                }
                if (categorySelect.value === 'custom') {
                    if (!customCategoryInput.value.trim()) {
                        e.preventDefault();
                        alert('Please enter a custom category name.');
                        return false;
                    }
                }
            });
        });

        function previewImage(input) {
            const previewImg = document.getElementById('previewImg');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                    uploadPlaceholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                previewImg.src = '#';
                previewImg.classList.add('hidden');
                uploadPlaceholder.classList.remove('hidden');
            }
        }

        function toggleImageSource() {
            const imageSource = document.querySelector('input[name="image_source"]:checked').value;
            const fileUploadSection = document.getElementById('fileUploadSection');
            const urlInputSection = document.getElementById('urlInputSection');
            const imageInput = document.getElementById('image');
            const imageUrlInput = document.getElementById('image_url');

            if (imageSource === 'file') {
                fileUploadSection.classList.remove('hidden');
                urlInputSection.classList.add('hidden');
                imageInput.removeAttribute('disabled');
                imageUrlInput.setAttribute('disabled', 'disabled');
                imageUrlInput.value = '';
            } else {
                fileUploadSection.classList.add('hidden');
                urlInputSection.classList.remove('hidden');
                imageInput.setAttribute('disabled', 'disabled');
                imageInput.value = '';
                imageUrlInput.removeAttribute('disabled');
                // Reset file preview
                document.getElementById('previewImg').classList.add('hidden');
                document.getElementById('uploadPlaceholder').classList.remove('hidden');
            }
        }

        function previewImageFromUrl(url) {
            const urlPreviewContainer = document.getElementById('urlPreviewContainer');
            const urlPreviewImg = document.getElementById('urlPreviewImg');

            if (url && (url.match(/\.(jpeg|jpg|gif|png)$/i) || url.includes('unsplash') || url.includes('imgur') || url.includes('cloudinary'))) {
                urlPreviewImg.src = url;
                urlPreviewImg.onerror = function() {
                    urlPreviewContainer.classList.add('hidden');
                    alert('Unable to load image from URL. Please check the URL and try again.');
                };
                urlPreviewImg.onload = function() {
                    urlPreviewContainer.classList.remove('hidden');
                };
            } else if (url) {
                urlPreviewContainer.classList.add('hidden');
                alert('Please enter a valid image URL (must end with .jpg, .jpeg, .png, or .gif)');
            } else {
                urlPreviewContainer.classList.add('hidden');
            }
        }

        // Handle pricing method changes
        document.addEventListener('DOMContentLoaded', function() {
            const pricingMethodSelect = document.getElementById('pricing_method');
            const markupPriceField = document.getElementById('markup_price_field');
            const markupPriceSelect = document.getElementById('markup_price_id');
            const manualPriceField = document.getElementById('manual_price_field');
            const priceInput = document.getElementById('price');
            const priceRequired = document.getElementById('price_required');
            const priceHelper = document.getElementById('price_helper');

            function updatePricingFields() {
                const method = pricingMethodSelect.value;
                
                if (method === 'markup') {
                    // Show markup price field, hide/optional manual price
                    markupPriceField.style.display = 'block';
                    markupPriceSelect.setAttribute('required', 'required');
                    priceInput.removeAttribute('required');
                    priceRequired.style.display = 'none';
                    priceHelper.textContent = 'Optional base/reference price (selling price will be calculated from markup)';
                } else {
                    // Manual: hide markup field, require price
                    markupPriceField.style.display = 'none';
                    markupPriceSelect.removeAttribute('required');
                    priceInput.setAttribute('required', 'required');
                    priceRequired.style.display = 'inline';
                    priceHelper.textContent = 'Base price for the product';
                }
            }

            // Initialize on page load
            updatePricingFields();

            // Update when pricing method changes
            pricingMethodSelect.addEventListener('change', updatePricingFields);
        });
    </script>
</x-app-layout>