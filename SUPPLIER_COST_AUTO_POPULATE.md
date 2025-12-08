# Supplier Cost Auto-Population Feature

## Overview
When creating or editing purchase orders, the system now automatically populates the unit price field with the cost from the assigned supplier relationship.

## Implementation Details

### Backend Changes

#### 1. PurchaseOrderController.php
Added new method `getSupplierCost()` that:
- Accepts `product_id` and `supplier_id` as parameters
- Fetches cost from product-supplier pivot table
- Priority order:
  1. Cost from selected supplier (if supplier is assigned to product)
  2. Cost from primary supplier (if marked as primary)
  3. Cost from any assigned supplier
  4. Returns null if no supplier cost found

#### 2. Routes (web.php)
Added new AJAX route:
```php
Route::post('/get-supplier-cost', [PurchaseOrderController::class, 'getSupplierCost'])
    ->middleware('permission:view purchases')
    ->name('purchases.purchase-orders.get-supplier-cost');
```

### Frontend Changes

#### 1. create.blade.php
**Product Selection Event:**
- When a product is selected, the system checks if a supplier is also selected
- If both are selected, makes AJAX call to fetch supplier cost
- Auto-populates unit price field with supplier cost
- Falls back to product's default price if no supplier cost found

**Supplier Change Event:**
- When supplier is changed, updates all existing product line items
- Fetches new costs for all selected products based on new supplier
- Automatically recalculates line totals

#### 2. edit.blade.php
Same functionality as create.blade.php applied to purchase order editing.

## User Experience

### Workflow
1. User selects a supplier from dropdown
2. User adds product to purchase order
3. System automatically populates unit price with supplier's cost
4. If supplier is changed, all product prices update automatically
5. User can still manually override prices if needed

### Cost Priority Logic
- **First Priority:** Cost from supplier selected in the purchase order
- **Second Priority:** Cost from product's primary supplier
- **Third Priority:** Cost from any assigned supplier
- **Fallback:** Product's default selling price

## Benefits
- Reduces manual data entry errors
- Ensures consistent pricing from suppliers
- Speeds up purchase order creation
- Maintains supplier-specific pricing
- Automatic price updates when supplier changes

## Technical Notes

### Product-Supplier Relationship
```php
$product->suppliers()
    ->withPivot('cost', 'is_primary', 'notes')
```

The `cost` field in the pivot table stores supplier-specific costs for each product.

### AJAX Endpoint
- **Route:** `/purchases/purchase-orders/get-supplier-cost`
- **Method:** POST
- **Parameters:** `product_id`, `supplier_id` (optional)
- **Returns:** `{ "cost": 123.45 }` or `{ "cost": null }`

### JavaScript Events
- `change` event on `.product-select` - Triggers cost fetch when product selected
- `change` event on `#supplier_id` - Updates all product costs when supplier changes

## Testing Checklist
- ✅ Create new purchase order with supplier and products
- ✅ Verify correct supplier cost populates
- ✅ Change supplier and verify prices update
- ✅ Add product before selecting supplier (should use default price)
- ✅ Add product after selecting supplier (should use supplier cost)
- ✅ Edit existing purchase order and change products
- ✅ Manual price override still works
- ✅ Fallback to primary supplier cost when PO supplier not assigned
- ✅ Graceful handling when no supplier cost exists
