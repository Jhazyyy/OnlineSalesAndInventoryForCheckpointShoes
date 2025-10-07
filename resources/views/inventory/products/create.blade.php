<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Product</h2>
                            <p class="text-gray-600 dark:text-gray-400">Create a new product in your inventory</p>
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
                    <form method="POST" action="{{ route('inventory.products.store') }}" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

                            <!-- Product Brand -->
                            <div>
                                <label for="product_brand"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Brand <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="product_brand" name="product_brand"
                                    value="{{ old('product_brand') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('product_brand') border-red-500 @enderror">
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
                                <input type="text" id="product_category" name="product_category"
                                    value="{{ old('product_category') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('product_category') border-red-500 @enderror">
                                @error('product_category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Quantity and Price Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Quantity -->
                            <div>
                                <label for="quantity"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Quantity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 0) }}"
                                    min="0" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('quantity') border-red-500 @enderror">
                                @error('quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Price (Peso) <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01"
                                        min="0" required
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('price') border-red-500 @enderror">
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        {{-- Product Image and Description --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Product Image -->
                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Product Image
                                </label>

                                <div class="mt-1 relative flex justify-center items-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md dark:border-gray-600 overflow-hidden cursor-pointer"
                                    onclick="document.getElementById('image').click()">

                                    <!-- Upload placeholder -->
                                    <div id="uploadPlaceholder" class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                            viewBox="0 0 48 48">
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
                                    <input id="image" name="image" type="file" class="sr-only" accept="image/*"
                                        onchange="previewImage(this)">

                                    <!-- Image Preview (inside box) -->
                                    <img id="previewImg" src="#" alt="Preview"
                                        class="inset-0 max-w-md h-auto object-cover rounded-md hidden" />
                                </div>

                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Description
                                </label>
                                <textarea id="description" name="description" rows="5"
                                    placeholder="Enter product description..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('inventory.products.index') }}"
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
    </script>

</x-app-layout>