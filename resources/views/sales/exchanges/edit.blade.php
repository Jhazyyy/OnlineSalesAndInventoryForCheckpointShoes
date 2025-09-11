<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center">
                            <!-- Exchange Icon -->
                            <div class="mr-3">
                                {!! App\Helpers\NavigationHelper::getIcon('exchange', 'w-8 h-8 text-blue-600') !!}
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Exchange {{ $exchange->exchange_number }}</h2>
                                <p class="text-gray-600 dark:text-gray-400">Update exchange information and settings</p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('sales.exchanges.show', $exchange) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Exchange
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Exchange Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('sales.exchanges.update', $exchange) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Exchange Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Customer Selection -->
                                <div>
                                    <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Customer <span class="text-red-500">*</span>
                                    </label>
                                    <select id="customer_id" name="customer_id" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">Select a customer...</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->customer_id }}" 
                                                    {{ old('customer_id', $exchange->customer_id) == $customer->customer_id ? 'selected' : '' }}>
                                                {{ $customer->customer_name }} - {{ $customer->email ?? $customer->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Sales Order (Optional) -->
                                <div>
                                    <label for="sales_order_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Related Sales Order (Optional)
                                    </label>
                                    <input type="text" id="sales_order_display" 
                                           value="{{ $exchange->salesOrder->order_number ?? 'No sales order linked' }}" 
                                           readonly placeholder="No sales order linked"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 dark:bg-gray-600 dark:border-gray-600 dark:text-white">
                                    <input type="hidden" id="sales_order_id" name="sales_order_id" 
                                           value="{{ old('sales_order_id', $exchange->sales_order_id) }}">
                                </div>

                                <!-- Exchange Type -->
                                <div>
                                    <label for="exchange_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Exchange Type <span class="text-red-500">*</span>
                                    </label>
                                    <select id="exchange_type" name="exchange_type" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        @foreach($types as $type)
                                            <option value="{{ $type }}" 
                                                    {{ old('exchange_type', $exchange->exchange_type) == $type ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $type)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('exchange_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Exchange Date -->
                                <div>
                                    <label for="exchange_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Exchange Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="exchange_date" name="exchange_date" 
                                           value="{{ old('exchange_date', $exchange->exchange_date->format('Y-m-d')) }}" required
                                           max="{{ now()->toDateString() }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @error('exchange_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Requested Completion Date -->
                                <div>
                                    <label for="requested_completion_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Requested Completion Date
                                    </label>
                                    <input type="date" id="requested_completion_date" name="requested_completion_date" 
                                           value="{{ old('requested_completion_date', $exchange->requested_completion_date?->format('Y-m-d')) }}"
                                           min="{{ now()->toDateString() }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @error('requested_completion_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select id="status" name="status" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" 
                                                    {{ old('status', $exchange->status) == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Notes & Details</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Reason -->
                                <div>
                                    <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Exchange Reason
                                    </label>
                                    <textarea id="reason" name="reason" rows="4" 
                                              placeholder="Enter the reason for this exchange..."
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('reason', $exchange->reason) }}</textarea>
                                    @error('reason')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Public Notes
                                    </label>
                                    <textarea id="notes" name="notes" rows="4" 
                                              placeholder="Enter public notes for this exchange..."
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes', $exchange->notes) }}</textarea>
                                    @error('notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Internal Notes -->
                            <div class="mt-6">
                                <label for="internal_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Internal Notes
                                </label>
                                <textarea id="internal_notes" name="internal_notes" rows="3" 
                                          placeholder="Enter internal notes (not visible to customer)..."
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('internal_notes', $exchange->internal_notes) }}</textarea>
                                @error('internal_notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Items Summary (Read-only) -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Current Exchange Items</h3>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-red-800 dark:text-red-200">Original Total</h4>
                                        <p class="text-2xl font-bold text-red-900 dark:text-red-100">₱{{ number_format($exchange->original_total_amount, 2) }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $exchange->originalItems->count() }} item(s) being returned</p>
                                    </div>
                                    
                                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-green-800 dark:text-green-200">New Total</h4>
                                        <p class="text-2xl font-bold text-green-900 dark:text-green-100">₱{{ number_format($exchange->new_total_amount, 2) }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $exchange->newItems->count() }} item(s) being given</p>
                                    </div>
                                    
                                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Difference</h4>
                                        <p class="text-2xl font-bold 
                                            @if($exchange->difference_amount >= 0) text-red-900 dark:text-red-100 
                                            @else text-green-900 dark:text-green-100 @endif">
                                            ₱{{ number_format(abs($exchange->difference_amount), 2) }}
                                        </p>
                                        <p class="text-sm 
                                            @if($exchange->difference_amount > 0) text-red-600 dark:text-red-400 
                                            @elseif($exchange->difference_amount < 0) text-green-600 dark:text-green-400 
                                            @else text-gray-600 dark:text-gray-400 @endif mt-1">
                                            @if($exchange->difference_amount > 0)
                                                Customer owes additional
                                            @elseif($exchange->difference_amount < 0)
                                                Customer receives refund
                                            @else
                                                No difference
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <strong>Note:</strong> To modify exchange items, please create a new exchange. 
                                        This form only allows updating exchange details and status.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('sales.exchanges.show', $exchange) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Exchange
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
