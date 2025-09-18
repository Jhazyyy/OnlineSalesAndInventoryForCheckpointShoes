<x-app-layout>
    <div class="w-full h-screen">
        <!-- Main Content Area -->
        <div :class="navOpen ? 'flex-1' : 'w-full'" class="h-full overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 min-h-full flex flex-col">
                <div class="flex-1 p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-4xl font-bold mb-4">Welcome, {{ Auth::user()->name}}👋! </h2>

                    <!-- Dashboard Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                        <!-- Inventory Card -->
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Products
                                    </p>
                                    <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100">
                                        {{ $inventoryStats['total_products'] ?? 0 }}
                                    </p>
                                    <p class="text-xs text-blue-500 dark:text-blue-300 mt-1">
                                        {{ $inventoryStats['low_stock_products'] ?? 0 }} low stock
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Card -->
                        <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600 dark:text-green-400">Sales
                                    </p>
                                    <p class="text-2xl font-semibold text-green-900 dark:text-green-100">
                                        {{ $salesStats['total_sales'] ?? 0 }}
                                    </p>
                                    <p class="text-xs text-green-500 dark:text-green-300 mt-1">
                                        ₱{{ number_format($salesStats['total_sales_value'] ?? 0, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Customers Card -->
                        <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-600 dark:text-purple-400">
                                        Customers</p>
                                    <p class="text-2xl font-semibold text-purple-900 dark:text-purple-100">
                                        {{ $customerStats['total_customers'] ?? 0 }}
                                    </p>
                                    <p class="text-xs text-purple-500 dark:text-purple-300 mt-1">
                                        {{ $customerStats['active_customers'] ?? 0 }} active
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Returns Card -->
                        <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-red-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-red-600 dark:text-red-400">
                                        Returns</p>
                                    <p class="text-2xl font-semibold text-red-900 dark:text-red-100">
                                        {{ $returnStats['total_returns'] ?? 0 }}
                                    </p>
                                    <p class="text-xs text-red-500 dark:text-red-300 mt-1">
                                        {{ $returnStats['pending_returns'] ?? 0 }} pending
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Secondary Stats Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                        <!-- Purchases Card -->
                        <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-yellow-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8.5m-8.5 0a2 2 0 11-4 0 2 2 0 014 0zm8.5 0a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">
                                        Purchases</p>
                                    <p class="text-2xl font-semibold text-yellow-900 dark:text-yellow-100">
                                        {{ $purchaseStats['total_purchases'] ?? 0 }}
                                    </p>
                                    <p class="text-xs text-yellow-500 dark:text-yellow-300 mt-1">
                                        ₱{{ number_format($purchaseStats['total_purchase_value'] ?? 0, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Suppliers Card -->
                        <div class="bg-indigo-50 dark:bg-indigo-900 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-indigo-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                        Suppliers</p>
                                    <p class="text-2xl font-semibold text-indigo-900 dark:text-indigo-100">
                                        {{ $supplierStats['total_suppliers'] ?? 0 }}
                                    </p>
                                    <p class="text-xs text-indigo-500 dark:text-indigo-300 mt-1">
                                        {{ $supplierStats['active_suppliers'] ?? 0 }} active
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Value Card -->
                        <div class="bg-teal-50 dark:bg-teal-900 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-teal-500 rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M4 6h2v12h-2V6z" fill="currentColor" />
                                        <path d="M6 6h6c2.2 0 4 1.8 4 4s-1.8 4-4 4H6v4h-2v-4h2V6z"
                                            fill="currentColor" />
                                        <path d="M6 8v4h6c1.1 0 2-0.9 2-2s-0.9-2-2-2H6z" fill="currentColor" />
                                        <path d="M2 9h8v1H2V9z" fill="currentColor" />
                                        <path d="M2 12h8v1H2v-1z" fill="currentColor" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-teal-600 dark:text-teal-400">
                                        Inventory Value</p>
                                    <p class="text-2xl font-semibold text-teal-900 dark:text-teal-100">
                                        ₱{{ number_format($inventoryStats['total_inventory_value'] ?? 0, 2) }}</p>
                                    <p class="text-xs text-teal-500 dark:text-teal-300 mt-1">
                                        {{ $inventoryStats['out_of_stock_products'] ?? 0 }} out of stock
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Shipments Card -->
                        <a href="{{ route('sales.shipments.index') }}" class="block">
                            <div class="bg-orange-50 dark:bg-orange-900 p-4 rounded-lg hover:shadow-md transition-shadow duration-200 border border-orange-200 dark:border-orange-700 hover:border-orange-300 dark:hover:border-orange-600">
                                <div class="flex items-center">
                                    <div class="p-2 bg-orange-500 rounded-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-orange-600 dark:text-orange-400">
                                            Shipments</p>
                                        <p class="text-2xl font-semibold text-orange-900 dark:text-orange-100">
                                            {{ $shipmentStats['total_shipments'] ?? 0 }}
                                        </p>
                                        <p class="text-xs text-orange-500 dark:text-orange-300 mt-1">
                                            {{ $shipmentStats['active_shipments'] ?? 0 }} active
                                            @if($shipmentStats['overdue_shipments'] > 0)
                                                • <span class="text-red-600 dark:text-red-400 font-medium">{{ $shipmentStats['overdue_shipments'] }} overdue</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Detailed Shipment Statistics -->
                    @if($shipmentStats['total_shipments'] > 0)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Shipment Analytics</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                            <!-- Pending Shipments -->
                            <div class="bg-amber-50 dark:bg-amber-900 p-3 rounded-lg border border-amber-200 dark:border-amber-700">
                                <div class="text-center">
                                    <p class="text-lg font-semibold text-amber-900 dark:text-amber-100">
                                        {{ $shipmentStats['pending_shipments'] }}
                                    </p>
                                    <p class="text-xs text-amber-600 dark:text-amber-400">Pending</p>
                                </div>
                            </div>

                            <!-- In Transit Shipments -->
                            <div class="bg-blue-50 dark:bg-blue-900 p-3 rounded-lg border border-blue-200 dark:border-blue-700">
                                <div class="text-center">
                                    <p class="text-lg font-semibold text-blue-900 dark:text-blue-100">
                                        {{ $shipmentStats['in_transit_shipments'] }}
                                    </p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400">In Transit</p>
                                </div>
                            </div>

                            <!-- Delivered Shipments -->
                            <div class="bg-green-50 dark:bg-green-900 p-3 rounded-lg border border-green-200 dark:border-green-700">
                                <div class="text-center">
                                    <p class="text-lg font-semibold text-green-900 dark:text-green-100">
                                        {{ $shipmentStats['delivered_shipments'] }}
                                    </p>
                                    <p class="text-xs text-green-600 dark:text-green-400">Delivered</p>
                                </div>
                            </div>

                            <!-- Overdue Shipments -->
                            <div class="bg-red-50 dark:bg-red-900 p-3 rounded-lg border border-red-200 dark:border-red-700">
                                <div class="text-center">
                                    <p class="text-lg font-semibold text-red-900 dark:text-red-100">
                                        {{ $shipmentStats['overdue_shipments'] }}
                                    </p>
                                    <p class="text-xs text-red-600 dark:text-red-400">Overdue</p>
                                </div>
                            </div>

                            <!-- This Week -->
                            <div class="bg-purple-50 dark:bg-purple-900 p-3 rounded-lg border border-purple-200 dark:border-purple-700">
                                <div class="text-center">
                                    <p class="text-lg font-semibold text-purple-900 dark:text-purple-100">
                                        {{ $shipmentStats['this_week_shipments'] }}
                                    </p>
                                    <p class="text-xs text-purple-600 dark:text-purple-400">This Week</p>
                                </div>
                            </div>

                            <!-- Shipping Cost -->
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="text-center">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        ₱{{ number_format($shipmentStats['total_shipping_cost'], 2) }}
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Total Cost</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- User Management Section -->
                    {{-- <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Quick Access</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- User Management Card -->
                            <a href="{{ route('user-management.index') }}"
                                class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900 dark:to-blue-900 p-6 rounded-lg hover:shadow-lg transition-all duration-200 border border-indigo-100 dark:border-indigo-800 hover:border-indigo-200 dark:hover:border-indigo-700">
                                <div class="flex items-center">
                                    <div class="p-3 bg-indigo-500 rounded-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold text-indigo-900 dark:text-indigo-100">
                                            User Management</h4>
                                        <p class="text-sm text-indigo-600 dark:text-indigo-300">Manage system
                                            users and permissions</p>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center text-indigo-600 dark:text-indigo-300">
                                    <span class="text-sm">Access User Management</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </a>
                        </div>
                    </div> --}}
                </div>

                <!-- Footer -->
                <footer
                    class="mt-auto pt-2 pb-4 px-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-6">
                            <!-- Company Info -->
                            <div class="flex items-center space-x-2">
                                <div class="bg-blue-600 p-1.5 rounded-md">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Checkpoint
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Sales & Inventory System
                                    </p>
                                </div>
                            </div>

                            <!-- Quick Links -->
                            <div class="hidden md:flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                <a href="#"
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">About</a>
                                <a href="#"
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Support</a>
                                <a href="#"
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Documentation</a>
                                <a href="#"
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Privacy</a>
                            </div>
                        </div>

                        <div
                            class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-4 mt-4 md:mt-0">
                            <!-- Version Info -->
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">v1.0.0</span>
                            </div>

                            <!-- Copyright -->
                            <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                <p>&copy; {{ date('Y') }} Checkpoint. All Rights Reserved.</p>
                                <p class="mt-1">Built with Laravel</p>
                            </div>

                            <!-- Social Links -->
                            {{-- Facebook --}}
                            <div class="flex items-center space-x-2">
                                <a href="https://www.facebook.com/checkpointshoesph" target="_blank"
                                    class="text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                                {{-- Twitter --}}
                                <a href="#"
                                    class="text-gray-400 hover:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84">
                                        </path>
                                    </svg>
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- Mobile Quick Links -->
                    <div class="md:hidden mt-4 pt-4 border-t border-gray-400 dark:border-gray-700">
                        <div class="flex justify-center space-x-6 text-xs text-gray-500 dark:text-gray-400">
                            <a href="#" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">About</a>
                            <a href="#"
                                class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Support</a>
                            {{-- <a href="#"
                                class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Documentation</a>
                            --}}
                            <a href="#"
                                class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Privacy</a>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</x-app-layout>