<x-app-layout>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Inventory Alerts</h1>
        <div>
            <button type="button" class="btn btn-success" id="bulkResolveBtn">
                <i class="fas fa-check"></i> Resolve Selected
            </button>
            <a href="{{ route('inventory.thresholds.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Alert Type</label>
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="low_stock" {{ request('type') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                <option value="critical_stock" {{ request('type') === 'critical_stock' ? 'selected' : '' }}>Critical Stock</option>
                                <option value="overstocked" {{ request('type') === 'overstocked' ? 'selected' : '' }}>Overstocked</option>
                                <option value="reorder_needed" {{ request('type') === 'reorder_needed' ? 'selected' : '' }}>Reorder Needed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Severity</label>
                            <select name="severity" class="form-control">
                                <option value="">All Severities</option>
                                <option value="info" {{ request('severity') === 'info' ? 'selected' : '' }}>Info</option>
                                <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Warning</option>
                                <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('inventory.thresholds.alerts') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Alerts Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Alerts ({{ $alerts->total() }})</h6>
            <div>
                <input type="checkbox" id="selectAll" class="mr-2">
                <label for="selectAll" class="mb-0 mr-3">Select All</label>
            </div>
        </div>
        <div class="card-body">
            @if($alerts->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="alertsTable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" class="select-all-checkbox">
                            </th>
                            <th>Product</th>
                            <th>Alert Type</th>
                            <th>Severity</th>
                            <th>Message</th>
                            <th>Current Stock</th>
                            <th>Threshold</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alerts as $alert)
                        <tr class="{{ $alert->is_resolved ? 'table-light' : '' }}" data-alert-id="{{ $alert->id }}">
                            <td>
                                @if(!$alert->is_resolved)
                                <input type="checkbox" name="alert_ids[]" value="{{ $alert->id }}" class="alert-checkbox">
                                @endif
                            </td>
                            <td>
                                <strong>{{ $alert->product->product_name ?? 'N/A' }}</strong>
                                <br>
                                <small class="text-muted">{{ $alert->product->product_brand ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <span class="badge badge-{{ $alert->alert_type === 'critical_stock' ? 'danger' : ($alert->alert_type === 'low_stock' ? 'warning' : 'info') }}">
                                    {{ ucfirst(str_replace('_', ' ', $alert->alert_type)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'warning' ? 'warning' : 'info') }}">
                                    {{ ucfirst($alert->severity) }}
                                </span>
                            </td>
                            <td>
                                {{ $alert->alert_message }}
                                @if($alert->additional_data)
                                    <br>
                                    <small class="text-muted">
                                        @php
                                            $data = json_decode($alert->additional_data, true);
                                        @endphp
                                        @if(is_array($data))
                                            @foreach($data as $key => $value)
                                                {{ ucfirst($key) }}: {{ $value }}
                                                @if(!$loop->last), @endif
                                            @endforeach
                                        @endif
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($alert->product)
                                    <span class="badge badge-{{ $alert->product->quantity <= ($alert->product->critical_level ?? 0) ? 'danger' : ($alert->product->quantity <= ($alert->product->reorder_level ?? 0) ? 'warning' : 'success') }}">
                                        {{ $alert->product->quantity }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($alert->product)
                                    @switch($alert->alert_type)
                                        @case('critical_stock')
                                            Critical: {{ $alert->product->critical_level ?? '-' }}
                                            @break
                                        @case('low_stock')
                                        @case('reorder_needed')
                                            Reorder: {{ $alert->product->reorder_level ?? '-' }}
                                            @break
                                        @case('overstocked')
                                            Ceiling: {{ $alert->product->ceiling_level ?? '-' }}
                                            @break
                                        @default
                                            -
                                    @endswitch
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($alert->is_resolved)
                                    <span class="badge badge-success">Resolved</span>
                                    @if($alert->resolved_at)
                                        <br>
                                        <small class="text-muted">{{ $alert->resolved_at->format('M j, Y H:i') }}</small>
                                    @endif
                                @else
                                    <span class="badge badge-warning">Active</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $alert->created_at->format('M j, Y') }}
                                    <br>
                                    {{ $alert->created_at->format('H:i') }}
                                </small>
                            </td>
                            <td>
                                @if(!$alert->is_resolved)
                                    <button type="button" class="btn btn-sm btn-success resolve-alert" data-alert-id="{{ $alert->id }}" title="Resolve Alert">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $alerts->appends(request()->query())->links() }}
            @else
            <div class="text-center py-4">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No alerts found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['type', 'status', 'severity']))
                        Try adjusting your filters to see more alerts.
                    @else
                        Great! No alerts to show at the moment.
                    @endif
                </p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Alert Details Modal -->
