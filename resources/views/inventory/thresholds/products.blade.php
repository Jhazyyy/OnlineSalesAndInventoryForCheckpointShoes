<x-app-layout>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Product Threshold Management</h1>
        <a href="{{ route('inventory.thresholds.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Product name or code...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Stock Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Products</option>
                                <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Low Stock</option>
                                <option value="critical" {{ request('status') === 'critical' ? 'selected' : '' }}>Critical Stock</option>
                                <option value="overstocked" {{ request('status') === 'overstocked' ? 'selected' : '' }}>Overstocked</option>
                                <option value="no_thresholds" {{ request('status') === 'no_thresholds' ? 'selected' : '' }}>No Thresholds Set</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('inventory.thresholds.products') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Products ({{ $products->total() }})</h6>
            <button type="button" class="btn btn-sm btn-success" id="bulkUpdateBtn">
                <i class="fas fa-edit"></i> Bulk Update Selected
            </button>
        </div>
        <div class="card-body">
            @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="productsTable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Product</th>
                            <th>Current Stock</th>
                            <th>Reorder Level</th>
                            <th>Critical Level</th>
                            <th>Stock Status</th>
                            <th>Alerts</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr data-product-id="{{ $product->product_id }}">
                            <td>
                                <input type="checkbox" name="product_ids[]" value="{{ $product->product_id }}" class="product-checkbox">
                            </td>
                            <td>
                                <strong>{{ $product->product_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $product->product_brand }}</small>
                            </td>
                            <td>
                                <span class="badge badge-{{ $product->isLowStock() ? ($product->isCriticalStock() ? 'danger' : 'warning') : 'success' }}">
                                    {{ $product->quantity }}
                                </span>
                            </td>
                            <td>
                                <span class="editable-threshold" data-field="reorder_level" data-product-id="{{ $product->product_id }}">
                                    {{ $product->reorder_level ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="editable-threshold" data-field="critical_level" data-product-id="{{ $product->product_id }}">
                                    {{ $product->critical_level ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusInfo = $product->getStockStatus();
                                @endphp
                                @foreach($statusInfo as $status)
                                    <span class="badge badge-{{ $status === 'critical_stock' ? 'danger' : ($status === 'low_stock' ? 'warning' : ($status === 'overstocked' ? 'info' : 'success')) }} mr-1">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                @if($product->threshold_alerts_enabled)
                                    <i class="fas fa-bell text-success" title="Alerts enabled"></i>
                                @else
                                    <i class="fas fa-bell-slash text-muted" title="Alerts disabled"></i>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary edit-thresholds" data-product-id="{{ $product->product_id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $products->appends(request()->query())->links() }}
            @else
            <div class="text-center py-4">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No products found</h5>
                <p class="text-muted">Try adjusting your filters or search criteria.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Product Thresholds Modal -->
<div class="modal fade" id="editThresholdsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product Thresholds</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="thresholdForm">
                @csrf
                <div class="modal-body">
                    <div id="productInfo" class="mb-3"></div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Reorder Level <i class="fas fa-info-circle" title="Stock level that triggers reorder alerts"></i></label>
                                <input type="number" name="reorder_level" class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Critical Level <i class="fas fa-info-circle" title="Critical low stock level"></i></label>
                                <input type="number" name="critical_level" class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ceiling Level <i class="fas fa-info-circle" title="Maximum stock level (overstocking alert)"></i></label>
                                <input type="number" name="ceiling_level" class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Floor Level <i class="fas fa-info-circle" title="Minimum stock level"></i></label>
                                <input type="number" name="floor_level" class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Economic Order Quantity</label>
                                <input type="number" name="economic_order_quantity" class="form-control" min="1" step="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lead Time (Days)</label>
                                <input type="number" name="lead_time_days" class="form-control" min="1" step="1">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Preferred Supplier</label>
                        <input type="text" name="preferred_supplier" class="form-control" maxlength="255">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="auto_reorder_enabled" class="form-check-input" id="autoReorder" value="1">
                                <label class="form-check-label" for="autoReorder">
                                    Enable Auto Reorder
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="threshold_alerts_enabled" class="form-check-input" id="thresholdAlerts" value="1">
                                <label class="form-check-label" for="thresholdAlerts">
                                    Enable Threshold Alerts
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Update Thresholds</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('inventory.thresholds.bulk-update') }}" method="POST" id="bulkUpdateForm">
                @csrf
                <div class="modal-body">
                    <div id="selectedCount" class="alert alert-info mb-3"></div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Reorder Level</label>
                                <input type="number" name="reorder_level" class="form-control" min="0" step="0.01">
                                <small class="form-text text-muted">Leave blank to keep existing values</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Critical Level</label>
                                <input type="number" name="critical_level" class="form-control" min="0" step="0.01">
                                <small class="form-text text-muted">Leave blank to keep existing values</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="threshold_alerts_enabled" class="form-check-input" id="bulkAlertsEnabled" value="1">
                            <label class="form-check-label" for="bulkAlertsEnabled">Enable threshold alerts</label>
                        </div>
                    </div>
                    
                    <input type="hidden" name="product_ids" id="bulkProductIds">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Selected Products</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    var currentProductId = null;
    
    // Select all checkbox
    $('#selectAll').change(function() {
        $('.product-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    // Edit thresholds button
    $('.edit-thresholds').click(function() {
        currentProductId = $(this).data('product-id');
        loadProductThresholds(currentProductId);
    });
    
    // Bulk update button
    $('#bulkUpdateBtn').click(function() {
        var selected = $('.product-checkbox:checked');
        
        if (selected.length === 0) {
            Swal.fire('Warning!', 'Please select at least one product.', 'warning');
            return;
        }
        
        var productIds = selected.map(function() { return $(this).val(); }).get();
        $('#bulkProductIds').val(JSON.stringify(productIds));
        $('#selectedCount').text(selected.length + ' products selected for bulk update');
        $('#bulkUpdateModal').modal('show');
    });
    
    // Threshold form submit
    $('#thresholdForm').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        $.ajax({
            url: `/inventory/thresholds/products/${currentProductId}/update`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message, 'success').then(() => {
                        $('#editThresholdsModal').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON?.errors;
                if (errors) {
                    var errorMsg = Object.values(errors).flat().join('\\n');
                    Swal.fire('Validation Error!', errorMsg, 'error');
                } else {
                    Swal.fire('Error!', 'Failed to update thresholds', 'error');
                }
            }
        });
    });
    
    // Load product thresholds
    function loadProductThresholds(productId) {
        // Get product data from the table row
        var row = $(`tr[data-product-id="${productId}"]`);
        var productName = row.find('td:eq(1) strong').text();
        var currentStock = row.find('td:eq(3) .badge').text();
        
        $('#productInfo').html(`
            <div class="card bg-light">
                <div class="card-body py-2">
                    <strong>${productName}</strong> - Current Stock: <span class="badge badge-info">${currentStock}</span>
                </div>
            </div>
        `);
        
        // Reset form
        $('#thresholdForm')[0].reset();
        
        // Load existing values (you might want to fetch these via AJAX)
        $('#editThresholdsModal').modal('show');
    }
    
    // Make threshold values inline editable
    $('.editable-threshold').click(function() {
        var element = $(this);
        var field = element.data('field');
        var productId = element.data('product-id');
        var currentValue = element.text().trim();
        
        if (currentValue === '-') currentValue = '';
        
        var input = $(`<input type="number" class="form-control form-control-sm" value="${currentValue}" min="0" step="0.01">`);
        element.html(input);
        input.focus().select();
        
        input.blur(function() {
            var newValue = $(this).val();
            updateThresholdField(productId, field, newValue, element);
        });
        
        input.keypress(function(e) {
            if (e.which === 13) { // Enter key
                $(this).blur();
            }
        });
    });
    
    function updateThresholdField(productId, field, value, element) {
        var data = {
            _token: '{{ csrf_token() }}'
        };
        data[field] = value;
        
        $.ajax({
            url: `/inventory/thresholds/products/${productId}/update`,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    element.text(value || '-');
                    // Show a brief success indicator
                    element.addClass('text-success').delay(1000).queue(function() {
                        $(this).removeClass('text-success').dequeue();
                    });
                } else {
                    element.text(element.data('original-value') || '-');
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function() {
                element.text(element.data('original-value') || '-');
                Swal.fire('Error!', 'Failed to update threshold', 'error');
            }
        });
    }
});
</script>

</x-app-layout>
