<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

    <!-- SKU -->
    <div>
        <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
        <input type="text" name="sku" id="sku" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Enter SKU">
    </div>

    <!-- Brand -->
    <div>
        <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
        <input type="text" name="brand" id="brand" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g. Samsung">
    </div>

    <!-- Unit -->
    <div>
        <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
        <input type="text" name="unit" id="unit" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="pcs, box, kg">
    </div>

    <!-- Cost Price -->
    <div>
        <label for="cost_price" class="block text-sm font-medium text-gray-700">Cost Price</label>
        <input type="number" step="0.01" name="cost_price" id="cost_price" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="0.00">
    </div>

    <!-- Selling Price -->
    <div>
        <label for="selling_price" class="block text-sm font-medium text-gray-700">Selling Price</label>
        <input type="number" step="0.01" name="selling_price" id="selling_price" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="0.00">
    </div>

    <!-- Reorder Level -->
    <div>
        <label for="reorder_level" class="block text-sm font-medium text-gray-700">Reorder Level</label>
        <input type="number" name="reorder_level" id="reorder_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Enter minimum stock">
    </div>

    <!-- Tax Rate -->
    <div>
        <label for="tax_rate" class="block text-sm font-medium text-gray-700">Tax Rate (%)</label>
        <input type="number" step="0.01" name="tax_rate" id="tax_rate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g. 12">
    </div>

    <!-- Supplier -->
    <div>
        <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier</label>
        <select name="supplier_id" id="supplier_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">-- Select Supplier --</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Status -->
    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <!-- Image -->
    <div>
        <label for="image" class="block text-sm font-medium text-gray-700">Product Image</label>
        <input type="file" name="image" id="image" class="mt-1 block w-full text-gray-700">
    </div>

    <!-- Weight -->
    <div>
        <label for="weight" class="block text-sm font-medium text-gray-700">Weight</label>
        <input type="number" step="0.01" name="weight" id="weight" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g. 1.25 kg">
    </div>

    <!-- Dimensions -->
    <div>
        <label for="dimensions" class="block text-sm font-medium text-gray-700">Dimensions</label>
        <input type="text" name="dimensions" id="dimensions" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g. 10x20x5 cm">
    </div>

    <!-- Barcode -->
    <div>
        <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode</label>
        <input type="text" name="barcode" id="barcode" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Enter barcode">
    </div>

</div>