<div class="modal fade" id="alertDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alert Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="alertDetailsContent">
                <!-- Alert details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="resolveFromModal">Resolve Alert</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    var currentAlertId = null;
    
    // Select all functionality
    $('#selectAll, .select-all-checkbox').change(function() {
        $('.alert-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    // Individual checkbox change
    $('.alert-checkbox').change(function() {
        var allChecked = $('.alert-checkbox').length === $('.alert-checkbox:checked').length;
        $('#selectAll, .select-all-checkbox').prop('checked', allChecked);
    });
    
    // Bulk resolve alerts
    $('#bulkResolveBtn').click(function() {
        var selectedAlerts = $('.alert-checkbox:checked');
        
        if (selectedAlerts.length === 0) {
            Swal.fire('Warning!', 'Please select at least one alert to resolve.', 'warning');
            return;
        }
        
        var alertIds = selectedAlerts.map(function() { return $(this).val(); }).get();
        
        Swal.fire({
            title: 'Resolve Alerts?',
            text: `Are you sure you want to resolve ${alertIds.length} selected alert(s)?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, resolve them',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                bulkResolveAlerts(alertIds);
            }
        });
    });
    
    // Single alert resolve
    $('.resolve-alert').click(function() {
        var alertId = $(this).data('alert-id');
        
        Swal.fire({
            title: 'Resolve Alert?',
            text: 'Are you sure you want to resolve this alert?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, resolve it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                resolveAlert(alertId);
            }
        });
    });
    
    // Alert row click for details (optional)
    $('tbody tr').click(function(e) {
        // Don't trigger if clicking on checkbox or button
        if ($(e.target).is('input, button, .btn, .fas')) {
            return;
        }
        
        var alertId = $(this).data('alert-id');
        if (alertId) {
            showAlertDetails(alertId);
        }
    });
    
    function resolveAlert(alertId) {
        $.ajax({
            url: `/inventory/thresholds/alerts/${alertId}/resolve`,
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
                Swal.fire('Error!', 'Failed to resolve alert', 'error');
            }
        });
    }
    
    function bulkResolveAlerts(alertIds) {
        $.ajax({
            url: '{{ route("inventory.thresholds.alerts.bulk-resolve") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                alert_ids: alertIds
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', `${alertIds.length} alert(s) resolved successfully`, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', response.message || 'Failed to resolve alerts', 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Failed to resolve alerts', 'error');
            }
        });
    }
    
    function showAlertDetails(alertId) {
        // In a real implementation, you would load alert details via AJAX
        // For now, just show the modal with basic info
        currentAlertId = alertId;
        
        var row = $(`tr[data-alert-id="${alertId}"]`);
        var productName = row.find('td:eq(1) strong').text();
        var alertType = row.find('td:eq(2) .badge').text();
        var severity = row.find('td:eq(3) .badge').text();
        var message = row.find('td:eq(4)').contents().first().text().trim();
        
        $('#alertDetailsContent').html(`
            <div class="alert alert-info">
                <h6>Product: ${productName}</h6>
                <p><strong>Type:</strong> ${alertType}</p>
                <p><strong>Severity:</strong> ${severity}</p>
                <p><strong>Message:</strong> ${message}</p>
            </div>
        `);
        
        $('#alertDetailsModal').modal('show');
    }
    
    // Resolve from modal
    $('#resolveFromModal').click(function() {
        if (currentAlertId) {
            resolveAlert(currentAlertId);
            $('#alertDetailsModal').modal('hide');
        }
    });
    
    // Add tooltips
    $('[title]').tooltip();
});
</script>

</x-app-layout>
