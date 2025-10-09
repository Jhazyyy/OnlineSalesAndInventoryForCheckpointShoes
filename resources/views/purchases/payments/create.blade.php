<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Record Payment</h2>
                            <p class="text-gray-600 dark:text-gray-400">Record a new payment to a vendor</p>
                        </div>
                        <div>
                            <a href="{{ route('purchases.payments.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Payments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('purchases.payments.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Vendor Selection -->
                            <div>
                                <x-input-label for="supplier_id" :value="__('Vendor')" />
                                <select id="supplier_id" name="supplier_id" required 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Vendor</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_id }}" 
                                                {{ old('supplier_id', $selectedSupplier) == $supplier->supplier_id ? 'selected' : '' }}>
                                            {{ $supplier->supplier_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                            </div>

                            <!-- Purchase Order (Optional) -->
                            <div>
                                <x-input-label for="purchase_order_id" :value="__('Purchase Order (Optional)')" />
                                <select id="purchase_order_id" name="purchase_order_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">No specific order</option>
                                    @foreach($purchaseOrders as $order)
                                        <option value="{{ $order->order_id }}" 
                                                data-supplier="{{ $order->supplier_id }}"
                                                data-amount="{{ $order->total_amount }}"
                                                {{ old('purchase_order_id', $selectedOrder) == $order->order_id ? 'selected' : '' }}>
                                            {{ $order->order_number }} - {{ $order->supplier->supplier_name }} (₱{{ number_format($order->total_amount, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('purchase_order_id')" class="mt-2" />
                            </div>

                            <!-- Payment Amount -->
                            <div>
                                <x-input-label for="amount" :value="__('Payment Amount')" />
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01"
                                        class="pl-7 block w-full" :value="old('amount')" required />
                                </div>
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            </div>

                            <!-- Payment Date -->
                            <div>
                                <x-input-label for="payment_date" :value="__('Payment Date')" />
                                <x-text-input id="payment_date" name="payment_date" type="date" class="mt-1 block w-full"
                                    :value="old('payment_date', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('payment_date')" class="mt-2" />
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <x-input-label for="payment_method" :value="__('Payment Method')" />
                                <select id="payment_method" name="payment_method" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Payment Method</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                    <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online Payment</option>
                                    <option value="gcash" {{ old('payment_method') == 'gcash' ? 'selected' : '' }}>GCash</option>
                                    <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                            </div>

                            <!-- Reference Number -->
                            <div>
                                <x-input-label for="reference_number" :value="__('Reference Number (Optional)')" />
                                <x-text-input id="reference_number" name="reference_number" type="text" class="mt-1 block w-full"
                                    :value="old('reference_number')" placeholder="Check number, transaction ID, etc." />
                                <x-input-error :messages="$errors->get('reference_number')" class="mt-2" />
                            </div>

                            <!-- Payment Mode (Optional) -->
                            <div>
                                <x-input-label for="payment_mode" :value="__('Payment Mode (Optional)')" />
                                <select id="payment_mode" name="payment_mode"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Mode</option>
                                    <option value="cash" {{ old('payment_mode') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="bank_transfer" {{ old('payment_mode') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="check" {{ old('payment_mode') == 'check' ? 'selected' : '' }}>Check</option>
                                    <option value="other" {{ old('payment_mode') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <x-input-error :messages="$errors->get('payment_mode')" class="mt-2" />
                            </div>

                            <!-- Paid By -->
                            <div>
                                <x-input-label for="paid_by" :value="__('Paid By (Optional)')" />
                                <x-text-input id="paid_by" name="paid_by" type="text" class="mt-1 block w-full"
                                    :value="old('paid_by', Auth::user()->name ?? '')" />
                                <x-input-error :messages="$errors->get('paid_by')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" name="notes" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Additional notes about this payment...">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('purchases.payments.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Record Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Filter purchase orders by selected vendor
        document.getElementById('supplier_id').addEventListener('change', function() {
            const supplierId = this.value;
            const orderSelect = document.getElementById('purchase_order_id');
            const options = orderSelect.querySelectorAll('option');
            
            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = '';
                    return;
                }
                
                const orderSupplierId = option.dataset.supplier;
                if (!supplierId || orderSupplierId === supplierId) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
            
            // Reset order selection if current order doesn't match selected vendor
            const currentOption = orderSelect.options[orderSelect.selectedIndex];
            if (currentOption && currentOption.dataset.supplier && currentOption.dataset.supplier !== supplierId) {
                orderSelect.value = '';
            }
        });

        // Pre-fill amount when order is selected
        document.getElementById('purchase_order_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const amount = selectedOption.dataset.amount;
            const amountInput = document.getElementById('amount');
            
            if (amount && !amountInput.value) {
                amountInput.value = parseFloat(amount).toFixed(2);
            }
        });
    </script>
    @endpush
</x-app-layout>
