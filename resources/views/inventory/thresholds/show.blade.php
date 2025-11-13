<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $product->product_name }}</h2>
                        <p class="text-gray-600 dark:text-gray-400">{{ $product->sku }}
                        </p>
                        
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('inventory.thresholds.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back
                        </a>
                        {{-- <a href="{{ route('inventory.thresholds.alerts') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            View Alerts
                        </a> --}}
                    </div>
                </div>
            </div>

            <!-- Flash messages -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Summary cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Current Stock</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($product->quantity) }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Reorder Level</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $product->reorder_level !== null ? number_format($product->reorder_level) : '-' }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Critical Level</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $product->critical_level !== null ? number_format($product->critical_level) : '-' }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Suggested Order</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($thresholdData['suggested_order_quantity']) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Threshold details -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Status</h3>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @forelse($thresholdData['stock_status'] as $status)
                                    @php
                                        $map = [
                                            'out_of_stock' => 'bg-red-100 text-red-800',
                                            'critical_stock' => 'bg-red-100 text-red-800',
                                            'low_stock' => 'bg-yellow-100 text-yellow-800',
                                            'overstock' => 'bg-blue-100 text-blue-800',
                                            'reorder_needed' => 'bg-orange-100 text-orange-800',
                                        ];
                                    @endphp
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $map[$status['type']] ?? 'bg-green-100 text-green-800' }}">
                                        {{ ucwords(str_replace('_', ' ', $status['type'])) }}
                                    </span>
                                @empty
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Normal</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Threshold Settings</h3>
                                <dl class="mt-3 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <div class="flex justify-between">
                                        <dt>Ceiling Level</dt>
                                        <dd>{{ $product->ceiling_level !== null ? number_format($product->ceiling_level) : '-' }}
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt>Floor Level</dt>
                                        <dd>{{ $product->floor_level !== null ? number_format($product->floor_level) : '-' }}
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt>Lead Time (days)</dt>
                                        <dd>{{ $product->lead_time_days ?? '-' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt>EOQ</dt>
                                        <dd>{{ $product->economic_order_quantity ?? '-' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt>Auto Reorder</dt>
                                        <dd>{{ $product->auto_reorder_enabled ? 'Enabled' : 'Disabled' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt>Alerts</dt>
                                        <dd>{{ $product->threshold_alerts_enabled ? 'Enabled' : 'Disabled' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt>Preferred Supplier</dt>
                                        <dd>{{ $product->preferredSupplier?->supplier_name ?? '-' }}</dd>
                                    </div>
                                </dl>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Insights</h3>
                                <dl class="mt-3 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <div class="flex justify-between">
                                        <dt>Days until stockout</dt>
                                        <dd>{{ $thresholdData['days_until_stockout'] ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt>Reorder needed</dt>
                                        <dd>{{ $thresholdData['reorder_needed'] ? 'Yes' : 'No' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt>Coverage</dt>
                                        <dd>{{ $thresholdData['threshold_coverage'] }}%</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt>Last Checked</dt>
                                        <dd>{{ $product->last_threshold_check?->format('M j, Y g:i A') ?? '-' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent alerts -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Recent Alerts</h3>
                        @if ($recentAlerts->isEmpty())
                            <p class="text-sm text-gray-500 dark:text-gray-400">No alerts yet.</p>
                        @else
                            <ul class="space-y-3">
                                @foreach ($recentAlerts as $alert)
                                    <li class="border dark:border-gray-700 rounded p-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $alert->severity === 'critical' ? 'bg-red-100 text-red-800' : ($alert->severity === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                                    {{ ucfirst($alert->severity) }}
                                                </span>
                                                <span
                                                    class="text-xs text-gray-500">{{ $alert->created_at->diffForHumans() }}</span>
                                            </div>
                                            <a href="{{ route('inventory.thresholds.alerts.show', $alert) }}"
                                                class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Details</a>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $alert->message }}
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
