<x-app-layout>
    <div class="w-full h-screen">
        <div :class="navOpen ? 'flex-1' : 'w-full'" class="h-full overflow-y-auto">
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
                                <svg class="w-8 h-8 mr-3 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Inventory Settings
                            </h1>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            Configure stock thresholds, auto-reorder settings, and inventory management preferences.
                        </p>
                    </div>

                    <!-- Settings Form -->
                    <form action="{{ route('settings.inventory.update') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Stock Level Management</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Low Stock Threshold -->
                                <div>
                                    <x-input-label for="low_stock_threshold" :value="__('Low Stock Threshold')" />
                                    <x-text-input id="low_stock_threshold" name="low_stock_threshold" type="number" min="0" class="mt-1 block w-full" :value="old('low_stock_threshold', $settings['low_stock_threshold'] ?? 10)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('low_stock_threshold')" />
                                    <p class="mt-1 text-sm text-gray-500">Alert when stock falls below this level</p>
                                </div>

                                <!-- Critical Stock Level -->
                                <div>
                                    <x-input-label for="critical_stock_level" :value="__('Critical Stock Level')" />
                                    <x-text-input id="critical_stock_level" name="critical_stock_level" type="number" min="0" class="mt-1 block w-full" :value="old('critical_stock_level', $settings['critical_stock_level'] ?? 5)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('critical_stock_level')" />
                                    <p class="mt-1 text-sm text-gray-500">Urgent restocking needed below this level</p>
                                </div>
                            </div>

                            <div class="mt-6 space-y-6">
                                <!-- Auto Reorder Enabled -->
                                <div class="flex items-start">
                                    <input id="auto_reorder_enabled" name="auto_reorder_enabled" type="checkbox" value="1" {{ old('auto_reorder_enabled', $settings['auto_reorder_enabled'] ?? false) ? 'checked' : '' }} class="mt-1 mr-3 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <div>
                                        <label for="auto_reorder_enabled" class="text-sm font-medium text-gray-700 dark:text-gray-300">Enable Auto-Reorder Suggestions</label>
                                        <p class="text-sm text-gray-500">System will suggest reorders when stock reaches low threshold</p>
                                    </div>
                                </div>

                                <!-- Waste Tracking Enabled -->
                                <div class="flex items-start">
                                    <input id="waste_tracking_enabled" name="waste_tracking_enabled" type="checkbox" value="1" {{ old('waste_tracking_enabled', $settings['waste_tracking_enabled'] ?? true) ? 'checked' : '' }} class="mt-1 mr-3 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <div>
                                        <label for="waste_tracking_enabled" class="text-sm font-medium text-gray-700 dark:text-gray-300">Enable Waste & Damage Tracking</label>
                                        <p class="text-sm text-gray-500">Track damaged or wasted inventory items</p>
                                    </div>
                                </div>

                                <!-- Negative Stock Allowed -->
                                <div class="flex items-start">
                                    <input id="negative_stock_allowed" name="negative_stock_allowed" type="checkbox" value="1" {{ old('negative_stock_allowed', $settings['negative_stock_allowed'] ?? false) ? 'checked' : '' }} class="mt-1 mr-3 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <div>
                                        <label for="negative_stock_allowed" class="text-sm font-medium text-gray-700 dark:text-gray-300">Allow Negative Stock Levels</label>
                                        <p class="text-sm text-gray-500">Permit sales when inventory shows negative quantities</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex space-x-3">
                            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                Save Inventory Settings
                            </button>
                            <a href="{{ route('settings.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                Cancel
                            </a>
                        </div>
                    </form>

                    <!-- Current Settings Preview -->
                    <div class="mt-8 bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Inventory Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Stock Thresholds</h4>
                                <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    <li><strong>Low Stock:</strong> {{ $settings['low_stock_threshold'] ?? 10 }} units</li>
                                    <li><strong>Critical Stock:</strong> {{ $settings['critical_stock_level'] ?? 5 }} units</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Features</h4>
                                <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    <li><strong>Auto-Reorder:</strong> {{ ($settings['auto_reorder_enabled'] ?? false) ? 'Enabled' : 'Disabled' }}</li>
                                    <li><strong>Waste Tracking:</strong> {{ ($settings['waste_tracking_enabled'] ?? true) ? 'Enabled' : 'Disabled' }}</li>
                                    <li><strong>Negative Stock:</strong> {{ ($settings['negative_stock_allowed'] ?? false) ? 'Allowed' : 'Not Allowed' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>