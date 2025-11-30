<!-- Sidebar Navigation Component -->
<div x-data class="relative">
    <!-- Navigation Pane -->
    <div x-show="$store.sidebar.open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-x-full"
        x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform -translate-x-full"
        class="fixed left-0 top-14 h-screen w-72 bg-white dark:bg-gray-800 shadow-lg z-30 overflow-y-auto">

        <!-- Logo Section -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <!-- Logo Icon/Image -->
                <div class="flex-shrink-0">
                    <img src="{{ asset('checkpointlogo.jpg') }}" alt="{{ setting('general.company_name', 'Checkpoint') }}"
                        class="w-10 h-10 rounded-lg object-contain bg-white dark:bg-gray-700 p-1 shadow-sm border-2 dark:border-gray-700">
                </div>

                <!-- Logo Text -->
                <div>
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ setting('general.company_name', 'Checkpoint') }}</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Sales & Inventory</p>
                </div>
            </div>

            <!-- Close Button -->
            <button @click="$store.sidebar.toggle()"
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
                <h3 class="text-sm font-thin text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                    Main Menu</h3>
                <nav class="space-y-2">
                    <!-- Dashboard -->
                    <x-nav-item route="dashboard" :icon="App\Helpers\NavigationHelper::getIcon('dashboard')" title="Dashboard" />

                    <!-- Inventory Section -->
                    @hasanyrole('super_admin|admin|inventory_clerk|salesperson')
                        <x-nav-item
                            route-pattern="inventory.products.*|inventory.product_stocks.*|inventory.product-movement.*|inventory.product-costing.*"
                            :icon="App\Helpers\NavigationHelper::getIcon('inventory')" title="Inventory" :is-dropdown="true">

                            <x-nav-item route="inventory.products.index" route-pattern="inventory.products.*"
                                :icon="App\Helpers\NavigationHelper::getIcon('products', 'w-4 h-4 mr-3')" title="Inventory List" size="small" />
                        @endhasanyrole

                        <!-- Stock Adjustment -->
                        {{-- <x-nav-item route="inventory.product_stock_adjustment.index"
                                route-pattern="inventory.product_stock_adjustment.*" :icon="App\Helpers\NavigationHelper::getIcon('stock_adjustment', 'w-4 h-4 mr-3')"
                                title="Stock Adjustment" size="small" /> --}}

                        {{-- <!-- Product Movement (Fast/Slow/Non-Moving) --> --}}
                        {{-- <x-nav-item route="inventory.product-movement.index"
                            route-pattern="inventory.product-movement.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>'
                            title="Product Movement" size="small" /> --}}


                        <!-- Product Costing -->
                        @hasanyrole('super_admin|admin')
                            <x-nav-item route="inventory.product-costing.index" route-pattern="inventory.product-costing.*"
                                icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                                title="Product Costing" size="small" />
                        @endhasanyrole
                    </x-nav-item>


                    <!-- Purchases Section-->
                    @hasanyrole('super_admin|admin|inventory_clerk')
                        <x-nav-item
                            route-pattern="purchases.purchase-orders.*|purchases.purchase-receives.*|purchases.deliveries.*"
                            :icon="App\Helpers\NavigationHelper::getIcon('purchases')" title="Purchases" :is-dropdown="true">

                            <!-- Purchase Order -->
                            <x-nav-item route="purchases.purchase-orders.index" route-pattern="purchases.purchase-orders.*"
                                icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>'
                                title="Purchase Order" size="small" />

                            <!-- Goods Received -->
                            <x-nav-item route="purchases.purchase-receives.index"
                                route-pattern="purchases.purchase-receives.*"
                                icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>'
                                title="Goods Received" size="small" />

                            <!-- Purchase Return -->
                            {{-- <x-nav-item route="purchases.purchase-returns.index"
                            route-pattern="purchases.purchase-returns.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>'
                            title="Purchase Return" size="small" /> --}}

                            <!-- Payments Made -->
                            {{-- <x-nav-item route="purchases.payments.index" route-pattern="purchases.payments.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>'
                            title="Payments Made" size="small" /> --}}
                        @endhasanyrole
                    </x-nav-item>



                    @hasanyrole('super_admin|admin|salesperson')
                        <!-- Sales Section-->
                        <x-nav-item route-pattern="pos.*" :icon="App\Helpers\NavigationHelper::getIcon('sales')" title="Sales" :is-dropdown="true">

                            <!-- Sales Order -->
                            <x-nav-item route="pos.index" route-pattern="pos.orders.*"
                                icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
                                title="Sales (POS)" size="small" />

                            @hasrole('admin')
                                <!-- Bank Transfer Payments -->
                                <x-nav-item route="admin.bank-transfer-payments.index"
                                    route-pattern="admin.bank-transfer-payments.*"
                                    icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>'
                                    title="Bank Transfer Payments" size="small">
                                    @php
                                        $pendingCount = \App\Models\BankTransferPayment::pending()->count();
                                    @endphp
                                    @if ($pendingCount > 0)
                                        <span
                                            class="ml-auto inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                            {{ $pendingCount }}
                                        </span>
                                    @endif
                                </x-nav-item>
                            @endhasrole

                            <!-- Customers -->
                            <x-nav-item route="sales.customers.index" route-pattern="sales.customers.*" :icon="App\Helpers\NavigationHelper::getIcon('customers', 'w-4 h-4 mr-3')"
                                title="Customers" size="small" />

                            <!-- Packages -->
                            {{-- <x-nav-item route="sales.packages.index" route-pattern="sales.packages.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>'
                            title="Packages" size="small" /> --}}

                            <!-- Shipments -->
                            {{-- <x-nav-item route="sales.shipments.index" route-pattern="sales.shipments.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>'
                            title="Shipments" size="small" /> --}}

                            <!-- Invoices -->
                            {{-- <x-nav-item route="sales.invoices.index" route-pattern="sales.invoices.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
                            title="Invoices" size="small" /> --}}

                            <!-- Payments Received -->
                            {{-- <x-nav-item route="sales.payments.index" route-pattern="sales.payments.*"
                            icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>'
                            title="Payments Received" size="small" /> --}}

                            <!-- Sales Return -->
                            <x-nav-item route="sales.returns.index" route-pattern="sales.returns.*" :icon="App\Helpers\NavigationHelper::getIcon('returns', 'w-4 h-4 mr-3')"
                                title="Sales Return" size="small" />

                            <!-- Exchange -->
                            {{-- <x-nav-item route="sales.exchanges.index" route-pattern="sales.exchanges.*" :icon="App\Helpers\NavigationHelper::getIcon('exchange', 'w-4 h-4 mr-3')"
                            title="Exchange" size="small" /> --}}
                        </x-nav-item>
                    @endhasanyrole


                    <!-- Master Data Section -->
                    @hasanyrole('super_admin|admin')
                        @can('view categories')
                            <x-nav-item
                                route-pattern="master_data.categories.*|master_data.brands.*|master_data.suppliers.*|master_data.tax_discounts.*"
                                :icon="App\Helpers\NavigationHelper::getIcon('master_data')" title="Master Data" :is-dropdown="true">

                                <!-- Supplier -->
                                @can('view suppliers')
                                    <x-nav-item route="master_data.suppliers.index" route-pattern="master_data.suppliers.*"
                                        :icon="App\Helpers\NavigationHelper::getIcon('suppliers', 'w-4 h-4 mr-3')" title="Supplier" size="small" />
                                @endcan

                                <!-- Stock Name Management (Admin Only) -->
                                <x-nav-item route="master_data.stock_names.index" route-pattern="master_data.stock_names.*"
                                    :icon="App\Helpers\NavigationHelper::getIcon(
                                        'stock-name-management',
                                        'w-4 h-4 mr-3',
                                    )" title="Stock Name" size="small" />

                                <!-- Categories -->
                                @can('view categories')
                                    <x-nav-item route="master_data.categories.index" route-pattern="master_data.categories.*"
                                        :icon="App\Helpers\NavigationHelper::getIcon('categories', 'w-4 h-4 mr-3')" title="Categories" size="small" />
                                @endcan

                                <!-- Brands -->
                                @can('view brands')
                                    <x-nav-item route="master_data.brands.index" route-pattern="master_data.brands.*"
                                        :icon="App\Helpers\NavigationHelper::getIcon('brands', 'w-4 h-4 mr-3')" title="Brands" size="small" />
                                @endcan

                                <!-- Tax & Discount (Admin Only) -->
                                <x-nav-item route="master_data.tax_discounts.index" route-pattern="master_data.tax_discounts.*"
                                    :icon="App\Helpers\NavigationHelper::getIcon(
                                        'tax_and_discount',
                                        'w-5 h-5 mr-3',
                                    )" title="Tax & Discount" size="small" />
                            </x-nav-item>
                        @endcan
                    @endhasanyrole


                    <!-- Reports Section -->
                    <x-nav-item route-pattern="reports.*" :icon="App\Helpers\NavigationHelper::getIcon('reports')" title="Reports" :is-dropdown="true">

                        <!-- Reports Dashboard -->
                        {{-- <x-nav-item route="reports.index" route-pattern="reports.index"
                                icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>'
                                title="Reports Dashboard" size="small" /> --}}

                        <!-- Sales Report -->
                        @hasanyrole('super_admin|admin|salesperson')
                            <x-nav-item route="reports.sales" route-pattern="reports.sales" :icon="App\Helpers\NavigationHelper::getIcon('sales_order_master', 'w-4 h-4 mr-3')"
                                title="Sales Report" size="small" />
                        @endhasanyrole

                        <!-- Purchase Report -->
                        @hasanyrole('super_admin|admin|inventory_clerk')
                            <x-nav-item route="reports.purchases" route-pattern="reports.purchases" :icon="App\Helpers\NavigationHelper::getIcon('purchase_order_master', 'w-4 h-4 mr-3')"
                                title="Purchase Report" size="small" />
                        @endhasanyrole

                        <!-- Inventory Report -->
                        <x-nav-item route="reports.inventory" route-pattern="reports.inventory" :icon="App\Helpers\NavigationHelper::getIcon('inventory_report', 'w-4 h-4 mr-3')"
                            title="Inventory Report" size="small" />

                        {{-- Product Movement --}}
                        <x-nav-item route="reports.product-movement" route-pattern="reports.product-movement"
                            :icon="App\Helpers\NavigationHelper::getIcon(
                                'product_movement_analysis',
                                'w-4 h-4 mr-3',
                            )" title="Product Movement" size="small" />

                        <!-- Reorder Items -->
                        @hasanyrole('super_admin|admin|inventory_clerk')
                            <x-nav-item route="reports.reorder" route-pattern="reports.reorder" :icon="App\Helpers\NavigationHelper::getIcon('reorder_items', 'w-4 h-4 mr-3')"
                                title="Reorder Items" size="small" />
                        @endhasanyrole

                        <!-- Critical Level Items -->
                        {{-- @can('view inventory')
                                <x-nav-item route="reports.critical" route-pattern="reports.critical" :icon="App\Helpers\NavigationHelper::getIcon('critical_level_items', 'w-4 h-4 mr-3')"
                                    title="Critical Level Items" size="small" />
                            @endcan --}}

                        <!-- Financial Report (P&L) -->
                        {{-- <x-nav-item route="reports.financial" route-pattern="reports.financial" :icon="App\Helpers\NavigationHelper::getIcon('financial_report', 'w-4 h-4 mr-3')"
                                title="Financial Report (P&L)" size="small" /> --}}

                        <!-- Supplier Cost -->
                        {{-- <x-nav-item route="reports.financial" route-pattern="reports.financial" :icon="App\Helpers\NavigationHelper::getIcon('supplier_cost', 'w-4 h-4 mr-3')"
                                title="Supplier Cost" size="small" /> --}}

                        <!-- Stock Movement Report -->
                        {{-- <x-nav-item route="reports.movement" route-pattern="reports.movement"
                                icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>'
                                title="Stock Movement" size="small" /> --}}


                        {{-- Block Items --}}
                        {{-- @can('view inventory')
                                <x-nav-item route="reports.blocked" route-pattern="reports.blocked" :icon="App\Helpers\NavigationHelper::getIcon('block_items', 'w-4 h-4 mr-3')"
                                    title="Block Items" size="small" />
                            @endcan --}}

                        <!-- Expense Report -->
                        {{-- <x-nav-item href="#"
                                icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>'
                                title="Expense Report" size="small" /> --}}
                    </x-nav-item>

                    {{-- Audit Trail (Admin Only) --}}
                    @hasanyrole('super_admin|admin')
                        <x-nav-item route="audit-logs.index" route-pattern="audit-logs.*" :icon="App\Helpers\NavigationHelper::getIcon('audit_trail', 'w-5 h-6 mr-1')"
                            title="Audit Logs" />
                    @endhasanyrole

                    <!-- User Management (Super Admin and Admin Only) -->
                    @hasanyrole('super_admin|admin')
                        <x-nav-item route="user-management.index" route-pattern="user-management.*" :icon="App\Helpers\NavigationHelper::getIcon('user_accounts_control', 'w-5 h-5 mr-1')"
                            title="User Management" />
                    @endhasanyrole
                </nav>
            </div>
        </div>
    </div>

    <!-- Overlay for mobile -->
    <div x-show="$store.sidebar.open" @click="$store.sidebar.toggle()"
        x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"></div>
</div>
