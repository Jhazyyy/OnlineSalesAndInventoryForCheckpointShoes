<x-app-layout>
    <div class="w-full min-h-screen">
        <div class="h-full overflow-y-auto">
            <div class="bg-white dark:bg-gray-800 min-h-full flex flex-col">
                <div class="flex-1 p-6">
                    <!-- Header Section -->
                    <div class="mb-6">
                        <div class="flex items-center mb-4">
                            <a href="{{ route('settings.index') }}" class="mr-4 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                            </a>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-8 h-8 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Notification Settings
                            </h1>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            Configure email alerts, system notifications, and messaging preferences.
                        </p>
                    </div>

                    <!-- Settings Form -->
                    <form action="{{ route('settings.notifications.update') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Email Notifications -->
                        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Email Notifications</h2>
                            
                            <div class="mb-6">
                                <!-- Admin Email -->
                                <div>
                                    <x-input-label for="admin_email" :value="__('Administrator Email')" />
                                    <x-text-input id="admin_email" name="admin_email" type="email" class="mt-1 block w-full" :value="old('admin_email', $settings['admin_email'] ?? '')" placeholder="admin@checkpoint.com" />
                                    <x-input-error class="mt-2" :messages="$errors->get('admin_email')" />
                                    <p class="mt-1 text-sm text-gray-500">Email address for system notifications and alerts</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <!-- Email Notifications Enabled -->
                                <div class="flex items-start">
                                    <input id="email_notifications_enabled" name="email_notifications_enabled" type="checkbox" value="1" {{ old('email_notifications_enabled', $settings['email_notifications_enabled'] ?? true) ? 'checked' : '' }} class="mt-1 mr-3 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <div>
                                        <label for="email_notifications_enabled" class="text-sm font-medium text-gray-700 dark:text-gray-300">Enable Email Notifications</label>
                                        <p class="text-sm text-gray-500">Send notifications via email to administrators and users</p>
                                    </div>
                                </div>

                                <!-- Low Stock Alerts -->
                                <div class="flex items-start">
                                    <input id="low_stock_alerts" name="low_stock_alerts" type="checkbox" value="1" {{ old('low_stock_alerts', $settings['low_stock_alerts'] ?? true) ? 'checked' : '' }} class="mt-1 mr-3 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <div>
                                        <label for="low_stock_alerts" class="text-sm font-medium text-gray-700 dark:text-gray-300">Low Stock Alerts</label>
                                        <p class="text-sm text-gray-500">Notify when products fall below the low stock threshold</p>
                                    </div>
                                </div>

                                <!-- Order Status Notifications -->
                                <div class="flex items-start">
                                    <input id="order_status_notifications" name="order_status_notifications" type="checkbox" value="1" {{ old('order_status_notifications', $settings['order_status_notifications'] ?? true) ? 'checked' : '' }} class="mt-1 mr-3 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <div>
                                        <label for="order_status_notifications" class="text-sm font-medium text-gray-700 dark:text-gray-300">Order Status Updates</label>
                                        <p class="text-sm text-gray-500">Send notifications when order status changes (confirmed, shipped, delivered)</p>
                                    </div>
                                </div>

                                <!-- Payment Confirmations -->
                                <div class="flex items-start">
                                    <input id="payment_confirmations" name="payment_confirmations" type="checkbox" value="1" {{ old('payment_confirmations', $settings['payment_confirmations'] ?? true) ? 'checked' : '' }} class="mt-1 mr-3 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <div>
                                        <label for="payment_confirmations" class="text-sm font-medium text-gray-700 dark:text-gray-300">Payment Confirmations</label>
                                        <p class="text-sm text-gray-500">Send payment confirmation emails to customers</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex space-x-3">
                            <button type="submit" class="bg-blue-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                Save
                            </button>
                            <a href="{{ route('settings.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                Cancel
                            </a>
                        </div>
                    </form>

                    <!-- Current Settings Preview -->
                    <div class="mt-8 bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Notification Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Email Configuration</h4>
                                <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    <li><strong>Admin Email:</strong> {{ $settings['admin_email'] ?? 'Not set' }}</li>
                                    <li><strong>Email Notifications:</strong> {{ ($settings['email_notifications_enabled'] ?? true) ? 'Enabled' : 'Disabled' }}</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Alert Types</h4>
                                <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    <li><strong>Low Stock Alerts:</strong> {{ ($settings['low_stock_alerts'] ?? true) ? 'Enabled' : 'Disabled' }}</li>
                                    <li><strong>Order Updates:</strong> {{ ($settings['order_status_notifications'] ?? true) ? 'Enabled' : 'Disabled' }}</li>
                                    <li><strong>Payment Confirmations:</strong> {{ ($settings['payment_confirmations'] ?? true) ? 'Enabled' : 'Disabled' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>