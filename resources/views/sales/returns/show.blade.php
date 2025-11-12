<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Return #{{ $return->return_id }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">View return details and manage status</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('sales.returns.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Returns
                            </a>
                            
                            @if($return->isPending())
                                <a href="{{ route('sales.returns.edit', $return) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
                <!-- Return Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Sales Order Information (if linked) -->
                    @if($return->salesOrder)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Linked Sales Order
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Number</label>
                                        <div class="mt-1 text-sm">
                                            <a href="{{ $return->salesOrder->purchase_type === 'in_store' ? route('pos.show', $return->salesOrder) : route('sales.orders.show', $return->salesOrder) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                                                {{ $return->salesOrder->order_number }}
                                            </a>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Date</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $return->salesOrder->order_date ? $return->salesOrder->order_date->format('M d, Y') : 'N/A' }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Status</label>
                                        <div class="mt-1">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($return->salesOrder->status === 'delivered') bg-green-100 text-green-800
                                                @elseif($return->salesOrder->status === 'cancelled') bg-red-100 text-red-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ ucfirst($return->salesOrder->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Total</label>
                                        <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                            ₱{{ number_format($return->salesOrder->total_amount, 2) }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Items -->
                                @if($return->salesOrder->items && $return->salesOrder->items->count() > 0)
                                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Order Items</h4>
                                        <div class="space-y-2">
                                            @foreach($return->salesOrder->items as $item)
                                                <div class="flex justify-between items-center p-2 rounded {{ $item->product_id == $return->product_id ? 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800' : 'bg-gray-50 dark:bg-gray-700' }}">
                                                    <div class="flex-1">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $item->product->product_name ?? 'Unknown Product' }}
                                                            @if($item->product_id == $return->product_id)
                                                                <span class="ml-2 text-xs text-blue-600 dark:text-blue-400">(Returned Item)</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                                            Qty: {{ $item->quantity }} × ₱{{ number_format($item->unit_price, 2) }}
                                                        </div>
                                                    </div>
                                                    <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                        ₱{{ number_format($item->total_price, 2) }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Customer Information (if linked) -->
                    @if($return->customer)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Customer Information
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $return->customer->display_name }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $return->customer->email ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $return->customer->phone ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer Type</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ ucfirst($return->customer->customer_type ?? 'individual') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Return Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Basic Information -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Return ID</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white font-mono">
                                            #{{ $return->return_id }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                                        <div class="mt-1">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $return->product->product_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                SKU: {{ $return->product->sku }}
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ number_format($return->quantity) }} {{ Str::plural('unit', $return->quantity) }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            ₱{{ number_format($return->price, 2) }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Status and Dates -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <div class="mt-1">
                                            @switch($return->return_status)
                                                @case('pending')
                                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                    @break
                                                @case('approved')
                                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                                        Approved
                                                    </span>
                                                    @break
                                                @case('rejected')
                                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                                        Rejected
                                                    </span>
                                                    @break
                                                @case('processed')
                                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Processed
                                                    </span>
                                                    @break
                                                @case('refunded')
                                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        Refunded
                                                    </span>
                                                    @break
                                            @endswitch
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Amount</label>
                                        <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                            ₱{{ number_format($return->total_amount, 2) }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Return Date</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $return->return_date->format('F j, Y') }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $return->created_at->format('F j, Y g:i A') }}
                                        </div>
                                    </div>

                                    @if($return->updated_at != $return->created_at)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Updated</label>
                                            <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ $return->updated_at->format('F j, Y g:i A') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Return Reason -->
                            @if($return->reason)
                                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Return Reason</label>
                                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $return->reason }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Product Details -->
                    {{-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Product Details</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Name</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $return->product->product_name }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $return->product->category ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Stock</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ number_format($return->product->stock_quantity) }} units
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">SKU</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white font-mono">
                                            {{ $return->product->sku }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Selling Price</label>
                                        <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                            ₱{{ number_format($return->product->selling_price, 2) }}
                                        </div>
                                    </div>

                                    @if($return->product->description)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                            <div class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ Str::limit($return->product->description, 100) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>

                <!-- Actions Panel -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Actions</h3>
                            
                            <div class="space-y-4">
                                @if($return->isPending())
                                    <form method="POST" action="{{ route('sales.returns.approve', $return) }}" class="w-full">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to approve this return? This will add the quantity back to inventory.')"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Approve Return
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('sales.returns.reject', $return) }}" class="w-full">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to reject this return?')"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Reject Return
                                        </button>
                                    </form>
                                @elseif($return->isApproved())
                                    <form method="POST" action="{{ route('sales.returns.mark-as-processed', $return) }}" class="w-full">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to mark this return as processed?')"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Mark as Processed
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($return->return_status, ['pending', 'rejected']))
                                    <form method="POST" action="{{ route('sales.returns.destroy', $return) }}" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to delete this return? This action cannot be undone.')"
                                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete Return
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <!-- Status Information -->
                            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Status Information</h4>
                                <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                                    @switch($return->return_status)
                                        @case('pending')
                                            <p>• This return is awaiting for approval</p>
                                            <p>• You can edit, approve, or reject this return</p>
                                            @break
                                        @case('approved')
                                            <p>• This return has been approved</p>
                                            <p>• {{ $return->quantity }} units have been added back to inventory</p>
                                            <p>• You can mark this return as processed</p>
                                            @break
                                        @case('rejected')
                                            <p>• This return has been rejected</p>
                                            <p>• No inventory changes were made</p>
                                            @break
                                        @case('processed')
                                            <p>• This return has been processed</p>
                                            <p>• All necessary actions have been completed</p>
                                            @break
                                        @case('refunded')
                                            <p>• This return has been refunded</p>
                                            <p>• Customer has been refunded ₱{{ number_format($return->total_amount, 2) }}</p>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
