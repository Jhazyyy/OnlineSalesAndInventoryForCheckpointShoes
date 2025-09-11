<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $exchange->exchange_number }}</h2>
                                <p class="text-gray-600 dark:text-gray-400">View exchange details and manage status</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('sales.exchanges.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Exchanges
                            </a>
                            
                            @if(in_array($exchange->status, ['pending', 'approved']))
                                <a href="{{ route('sales.exchanges.edit', $exchange) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                            @endif
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
                <!-- Exchange Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Exchange Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Exchange Number</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white font-mono">
                                            {{ $exchange->exchange_number }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer</label>
                                        <div class="mt-1">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $exchange->customer->customer_name ?? 'N/A' }}
                                            </div>
                                            @if($exchange->customer)
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $exchange->customer->email ?? $exchange->customer->phone }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Exchange Type</label>
                                        <div class="mt-1">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($exchange->exchange_type === 'product_exchange') bg-blue-100 text-blue-800 
                                                @elseif($exchange->exchange_type === 'refund_exchange') bg-red-100 text-red-800 
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $exchange->exchange_type)) }}
                                            </span>
                                        </div>
                                    </div>

                                    @if($exchange->salesOrder)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Related Sales Order</label>
                                            <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                                <a href="{{ route('sales.orders.show', $exchange->salesOrder) }}" class="text-blue-600 hover:text-blue-800">
                                                    {{ $exchange->salesOrder->order_number }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <div class="mt-1">
                                            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                                @if($exchange->status === 'pending') bg-yellow-100 text-yellow-800 
                                                @elseif($exchange->status === 'approved') bg-green-100 text-green-800 
                                                @elseif($exchange->status === 'processing') bg-blue-100 text-blue-800 
                                                @elseif($exchange->status === 'completed') bg-green-100 text-green-800 
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($exchange->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Exchange Date</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $exchange->exchange_date->format('F j, Y') }}
                                        </div>
                                    </div>

                                    @if($exchange->requested_completion_date)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Requested Completion</label>
                                            <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $exchange->requested_completion_date->format('F j, Y') }}
                                            </div>
                                        </div>
                                    @endif

                                    @if($exchange->actual_completion_date)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Actual Completion</label>
                                            <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $exchange->actual_completion_date->format('F j, Y') }}
                                            </div>
                                        </div>
                                    @endif

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $exchange->created_at->format('F j, Y g:i A') }}
                                        </div>
                                    </div>

                                    @if($exchange->updated_at != $exchange->created_at)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Updated</label>
                                            <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $exchange->updated_at->format('F j, Y g:i A') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if($exchange->reason || $exchange->notes || $exchange->internal_notes)
                                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    @if($exchange->reason)
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason</label>
                                            <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $exchange->reason }}
                                            </div>
                                        </div>
                                    @endif

                                    @if($exchange->notes)
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Public Notes</label>
                                            <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $exchange->notes }}
                                            </div>
                                        </div>
                                    @endif

                                    @if($exchange->internal_notes)
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Internal Notes</label>
                                            <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $exchange->internal_notes }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Original Items (Being Returned) -->
                    @if($exchange->originalItems->count() > 0)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Original Items (Being Returned)</h3>
                                
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit Price</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condition</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($exchange->originalItems as $item)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $item->product->product_name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            SKU: {{ $item->product->sku }}
                                                        </div>
                                                        @if($item->notes)
                                                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                                {{ $item->notes }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        {{ number_format($item->quantity) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        ₱{{ number_format($item->unit_price, 2) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if($item->condition)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                @if($item->condition === 'new') bg-green-100 text-green-800
                                                                @elseif($item->condition === 'good') bg-blue-100 text-blue-800
                                                                @elseif($item->condition === 'fair') bg-yellow-100 text-yellow-800
                                                                @elseif($item->condition === 'poor') bg-orange-100 text-orange-800
                                                                @else bg-red-100 text-red-800 @endif">
                                                                {{ ucfirst($item->condition) }}
                                                            </span>
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                        ₱{{ number_format($item->total_price, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">Original Total:</td>
                                                <td class="px-6 py-3 text-sm font-bold text-gray-900 dark:text-white">₱{{ number_format($exchange->original_total_amount, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- New Items (Being Given) -->
                    @if($exchange->newItems->count() > 0)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">New Items (Being Given)</h3>
                                
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit Price</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condition</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($exchange->newItems as $item)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $item->product->product_name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            SKU: {{ $item->product->sku }}
                                                        </div>
                                                        @if($item->notes)
                                                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                                {{ $item->notes }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        {{ number_format($item->quantity) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        ₱{{ number_format($item->unit_price, 2) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if($item->condition)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                @if($item->condition === 'new') bg-green-100 text-green-800
                                                                @elseif($item->condition === 'refurbished') bg-blue-100 text-blue-800
                                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                                {{ ucfirst(str_replace('_', ' ', $item->condition)) }}
                                                            </span>
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                        ₱{{ number_format($item->total_price, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">New Total:</td>
                                                <td class="px-6 py-3 text-sm font-bold text-gray-900 dark:text-white">₱{{ number_format($exchange->new_total_amount, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Exchange Summary -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Exchange Summary</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-red-800 dark:text-red-200">Original Total</h4>
                                    <p class="text-2xl font-bold text-red-900 dark:text-red-100">₱{{ number_format($exchange->original_total_amount, 2) }}</p>
                                </div>
                                
                                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-green-800 dark:text-green-200">New Total</h4>
                                    <p class="text-2xl font-bold text-green-900 dark:text-green-100">₱{{ number_format($exchange->new_total_amount, 2) }}</p>
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
                                            Customer owes additional payment
                                        @elseif($exchange->difference_amount < 0)
                                            Customer receives refund
                                        @else
                                            No difference
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Panel -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Actions</h3>
                            
                            <div class="space-y-4">
                                @if($exchange->isPending())
                                    <form method="POST" action="{{ route('sales.exchanges.approve', $exchange) }}" class="w-full">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to approve this exchange?')"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Approve Exchange
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('sales.exchanges.cancel', $exchange) }}" class="w-full">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to cancel this exchange?')"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Cancel Exchange
                                        </button>
                                    </form>
                                @elseif($exchange->isApproved())
                                    <form method="POST" action="{{ route('sales.exchanges.start-processing', $exchange) }}" class="w-full">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to start processing this exchange?')"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Start Processing
                                        </button>
                                    </form>
                                @elseif($exchange->isProcessing())
                                    <form method="POST" action="{{ route('sales.exchanges.complete', $exchange) }}" class="w-full">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to complete this exchange?')"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Complete Exchange
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($exchange->status, ['pending', 'cancelled']))
                                    <form method="POST" action="{{ route('sales.exchanges.destroy', $exchange) }}" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to delete this exchange? This action cannot be undone.')"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete Exchange
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <!-- Status Information -->
                            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Status Information</h4>
                                <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                                    @switch($exchange->status)
                                        @case('pending')
                                            <p>• This exchange is awaiting approval</p>
                                            <p>• You can edit, approve, or cancel this exchange</p>
                                            @break
                                        @case('approved')
                                            <p>• This exchange has been approved</p>
                                            <p>• Ready to start processing</p>
                                            @break
                                        @case('processing')
                                            <p>• This exchange is currently being processed</p>
                                            <p>• Items are being prepared for exchange</p>
                                            @break
                                        @case('completed')
                                            <p>• This exchange has been completed</p>
                                            <p>• All items have been exchanged successfully</p>
                                            @if($exchange->difference_amount != 0)
                                                <p>• Financial difference: ₱{{ number_format(abs($exchange->difference_amount), 2) }}</p>
                                            @endif
                                            @break
                                        @case('cancelled')
                                            <p>• This exchange has been cancelled</p>
                                            <p>• No items were exchanged</p>
                                            @break
                                    @endswitch

                                    @if($exchange->processedBy)
                                        <p>• Processed by: {{ $exchange->processedBy->name }}</p>
                                        @if($exchange->processed_at)
                                            <p>• Processed on: {{ $exchange->processed_at->format('F j, Y g:i A') }}</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
