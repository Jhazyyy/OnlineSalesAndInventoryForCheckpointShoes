<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Shipment
                                {{ $shipment->shipment_number }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">Update shipment details and tracking information
                            </p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
                            <a href="{{ route('sales.shipments.show', $shipment->shipment_id) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <div class="font-bold">Please correct the following errors:</div>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Edit Form -->
            <form method="POST" action="{{ route('sales.shipments.update', $shipment->shipment_id) }}"
                class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Main Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sales
                                            Order</label>
                                        <p
                                            class="mt-1 text-sm text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-md">
                                            {{ $shipment->salesOrder->order_number }} -
                                            {{ $shipment->salesOrder->customer->display_name }}
                                        </p>
                                        <input type="hidden" name="sales_order_id"
                                            value="{{ $shipment->sales_order_id }}">
                                    </div>
                                    <div>
                                        <label for="priority"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                        <select id="priority" name="priority"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="low" {{ old('priority', $shipment->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="normal" {{ old('priority', $shipment->priority) == 'normal' ? 'selected' : '' }}>Normal</option>
                                            <option value="high" {{ old('priority', $shipment->priority) == 'high' ? 'selected' : '' }}>High</option>
                                            <option value="urgent" {{ old('priority', $shipment->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="shipment_date"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipment
                                            Date *</label>
                                        <input type="date" id="shipment_date" name="shipment_date"
                                            value="{{ old('shipment_date', $shipment->shipment_date?->format('Y-m-d')) }}"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="expected_delivery_date"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expected
                                            Delivery Date</label>
                                        <input type="date" id="expected_delivery_date" name="expected_delivery_date"
                                            value="{{ old('expected_delivery_date', $shipment->expected_delivery_date?->format('Y-m-d')) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Carrier Information -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Carrier Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="carrier"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Carrier</label>
                                        <input type="text" id="carrier" name="carrier"
                                            value="{{ old('carrier', $shipment->carrier) }}"
                                            placeholder="e.g., FedEx, UPS, DHL"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="service_type"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service
                                            Type</label>
                                        <input type="text" id="service_type" name="service_type"
                                            value="{{ old('service_type', $shipment->service_type) }}"
                                            placeholder="e.g., Ground, Express, Overnight"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="tracking_number"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tracking
                                            Number</label>
                                        <input type="text" id="tracking_number" name="tracking_number"
                                            value="{{ old('tracking_number', $shipment->tracking_number) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="reference_number"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reference
                                            Number</label>
                                        <input type="text" id="reference_number" name="reference_number"
                                            value="{{ old('reference_number', $shipment->reference_number) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recipient Information -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recipient
                                    Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="recipient_name"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recipient
                                            Name *</label>
                                        <input type="text" id="recipient_name" name="recipient_name"
                                            value="{{ old('recipient_name', $shipment->recipient_name) }}" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="recipient_phone"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recipient
                                            Phone</label>
                                        <input type="text" id="recipient_phone" name="recipient_phone"
                                            value="{{ old('recipient_phone', $shipment->recipient_phone) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="recipient_email"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recipient
                                            Email</label>
                                        <input type="email" id="recipient_email" name="recipient_email"
                                            value="{{ old('recipient_email', $shipment->recipient_email) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Addresses -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Addresses</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="shipping_address"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipping
                                            Address *</label>
                                        <textarea id="shipping_address" name="shipping_address" rows="3" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="Complete shipping address including name, street, city, state, ZIP">{{ old('shipping_address', $shipment->shipping_address) }}</textarea>
                                    </div>
                                    <div>
                                        <label for="billing_address"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Billing
                                            Address</label>
                                        <textarea id="billing_address" name="billing_address" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="Billing address (if different from shipping)">{{ old('billing_address', $shipment->billing_address) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipment Items -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Shipment Items</h3>
                                    <button type="button" id="add-item"
                                        class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Item
                                    </button>
                                </div>
                                <div id="items-container" class="space-y-4">
                                    @php
                                        $items = old('items', $shipment->items->map(function ($item) {
                                            return [
                                                'product_id' => $item->product_id,
                                                'quantity_shipped' => $item->quantity_shipped,
                                                'package_number' => $item->package_number,
                                            ];
                                        })->toArray());
                                    @endphp
                                    @foreach($items as $index => $item)
                                        <div class="item-row border border-gray-300 rounded-lg p-4">
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product
                                                        *</label>
                                                    <select name="items[{{ $index }}][product_id]" required
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->product_id }}" {{ $item['product_id'] == $product->product_id ? 'selected' : '' }}>
                                                                {{ $product->name }} ({{ $product->sku }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity
                                                        *</label>
                                                    <input type="number" name="items[{{ $index }}][quantity_shipped]"
                                                        value="{{ $item['quantity_shipped'] }}" min="1" required
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Package
                                                        #</label>
                                                    <input type="text" name="items[{{ $index }}][package_number]"
                                                        value="{{ $item['package_number'] ?? '1' }}"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                </div>
                                                <div class="flex items-end">
                                                    <button type="button"
                                                        class="remove-item w-full px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Additional Details -->
                    <div class="space-y-6">
                        <!-- Package Information -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Package Information
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="total_packages"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total
                                            Packages *</label>
                                        <input type="number" id="total_packages" name="total_packages"
                                            value="{{ old('total_packages', $shipment->total_packages) }}" min="1"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="total_weight"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total
                                            Weight (kg)</label>
                                        <input type="number" id="total_weight" name="total_weight" step="0.01" min="0"
                                            value="{{ old('total_weight', $shipment->total_weight) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Special Services -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Special Services
                                </h3>
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="is_insured" name="is_insured" value="1" {{ old('is_insured', $shipment->is_insured) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <label for="is_insured"
                                            class="ml-2 text-sm text-gray-700 dark:text-gray-300">Insurance
                                            Required</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="requires_signature" name="requires_signature"
                                            value="1" {{ old('requires_signature', $shipment->requires_signature) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <label for="requires_signature"
                                            class="ml-2 text-sm text-gray-700 dark:text-gray-300">Signature
                                            Required</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="is_fragile" name="is_fragile" value="1" {{ old('is_fragile', $shipment->is_fragile) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <label for="is_fragile"
                                            class="ml-2 text-sm text-gray-700 dark:text-gray-300">Fragile Items</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="is_perishable" name="is_perishable" value="1" {{ old('is_perishable', $shipment->is_perishable) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <label for="is_perishable"
                                            class="ml-2 text-sm text-gray-700 dark:text-gray-300">Perishable
                                            Items</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cost Information -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cost Information
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="shipping_cost"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipping
                                            Cost</label>
                                        <input type="number" id="shipping_cost" name="shipping_cost" step="0.01" min="0"
                                            value="{{ old('shipping_cost', $shipment->shipping_cost) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="insurance_cost"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Insurance
                                            Cost</label>
                                        <input type="number" id="insurance_cost" name="insurance_cost" step="0.01"
                                            min="0" value="{{ old('insurance_cost', $shipment->insurance_cost) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="additional_fees"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional
                                            Fees</label>
                                        <input type="number" id="additional_fees" name="additional_fees" step="0.01"
                                            min="0" value="{{ old('additional_fees', $shipment->additional_fees) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="insurance_value"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Insurance
                                            Value</label>
                                        <input type="number" id="insurance_value" name="insurance_value" step="0.01"
                                            min="0" value="{{ old('insurance_value', $shipment->insurance_value) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="special_instructions"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Special
                                            Instructions</label>
                                        <textarea id="special_instructions" name="special_instructions" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="Any special delivery instructions">{{ old('special_instructions', $shipment->special_instructions) }}</textarea>
                                    </div>
                                    <div>
                                        <label for="internal_notes"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Internal
                                            Notes</label>
                                        <textarea id="internal_notes" name="internal_notes" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="Internal notes for staff">{{ old('internal_notes', $shipment->internal_notes) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row gap-3 justify-end">
                            <a href="{{ route('sales.shipments.show', $shipment->shipment_id) }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Update Shipment
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let itemIndex = {{ count($items) }};

            // Add item functionality
            document.getElementById('add-item').addEventListener('click', function () {
                const container = document.getElementById('items-container');
                const newItem = createItemRow(itemIndex);
                container.appendChild(newItem);
                itemIndex++;
            });

            // Remove item functionality
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
                    const itemRow = e.target.closest('.item-row');
                    if (document.querySelectorAll('.item-row').length > 1) {
                        itemRow.remove();
                    } else {
                        alert('At least one item is required');
                    }
                }
            });

            function createItemRow(index) {
                const div = document.createElement('div');
                div.className = 'item-row border border-gray-300 rounded-lg p-4';
                div.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product *</label>
                            <select name="items[\${index}][product_id]" required 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->product_id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity *</label>
                            <input type="number" name="items[\${index}][quantity_shipped]" value="1" min="1" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Package #</label>
                            <input type="text" name="items[\${index}][package_number]" value="1"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div class="flex items-end">
                            <button type="button" class="remove-item w-full px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Remove
                            </button>
                        </div>
                    </div>
                `;
                return div;
            }
        });
    </script>
</x-app-layout>