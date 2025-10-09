<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Payment</h2>
                        <a href="{{ route('purchases.payments.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Back to Payments
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('purchases.payments.update', $purchasePayment->payment_id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Vendor Name -->
                            <div>
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Vendor Name <span class="text-red-500">*</span>
                                </label>
                                <select id="supplier_id" name="supplier_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('supplier_id') border-red-500 @enderror">
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_id }}" 
                                                {{ old('supplier_id', $purchasePayment->supplier_id) == $supplier->supplier_id ? 'selected' : '' }}>
                                            {{ $supplier->supplier_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment # -->
                            <div>
                                <label for="payment_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Payment #
                                </label>
                                <input type="text" id="payment_number" 
                                       value="{{ $purchasePayment->payment_number }}" 
                                       readonly
                                       class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-600 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Payment Date -->
                            <div>
                                <label for="payment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Payment Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="payment_date" name="payment_date" 
                                       value="{{ old('payment_date', $purchasePayment->payment_date->format('Y-m-d')) }}" 
                                       required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('payment_date') border-red-500 @enderror">
                                @error('payment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Amount <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₱</span>
                                    <input type="number" id="amount" name="amount" 
                                           value="{{ old('amount', $purchasePayment->amount) }}"
                                           step="0.01"
                                           required
                                           class="pl-8 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('amount') border-red-500 @enderror">
                                </div>
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bank Charges -->
                            <div>
                                <label for="bank_charges" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Bank Charges
                                </label>
                                <div class="mt-1 relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₱</span>
                                    <input type="number" id="bank_charges" name="bank_charges" 
                                           value="{{ old('bank_charges', $purchasePayment->bank_charges) }}"
                                           step="0.01"
                                           class="pl-8 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>

                            <!-- Payment Mode -->
                            <div>
                                <label for="payment_mode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Payment Mode
                                </label>
                                <select id="payment_mode" name="payment_mode" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="cash" {{ old('payment_mode', $purchasePayment->payment_mode) == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="bank_transfer" {{ old('payment_mode', $purchasePayment->payment_mode) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="icici_bank" {{ old('payment_mode', $purchasePayment->payment_mode) == 'icici_bank' ? 'selected' : '' }}>ICICI Bank</option>
                                    <option value="standard_chartered" {{ old('payment_mode', $purchasePayment->payment_mode) == 'standard_chartered' ? 'selected' : '' }}>Standard Chartered Bank</option>
                                    <option value="yes_bank" {{ old('payment_mode', $purchasePayment->payment_mode) == 'yes_bank' ? 'selected' : '' }}>YES Bank</option>
                                    <option value="kotak_bank" {{ old('payment_mode', $purchasePayment->payment_mode) == 'kotak_bank' ? 'selected' : '' }}>Kotak Mahindra Bank</option>
                                    <option value="other" {{ old('payment_mode', $purchasePayment->payment_mode) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Payment Method
                                </label>
                                <select id="payment_method" name="payment_method" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="cash" {{ old('payment_method', $purchasePayment->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="card" {{ old('payment_method', $purchasePayment->payment_method) == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                    <option value="bank_transfer" {{ old('payment_method', $purchasePayment->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="check" {{ old('payment_method', $purchasePayment->payment_method) == 'check' ? 'selected' : '' }}>Check</option>
                                    <option value="online" {{ old('payment_method', $purchasePayment->payment_method) == 'online' ? 'selected' : '' }}>Online Payment</option>
                                    <option value="gcash" {{ old('payment_method', $purchasePayment->payment_method) == 'gcash' ? 'selected' : '' }}>GCash</option>
                                    <option value="other" {{ old('payment_method', $purchasePayment->payment_method) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <!-- Reference # -->
                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Reference#
                                </label>
                                <input type="text" id="reference_number" name="reference_number" 
                                       value="{{ old('reference_number', $purchasePayment->reference_number) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Bill Number -->
                            <div>
                                <label for="bill_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Bill Number
                                </label>
                                <input type="text" id="bill_number" name="bill_number" 
                                       value="{{ old('bill_number', $purchasePayment->bill_number) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Status
                                </label>
                                <select id="status" name="status" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="pending" {{ old('status', $purchasePayment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ old('status', $purchasePayment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $purchasePayment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="refunded" {{ old('status', $purchasePayment->status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Notes (Internal use. Not visible to vendor)
                            </label>
                            <textarea id="notes" name="notes" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes', $purchasePayment->notes) }}</textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-6 flex items-center justify-between border-t pt-6">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Update Payment
                            </button>
                            <a href="{{ route('purchases.payments.show', $purchasePayment->payment_id) }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>