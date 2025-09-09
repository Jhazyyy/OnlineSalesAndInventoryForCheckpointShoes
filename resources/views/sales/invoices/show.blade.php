<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Invoice {{ $invoice->invoice_number }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">View and manage invoice details</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            @if($invoice->canBeEdited())
                                <a href="{{ route('sales.invoices.edit', $invoice->invoice_id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Edit Invoice
                                </a>
                            @endif
                            @if($invoice->canBeSent())
                                <form method="POST" action="{{ route('sales.invoices.mark-as-sent', $invoice->invoice_id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Mark as Sent
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('sales.invoices.duplicate', $invoice->invoice_id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Duplicate
                            </a>
                            <a href="{{ route('sales.invoices.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Back to List
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Invoice Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Invoice Info -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Invoice Information</h3>
                                    <dl class="space-y-2">
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600 dark:text-gray-400">Invoice Number:</dt>
                                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600 dark:text-gray-400">Invoice Date:</dt>
                                            <dd class="text-sm text-gray-900 dark:text-white">{{ $invoice->invoice_date->format('M d, Y') }}</dd>
                                        </div>
                                        @if($invoice->due_date)
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-600 dark:text-gray-400">Due Date:</dt>
                                                <dd class="text-sm text-gray-900 dark:text-white {{ $invoice->is_overdue ? 'text-red-600' : '' }}">
                                                    {{ $invoice->due_date->format('M d, Y') }}
                                                    @if($invoice->is_overdue)
                                                        ({{ abs($invoice->days_until_due) }} days overdue)
                                                    @elseif($invoice->days_until_due !== null && $invoice->days_until_due < 7)
                                                        ({{ $invoice->days_until_due }} days left)
                                                    @endif
                                                </dd>
                                            </div>
                                        @endif
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600 dark:text-gray-400">Status:</dt>
                                            <dd><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $invoice->status_badge_class }}">{{ ucfirst($invoice->status) }}</span></dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600 dark:text-gray-400">Payment Status:</dt>
                                            <dd><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $invoice->payment_status_badge_class }}">{{ ucfirst(str_replace('_', ' ', $invoice->payment_status)) }}</span></dd>
                                        </div>
                                        @if($invoice->salesOrder)
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-600 dark:text-gray-400">Sales Order:</dt>
                                                <dd class="text-sm text-gray-900 dark:text-white">
                                                    <a href="{{ route('sales.orders.show', $invoice->salesOrder->order_id) }}" class="text-blue-600 hover:text-blue-900">
                                                        {{ $invoice->salesOrder->order_number }}
                                                    </a>
                                                </dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>

                                <!-- Customer Info -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Customer Information</h3>
                                    <dl class="space-y-2">
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600 dark:text-gray-400">Name:</dt>
                                            <dd class="text-sm text-gray-900 dark:text-white">{{ $invoice->customer->display_name }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600 dark:text-gray-400">Email:</dt>
                                            <dd class="text-sm text-gray-900 dark:text-white">{{ $invoice->customer->email }}</dd>
                                        </div>
                                        @if($invoice->customer->phone)
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-600 dark:text-gray-400">Phone:</dt>
                                                <dd class="text-sm text-gray-900 dark:text-white">{{ $invoice->customer->phone }}</dd>
                                            </div>
                                        @endif
                                        @if($invoice->billing_address)
                                            <div>
                                                <dt class="text-sm text-gray-600 dark:text-gray-400 mb-1">Billing Address:</dt>
                                                <dd class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $invoice->billing_address }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>

                            <!-- Invoice Items -->
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Invoice Items</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit Price</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Discount</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($invoice->items as $item)
                                                <tr>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->product->product_name }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $item->product->product_brand }}</div>
                                                        @if($item->notes)
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $item->notes }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->quantity }}</td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">₱{{ number_format($item->unit_price, 2) }}</td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">₱{{ number_format($item->discount_amount, 2) }}</td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">₱{{ number_format($item->line_total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Invoice Totals -->
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                                <div class="flex justify-end">
                                    <div class="w-64">
                                        <dl class="space-y-2">
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-600 dark:text-gray-400">Subtotal:</dt>
                                                <dd class="text-sm text-gray-900 dark:text-white">₱{{ number_format($invoice->subtotal, 2) }}</dd>
                                            </div>
                                            @if($invoice->tax_amount > 0)
                                                <div class="flex justify-between">
                                                    <dt class="text-sm text-gray-600 dark:text-gray-400">Tax:</dt>
                                                    <dd class="text-sm text-gray-900 dark:text-white">₱{{ number_format($invoice->tax_amount, 2) }}</dd>
                                                </div>
                                            @endif
                                            @if($invoice->discount_amount > 0)
                                                <div class="flex justify-between">
                                                    <dt class="text-sm text-gray-600 dark:text-gray-400">Discount:</dt>
                                                    <dd class="text-sm text-gray-900 dark:text-white">-₱{{ number_format($invoice->discount_amount, 2) }}</dd>
                                                </div>
                                            @endif
                                            <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-2">
                                                <dt class="text-base font-medium text-gray-900 dark:text-white">Total:</dt>
                                                <dd class="text-base font-medium text-gray-900 dark:text-white">₱{{ number_format($invoice->total_amount, 2) }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-600 dark:text-gray-400">Paid:</dt>
                                                <dd class="text-sm text-green-600">₱{{ number_format($invoice->paid_amount, 2) }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-sm font-medium text-gray-900 dark:text-white">Balance:</dt>
                                                <dd class="text-sm font-medium {{ $invoice->remaining_balance > 0 ? ($invoice->is_overdue ? 'text-red-600' : 'text-yellow-600') : 'text-green-600' }}">
                                                    ₱{{ number_format($invoice->remaining_balance, 2) }}
                                                </dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>
                            </div>

                            @if($invoice->notes)
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Notes</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $invoice->notes }}</p>
                                </div>
                            @endif

                            @if($invoice->terms_conditions)
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Terms & Conditions</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $invoice->terms_conditions }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar Actions -->
                <div class="space-y-6">
                    <!-- Payment Actions -->
                    @if($invoice->canRecordPayment())
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Record Payment</h3>
                                <form method="POST" action="{{ route('sales.invoices.record-payment', $invoice->invoice_id) }}">
                                    @csrf
                                    <div class="space-y-4">
                                        <div>
                                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Amount</label>
                                            <input type="number" id="amount" name="amount" step="0.01" 
                                                   max="{{ $invoice->remaining_balance }}" 
                                                   value="{{ $invoice->remaining_balance }}"
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        </div>
                                        <div>
                                            <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Method</label>
                                            <select id="payment_method" name="payment_method" 
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                <option value="cash">Cash</option>
                                                <option value="card">Card</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="check">Check</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="payment_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes (Optional)</label>
                                            <textarea id="payment_notes" name="payment_notes" rows="3"
                                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                                        </div>
                                        <button type="submit" 
                                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Record Payment
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Status Actions -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Actions</h3>
                            <div class="space-y-2">
                                @if($invoice->canBeSent())
                                    <form method="POST" action="{{ route('sales.invoices.mark-as-sent', $invoice->invoice_id) }}">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Mark as Sent
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('sales.invoices.duplicate', $invoice->invoice_id) }}" 
                                   class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Duplicate Invoice
                                </a>

                                @if($invoice->canBeCancelled())
                                    <form method="POST" action="{{ route('sales.invoices.change-status', $invoice->invoice_id) }}" 
                                          onsubmit="return confirm('Are you sure you want to cancel this invoice?')">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Cancel Invoice
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Invoice Summary</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600 dark:text-gray-400">Total Items:</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $invoice->total_products }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600 dark:text-gray-400">Total Quantity:</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $invoice->total_quantity }}</dd>
                                </div>
                                @if($invoice->payment_date)
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600 dark:text-gray-400">Payment Date:</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ $invoice->payment_date->format('M d, Y') }}</dd>
                                    </div>
                                @endif
                                @if($invoice->payment_method)
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600 dark:text-gray-400">Payment Method:</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}</dd>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600 dark:text-gray-400">Created:</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $invoice->created_at->format('M d, Y g:i A') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600 dark:text-gray-400">Updated:</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">{{ $invoice->updated_at->format('M d, Y g:i A') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
