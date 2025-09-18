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
                                <svg class="w-8 h-8 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                Sales Settings
                            </h1>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            Configure order prefixes, return policies, and sales preferences.
                        </p>
                    </div>

                    <!-- Settings Form -->
                    <form action="{{ route('settings.sales.update') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Order Configuration -->
                        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Order Configuration</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Order Prefix -->
                                <div>
                                    <x-input-label for="order_prefix" :value="__('Sales Order Prefix')" />
                                    <x-text-input id="order_prefix" name="order_prefix" type="text" class="mt-1 block w-full" :value="old('order_prefix', $settings['order_prefix'] ?? 'SO-')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('order_prefix')" />
                                    <p class="mt-1 text-sm text-gray-500">Prefix for sales order numbers (e.g., SO-001)</p>
                                </div>

                                <!-- Invoice Prefix -->
                                <div>
                                    <x-input-label for="invoice_prefix" :value="__('Invoice Prefix')" />
                                    <x-text-input id="invoice_prefix" name="invoice_prefix" type="text" class="mt-1 block w-full" :value="old('invoice_prefix', $settings['invoice_prefix'] ?? 'INV-')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('invoice_prefix')" />
                                    <p class="mt-1 text-sm text-gray-500">Prefix for invoice numbers (e.g., INV-001)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Return & Exchange Policies -->
                        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Return & Exchange Policies</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Return Policy Days -->
                                <div>
                                    <x-input-label for="return_policy_days" :value="__('Return Policy (Days)')" />
                                    <x-text-input id="return_policy_days" name="return_policy_days" type="number" min="0" max="365" class="mt-1 block w-full" :value="old('return_policy_days', $settings['return_policy_days'] ?? 30)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('return_policy_days')" />
                                    <p class="mt-1 text-sm text-gray-500">Number of days customers can return items</p>
                                </div>

                                <!-- Exchange Policy Days -->
                                <div>
                                    <x-input-label for="exchange_policy_days" :value="__('Exchange Policy (Days)')" />
                                    <x-text-input id="exchange_policy_days" name="exchange_policy_days" type="number" min="0" max="365" class="mt-1 block w-full" :value="old('exchange_policy_days', $settings['exchange_policy_days'] ?? 15)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('exchange_policy_days')" />
                                    <p class="mt-1 text-sm text-gray-500">Number of days customers can exchange items</p>
                                </div>
                            </div>

                            <div class="mt-6 space-y-6">
                                <!-- Require Receipt for Return -->
                                <div class="flex items-start">
                                    <input id="require_receipt_for_return" name="require_receipt_for_return" type="checkbox" value="1" {{ old('require_receipt_for_return', $settings['require_receipt_for_return'] ?? true) ? 'checked' : '' }} class="mt-1 mr-3 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <div>
                                        <label for="require_receipt_for_return" class="text-sm font-medium text-gray-700 dark:text-gray-300">Require Receipt for Returns</label>
                                        <p class="text-sm text-gray-500">Customers must present receipt/proof of purchase for returns</p>
                                    </div>
                                </div>

                                <!-- Allow Defective Returns -->
                                <div class="flex items-start">
                                    <input id="allow_defective_returns" name="allow_defective_returns" type="checkbox" value="1" {{ old('allow_defective_returns', $settings['allow_defective_returns'] ?? true) ? 'checked' : '' }} class="mt-1 mr-3 border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <div>
                                        <label for="allow_defective_returns" class="text-sm font-medium text-gray-700 dark:text-gray-300">Allow Returns for Defective Products</label>
                                        <p class="text-sm text-gray-500">Accept returns for defective or damaged merchandise</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex space-x-3">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                Save Sales Settings
                            </button>
                            <a href="{{ route('settings.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                Cancel
                            </a>
                        </div>
                    </form>

                    <!-- Current Settings Preview -->
                    <div class="mt-8 bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Sales Settings</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Order Configuration</h4>
                                <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    <li><strong>Sales Order Prefix:</strong> {{ $settings['order_prefix'] ?? 'SO-' }}</li>
                                    <li><strong>Invoice Prefix:</strong> {{ $settings['invoice_prefix'] ?? 'INV-' }}</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Return Policies</h4>
                                <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    <li><strong>Return Period:</strong> {{ $settings['return_policy_days'] ?? 30 }} days</li>
                                    <li><strong>Exchange Period:</strong> {{ $settings['exchange_policy_days'] ?? 15 }} days</li>
                                    <li><strong>Receipt Required:</strong> {{ ($settings['require_receipt_for_return'] ?? true) ? 'Yes' : 'No' }}</li>
                                    <li><strong>Defective Returns:</strong> {{ ($settings['allow_defective_returns'] ?? true) ? 'Allowed' : 'Not Allowed' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>