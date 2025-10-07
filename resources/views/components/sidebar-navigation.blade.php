<!-- Sidebar Navigation Component -->
<div x-data="{ sidebarOpen: false }" x-init="$watch('sidebarOpen', value => console.log('Sidebar:', value))"
    @sidebar-toggle.window="sidebarOpen = !sidebarOpen" class="relative">
    <!-- Navigation Pane -->
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-x-full"
        x-transition:enter-end="opacity-100 transform translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform -translate-x-full" 
        {{-- @mouseenter="sidebarOpen = true" @mouseleave="sidebarOpen = false" --}}
        class="fixed top-14 left-0 h-screen w-64 bg-white dark:bg-gray-800 shadow-lg z-30 overflow-y-auto">

        <!-- Logo Section -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <!-- Logo Icon -->
                <div class="bg-blue-600 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>

                <!-- Logo Text -->
                <div>
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">Checkpoint</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Sales & Inventory</p>
                </div>
            </div>

            <!-- Close Button -->
            <button @click="sidebarOpen = false"
                class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                <svg class="w-6 h-6 text-gray-900 dark:text-white" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-4">
                <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                    Main Menu</h3>
                <nav class="space-y-2">
                    <!-- Dashboard -->
                    <x-nav-item route="dashboard" :icon="App\Helpers\NavigationHelper::getIcon('dashboard')"
                        title="Dashboard" />

                    <!-- Inventory Section -->
                    <x-nav-item route-pattern="inventory.products.*|inventory.product_stocks.*|inventory.thresholds.*" :icon="App\Helpers\NavigationHelper::getIcon('inventory')"
                        title="Inventory" :is-dropdown="true">

                        <!-- Products -->
                        <x-nav-item route="inventory.products.index" route-pattern="inventory.products.*"
                            :icon="App\Helpers\NavigationHelper::getIcon('products', 'w-4 h-4 mr-3')" title="Products"
                            size="small" />

                        <!-- Composite Products -->
                        {{-- <x-nav-item href="#"
                            :icon="App\Helpers\NavigationHelper::getIcon('composite-products', 'w-4 h-4 mr-3')"
                            title="Composite Products" size="small" /> --}}

                        <!-- Stock Management -->
                        <x-nav-item route="inventory.product_stock_adjustment.index" route-pattern="inventory.product_stock_adjustment.*"
                            :icon="App\Helpers\NavigationHelper::getIcon('stock-adjustment', 'w-4 h-4 mr-3')"
                            title="Stock Adjustment" size="small" />

                        <!-- Inventory Thresholds -->
                        <x-nav-item route="inventory.thresholds.index" route-pattern="inventory.thresholds.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>'
                            title="Inventory Thresholds" size="small" />
                    </x-nav-item>


                    <!-- Sales Section-->
                    <x-nav-item route-pattern="sales.*" :icon="App\Helpers\NavigationHelper::getIcon('sales')"
                        title="Sales" :is-dropdown="true">

                        <!-- Customers -->
                        <x-nav-item route="sales.customers.index" route-pattern="sales.customers.*"
                            :icon="App\Helpers\NavigationHelper::getIcon('customers', 'w-4 h-4 mr-3')" title="Customers"
                            size="small" />

                        <!-- Sales Order -->
                        <x-nav-item route="sales.orders.index" route-pattern="sales.orders.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
                            title="Sales Order" size="small" />

                        <!-- Packages -->
                        <x-nav-item route="sales.packages.index" route-pattern="sales.packages.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>'
                            title="Packages" size="small" />

                        <!-- Shipments -->
                        <x-nav-item route="sales.shipments.index" route-pattern="sales.shipments.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>'
                            title="Shipments" size="small" />

                        <!-- Invoices -->
                        <x-nav-item route="sales.invoices.index" route-pattern="sales.invoices.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
                            title="Invoices" size="small" />

                        <!-- Payments Received -->
                        <x-nav-item route="sales.payments.index" route-pattern="sales.payments.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>'
                            title="Payments Received" size="small" />

                        <!-- Sales Return -->
                        <x-nav-item route="sales.returns.index" route-pattern="sales.returns.*"
                            :icon="App\Helpers\NavigationHelper::getIcon('returns', 'w-4 h-4 mr-3')"
                            title="Sales Return" size="small" />

                        <!-- Exchange -->
                        <x-nav-item route="sales.exchanges.index" route-pattern="sales.exchanges.*"
                            :icon="App\Helpers\NavigationHelper::getIcon('exchange', 'w-4 h-4 mr-3')"
                            title="Exchange" size="small" />
                    </x-nav-item>

                    <!-- Purchases Section-->
                    <x-nav-item route-pattern="inventory.suppliers.*|inventory.purchase-orders.*|inventory.purchase-receives.*" :icon="App\Helpers\NavigationHelper::getIcon('purchases')"
                        title="Purchases" :is-dropdown="true">

                        <!-- Vendor/Supplier -->
                        <x-nav-item route="purchases.suppliers.index" route-pattern="purchases.suppliers.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>'
                            title="Vendor/Supplier" size="small" />

                        <!-- Purchase Order -->
                        <x-nav-item route="purchases.purchase-orders.index" route-pattern="purchases.purchase-orders.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>'
                            title="Purchase Order" size="small" />

                        <!-- Purchase Receives -->
                        <x-nav-item route="purchases.purchase-receives.index" route-pattern="purchases.purchase-receives.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>'
                            title="Purchase Receives" size="small" />

                        <!-- Purchase Return -->
                        <x-nav-item route="purchases.purchase-returns.index" route-pattern="purchases.purchase-returns.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>'
                            title="Purchase Return" size="small" />

                        <!-- Payments Made -->
                        <x-nav-item route="purchases.payments.index" route-pattern="purchases.payments.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>'
                            title="Payments Made" size="small" />

                        <!-- Bills -->
                        {{-- <x-nav-item href="#"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
                            title="Bills" size="small" /> --}}
                    </x-nav-item>

                    <!-- Reports Section -->
                    <x-nav-item route-pattern="reports.*" :icon="App\Helpers\NavigationHelper::getIcon('reports')"
                        title="Reports" :is-dropdown="true">

                        <!-- Sales Report -->
                        <x-nav-item href="#" :icon="App\Helpers\NavigationHelper::getIcon('sales', 'w-4 h-4 mr-3')"
                            title="Sales Report" size="small" />

                        <!-- Purchases Report -->
                        <x-nav-item href="#" :icon="App\Helpers\NavigationHelper::getIcon('purchases', 'w-4 h-4 mr-3')"
                            title="Purchases Report" size="small" />

                        <!-- Inventory Report -->
                        <x-nav-item href="#" :icon="App\Helpers\NavigationHelper::getIcon('inventory', 'w-4 h-4 mr-3')"
                            title="Inventory Report" size="small" />

                        <!-- Returns & Exchange Lists -->
                        <x-nav-item href="#"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>'
                            title="Returns & Exchange Lists" size="small" />

                        <!-- Supplier Report -->
                        <x-nav-item href="#"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>'
                            title="Supplier Report" size="small" />

                        <!-- Profit & Loss -->
                        <x-nav-item href="#"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>'
                            title="Profit & Loss" size="small" />

                        <!-- Income Report -->
                        <x-nav-item href="#"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path><text x="12" y="14" text-anchor="middle" font-size="4" font-weight="bold" fill="currentColor">₱</text></svg>'
                            title="Income Report" size="small" />

                        <!-- Expense Report -->
                        <x-nav-item href="#"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>'
                            title="Expense Report" size="small" />
                    </x-nav-item>

                    <!-- Integration -->
                    <x-nav-item href="#" :icon="App\Helpers\NavigationHelper::getIcon('integration')"
                        title="Integration" />

                    <!-- User Management -->
                    <a href="{{ route('user-management.index') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('user-management.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        User Management
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('profile.*') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors duration-150">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profile
                    </a>
                </nav>
            </div>
        </div>
    </div>


    <!-- Overlay for mobile -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @mouseenter="sidebarOpen = true"
        @mouseleave="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-20 sm:hidden"></div>
</div>