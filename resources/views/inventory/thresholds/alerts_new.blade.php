<x-app-layout>
<div class="py-6">
    <div class="w-full mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Inventory Alerts</h2>
                        <p class="text-gray-600 dark:text-gray-400">View and manage inventory threshold alerts</p>
                    </div>
                    <div class="flex space-x-3 mt-4 sm:mt-0">
                        <a href="{{ route('inventory.thresholds.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" id="markAllResolvedBtn">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mark All Resolved
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Alerts Card -->
            <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7H4l5-5v5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Alerts</p>
                        <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100">{{ $alerts->total() }}</p>
                    </div>
                </div>
            </div>

            <!-- Critical Alerts Card -->
            <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-red-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-red-600 dark:text-red-400">Critical</p>
                        <p class="text-2xl font-semibold text-red-900 dark:text-red-100">{{ $alerts->where('severity', 'critical')->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Warning Alerts Card -->
            <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Warning</p>
                        <p class="text-2xl font-semibold text-yellow-900 dark:text-yellow-100">{{ $alerts->where('severity', 'warning')->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Info Alerts Card -->
            <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-green-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">Info</p>
                        <p class="text-2xl font-semibold text-green-900 dark:text-green-100">{{ $alerts->where('severity', 'info')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('inventory.thresholds.alerts') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="severity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Severity</label>
                        <select id="severity" name="severity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">All Severities</option>
                            <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                            <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="info" {{ request('severity') === 'info' ? 'selected' : '' }}>Info</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Filter
                        </button>
                    </div>

                    <div class="flex items-end">
                        <a href="{{ route('inventory.thresholds.alerts') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Alerts List -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($alerts->count() > 0)
                    <div class="space-y-4">
                        @foreach($alerts as $alert)
                        <div class="border dark:border-gray-700 rounded-lg p-4 {{ $alert->status === 'resolved' ? 'bg-gray-50 dark:bg-gray-700' : 'bg-white dark:bg-gray-800' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <!-- Severity Badge -->
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $alert->severity === 'critical' ? 'bg-red-100 text-red-800' : ($alert->severity === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ ucfirst($alert->severity) }}
                                        </span>

                                        <!-- Alert Type Badge -->
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst(str_replace('_', ' ', $alert->alert_type)) }}
                                        </span>

                                        <!-- Status Badge -->
                                        @if($alert->status === 'resolved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Resolved
                                        </span>
                                        @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Active
                                        </span>
                                        @endif
                                    </div>

                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">
                                        {{ $alert->product->product_name ?? 'Unknown Product' }}
                                    </h3>
                                    
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        {{ $alert->message }}
                                    </p>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Current Stock:</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $alert->current_quantity ?? 'N/A' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Threshold:</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $alert->threshold_value ?? 'N/A' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Created:</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $alert->created_at ? $alert->created_at->format('M j, Y g:i A') : 'N/A' }}</span>
                                        </div>
                                        @if($alert->status === 'resolved')
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Resolved:</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $alert->resolved_at ? $alert->resolved_at->format('M j, Y g:i A') : 'N/A' }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2 ml-4">
                                    @if($alert->status !== 'resolved')
                                    <button type="button" 
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 active:bg-green-700 transition ease-in-out duration-150"
                                            onclick="resolveAlert({{ $alert->id }})">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Resolve
                                    </button>
                                    @endif

                                    @if($alert->product)
                                    <a href="{{ route('inventory.thresholds.products', ['search' => $alert->product->sku]) }}" 
                                       class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Manage
                                    </a>
                                    @endif

                                    <button type="button" 
                                            class="inline-flex items-center px-3 py-1 border border-red-300 text-xs leading-4 font-medium rounded-md text-red-700 bg-white hover:text-red-500 focus:outline-none focus:border-red-300 focus:ring focus:ring-red-200 active:text-red-800 active:bg-red-50 transition ease-in-out duration-150"
                                            onclick="deleteAlert({{ $alert->id }})">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $alerts->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7H4l5-5v5z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No alerts found</h3>
                        <p class="mt-1 text-sm text-gray-500">No inventory alerts match your current filters.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Mark all resolved button
    $('#markAllResolvedBtn').click(function() {
        if (confirm('Are you sure you want to mark all active alerts as resolved?')) {
            $.ajax({
                url: '{{ route("inventory.thresholds.alerts.resolve-all") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Success! ' + response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Failed to resolve alerts');
                }
            });
        }
    });
});

// Resolve single alert
function resolveAlert(alertId) {
    $.ajax({
        url: '/inventory/thresholds/alerts/' + alertId + '/resolve',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error resolving alert: ' + response.message);
            }
        },
        error: function() {
            alert('Failed to resolve alert');
        }
    });
}

// Delete alert
function deleteAlert(alertId) {
    if (confirm('Are you sure you want to delete this alert? This action cannot be undone.')) {
        $.ajax({
            url: '/inventory/thresholds/alerts/' + alertId,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error deleting alert: ' + response.message);
                }
            },
            error: function() {
                alert('Failed to delete alert');
            }
        });
    }
}
</script>
@endpush

</x-app-layout>