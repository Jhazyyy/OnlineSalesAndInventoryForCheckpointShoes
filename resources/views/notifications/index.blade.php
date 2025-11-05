<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Notifications</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Stay updated with your latest activities and alerts</p>
            </div>

            <!-- Actions Bar -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Filter Tabs -->
                    <div class="flex flex-wrap gap-2">
                        <button class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors">
                            All
                            <span class="ml-2 px-2 py-0.5 text-xs bg-blue-600 dark:bg-blue-700 text-white rounded-full">24</span>
                        </button>
                        <button class="px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Unread
                            <span class="ml-2 px-2 py-0.5 text-xs bg-red-500 text-white rounded-full">5</span>
                        </button>
                        <button class="px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Orders
                        </button>
                        <button class="px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Inventory
                        </button>
                        <button class="px-4 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            System
                        </button>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button class="px-4 py-2 text-sm font-medium rounded-lg text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mark all as read
                        </button>
                        <button class="px-4 py-2 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Settings
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="space-y-3">
                
                <!-- Unread Notification Example - Low Stock Alert -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-red-500 hover:shadow-md transition-shadow">
                    <div class="p-4">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            Low Stock Alert
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            <span class="font-medium">Nike Air Max 270</span> stock is running low. Current quantity: <span class="font-semibold text-red-600 dark:text-red-400">5 units</span>
                                        </p>
                                        <div class="mt-2 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                5 minutes ago
                                            </span>
                                            <span class="px-2 py-0.5 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-full">
                                                Inventory
                                            </span>
                                        </div>
                                    </div>
                                    <!-- Unread Indicator -->
                                    <div class="flex-shrink-0">
                                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-3 flex gap-2">
                                    <button class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                                        View Details
                                    </button>
                                    <button class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md transition-colors">
                                        Dismiss
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unread Notification Example - New Order -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-green-500 hover:shadow-md transition-shadow">
                    <div class="p-4">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            New Order Received
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            Order <span class="font-mono font-semibold text-blue-600 dark:text-blue-400">#SO-2024-001234</span> has been placed by <span class="font-medium">John Doe</span>
                                        </p>
                                        <div class="mt-2 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                15 minutes ago
                                            </span>
                                            <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-full">
                                                Orders
                                            </span>
                                        </div>
                                    </div>
                                    <!-- Unread Indicator -->
                                    <div class="flex-shrink-0">
                                        <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-3 flex gap-2">
                                    <button class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                                        View Order
                                    </button>
                                    <button class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md transition-colors">
                                        Dismiss
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Read Notification Example - Purchase Order Approved -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-gray-300 dark:border-gray-600 hover:shadow-md transition-shadow opacity-75">
                    <div class="p-4">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            Purchase Order Approved
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            Purchase order <span class="font-mono font-semibold">#PO-2024-005678</span> has been approved and is ready for processing
                                        </p>
                                        <div class="mt-2 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                2 hours ago
                                            </span>
                                            <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full">
                                                Purchases
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Read Notification Example - Stock Adjustment -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-gray-300 dark:border-gray-600 hover:shadow-md transition-shadow opacity-75">
                    <div class="p-4">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            Stock Adjustment Completed
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            Stock adjustment for <span class="font-medium">15 products</span> has been successfully processed
                                        </p>
                                        <div class="mt-2 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Yesterday at 4:30 PM
                                            </span>
                                            <span class="px-2 py-0.5 bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded-full">
                                                Inventory
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Read Notification Example - System Update -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 border-gray-300 dark:border-gray-600 hover:shadow-md transition-shadow opacity-75">
                    <div class="p-4">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            System Maintenance Scheduled
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            System maintenance is scheduled for Sunday, November 10, 2025 from 2:00 AM to 4:00 AM
                                        </p>
                                        <div class="mt-2 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                3 days ago
                                            </span>
                                            <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full">
                                                System
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State (show when no notifications) -->
                {{-- 
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12">
                    <div class="text-center">
                        <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">No notifications</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">You're all caught up! Check back later for new updates.</p>
                    </div>
                </div>
                --}}

            </div>

            <!-- Load More -->
            <div class="mt-6 text-center">
                <button class="px-6 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Load More Notifications
                </button>
            </div>

        </div>
    </div>
</x-app-layout>
