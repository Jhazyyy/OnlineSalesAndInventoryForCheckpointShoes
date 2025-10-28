<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Payment {{ $purchasePayment->payment_number }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">Payment details and information</p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
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

            <!-- Payment Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Payment Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Number</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $purchasePayment->payment_number }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Amount</label>
                            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">₱{{ number_format($purchasePayment->amount, 2) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                            <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $purchasePayment->status_badge_class }}">
                                {{ ucfirst($purchasePayment->status) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Date</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ optional($purchasePayment->payment_date)->format('M d, Y') ?? '—' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $purchasePayment->payment_method_display }}</p>
                        </div>

                        @if($purchasePayment->reference_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Reference Number</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $purchasePayment->reference_number }}</p>
                        </div>
                        @endif

                        @if($purchasePayment->payment_mode)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Mode</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $purchasePayment->payment_mode_display }}</p>
                        </div>
                        @endif

                        @if($purchasePayment->paid_by)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Received By</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $purchasePayment->paid_by }}</p>
                        </div>
                        @endif

                        @if($purchasePayment->bank_account)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Bank Account</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $purchasePayment->bank_account }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Vendor Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Vendor Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Vendor Name</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="{{ route('master_data.suppliers.show', $purchasePayment->supplier->supplier_id) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $purchasePayment->supplier->supplier_name }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $purchasePayment->supplier->email ?? 'N/A' }}</p>
                        </div>

                        @if($purchasePayment->supplier->phone_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $purchasePayment->supplier->phone_number }}</p>
                        </div>
                        @endif

                        @if($purchasePayment->supplier->company_name)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Company</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $purchasePayment->supplier->company_name }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Purchase Order Information (if linked) -->
            @if($purchasePayment->purchaseOrder)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Related Purchase Order</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Order Number</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="{{ route('purchases.purchase-orders.show', $purchasePayment->purchaseOrder->order_id) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $purchasePayment->purchaseOrder->order_number }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Order Date</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $purchasePayment->purchaseOrder->order_date->format('M d, Y') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Order Total</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">₱{{ number_format($purchasePayment->purchaseOrder->total_amount, 2) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Order Status</label>
                            <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $purchasePayment->purchaseOrder->status_badge_class }}">
                                {{ ucfirst($purchasePayment->purchaseOrder->status) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Status</label>
                            <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $purchasePayment->purchaseOrder->payment_status_badge_class }}">
                                {{ ucfirst($purchasePayment->purchaseOrder->payment_status) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Total Paid</label>
                            <p class="mt-1 text-sm font-semibold text-green-600">₱{{ number_format($purchasePayment->purchaseOrder->paid_amount, 2) }}</p>
                        </div>

                        @if($purchasePayment->purchaseOrder->payment_status === 'partial')
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Remaining Balance</label>
                            <p class="mt-1 text-sm font-semibold text-orange-600">₱{{ number_format($purchasePayment->purchaseOrder->remaining_balance, 2) }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($purchasePayment->notes)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Notes</h3>
                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $purchasePayment->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Actions</h3>
                    
                    <div class="flex flex-wrap gap-3">
                        @if($purchasePayment->status === 'pending')
                            <form method="POST" action="{{ route('purchases.payments.mark-completed', $purchasePayment->payment_id) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Mark as Completed
                                </button>
                            </form>
                        @endif

                        @if($purchasePayment->canBeCancelled())
                            <form method="POST" action="{{ route('purchases.payments.mark-cancelled', $purchasePayment->payment_id) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        onclick="return confirm('Are you sure you want to cancel this payment?')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancel Payment
                                </button>
                            </form>
                        @endif

                        @if($purchasePayment->canBeEdited())
                            <a href="{{ route('purchases.payments.edit', $purchasePayment->payment_id) }}" 
                              class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Payment
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
