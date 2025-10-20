<x-app-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center">
                        <a href="{{ route('master_data.products.index') }}" 
                           class="inline-flex items-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 mr-4">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Back to Products
                        </a>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Import Products</h2>
                            <p class="text-gray-600 dark:text-gray-400">Upload products from Excel or CSV files</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-6 mb-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100">Import Instructions</h3>
                        <div class="mt-2 text-blue-800 dark:text-blue-200">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Upload Excel (.xlsx, .xls) or CSV files</li>
                                <li>First row should contain column headers</li>
                                <li>Required columns: Product Name, Brand, Quantity, Price</li>
                                <li>Optional column: Description</li>
                                <li>Maximum file size: 5MB</li>
                            </ul>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('inventory.products.template') }}" 
                               class="inline-flex items-center px-3 py-2 border border-blue-300 rounded-md text-sm font-medium text-blue-800 bg-blue-100 hover:bg-blue-200 dark:bg-blue-800 dark:text-blue-200 dark:border-blue-600 dark:hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                </svg>
                                Download Sample Template
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

            <!-- Import Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('inventory.products.import') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- File Upload -->
                        <div>
                            <label for="excel_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select File <span class="text-red-500">*</span>
                            </label>
                            
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 transition-colors duration-200"
                                 id="file-drop-zone"
                                 ondrop="dropHandler(event);"
                                 ondragover="dragOverHandler(event);"
                                 ondragleave="dragLeaveHandler(event);">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="excel_file" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload a file</span>
                                            <input id="excel_file" name="excel_file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" onchange="displayFileName(this)" required>
                                        </label>
                                        <p class="pl-1 text-gray-500 dark:text-gray-400">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Excel (.xlsx, .xls) or CSV files up to 5MB</p>
                                </div>
                            </div>

                            <!-- File Name Display -->
                            <div id="file-info" class="mt-3 hidden">
                                <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                    <span id="file-name" class="text-sm text-gray-700 dark:text-gray-300"></span>
                                    <button type="button" onclick="removeFile()" class="ml-auto text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            @error('excel_file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Import Options -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Import Options</h4>
                            
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="skip_duplicates" value="1" checked 
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Skip duplicate products (based on name and brand)</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" name="update_existing" value="1" 
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Update existing products if found</span>
                                </label>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('master_data.products.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Import Products
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- File Format Example -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Expected File Format</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Brand</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">iPhone 14 Pro</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">Apple</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">50</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">999.99</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">Latest iPhone with ProMotion display</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">Galaxy S24</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">Samsung</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">30</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">899.99</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">Flagship Android smartphone</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function displayFileName(input) {
            const fileInfo = document.getElementById('file-info');
            const fileName = document.getElementById('file-name');
            
            if (input.files && input.files[0]) {
                fileName.textContent = input.files[0].name;
                fileInfo.classList.remove('hidden');
            }
        }

        function removeFile() {
            const fileInput = document.getElementById('excel_file');
            const fileInfo = document.getElementById('file-info');
            
            fileInput.value = '';
            fileInfo.classList.add('hidden');
        }

        function dragOverHandler(event) {
            event.preventDefault();
            const dropZone = document.getElementById('file-drop-zone');
            dropZone.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900');
        }

        function dragLeaveHandler(event) {
            event.preventDefault();
            const dropZone = document.getElementById('file-drop-zone');
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900');
        }

        function dropHandler(event) {
            event.preventDefault();
            const dropZone = document.getElementById('file-drop-zone');
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900');
            
            const fileInput = document.getElementById('excel_file');
            const files = event.dataTransfer.files;
            
            if (files.length > 0) {
                fileInput.files = files;
                displayFileName(fileInput);
            }
        }
    </script>
</x-app-layout>
