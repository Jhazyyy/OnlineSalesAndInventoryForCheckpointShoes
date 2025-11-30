<x-app-layout>
    <div class="py-2">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Supplier</h2>
                            <p class="text-gray-600 dark:text-gray-400">Create a new supplier record</p>
                        </div>
                        <a href="{{ route('master_data.suppliers.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Back to Suppliers
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('master_data.suppliers.store') }}" class="space-y-6">
                        @csrf

                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <!-- Supplier Name -->
                                <div>
                                    <x-input-label for="supplier_name" :value="__('Supplier Name')" />
                                    <x-text-input id="supplier_name" name="supplier_name" type="text"
                                        class="mt-1 block w-full" :value="old('supplier_name')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('supplier_name')" />
                                </div>

                                <!-- Contact Number -->
                                <div>
                                    <x-input-label for="phone" :value="__('Contact Number')" />
                                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                                        :value="old('phone')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <x-input-label for="email" :value="__('Email Address')" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                        :value="old('email')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <!-- Supplier Type -->
                                <div>
                                    <x-input-label for="supplier_type" :value="__('Supplier Type')" />
                                    <select id="supplier_type" name="supplier_type"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">Select Type</option>
                                        <option value="local" {{ old('supplier_type') === 'local' ? 'selected' : '' }}>
                                            Local</option>
                                        <option value="distributor"
                                            {{ old('supplier_type') === 'distributor' ? 'selected' : '' }}>Distributor
                                        </option>
                                        <option value="manufacturer"
                                            {{ old('supplier_type') === 'manufacturer' ? 'selected' : '' }}>Manufacturer
                                        </option>
                                        <option value="service_provider"
                                            {{ old('supplier_type') === 'service_provider' ? 'selected' : '' }}>Service
                                            Provider</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('supplier_type')" />
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Address Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
                                <!-- Address -->
                                <div class="md:col-span-1">
                                    <x-input-label for="address" :value="__('Street Address')" />
                                    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full"
                                        :value="old('address')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>

                                <!-- City -->
                                <div>
                                    <x-input-label for="city" :value="__('City')" />
                                    <x-text-input id="city" name="city" type="text" class="mt-1 block w-full"
                                        :value="old('city')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                </div>

                                <!-- State -->
                                <div>
                                    <x-input-label for="state" :value="__('State/Province')" />
                                    <x-text-input id="state" name="state" type="text" class="mt-1 block w-full"
                                        :value="old('state')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('state')" />
                                </div>

                                <!-- Postal Code -->
                                <div>
                                    <x-input-label for="postal_code" :value="__('Postal Code')" />
                                    <x-text-input id="postal_code" name="postal_code" type="text"
                                        class="mt-1 block w-full" :value="old('postal_code')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                                </div>

                                <!-- Country -->
                                <div>
                                    <x-input-label for="country" :value="__('Country')" />
                                    <x-text-input id="country" name="country" type="text" class="mt-1 block w-full"
                                        :value="old('country', 'Philippines')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('country')" />
                                </div>
                            </div>
                        </div>

                        <!-- Business Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Business Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                <!-- Tax ID -->
                                <div>
                                    <x-input-label for="tax_id" :value="__('Tax ID/Registration Number')" />
                                    <x-text-input id="tax_id" name="tax_id" type="text" class="mt-1 block w-full"
                                        :value="old('tax_id')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('tax_id')" />
                                </div>

                                <!-- Payment Terms -->
                                <div>
                                    <x-input-label for="payment_terms" :value="__('Payment Terms')" />
                                    <x-text-input id="payment_terms" name="payment_terms" type="text"
                                        class="mt-1 block w-full" :value="old('payment_terms')"
                                        placeholder="e.g., Net 30, COD, Prepaid" />
                                    <x-input-error class="mt-2" :messages="$errors->get('payment_terms')" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <x-input-label for="status" :value="__('Status')" />
                                    <select id="status" name="status"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        required>
                                        <option value="active"
                                            {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="4"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                placeholder="Additional notes about the supplier...">{{ old('notes') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                        </div>

                        <!-- Form Actions -->
                        <div
                            class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('master_data.suppliers.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Create Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
