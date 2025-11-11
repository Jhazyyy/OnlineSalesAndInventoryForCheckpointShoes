<x-app-layout>
    <div class="w-full min-h-screen">
        <div class="h-full overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 min-h-full flex flex-col">
                <div class="flex-1 p-6">
                    <!-- Header -->
                    <div class="mb-6">
                        <h2 class="text-3xl font-bold">Reports Management</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Generate comprehensive business reports</p>
                    </div>

                    <!-- Report Categories Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <!-- Sales Report Card -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-blue-500 rounded-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-blue-600 dark:text-blue-300 bg-blue-200 dark:bg-blue-700 px-2 py-1 rounded">New</span>
                            </div>
                            <h3 class="text-xl font-bold text-blue-900 dark:text-blue-100 mb-2">Sales Report</h3>
                            <p class="text-sm text-blue-700 dark:text-blue-300 mb-4">View sales performance, revenue, top products, and customer analytics</p>
                            <div class="flex gap-2">
                                <a href="{{ route('reports.sales') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded font-medium">
                                    View Report
                                </a>
                            </div>
                        </div>

                        <!-- Purchase Report Card -->
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-purple-500 rounded-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-purple-600 dark:text-purple-300 bg-purple-200 dark:bg-purple-700 px-2 py-1 rounded">New</span>
                            </div>
                            <h3 class="text-xl font-bold text-purple-900 dark:text-purple-100 mb-2">Purchase Report</h3>
                            <p class="text-sm text-purple-700 dark:text-purple-300 mb-4">Analyze purchase orders, supplier performance, and procurement expenses</p>
                            <div class="flex gap-2">
                                <a href="{{ route('reports.purchases') }}" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white text-center px-4 py-2 rounded font-medium">
                                    View Report
                                </a>
                            </div>
                        </div>

                        <!-- Inventory Report Card -->
                        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-green-500 rounded-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-green-600 dark:text-green-300 bg-green-200 dark:bg-green-700 px-2 py-1 rounded">New</span>
                            </div>
                            <h3 class="text-xl font-bold text-green-900 dark:text-green-100 mb-2">Inventory Report</h3>
                            <p class="text-sm text-green-700 dark:text-green-300 mb-4">Current stock levels, valuation, movement categories, and alerts</p>
                            <div class="flex gap-2">
                                <a href="{{ route('reports.inventory') }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded font-medium">
                                    View Report
                                </a>
                            </div>
                        </div>

                        <!-- Financial Report Card -->
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800 rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-yellow-500 rounded-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-yellow-600 dark:text-yellow-300 bg-yellow-200 dark:bg-yellow-700 px-2 py-1 rounded">New</span>
                            </div>
                            <h3 class="text-xl font-bold text-yellow-900 dark:text-yellow-100 mb-2">Financial Report</h3>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-4">Revenue, expenses, profit margins, payments, and invoices</p>
                            <div class="flex gap-2">
                                <a href="{{ route('reports.financial') }}" class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white text-center px-4 py-2 rounded font-medium">
                                    View Report
                                </a>
                            </div>
                        </div>

                        <!-- Stock Movement Report Card -->
                        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900 dark:to-indigo-800 rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-indigo-500 rounded-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-300 bg-indigo-200 dark:bg-indigo-700 px-2 py-1 rounded">New</span>
                            </div>
                            <h3 class="text-xl font-bold text-indigo-900 dark:text-indigo-100 mb-2">Stock Movement</h3>
                            <p class="text-sm text-indigo-700 dark:text-indigo-300 mb-4">Track all stock movements, adjustments, and activity</p>
                            <div class="flex gap-2">
                                <a href="{{ route('reports.movement') }}" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-center px-4 py-2 rounded font-medium">
                                    View Report
                                </a>
                            </div>
                        </div>

                        <!-- Reorder Items Report Card -->
                        <div class="bg-gradient-to-br from-rose-50 to-rose-100 dark:from-rose-900 dark:to-rose-800 rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-rose-500 rounded-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M9 3v18m6-18v18M4 7h16M4 12h16M4 17h16" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-rose-600 dark:text-rose-300 bg-rose-200 dark:bg-rose-700 px-2 py-1 rounded">New</span>
                            </div>
                            <h3 class="text-xl font-bold text-rose-900 dark:text-rose-100 mb-2">Reorder Items</h3>
                            <p class="text-sm text-rose-700 dark:text-rose-300 mb-4">See items below reorder level and create purchase orders quickly</p>
                            <div class="flex gap-2">
                                <a href="{{ route('reports.reorder') }}" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white text-center px-4 py-2 rounded font-medium">
                                    View Reorder List
                                </a>
                            </div>
                        </div>

                        <!-- Critical Level Items Report Card -->
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900 dark:to-orange-800 rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-orange-500 rounded-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-orange-600 dark:text-orange-300 bg-orange-200 dark:bg-orange-700 px-2 py-1 rounded">New</span>
                            </div>
                            <h3 class="text-xl font-bold text-orange-900 dark:text-orange-100 mb-2">Critical Level Items</h3>
                            <p class="text-sm text-orange-700 dark:text-orange-300 mb-4">Items at critical stock levels requiring immediate attention</p>
                            <div class="flex gap-2">
                                <a href="{{ route('reports.critical') }}" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white text-center px-4 py-2 rounded font-medium">
                                    View Critical Items
                                </a>
                            </div>
                        </div>

                        <!-- Blocked Items Report Card -->
                        <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900 dark:to-red-800 rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-red-500 rounded-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M9.172 16.172a4 4 0 015.656 0M7.05 7.05a7 7 0 019.9 9.9M12 19a7 7 0 110-14 7 7 0 010 14z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-red-600 dark:text-red-300 bg-red-200 dark:bg-red-700 px-2 py-1 rounded">New</span>
                            </div>
                            <h3 class="text-xl font-bold text-red-900 dark:text-red-100 mb-2">Blocked Items</h3>
                            <p class="text-sm text-red-700 dark:text-red-300 mb-4">See products that are refurbished, damaged, or wasted</p>
                            <div class="flex gap-2">
                                <a href="{{ route('reports.blocked') }}" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-center px-4 py-2 rounded font-medium">
                                    View Report
                                </a>
                            </div>
                        </div>

                        <!-- Custom Reports (Coming Soon) -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 rounded-lg shadow-lg p-6 hover:shadow-xl transition opacity-75">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-gray-400 rounded-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-200 dark:bg-gray-500 px-2 py-1 rounded">Coming Soon</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-700 dark:text-gray-200 mb-2">Custom Reports</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Build your own custom reports with flexible filters</p>
                            <div class="flex gap-2">
                                <button disabled class="flex-1 bg-gray-400 cursor-not-allowed text-white text-center px-4 py-2 rounded font-medium">
                                    Coming Soon
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4">Quick Export Options</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Export reports to Excel (.xlsx)</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Generate PDF documents</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Custom date range filtering</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Real-time data analysis</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
