<div class="relative" x-data="{ open: @entangle('showResults') }">
    <div class="relative">
        <input 
            type="text" 
            wire:model.live.debounce.300ms="query"
            @click.away="open = false"
            placeholder="Search products, customers, orders..."
            class="w-full px-4 py-2 pl-10 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
        >
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        @if($query)
            <button 
                wire:click="clearSearch" 
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
            >
                <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif
    </div>

    <!-- Search Results Dropdown -->
    @if($showResults && $query)
        <div class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg max-h-96 overflow-y-auto">
            <!-- Products -->
            @if(isset($results['products']) && count($results['products']) > 0)
                <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase px-2 py-1">Products</h3>
                    @foreach($results['products'] as $product)
                        <a href="{{ route('master_data.products.show', $product) }}" 
                           class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->sku }}</div>
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Customers -->
            @if(isset($results['customers']) && count($results['customers']) > 0)
                <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase px-2 py-1">Customers</h3>
                    @foreach($results['customers'] as $customer)
                        <a href="{{ route('livewire.customers', ['search' => $customer->name]) }}" 
                           class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $customer->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->email }}</div>
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Orders -->
            @if(isset($results['orders']) && count($results['orders']) > 0)
                <div class="p-2">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase px-2 py-1">Orders</h3>
                    @foreach($results['orders'] as $order)
                        <a href="{{ route('sales.orders.show', $order->order_id ?? $order) }}" 
                           class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</div>
                        </a>
                    @endforeach
                </div>
            @endif

            @if(
                (!isset($results['products']) || count($results['products']) == 0) &&
                (!isset($results['customers']) || count($results['customers']) == 0) &&
                (!isset($results['orders']) || count($results['orders']) == 0)
            )
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    No results found
                </div>
            @endif
        </div>
    @endif

    <!-- Loading Indicator -->
    <div wire:loading class="absolute right-12 top-2.5">
        <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
</div>
