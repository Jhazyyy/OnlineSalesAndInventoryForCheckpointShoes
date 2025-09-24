<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Record Payment</h2>
                        <a href="{{ route('purchases.payments.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                             <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Payments
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('purchases.payments.store') }}" id="paymentForm">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Vendor Name -->
                            <div>
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Vendor Name <span class="text-red-500">*</span>
                                </label>
                                <select id="supplier_id" name="supplier_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('supplier_id') border-red-500 @enderror">
                                    <option value="">Select Vendor</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_id }}" 
                                                {{ old('supplier_id', $selectedSupplier) == $supplier->supplier_id ? 'selected' : '' }}>
                                            {{ $supplier->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-500 mt-1 inline-block">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    View Vendor Details
                                </a>
                            </div>

                            <!-- Payment # -->
                            <div>
                                <label for="payment_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Payment # <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative">
                                    <input type="text" id="payment_number" name="payment_number" 
                                           value="{{ old('payment_number', 'Auto-generated') }}" 
                                           readonly
                                           class="block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-600 dark:text-white">
                                    <button type="button" class="absolute inset-y-0 right-0 px-3 flex items-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Payment Mode -->
                            <div>
                                <label for="payment_mode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Payment Mode <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 flex gap-2">
                                    <input type="text" id="payment_mode_display" 
                                           value="PH" 
                                           class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                           readonly>
                                    <input type="text" name="bank_charges" 
                                           placeholder="Bank Charges (if any)" 
                                           value="{{ old('bank_charges', '') }}"
                                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <button type="button" class="px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" id="pay_full_amount" 
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                            Pay full amount (₱<span id="full_amount">0.00</span>)
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- Payment Date -->
                            <div>
                                <label for="payment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Payment Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="payment_date" name="payment_date" 
                                       value="{{ old('payment_date', date('Y-m-d')) }}" 
                                       required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('payment_date') border-red-500 @enderror">
                                @error('payment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Mode Dropdown -->
                            <div>
                                <label for="payment_mode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Payment Mode
                                </label>
                                <select id="payment_mode" name="payment_mode" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('payment_mode') border-red-500 @enderror">
                                    <option value="cash" {{ old('payment_mode') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="bank_transfer" {{ old('payment_mode') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="icici_bank" {{ old('payment_mode') == 'icici_bank' ? 'selected' : '' }}>ICICI Bank</option>
                                    <option value="standard_chartered" {{ old('payment_mode') == 'standard_chartered' ? 'selected' : '' }}>Standard Chartered Bank</option>
                                    <option value="yes_bank" {{ old('payment_mode') == 'yes_bank' ? 'selected' : '' }}>YES Bank</option>
                                    <option value="kotak_bank" {{ old('payment_mode') == 'kotak_bank' ? 'selected' : '' }}>Kotak Mahindra Bank</option>
                                    <option value="other" {{ old('payment_mode') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('payment_mode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Paid Through -->
                            <div>
                                <label for="bank_account" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Paid Through <span class="text-red-500">*</span>
                                </label>
                                <select id="bank_account" name="bank_account" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Account</option>
                                    <option value="bank_acc_1">Bank Acc 1 (BDO)</option>
                                    <option value="bank_acc_2">Bank Acc 2 (Landbank)</option>
                                </select>
                            </div>

                            <!-- Reference # -->
                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Reference#
                                </label>
                                <input type="text" id="reference_number" name="reference_number" 
                                       value="{{ old('reference_number') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Amount <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₱</span>
                                    <input type="number" id="amount" name="amount" 
                                           value="{{ old('amount', '') }}"
                                           step="0.01" required placeholder="0.00"
                                           class="pl-8 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('amount') border-red-500 @enderror">
                                </div>
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Payment Method
                                </label>
                                <select id="payment_method" name="payment_method" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                    <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online Payment</option>
                                    <option value="gcash" {{ old('payment_method') == 'gcash' ? 'selected' : '' }}>GCash</option>
                                    <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <!-- Status (Hidden, default to pending) -->
                            <input type="hidden" name="status" value="pending">
                        </div>

                        <!-- Bills Section -->
                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Bills</h3>
                            
                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                                <p class="text-sm text-yellow-800">(As on ) 1 USD = 0 USD <a href="#" class="text-blue-600 hover:underline ml-2">Clear Applied Amount</a></p>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bill#</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">PO#</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bill Amount</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount Due</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Payment</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="billsTableBody">
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                                There are no bills for this vendor.
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <td colspan="5" class="px-6 py-3 text-right font-medium text-gray-900 dark:text-white">Total:</td>
                                            <td class="px-6 py-3 text-right font-medium text-gray-900 dark:text-white">0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Summary Box -->
                            <div class="mt-6 bg-orange-50 border border-orange-200 rounded-lg p-4">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div class="text-right text-gray-600 dark:text-gray-400">Amount Paid:</div>
                                    <div class="font-medium text-gray-900 dark:text-white">0.00</div>
                                    
                                    <div class="text-right text-gray-600 dark:text-gray-400">Amount used for Payments:</div>
                                    <div class="font-medium text-gray-900 dark:text-white">0.00</div>
                                    
                                    <div class="text-right text-gray-600 dark:text-gray-400">Amount Refunded:</div>
                                    <div class="font-medium text-gray-900 dark:text-white">0.00</div>
                                    
                                    <div class="text-right text-gray-600 dark:text-gray-400 flex items-center justify-end">
                                        <svg class="w-4 h-4 mr-1 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        Amount in Excess:
                                    </div>
                                    <div class="font-medium text-gray-900 dark:text-white">₱ 0.00</div>
                                    
                                    <div class="text-right text-gray-600 dark:text-gray-400">Bank Charges:</div>
                                    <div class="font-medium text-gray-900 dark:text-white">₱ 0.00</div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Notes (Internal use. Not visible to vendor)
                            </label>
                            <textarea id="notes" name="notes" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Attachments Section -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Attachments
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload File</span>
                                            <input id="file-upload" name="file-upload" type="file" class="sr-only">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500">You can upload a maximum of 5 files, 5MB each</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Fields Info -->
                        <div class="mt-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Additional Fields: Start adding custom fields for your payments made by going to Settings → Payments Made.
                            </p>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-6 flex items-center justify-end border-t pt-6">                       
                         <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('purchases.payments.index') }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" action="save" 
                                class="inline-flex items-center justify-center px-8 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Save
                            </button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Load bills when supplier is selected
        document.getElementById('supplier_id').addEventListener('change', function() {
            const supplierId = this.value;
            if (supplierId) {
                fetch(`/purchases/payments/supplier/${supplierId}/bills`)
                    .then(response => response.json())
                    .then(bills => {
                        updateBillsTable(bills);
                    });
            }
        });

        function updateBillsTable(bills) {
            const tbody = document.getElementById('billsTableBody');
            if (bills.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">There are no bills for this vendor.</td></tr>';
            } else {
                tbody.innerHTML = bills.map(bill => `
                    <tr>
                        <td class="px-6 py-4 text-sm">${bill.order_date}</td>
                        <td class="px-6 py-4 text-sm">${bill.order_number}</td>
                        <td class="px-6 py-4 text-sm">-</td>
                        <td class="px-6 py-4 text-sm text-right">${bill.total_amount}</td>
                        <td class="px-6 py-4 text-sm text-right">${bill.remaining_amount}</td>
                        <td class="px-6 py-4 text-sm text-right">
                            <input type="number" name="bill_payment[${bill.purchase_order_id}]" 
                                   class="w-24 rounded-md border-gray-300" step="0.01" max="${bill.remaining_amount}">
                        </td>
                    </tr>
                `).join('');
            }
        }
    </script>
    @endpush
</x-app-layout>