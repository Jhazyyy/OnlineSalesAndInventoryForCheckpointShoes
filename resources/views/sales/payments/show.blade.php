<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Payment {{ $payment->payment_number }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">Payment details and information</p>
                        </div>
                        <div class="flex space-x-3 mt-4 sm:mt-0">
                            <a href="{{ route('sales.payments.index') }}"
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
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->payment_number }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Amount</label>
                            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">₱{{ number_format($payment->amount, 2) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                            <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payment->status_badge_class }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Date</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->payment_date->format('M d, Y') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->payment_method_display }}</p>
                        </div>

                        @if($payment->reference_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Reference Number</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->reference_number }}</p>
                        </div>
                        @endif

                        @if($payment->received_by)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Received By</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->received_by }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Customer Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Customer Name</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="{{ route('sales.customers.show', $payment->customer->customer_id) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $payment->customer->first_name }} {{ $payment->customer->last_name }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->customer->email }}</p>
                        </div>

                        @if($payment->customer->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->customer->phone }}</p>
                        </div>
                        @endif

                        @if($payment->customer->company_name)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Company</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->customer->company_name }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sales Order Information (if linked) -->
            @if($payment->salesOrder)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Related Sales Order</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Order Number</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="{{ route('sales.orders.show', $payment->salesOrder->order_id) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $payment->salesOrder->order_number }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Order Date</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $payment->salesOrder->order_date->format('M d, Y') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Order Total</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">₱{{ number_format($payment->salesOrder->total_amount, 2) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Order Status</label>
                            <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payment->salesOrder->status_badge_class }}">
                                {{ ucfirst($payment->salesOrder->status) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Status</label>
                            <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payment->salesOrder->payment_status_badge_class }}">
                                {{ ucfirst($payment->salesOrder->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($payment->notes)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Notes</h3>
                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $payment->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Actions</h3>
                    
                    <div class="flex flex-wrap gap-3">
                        @if($payment->status === 'pending')
                            <form method="POST" action="{{ route('sales.payments.mark-completed', $payment->payment_id) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Mark as Completed
                                </button>
                            </form>
                        @endif

                        @if($payment->canBeCancelled())
                            <form method="POST" action="{{ route('sales.payments.mark-cancelled', $payment->payment_id) }}" class="inline">
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

                        @if($payment->canBeEdited())
                            <a href="{{ route('sales.payments.edit', $payment->payment_id) }}" 
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
