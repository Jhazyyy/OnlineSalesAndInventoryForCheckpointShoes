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
                                <svg class="w-8 h-8 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Financial Settings
                            </h1>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            Configure currency, tax rates, and financial display preferences.
                        </p>
                    </div>

                    <!-- Settings Form -->
                    <form action="{{ route('settings.financial.update') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Currency & Display Settings</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Default Currency -->
                                <div>
                                    <x-input-label for="default_currency" :value="__('Default Currency')" />
                                    <select id="default_currency" name="default_currency" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        @php
                                            $currencies = ['PHP' => 'PHP - Philippine Peso', 'USD' => 'USD - US Dollar', 'EUR' => 'EUR - Euro', 'GBP' => 'GBP - British Pound'];
                                            $currentCurrency = old('default_currency', $settings['default_currency'] ?? 'PHP');
                                        @endphp
                                        @foreach($currencies as $code => $label)
                                            <option value="{{ $code }}" {{ $currentCurrency === $code ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('default_currency')" />
                                </div>

                                <!-- Currency Symbol -->
                                <div>
                                    <x-input-label for="currency_symbol" :value="__('Currency Symbol')" />
                                    <x-text-input id="currency_symbol" name="currency_symbol" type="text" class="mt-1 block w-full" :value="old('currency_symbol', $settings['currency_symbol'] ?? '₱')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('currency_symbol')" />
                                </div>

                                <!-- Tax Rate -->
                                <div>
                                    <x-input-label for="tax_rate" :value="__('Tax Rate (%)')" />
                                    <x-text-input id="tax_rate" name="tax_rate" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full" :value="old('tax_rate', $settings['tax_rate'] ?? 12.0)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('tax_rate')" />
                                </div>

                                <!-- Tax Display -->
                                <div>
                                    <x-input-label for="tax_display" :value="__('Tax Display')" />
                                    <select id="tax_display" name="tax_display" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        @php $currentTaxDisplay = old('tax_display', $settings['tax_display'] ?? 'inclusive'); @endphp
                                        <option value="inclusive" {{ $currentTaxDisplay === 'inclusive' ? 'selected' : '' }}>Tax Inclusive</option>
                                        <option value="exclusive" {{ $currentTaxDisplay === 'exclusive' ? 'selected' : '' }}>Tax Exclusive</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('tax_display')" />
                                </div>

                                <!-- Decimal Precision -->
                                <div>
                                    <x-input-label for="decimal_precision" :value="__('Decimal Precision')" />
                                    <select id="decimal_precision" name="decimal_precision" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        @php $currentPrecision = old('decimal_precision', $settings['decimal_precision'] ?? 2); @endphp
                                        @for($i = 0; $i <= 4; $i++)
                                            <option value="{{ $i }}" {{ $currentPrecision == $i ? 'selected' : '' }}>{{ $i }} decimal places</option>
                                        @endfor
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('decimal_precision')" />
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex space-x-3">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                Save Financial Settings
                            </button>
                            <a href="{{ route('settings.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                Cancel
                            </a>
                        </div>
                    </form>

                    <!-- Preview -->
                    <div class="mt-8 bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Format Preview</h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>Currency Format:</strong> {{ formatCurrency(1234.56) }}</p>
                            <p><strong>Tax Rate:</strong> {{ $settings['tax_rate'] ?? 12.0 }}%</p>
                            <p><strong>Tax Display:</strong> {{ ucfirst($settings['tax_display'] ?? 'inclusive') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>