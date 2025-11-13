<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Submit Bank Transfer Payment</h2>
                            <p class="text-gray-600 dark:text-gray-400">Upload proof of payment for order verification</p>
                        </div>
                        <a href="{{ route('sales.orders.show', $order->order_id) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Order
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Order Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Order Number</label>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $order->order_number }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Order Date</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->order_date->format('M d, Y') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Bank Transfer
                                </span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Total Amount</label>
                            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">₱{{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Submission Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Bank Transfer Details</h3>

                    <form method="POST" action="{{ route('bank-transfer-payments.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Hidden order_id -->
                        <input type="hidden" name="order_id" value="{{ $order->order_id }}">

                        <!-- Bank Name -->
                        <div>
                            <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Bank Name <span class="text-red-500">*</span>
                            </label>
                            <select id="bank_name" name="bank_name" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select Bank</option>
                                <option value="BDO" {{ old('bank_name') == 'BDO' ? 'selected' : '' }}>BDO (Banco de Oro)</option>
                                <option value="BPI" {{ old('bank_name') == 'BPI' ? 'selected' : '' }}>BPI (Bank of the Philippine Islands)</option>
                                <option value="Metrobank" {{ old('bank_name') == 'Metrobank' ? 'selected' : '' }}>Metrobank</option>
                                <option value="UnionBank" {{ old('bank_name') == 'UnionBank' ? 'selected' : '' }}>UnionBank</option>
                                <option value="PNB" {{ old('bank_name') == 'PNB' ? 'selected' : '' }}>PNB (Philippine National Bank)</option>
                                <option value="Landbank" {{ old('bank_name') == 'Landbank' ? 'selected' : '' }}>Landbank</option>
                                <option value="Security Bank" {{ old('bank_name') == 'Security Bank' ? 'selected' : '' }}>Security Bank</option>
                                <option value="RCBC" {{ old('bank_name') == 'RCBC' ? 'selected' : '' }}>RCBC</option>
                                <option value="Chinabank" {{ old('bank_name') == 'Chinabank' ? 'selected' : '' }}>Chinabank</option>
                                <option value="Other" {{ old('bank_name') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Select the bank where you made the transfer</p>
                        </div>

                        <!-- Reference Number -->
                        <div>
                            <label for="reference_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Reference/Transaction Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="reference_no" name="reference_no" value="{{ old('reference_no') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                   placeholder="Enter transaction reference number">
                            <p class="mt-1 text-xs text-gray-500">Enter the unique reference number from your bank transfer receipt</p>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Amount Paid <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" id="amount" name="amount" step="0.01" min="0.01"
                                       value="{{ old('amount', $order->total_amount) }}" required readonly
                                       class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white bg-gray-50">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Amount must match the order total exactly</p>
                        </div>

                        <!-- Proof of Payment -->
                        <div>
                            <label for="proof" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Proof of Payment <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md dark:border-gray-600">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="proof" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 dark:bg-gray-700 dark:text-indigo-400">
                                            <span>Upload a file</span>
                                            <input id="proof" name="proof" type="file" class="sr-only" required accept="image/*,.pdf"
                                                   onchange="previewFile(this)">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, PDF up to 5MB</p>
                                    
                                    <!-- Image Preview -->
                                    <div id="imagePreview" class="hidden mt-4">
                                        <img id="previewImage" src="" alt="Preview" class="mx-auto max-h-48 rounded-md border border-gray-300">
                                        <p id="fileName" class="mt-2 text-sm text-gray-600"></p>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Upload a clear photo or screenshot of your bank transfer receipt</p>
                        </div>

                        <!-- Instructions -->
                        <div class="bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Important Instructions</h3>
                                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Ensure your payment proof clearly shows the transaction reference number</li>
                                            <li>The amount must match exactly: ₱{{ number_format($order->total_amount, 2) }}</li>
                                            <li>Payment will be reviewed by our admin within 24 hours</li>
                                            <li>You will receive notification once payment is confirmed</li>
                                            <li>Your order will be processed only after payment confirmation</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('sales.orders.show', $order->order_id) }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 transition">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Submit Payment Proof
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewFile(input) {
            const preview = document.getElementById('imagePreview');
            const previewImage = document.getElementById('previewImage');
            const fileName = document.getElementById('fileName');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    if (file.type.includes('image')) {
                        previewImage.src = e.target.result;
                        preview.classList.remove('hidden');
                    } else {
                        preview.classList.add('hidden');
                    }
                    fileName.textContent = file.name;
                }
                
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>
