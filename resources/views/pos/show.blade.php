<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900 border-l-4 border-green-500 p-4 mb-6 rounded">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-green-700 dark:text-green-300 font-medium">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            <!-- Info Message -->
            @if(session('info'))
            <div class="bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-500 p-4 mb-6 rounded">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-blue-700 dark:text-blue-300 font-medium">{{ session('info') }}</p>
                </div>
            </div>
            @endif

            <!-- Error Messages -->
            @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900 border-l-4 border-red-500 p-4 mb-6 rounded">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-red-700 dark:text-red-300 font-medium">{{ $errors->first() }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Receipt -->
            <div id="receipt" class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-lg overflow-hidden">
                <!-- Receipt Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 text-center">
                    <h1 class="text-3xl font-bold mb-2">CHECKPOINT SHOES</h1>
                    <p class="text-sm opacity-90">Sales Receipt</p>
                    <div class="mt-4 pt-4 border-t border-blue-500">
                        <p class="text-lg font-semibold">Order #{{ $order->order_number }}</p>
                        <p class="text-sm mt-1">{{ $order->order_date->format('F d, Y - h:i A') }}</p>
                    </div>
                </div>

                <!-- Receipt Body -->
                <div class="p-8">
                    <!-- Customer Information -->
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Customer</h3>
                        <div class="space-y-1">
                            <p class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $order->customer->first_name }} {{ $order->customer->last_name }}
                            </p>
                            @if($order->customer->phone)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                {{ $order->customer->phone }}
                            </p>
                            @endif
                            @if($order->customer->email)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                {{ $order->customer->email }}
                            </p>
                            @endif
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-4">Items</h3>
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-xs text-gray-500 dark:text-gray-400 uppercase">
                                    <th class="pb-3">Product</th>
                                    <th class="pb-3 text-center">Qty</th>
                                    <th class="pb-3 text-right">Price</th>
                                    <th class="pb-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @foreach($order->items as $item)
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    <td class="py-3">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->product->sku }}</div>
                                    </td>
                                    <td class="py-3 text-center text-gray-700 dark:text-gray-300">{{ $item->quantity }}</td>
                                    <td class="py-3 text-right text-gray-700 dark:text-gray-300">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="py-3 text-right font-medium text-gray-900 dark:text-white">₱{{ number_format($item->line_total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                                <span class="text-gray-900 dark:text-white">₱{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="flex justify-between text-sm">
                                <div class="flex flex-col">
                                    <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                                    @if($order->discountRule)
                                    <span class="text-xs text-gray-500 dark:text-gray-500">{{ $order->discountRule->name }}</span>
                                    @endif
                                </div>
                                <span class="text-red-600 dark:text-red-400">-₱{{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            <!-- Tax Line - Always Visible -->
                            <div class="flex justify-between text-sm border-t border-gray-300 dark:border-gray-600 pt-2 mt-2">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Tax:</span>
                                    @if($order->taxRule)
                                        <span class="text-xs text-gray-500 dark:text-gray-500">{{ $order->taxRule->name }}</span>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-600">No tax applied</span>
                                    @endif
                                </div>
                                <span class="font-semibold {{ $order->tax_amount > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-500' }}">
                                    ₱{{ number_format($order->tax_amount ?? 0, 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between text-xl font-bold pt-2 border-t border-gray-300 dark:border-gray-600">
                                <span class="text-gray-900 dark:text-white">Total:</span>
                                <span class="text-gray-900 dark:text-white">₱{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Payment Details</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                                <span class="ml-2 font-medium text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                <span class="ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : ($order->payment_status === 'partial' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300') }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </div>
                            @if($amountReceived)
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Amount Received:</span>
                                <span class="ml-2 font-medium text-gray-900 dark:text-white">₱{{ number_format($amountReceived, 2) }}</span>
                            </div>
                            @if($change >= 0)
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Change:</span>
                                <span class="ml-2 font-medium text-green-600 dark:text-green-400">₱{{ number_format($change, 2) }}</span>
                            </div>
                            @else
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Remaining Balance:</span>
                                <span class="ml-2 font-medium text-red-600 dark:text-red-400">₱{{ number_format(abs($change), 2) }}</span>
                            </div>
                            @endif
                            @endif
                        </div>
                        
                        <!-- Bank Transfer Payment Info -->
                        @if($order->payment_method === 'bank_transfer')
                            @php
                                $bankPayment = $order->bankTransferPayments()->latest()->first();
                            @endphp
                            
                            @if($bankPayment)
                                <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900 rounded-md border border-blue-200 dark:border-blue-700">
                                    <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">
                                        Bank Transfer Details
                                    </h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-blue-700 dark:text-blue-300">Bank:</span>
                                            <span class="font-medium text-blue-900 dark:text-blue-200">{{ $bankPayment->bank_name }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-blue-700 dark:text-blue-300">Reference Number:</span>
                                            <span class="font-medium text-blue-900 dark:text-blue-200 font-mono">{{ $bankPayment->reference_no }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-blue-700 dark:text-blue-300">Proof Status:</span>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $bankPayment->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                                                {{ $bankPayment->status === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                                                {{ $bankPayment->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : '' }}">
                                                {{ ucfirst($bankPayment->status) }}
                                            </span>
                                        </div>
                                        @if($bankPayment->proof)
                                        <div class="flex justify-between items-center">
                                            <span class="text-blue-700 dark:text-blue-300">Payment Proof:</span>
                                            <a href="{{ route('admin.bank-transfer-payments.proof', $bankPayment->id) }}" 
                                               target="_blank"
                                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 underline text-xs">
                                                View Receipt
                                            </a>
                                        </div>
                                        @endif
                                        @if($bankPayment->status === 'pending')
                                        <div class="mt-2 pt-2 border-t border-blue-200 dark:border-blue-700">
                                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                                ⏳ Payment proof is pending admin confirmation. Order will be processed once confirmed.
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900 rounded-md border border-yellow-200 dark:border-yellow-700">
                                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                        ℹ️ Awaiting bank transfer proof submission.
                                    </p>
                                </div>
                            @endif
                        @endif

                        <!-- GCash Payment Info -->
                        @if($order->payment_method === 'gcash')
                            @php
                                $gcashPayment = $order->gcashPayments()->latest()->first();
                            @endphp
                            
                            @if($gcashPayment)
                                <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900 rounded-md border border-blue-200 dark:border-blue-700">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                            </svg>
                                            <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200">
                                                GCash Payment
                                            </h4>
                                        </div>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $gcashPayment->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                                            {{ $gcashPayment->status === 'verified' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                                            {{ $gcashPayment->status === 'failed' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : '' }}">
                                            {{ ucfirst($gcashPayment->status) }}
                                        </span>
                                    </div>

                                    <!-- GCash QR Code Display -->
                                    {{-- <div class="mb-4 flex justify-center">
                                        <div class="p-3 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-blue-200 dark:border-blue-700">
                                            <img src="{{ asset('storage/gcash_qr.png') }}" 
                                                 alt="GCash QR Code" 
                                                 class="w-32 h-32 object-contain"
                                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'128\' height=\'128\' viewBox=\'0 0 128 128\'%3E%3Crect width=\'128\' height=\'128\' fill=\'%23f3f4f6\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'monospace\' font-size=\'12\' fill=\'%236b7280\'%3EGCASH%3C/text%3E%3C/svg%3E'">
                                            <p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                Paid via GCash
                                            </p>
                                        </div>
                                    </div> --}}

                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-blue-700 dark:text-blue-300">Reference Number:</span>
                                            <span class="font-medium text-blue-900 dark:text-blue-200 font-mono">{{ $gcashPayment->reference_number }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-blue-700 dark:text-blue-300">Amount:</span>
                                            <span class="font-medium text-blue-900 dark:text-blue-200">₱{{ number_format($gcashPayment->amount, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-blue-700 dark:text-blue-300">Payment Date:</span>
                                            <span class="font-medium text-blue-900 dark:text-blue-200">{{ $gcashPayment->payment_date?->format('M d, Y h:i A') ?? 'N/A' }}</span>
                                        </div>
                                        @if($gcashPayment->status === 'verified' && $gcashPayment->verified_at)
                                        <div class="mt-2 pt-2 border-t border-blue-200 dark:border-blue-700">
                                            <p class="text-xs text-green-700 dark:text-green-300">
                                                ✓ Payment verified on {{ $gcashPayment->verified_at->format('M d, Y h:i A') }}
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Payment Update Form (for partial/pending payments) -->
                    @if(in_array($order->payment_status, ['partial', 'pending']))
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700 print:hidden">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-400 uppercase mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Update Payment
                            </h3>
                            <form action="{{ route('pos.complete-payment', $order->order_id) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                
                                <div>
                                    <label for="additional_payment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Additional Payment Amount
                                    </label>
                                    <input type="number" 
                                           id="additional_payment" 
                                           name="additional_payment" 
                                           step="0.01" 
                                           min="0.01"
                                           max="{{ $order->total_amount - ($order->amount_received ?? 0) }}"
                                           value="{{ $order->total_amount - ($order->amount_received ?? 0) }}"
                                           required
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Remaining balance: ₱{{ number_format($order->total_amount - ($order->amount_received ?? 0), 2) }}
                                    </p>
                                </div>

                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Payment Method (Optional)
                                    </label>
                                    <select id="payment_method" 
                                            name="payment_method"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Keep current ({{ ucfirst(str_replace('_', ' ', $order->payment_method)) }})</option>
                                        <option value="cash">Cash</option>
                                        <option value="gcash">GCash</option>
                                        <option value="card">Card</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="check">Check</option>
                                        <option value="online">Online Payment</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <button type="submit" 
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Update Payment
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    <!-- Order Status -->
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center px-4 py-2 bg-green-100 dark:bg-green-900 rounded-lg">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm font-semibold text-green-800 dark:text-green-300">
                                Order Status: {{ $order->status === 'delivered' ? 'Completed' : ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="text-center text-xs text-gray-500 dark:text-gray-400 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <p>Thank you for your purchase!</p>
                        <p class="mt-1">For inquiries, please contact us at +639603316595 or visit our Facebook Page.</p>
                        <p class="mt-2">Printed: {{ now()->format('F d, Y - h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex flex-wrap gap-3 justify-center print:hidden">
                <button onclick="window.print()" 
                        class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Receipt
                </button>
                <a href="{{ route('pos.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Sale
                </a>
                <a href="{{ route('pos.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Sales History
                </a>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #receipt, #receipt * {
                visibility: visible;
            }
            #receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .print\:hidden {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
