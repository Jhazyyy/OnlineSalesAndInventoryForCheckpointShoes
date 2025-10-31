<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Thresholds</h2>
                        <p class="text-gray-600 dark:text-gray-400">{{ $product->product_name }} • {{ $product->product_brand }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('inventory.thresholds.show', $product) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Cancel
                        </a>
                        <button type="submit" form="thresholdForm" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form id="thresholdForm" method="POST" action="{{ route('inventory.thresholds.update', $product) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Levels -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <label for="reorder_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reorder Level</label>
                                <input type="number" min="0" id="reorder_level" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="critical_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Critical Level</label>
                                <input type="number" min="0" id="critical_level" name="critical_level" value="{{ old('critical_level', $product->critical_level) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="ceiling_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ceiling Level</label>
                                <input type="number" min="0" id="ceiling_level" name="ceiling_level" value="{{ old('ceiling_level', $product->ceiling_level) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="floor_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Floor Level</label>
                                <input type="number" min="0" id="floor_level" name="floor_level" value="{{ old('floor_level', $product->floor_level) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <!-- EOQ / Lead Time / Supplier -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="economic_order_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Economic Order Quantity</label>
                                <input type="number" min="1" id="economic_order_quantity" name="economic_order_quantity" value="{{ old('economic_order_quantity', $product->economic_order_quantity) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="lead_time_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lead Time (days)</label>
                                <input type="number" min="1" id="lead_time_days" name="lead_time_days" value="{{ old('lead_time_days', $product->lead_time_days) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="preferred_supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preferred Supplier</label>
                                <select id="preferred_supplier_id" name="preferred_supplier_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">— None —</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->supplier_id }}" {{ (string)old('preferred_supplier_id', $product->preferred_supplier_id) === (string)$supplier->supplier_id ? 'selected' : '' }}>
                                            {{ $supplier->supplier_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Toggles -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <label class="inline-flex items-center">
                                <input type="hidden" name="auto_reorder_enabled" value="0" />
                                <input type="checkbox" name="auto_reorder_enabled" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('auto_reorder_enabled', $product->auto_reorder_enabled) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Enable Auto Reorder</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="hidden" name="threshold_alerts_enabled" value="0" />
                                <input type="checkbox" name="threshold_alerts_enabled" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('threshold_alerts_enabled', $product->threshold_alerts_enabled) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Enable Alerts</span>
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
