<x-app-layout>
    <div class="py-6">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Alert Details</h2>
                        <p class="text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $alert->alert_type)) }} • {{ ucfirst($alert->severity) }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('inventory.thresholds.alerts') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">Back</a>
                        @if($alert->status !== 'resolved')
                        <button onclick="resolveSingle()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">Resolve</button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $alert->severity === 'critical' ? 'bg-red-100 text-red-800' : ($alert->severity === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($alert->severity) }}
                            </span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $alert->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($alert->status) }}
                            </span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $alert->message }}</h3>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-300">
                            <div class="flex justify-between"><dt>Created</dt><dd>{{ $alert->created_at?->format('M j, Y g:i A') }}</dd></div>
                            <div class="flex justify-between"><dt>Resolved</dt><dd>{{ $alert->resolved_at?->format('M j, Y g:i A') ?? '-' }}</dd></div>
                            <div class="flex justify-between"><dt>Current Qty</dt><dd>{{ $alert->alert_data['current_quantity'] ?? 'N/A' }}</dd></div>
                            <div class="flex justify-between"><dt>Threshold</dt><dd>{{ $alert->alert_data['reorder_level'] ?? $alert->alert_data['critical_level'] ?? $alert->alert_data['ceiling_level'] ?? 'N/A' }}</dd></div>
                            @if(isset($alert->alert_data['suggested_quantity']))
                                <div class="flex justify-between"><dt>Suggested Qty</dt><dd>{{ $alert->alert_data['suggested_quantity'] }}</dd></div>
                            @endif
                        </dl>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-3">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Product</h4>
                        <p class="text-gray-800 dark:text-gray-200 font-medium">{{ $alert->product?->product_name ?? 'Unknown' }}</p>
                        @if($alert->product)
                        <a href="{{ route('inventory.thresholds.show', $alert->product) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500">Manage thresholds</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    async function resolveSingle() {
        try {
            const res = await fetch(`{{ route('inventory.thresholds.alerts.resolve', $alert) }}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await res.json();
            if (res.ok && data.success) {
                location.reload();
            } else {
                alert('Failed to resolve alert');
            }
        } catch (e) {
            alert('Failed to resolve alert');
        }
    }
    </script>
    @endpush
</x-app-layout>
