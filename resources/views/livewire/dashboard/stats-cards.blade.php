<div {{ $autoRefresh ? 'wire:poll.5s' : '' }}>
    <!-- Auto-refresh Toggle -->
    <div class="flex justify-end mb-4">
        <button 
            wire:click="toggleAutoRefresh"
            class="px-4 py-2 text-sm rounded-lg transition-colors {{ $autoRefresh ? 'bg-green-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}"
        >
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 {{ $autoRefresh ? 'animate-spin' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ $autoRefresh ? 'Auto-refresh ON' : 'Auto-refresh OFF' }}
            </span>
        </button>
    </div>

    <!-- Dashboard Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Inventory Card -->
        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-blue-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                        </path>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Products</p>
                    <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100">
                        {{ number_format($inventoryStats['total_products']) }}
                    </p>
                    <div class="flex gap-3 mt-1">
                        <p class="text-xs text-blue-500 dark:text-blue-300">
                            {{ $inventoryStats['low_stock_products'] }} low
                        </p>
                        <p class="text-xs text-red-500 dark:text-red-300">
                            {{ $inventoryStats['out_of_stock'] }} out
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Card -->
        <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-green-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-green-600 dark:text-green-400">Sales</p>
                    <p class="text-2xl font-semibold text-green-900 dark:text-green-100">
                        {{ number_format($salesStats['total_sales']) }}
                    </p>
                    <p class="text-xs text-green-500 dark:text-green-300 mt-1">
                        ₱{{ number_format($salesStats['total_sales_value'], 2) }}
                    </p>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                        Today: {{ $salesStats['today_sales'] }} (₱{{ number_format($salesStats['today_sales_value'], 2) }})
                    </p>
                </div>
            </div>
        </div>

        <!-- Customers Card -->
        <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-purple-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                        </path>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Customers</p>
                    <p class="text-2xl font-semibold text-purple-900 dark:text-purple-100">
                        {{ number_format($customerStats['total_customers']) }}
                    </p>
                    <div class="flex gap-3 mt-1">
                        <p class="text-xs text-purple-500 dark:text-purple-300">
                            {{ $customerStats['active_customers'] }} active
                        </p>
                        <p class="text-xs text-purple-600 dark:text-purple-400">
                            {{ $customerStats['new_this_month'] }} new
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Returns Card -->
        <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-2 bg-red-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6">
                        </path>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-red-600 dark:text-red-400">Returns & Exchange</p>
                    <p class="text-2xl font-semibold text-red-900 dark:text-red-100">
                        {{ number_format($returnStats['total_returns']) }}
                    </p>
                    <div class="flex gap-3 mt-1">
                        <p class="text-xs text-red-500 dark:text-red-300">
                            {{ $returnStats['pending_returns'] }} pending
                        </p>
                        <p class="text-xs text-red-600 dark:text-red-400">
                            {{ $returnStats['this_month'] }} this month
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed top-20 right-4 z-50">
        <div class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Refreshing...
        </div>
    </div>
</div>
