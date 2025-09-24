<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Payment Details</h2>
                        <div class="flex space-x-3">
                            @if($payment->canBeEdited())
                                <a href="{{ route('purchases.payments.edit', $payment->payment_id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Edit Payment
                                </a>
                            @endif
                            <a href="{{ route('purchases.payments.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Back to Payments
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Information</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Number</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $payment->payment_number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Date</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $payment->payment_date->format('M d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Amount</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">${{ number_format($payment->amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unused Amount</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">${{ number_format($payment->unused_amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bank Charges</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">${{ number_format($payment->bank_charges, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                    <dd><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payment->status_badge_class }}">
                                        {{ ucfirst($payment->status) }}
                                    </span></dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Vendor Information</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Vendor Name</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $payment->supplier->company_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Mode</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $payment->payment_mode_display }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $payment->payment_method_display }}</dd>
                                </div>
                                @if($payment->reference_number)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reference Number</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $payment->reference_number }}</dd>
                                </div>
                                @endif
                                @if($payment->bill_number)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bill Number</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $payment->bill_number }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    @if($payment->notes)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Notes</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $payment->notes }}</p>
                    </div>
                    @endif

                    <!-- Related Bills -->
                    @if($relatedBills && $relatedBills->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Related Purchase Orders</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Payment Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($relatedBills as $bill)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <a href="{{ route('purchases.purchase-orders.show', $bill->purchase_order_id) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ $bill->order_number }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $bill->order_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            ${{ number_format($bill->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $bill->payment_status == 'paid' ? 'bg-green-100 text-green-800' : ($bill->payment_status == 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($bill->payment_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="mt-6 flex space-x-3">
                        @if($payment->status === 'pending')
                            <form method="POST" action="{{ route('purchases.payments.mark-completed', $payment->payment_id) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Mark as Completed
                                </button>
                            </form>
                        @endif

                        @if($payment->canBeCancelled())
                            <form method="POST" action="{{ route('purchases.payments.mark-cancelled', $payment->payment_id) }}" class="inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Are you sure you want to cancel this payment?')" 
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cancel Payment
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>