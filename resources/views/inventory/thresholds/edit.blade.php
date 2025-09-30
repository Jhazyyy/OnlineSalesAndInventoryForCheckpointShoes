<x-app-layout>

@section('title', 'Edit Threshold Settings - ' . $product->product_name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Threshold Settings - {{ $product->product_name }}</h4>
                    <a href="{{ route('inventory.thresholds.show', $product) }}" class="btn btn-secondary float-right">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('inventory.thresholds.update', $product) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reorder_level">Reorder Level</label>
                                    <input type="number" class="form-control @error('reorder_level') is-invalid @enderror" 
                                           id="reorder_level" name="reorder_level" 
                                           value="{{ old('reorder_level', $product->reorder_level) }}" 
                                           min="0" step="0.01">
                                    @error('reorder_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="critical_level">Critical Level</label>
                                    <input type="number" class="form-control @error('critical_level') is-invalid @enderror" 
                                           id="critical_level" name="critical_level" 
                                           value="{{ old('critical_level', $product->critical_level) }}" 
                                           min="0" step="0.01">
                                    @error('critical_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ceiling_level">Ceiling Level</label>
                                    <input type="number" class="form-control @error('ceiling_level') is-invalid @enderror" 
                                           id="ceiling_level" name="ceiling_level" 
                                           value="{{ old('ceiling_level', $product->ceiling_level) }}" 
                                           min="0" step="0.01">
                                    @error('ceiling_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="floor_level">Floor Level</label>
                                    <input type="number" class="form-control @error('floor_level') is-invalid @enderror" 
                                           id="floor_level" name="floor_level" 
                                           value="{{ old('floor_level', $product->floor_level) }}" 
                                           min="0" step="0.01">
                                    @error('floor_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="economic_order_quantity">Economic Order Quantity</label>
                                    <input type="number" class="form-control @error('economic_order_quantity') is-invalid @enderror" 
                                           id="economic_order_quantity" name="economic_order_quantity" 
                                           value="{{ old('economic_order_quantity', $product->economic_order_quantity) }}" 
                                           min="1" step="0.01">
                                    @error('economic_order_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lead_time_days">Lead Time (Days)</label>
                                    <input type="number" class="form-control @error('lead_time_days') is-invalid @enderror" 
                                           id="lead_time_days" name="lead_time_days" 
                                           value="{{ old('lead_time_days', $product->lead_time_days) }}" 
                                           min="1">
                                    @error('lead_time_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="preferred_supplier_id">Preferred Supplier</label>
                            <select class="form-control @error('preferred_supplier_id') is-invalid @enderror" 
                                    id="preferred_supplier_id" name="preferred_supplier_id">
                                <option value="">Select a supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->supplier_id }}" 
                                            {{ old('preferred_supplier_id', $product->preferred_supplier_id) == $supplier->supplier_id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('preferred_supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="auto_reorder_enabled" 
                                       name="auto_reorder_enabled" value="1"
                                       {{ old('auto_reorder_enabled', $product->auto_reorder_enabled) ? 'checked' : '' }}>
                                <label class="form-check-label" for="auto_reorder_enabled">
                                    Enable Auto Reorder
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="threshold_alerts_enabled" 
                                       name="threshold_alerts_enabled" value="1"
                                       {{ old('threshold_alerts_enabled', $product->threshold_alerts_enabled) ? 'checked' : '' }}>
                                <label class="form-check-label" for="threshold_alerts_enabled">
                                    Enable Threshold Alerts
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Thresholds</button>
                            <a href="{{ route('inventory.thresholds.show', $product) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>