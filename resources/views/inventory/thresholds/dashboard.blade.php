<x-app-layout>
<div class="py-6">
    <div class="w-full mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Inventory Threshold Management</h2>
                        <p class="text-gray-600 dark:text-gray-400">Monitor and manage inventory thresholds and alerts</p>
                    </div>
                    <div class="flex space-x-3 mt-4 sm:mt-0">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" id="runThresholdCheck">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Run Threshold Check
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Products Card -->
            <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Products</p>
                        <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100">{{ number_format($stats['total_products']) }}</p>
                    </div>
                </div>
            </div>

            <!-- With Thresholds Card -->
            <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-green-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">With Thresholds</p>
                        <p class="text-2xl font-semibold text-green-900 dark:text-green-100">{{ number_format($stats['products_with_thresholds']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Alerts Card -->
            <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Active Alerts</p>
                        <p class="text-2xl font-semibold text-yellow-900 dark:text-yellow-100">{{ number_format($stats['active_alerts']) }}</p>
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
                        <p class="text-sm font-medium text-red-600 dark:text-red-400">Critical Alerts</p>
                        <p class="text-2xl font-semibold text-red-900 dark:text-red-100">{{ number_format($stats['critical_alerts']) }}</p>
                    </div>
                </div>
            </div>
        </div>

    <div class="row">
        <!-- Recent Alerts -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Alerts</h6>
                    <a href="{{ route('inventory.thresholds.alerts') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentAlerts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th>Severity</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAlerts as $alert)
                                    <tr>
                                        <td>{{ $alert->product->product_name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-secondary">{{ ucfirst($alert->alert_type) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'warning' ? 'warning' : 'info') }}">
                                                {{ ucfirst($alert->severity) }}
                                            </span>
                                        </td>
                                        <td>{{ $alert->created_at->format('M j, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-bell-slash fa-2x mb-2"></i>
                            <p>No recent alerts</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Products Needing Reorder -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Products Needing Reorder</h6>
                    <a href="{{ route('inventory.thresholds.products') }}?status=low" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($reorderProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current</th>
                                        <th>Reorder Level</th>
                                        <th>Suggested Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reorderProducts as $product)
                                    <tr>
                                        <td>{{ $product->product_name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $product->quantity <= ($product->critical_level ?? 0) ? 'danger' : 'warning' }}">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>
                                        <td>{{ $product->reorder_level ?? '-' }}</td>
                                        <td>{{ $product->getSuggestedOrderQuantity() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <p>No products need reordering</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('inventory.thresholds.products') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-cogs"></i> Manage Product Thresholds
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('inventory.thresholds.alerts') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-bell"></i> View All Alerts
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('inventory.thresholds.cycle-counts') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-clipboard-list"></i> Cycle Counts
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button type="button" class="btn btn-outline-success btn-block" id="bulkUpdateBtn">
                                <i class="fas fa-edit"></i> Bulk Update Thresholds
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Update Thresholds</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('inventory.thresholds.bulk-update') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Products</label>
                        <select name="product_ids[]" class="form-control select2" multiple required>
                            @foreach($lowStockProducts as $product)
                                <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Reorder Level</label>
                                <input type="number" name="reorder_level" class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Critical Level</label>
                                <input type="number" name="critical_level" class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="threshold_alerts_enabled" class="form-check-input" id="alertsEnabled" value="1">
                            <label class="form-check-label" for="alertsEnabled">Enable threshold alerts</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Thresholds</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // Run threshold check
    $('#runThresholdCheck').click(function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Running...');
        
        $.ajax({
            url: '{{ route("inventory.thresholds.run-check") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Failed to run threshold check', 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Run Threshold Check');
            }
        });
    });

    // Show bulk update modal
    $('#bulkUpdateBtn').click(function() {
        $('#bulkUpdateModal').modal('show');
    });

    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select products...'
    });
});
</script>

</x-app-layout>
