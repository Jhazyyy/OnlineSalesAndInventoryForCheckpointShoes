<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Delivery:
                        {{ $delivery->delivery_number }}</h2>

                    <form action="{{ route('purchases.deliveries.update', $delivery->delivery_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Purchase Order -->
                            <div>
                                <label for="purchase_order_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Purchase Order
                                    *</label>
                                <select id="purchase_order_id" name="purchase_order_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Purchase Order</option>
                                    @foreach ($purchase_orders as $order)
                                        <option value="{{ $order['id'] }}"
                                            {{ old('purchase_order_id', $delivery->purchase_order_id) == $order['id'] ? 'selected' : '' }}>
                                            {{ $order['order_number'] }} - {{ $order['supplier_name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('purchase_order_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Delivery Date -->
                            <div>
                                <label for="delivery_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Date
                                    *</label>
                                <input type="date" id="delivery_date" name="delivery_date"
                                    value="{{ old('delivery_date', $delivery->delivery_date->format('Y-m-d')) }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('delivery_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Carrier -->
                            <div>
                                <label for="carrier"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Carrier</label>
                                <select id="carrier" name="carrier"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Carrier</option>
                                    @foreach ($carriers as $carrier)
                                        <option value="{{ $carrier['id'] }}"
                                            {{ old('carrier', $delivery->carrier) == $carrier['id'] ? 'selected' : '' }}>
                                            {{ $carrier['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tracking Number -->
                            <div>
                                <label for="tracking_number"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tracking
                                    Number</label>
                                <input type="text" id="tracking_number" name="tracking_number"
                                    value="{{ old('tracking_number', $delivery->tracking_number) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    readonly>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Tracking number is auto-generated and cannot be changed
                                </p>
                            </div>

                            <!-- Scheduled Delivery Date -->
                            <div>
                                <label for="scheduled_delivery_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scheduled
                                    Delivery</label>
                                <input type="date" id="scheduled_delivery_date" name="scheduled_delivery_date"
                                    value="{{ old('scheduled_delivery_date', $delivery->scheduled_delivery_date?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Actual Delivery Date -->
                            <div>
                                <label for="actual_delivery_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Actual
                                    Delivery</label>
                                <input type="date" id="actual_delivery_date" name="actual_delivery_date"
                                    value="{{ old('actual_delivery_date', $delivery->actual_delivery_date?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select id="status" name="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="scheduled"
                                        {{ old('status', $delivery->status) == 'scheduled' ? 'selected' : '' }}>
                                        Scheduled</option>
                                    <option value="in_transit"
                                        {{ old('status', $delivery->status) == 'in_transit' ? 'selected' : '' }}>In
                                        Transit</option>
                                    <option value="out_for_delivery"
                                        {{ old('status', $delivery->status) == 'out_for_delivery' ? 'selected' : '' }}>
                                        Out for Delivery</option>
                                    <option value="delivered"
                                        {{ old('status', $delivery->status) == 'delivered' ? 'selected' : '' }}>
                                        Delivered</option>
                                    <option value="delayed"
                                        {{ old('status', $delivery->status) == 'delayed' ? 'selected' : '' }}>Delayed
                                    </option>
                                    <option value="failed"
                                        {{ old('status', $delivery->status) == 'failed' ? 'selected' : '' }}>Failed
                                    </option>
                                    <option value="cancelled"
                                        {{ old('status', $delivery->status) == 'cancelled' ? 'selected' : '' }}>
                                        Cancelled</option>
                                </select>
                            </div>

                            <!-- Priority -->
                            <div>
                                <label for="priority"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                <select id="priority" name="priority"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="low"
                                        {{ old('priority', $delivery->priority) == 'low' ? 'selected' : '' }}>Low
                                    </option>
                                    <option value="normal"
                                        {{ old('priority', $delivery->priority) == 'normal' ? 'selected' : '' }}>Normal
                                    </option>
                                    <option value="high"
                                        {{ old('priority', $delivery->priority) == 'high' ? 'selected' : '' }}>High
                                    </option>
                                    <option value="urgent"
                                        {{ old('priority', $delivery->priority) == 'urgent' ? 'selected' : '' }}>Urgent
                                    </option>
                                </select>
                            </div>

                            <!-- Recipient Name -->
                            {{-- <div>
                                <label for="recipient_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recipient
                                    Name</label>
                                <input type="text" id="recipient_name" name="recipient_name"
                                    value="{{ old('recipient_name', $delivery->recipient_name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div> --}}

                            <!-- Recipient Phone -->
                            {{-- <div>
                                <label for="recipient_phone"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recipient
                                    Phone</label>
                                <input type="text" id="recipient_phone" name="recipient_phone"
                                    value="{{ old('recipient_phone', $delivery->recipient_phone) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div> --}}

                            <!-- Shipping Cost -->
                            {{-- <div>
                                <label for="shipping_cost"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shipping
                                    Cost</label>
                                <input type="number" id="shipping_cost" name="shipping_cost" step="0.01" min="0"
                                    value="{{ old('shipping_cost', $delivery->shipping_cost ?? 0) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="0.00">
                                @error('shipping_cost')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div> --}}

                            <!-- Delivery Address -->
                            {{-- <div class="md:col-span-2">
                                <label for="delivery_address"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery
                                    Address</label>
                                <textarea id="delivery_address" name="delivery_address" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('delivery_address', $delivery->delivery_address) }}</textarea>
                            </div> --}}

                            <!-- Delivery Notes -->
                            <div class="md:col-span-2">
                                <label for="delivery_notes"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery
                                    Notes</label>
                                <textarea id="delivery_notes" name="delivery_notes" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('delivery_notes', $delivery->delivery_notes) }}</textarea>
                            </div>

                            <!-- Damage Notes -->
                            <div class="md:col-span-2">
                                <label for="damage_notes"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Damage
                                    Notes</label>
                                <textarea id="damage_notes" name="damage_notes" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('damage_notes', $delivery->damage_notes) }}</textarea>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Delivery Items</h3>

                            @if ($delivery->items->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Product</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Expected</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Delivered</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Damaged</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Unit Price</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Condition</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                    Total</th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach ($delivery->items as $index => $item)
                                                <tr>
                                                    <td class="px-6 py-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $item->product->product_name ?? 'N/A' }}
                                                        </div>
                                                        @if ($item->product && $item->product->sku)
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">SKU:
                                                                {{ $item->product->sku }}</div>
                                                        @endif
                                                        <input type="hidden" name="items[{{ $index }}][product_id]"
                                                            value="{{ $item->product_id }}">
                                                        <input type="hidden" name="items[{{ $index }}][condition]"
                                                            value="{{ $item->condition }}">
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <input type="number" name="items[{{ $index }}][quantity_expected]" min="0"
                                                            value="{{ old('items.' . $index . '.quantity_expected', $item->quantity_expected) }}"
                                                            class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <input type="number" name="items[{{ $index }}][quantity_delivered]" min="0"
                                                            value="{{ old('items.' . $index . '.quantity_delivered', $item->quantity_delivered) }}"
                                                            class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <input type="number" name="items[{{ $index }}][quantity_damaged]" min="0"
                                                            value="{{ old('items.' . $index . '.quantity_damaged', $item->quantity_damaged ?? 0) }}"
                                                            class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <input type="number" name="items[{{ $index }}][unit_price]" min="0" step="0.01"
                                                            value="{{ old('items.' . $index . '.unit_price', $item->unit_price) }}"
                                                            class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->condition_badge_class }}">
                                                            {{ ucwords($item->condition) }}
                                                        </span>
                                                    </td>
                                                    <td
                                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                        ₱{{ number_format($item->line_total, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <td colspan="6"
                                                    class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                                    Total:
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                                    ₱{{ number_format($delivery->total_amount_delivered, 2) }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">No items in this delivery.</p>
                                <input type="hidden" name="items[0][product_id]" value="1">
                                <input type="hidden" name="items[0][quantity_expected]" value="0">
                                <input type="hidden" name="items[0][quantity_delivered]" value="0">
                                <input type="hidden" name="items[0][unit_price]" value="0">
                                <input type="hidden" name="items[0][condition]" value="good">
                            @endif
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('purchases.deliveries.show', $delivery->delivery_id) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Update Delivery
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
