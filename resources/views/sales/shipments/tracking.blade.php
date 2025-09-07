<x-guest-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 text-orange-500 mb-4">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Track Your Shipment</h2>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Enter your tracking number to view shipment status and delivery information</p>
                    </div>
                </div>
            </div>

            <!-- Search Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('sales.shipments.tracking') }}" class="max-w-md mx-auto">
                        <div class="relative">
                            <label for="tracking_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tracking Number
                            </label>
                            <div class="flex">
                                <input type="text" 
                                       id="tracking_number" 
                                       name="tracking_number" 
                                       value="{{ $trackingNumber }}" 
                                       placeholder="Enter tracking number..." 
                                       class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                       required>
                                <button type="submit" 
                                        class="px-6 py-2 bg-orange-600 border border-orange-600 border-l-0 rounded-r-md font-semibold text-white hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 transition ease-in-out duration-150">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($trackingNumber && !$shipment)
                <!-- Not Found Message -->
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6 mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Shipment Not Found</h3>
                            <div class="mt-1 text-sm text-red-700 dark:text-red-300">
                                <p>No shipment found with tracking number: <strong>{{ $trackingNumber }}</strong></p>
                                <p class="mt-1">Please check your tracking number and try again.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($shipment)
                <!-- Shipment Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        Shipment {{ $shipment->shipment_number }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Tracking: {{ $shipment->tracking_number }}</p>
                                </div>
                                <div class="mt-2 sm:mt-0">
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $shipment->status_badge_class }}">
                                        {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                                    </span>
                                    @if($shipment->is_overdue)
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            OVERDUE
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Shipment Details -->
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Shipment Details</h4>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">Carrier:</dt>
                                        <dd class="text-gray-900 dark:text-white font-medium">{{ $shipment->carrier ?: 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">Service Type:</dt>
                                        <dd class="text-gray-900 dark:text-white">{{ $shipment->service_type ?: 'Standard' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">Priority:</dt>
                                        <dd>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $shipment->priority_badge_class }}">
                                                {{ ucfirst($shipment->priority) }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">Shipment Date:</dt>
                                        <dd class="text-gray-900 dark:text-white">
                                            {{ $shipment->shipment_date ? $shipment->shipment_date->format('M d, Y') : 'N/A' }}
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">Expected Delivery:</dt>
                                        <dd class="text-gray-900 dark:text-white">
                                            {{ $shipment->expected_delivery_date ? $shipment->expected_delivery_date->format('M d, Y') : 'N/A' }}
                                        </dd>
                                    </div>
                                    @if($shipment->actual_delivery_date)
                                        <div class="flex justify-between">
                                            <dt class="text-gray-600 dark:text-gray-400">Delivered On:</dt>
                                            <dd class="text-green-600 dark:text-green-400 font-medium">
                                                {{ $shipment->actual_delivery_date->format('M d, Y g:i A') }}
                                            </dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>

                            <!-- Recipient Information -->
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Delivery Information</h4>
                                <dl class="space-y-2 text-sm">
                                    <div>
                                        <dt class="text-gray-600 dark:text-gray-400">Recipient:</dt>
                                        <dd class="text-gray-900 dark:text-white font-medium">{{ $shipment->recipient_name ?: 'N/A' }}</dd>
                                    </div>
                                    @if($shipment->recipient_phone)
                                        <div>
                                            <dt class="text-gray-600 dark:text-gray-400">Phone:</dt>
                                            <dd class="text-gray-900 dark:text-white">{{ $shipment->recipient_phone }}</dd>
                                        </div>
                                    @endif
                                    <div>
                                        <dt class="text-gray-600 dark:text-gray-400">Shipping Address:</dt>
                                        <dd class="text-gray-900 dark:text-white">{{ $shipment->shipping_address ?: 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600 dark:text-gray-400">Total Packages:</dt>
                                        <dd class="text-gray-900 dark:text-white">{{ $shipment->total_packages }}</dd>
                                    </div>
                                    @if($shipment->total_weight)
                                        <div class="flex justify-between">
                                            <dt class="text-gray-600 dark:text-gray-400">Weight:</dt>
                                            <dd class="text-gray-900 dark:text-white">{{ number_format($shipment->total_weight, 2) }} lbs</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        </div>

                        <!-- Special Services -->
                        @if($shipment->is_insured || $shipment->requires_signature || $shipment->is_fragile || $shipment->is_perishable)
                            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <h4 class="font-medium text-gray-900 dark:text-white mb-3">Special Services</h4>
                                <div class="flex flex-wrap gap-2">
                                    @if($shipment->is_insured)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                            🛡️ Insured
                                        </span>
                                    @endif
                                    @if($shipment->requires_signature)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                            ✍️ Signature Required
                                        </span>
                                    @endif
                                    @if($shipment->is_fragile)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                            ⚠️ Fragile
                                        </span>
                                    @endif
                                    @if($shipment->is_perishable)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                            ❄️ Perishable
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tracking History -->
                @if($shipment->tracking_history && count($shipment->tracking_history) > 0)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tracking History</h3>
                            
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    @foreach(array_reverse($shipment->tracking_history) as $index => $update)
                                        <li>
                                            <div class="relative pb-8 {{ $index === count($shipment->tracking_history) - 1 ? '' : 'border-l-2 border-gray-200 dark:border-gray-700 ml-4' }}">
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full bg-orange-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm text-gray-900 dark:text-white font-medium">
                                                                {{ $update['status'] ?? 'Status Update' }}
                                                            </p>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                {{ $update['description'] ?? 'No description available' }}
                                                            </p>
                                                            @if(isset($update['location']) && $update['location'])
                                                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                                                    📍 {{ $update['location'] }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                        <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            @if(isset($update['timestamp']))
                                                                {{ \Carbon\Carbon::parse($update['timestamp'])->format('M d, Y') }}
                                                                <br>
                                                                {{ \Carbon\Carbon::parse($update['timestamp'])->format('g:i A') }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Shipment Items -->
                @if($shipment->items && $shipment->items->count() > 0)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Shipment Items</h3>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Package #</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condition</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($shipment->items as $item)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $item->product->name ?? 'Product #' . $item->product_id }}
                                                    </div>
                                                    @if($item->product->sku)
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            SKU: {{ $item->product->sku }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-white">
                                                    {{ $item->quantity_shipped }}
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-white">
                                                    {{ $item->package_number ?: 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                        {{ $item->condition === 'new' ? 'bg-green-100 text-green-800' : 
                                                           ($item->condition === 'used' ? 'bg-yellow-100 text-yellow-800' : 
                                                           ($item->condition === 'refurbished' ? 'bg-blue-100 text-blue-800' : 
                                                           ($item->condition === 'damaged' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                                                        {{ ucfirst($item->condition ?: 'new') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Navigation Buttons -->
            <div class="mt-6 text-center space-x-4">
                @auth
                    <a href="{{ route('sales.shipments.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Shipments
                    </a>
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Dashboard
                    </a>
                @else
                    <a href="{{ url('/') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Home
                    </a>
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</x-guest-layout>
