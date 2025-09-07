<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Import Shipments</h2>
                            <p class="text-gray-600 dark:text-gray-400">Upload a CSV or Excel file to import multiple shipments at once</p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            <a href="{{ route('sales.shipments.template') }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                </svg>
                                Download Template
                            </a>
                            <a href="{{ route('sales.shipments.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Shipments
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                    <div class="font-bold">Success!</div>
                    <span class="block sm:inline">{{ session('success') }}</span>
                    @if(session('imported_count'))
                        <div class="mt-2">
                            <strong>{{ session('imported_count') }}</strong> shipments imported successfully.
                        </div>
                    @endif
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <div class="font-bold">Error!</div>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <div class="font-bold">Please correct the following errors:</div>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('import_errors'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6" role="alert">
                    <div class="font-bold">Import completed with some errors:</div>
                    <ul class="mt-2 list-disc list-inside max-h-48 overflow-y-auto">
                        @foreach (session('import_errors') as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Instructions -->
            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-6 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-orange-800 dark:text-orange-200">Import Instructions</h3>
                        <div class="mt-2 text-sm text-orange-700 dark:text-orange-300">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Download the template file to see the correct format</li>
                                <li>Supported formats: CSV (.csv), Excel (.xlsx, .xls)</li>
                                <li>Required fields: Sales Order Number, Carrier, Recipient Name, Shipping Address</li>
                                <li>Sales Order Number must match an existing order in the system</li>
                                <li>Priority should be: low, normal, high, or urgent (defaults to normal)</li>
                                <li>Date format: YYYY-MM-DD, MM/DD/YYYY, DD/MM/YYYY, or MM-DD-YYYY</li>
                                <li>Boolean fields (Is Insured, Requires Signature, etc.): use yes/no, true/false, or 1/0</li>
                                <li>Tracking numbers must be unique (duplicates will be skipped)</li>
                                <li>All shipments will be created with "pending" status initially</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('sales.shipments.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- File Upload -->
                        <div class="mb-6">
                            <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select File <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload a file</span>
                                            <input id="file" name="excel_file" type="file" accept=".csv,.xlsx,.xls" class="sr-only" onchange="showFileName()" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">CSV, XLSX, XLS up to 5MB</p>
                                </div>
                            </div>
                            <div id="file-name" class="mt-2 text-sm text-gray-600 dark:text-gray-400 hidden"></div>
                        </div>

                        <!-- Import Options -->
                        <div class="mb-6">
                            <fieldset>
                                <legend class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Import Options</legend>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="skip_invalid_orders" value="1" checked 
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Skip rows with invalid sales order numbers</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="skip_duplicate_tracking" value="1" checked 
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Skip rows with duplicate tracking numbers</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="validate_dates" value="1" checked 
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Validate date formats and logic</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="auto_generate_numbers" value="1" 
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Auto-generate shipment numbers if not provided</span>
                                    </label>
                                </div>
                            </fieldset>
                        </div>

                        <!-- Default Values -->
                        <div class="mb-6">
                            <fieldset>
                                <legend class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Default Values (for empty fields)</legend>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div>
                                        <label for="default_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Default Status</label>
                                        <select id="default_status" name="default_status" 
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="pending">Pending</option>
                                            <option value="preparing">Preparing</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="default_priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Default Priority</label>
                                        <select id="default_priority" name="default_priority" 
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="low">Low</option>
                                            <option value="normal" selected>Normal</option>
                                            <option value="high">High</option>
                                            <option value="urgent">Urgent</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="default_packages" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Default Packages</label>
                                        <input type="number" id="default_packages" name="default_packages" value="1" min="1"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('sales.shipments.index') }}" 
                               class="inline-flex items-center justify-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" id="import-btn"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <span id="import-text">Import Shipments</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sample Data Preview -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Sample Data Format</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">sales_order_number *</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">carrier *</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">tracking_number</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">priority</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">recipient_name *</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">shipping_cost</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">is_fragile</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-white">SO20241201-0001</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-white">FedEx</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-white">FX123456789</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-white">high</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-white">John Doe</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-white">350.00</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-white">no</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-white">SO20241201-0002</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-white">DHL</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-white">DH987654321</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-white">normal</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-white">Jane Smith</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-white">250.00</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900 dark:text-white">no</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        * Required fields. All available fields: sales_order_number, carrier, service_type, tracking_number, priority, 
                        shipment_date, expected_delivery_date, recipient_name, recipient_phone, recipient_email, shipping_address, 
                        total_packages, total_weight, shipping_cost, insurance_cost, additional_fees, is_insured, requires_signature, 
                        is_fragile, is_perishable, special_instructions, notes
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showFileName() {
            const input = document.getElementById('file');
            const fileNameDiv = document.getElementById('file-name');
            
            if (input.files && input.files.length > 0) {
                const fileName = input.files[0].name;
                const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
                fileNameDiv.innerHTML = `<strong>Selected:</strong> ${fileName} (${fileSize} MB)`;
                fileNameDiv.classList.remove('hidden');
            } else {
                fileNameDiv.classList.add('hidden');
            }
        }

        // Add loading state to import button
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('import-btn');
            const text = document.getElementById('import-text');
            
            btn.disabled = true;
            text.innerHTML = 'Importing...';
            
            // Add spinner
            const spinner = document.createElement('div');
            spinner.className = 'animate-spin -ml-1 mr-3 h-4 w-4 border-2 border-white border-t-transparent rounded-full';
            btn.insertBefore(spinner, text);
        });
    </script>
</x-app-layout>
