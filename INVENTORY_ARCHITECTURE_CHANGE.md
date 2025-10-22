# Inventory Management Architecture - Quantity Removal from Product Master

## Overview
This document explains the architectural change to remove the `quantity` field from the Product Master table and manage inventory exclusively through the Stock Movements system.

## Objectives

The primary goal is to ensure that:
1. **Product Master stores only static product details** (name, category, brand, cost, price, etc.)
2. **Quantity is managed dynamically** through Stock Movements
3. **Stock levels update automatically** when:
   - Items are received through Purchase Orders or Stock Entry
   - Items are sold through the Sales Module
   - Purchase Returns, Sales Returns, or Stock Adjustments occur

## Architecture Changes

### 1. Database Schema Changes

#### Migration: `remove_quantity_from_products_table`
- **Removed**: `quantity` column from `products` table
- **Removed**: Indexes that referenced the quantity column:
  - `idx_reorder_threshold` (reorder_level, quantity)
  - `idx_critical_threshold` (critical_level, quantity)

#### Stock Movements Table (Existing)
The `stock_movements` table already exists and tracks all inventory changes:
```sql
- movement_id (primary key)
- product_id (foreign key to products)
- movement_type (sale, purchase, return, adjustment, transfer_in, transfer_out, audit, waste, production, initial_stock)
- quantity_before (stock level before the movement)
- quantity_change (positive or negative change)
- quantity_after (stock level after the movement)
- unit_cost
- total_value
- reference_type & reference_id (links to related records)
- movement_date
- status (pending, confirmed, cancelled)
```

### 2. Product Model Changes

#### Removed from `$fillable`:
- `quantity` field

#### Removed from `$casts`:
- `quantity` field

#### New Relationship Added:
```php
public function stockMovements(): HasMany
{
    return $this->hasMany(StockMovement::class, 'product_id', 'product_id');
}
```

#### New Accessor for Quantity:
```php
public function getQuantityAttribute(): int
{
    // Get the latest stock movement for this product
    $latestMovement = $this->stockMovements()
        ->orderBy('movement_id', 'desc')
        ->first();
    
    return $latestMovement ? $latestMovement->quantity_after : 0;
}
```

This accessor allows you to still use `$product->quantity` throughout the codebase, but it now pulls from the latest stock movement instead of a database column.

#### Updated Methods:
```php
// decreaseStock() - Now creates a stock movement record
// increaseStock() - Now creates a stock movement record
// Scopes updated: lowStock(), inStock(), outOfStock()
```

### 3. Service Layer Changes

#### StockService
- **Removed** direct `$product->update(['quantity' => ...])` calls
- **Kept** `StockMovement::recordMovement()` calls
- All inventory changes now flow through stock movements only

Methods updated:
- `createStockAdjustment()`
- `createStockTransfer()`
- `recordWaste()`
- `confirmMovement()`

#### PurchaseReceiveService
- **Removed** `$product->increment('quantity', ...)` calls
- **Added** `StockMovement::recordMovement()` for inventory updates
- Supplier tracking still updates on the product record

Method updated:
- `updateProductInventory()`

### 4. How It Works Now

#### Getting Current Stock:
```php
// Simple accessor - works transparently
$currentStock = $product->quantity;  // Returns latest quantity_after from stock_movements
```

#### Recording a Sale:
```php
// The sales service will use:
StockMovement::recordMovement(
    productId: $productId,
    quantityBefore: $currentQuantity,
    quantityChange: -$soldQuantity,
    quantityAfter: $currentQuantity - $soldQuantity,
    movementType: StockMovement::TYPE_SALE,
    userId: auth()->id(),
    referenceType: 'sale',
    referenceId: $saleId
);
```

#### Recording a Purchase:
```php
// The purchase receive service will use:
StockMovement::recordMovement(
    productId: $productId,
    quantityBefore: $currentQuantity,
    quantityChange: $receivedQuantity,
    quantityAfter: $currentQuantity + $receivedQuantity,
    movementType: StockMovement::TYPE_PURCHASE,
    userId: auth()->id(),
    referenceType: 'purchase_receive',
    referenceId: $receiveId
);
```

#### Stock Adjustments:
```php
// Manual adjustments via StockService:
$result = $stockService->createStockAdjustment([
    'product_id' => $productId,
    'new_quantity' => $newQuantity,
    'unit_cost' => $unitCost,
    'reason' => 'Physical count adjustment',
    'notes' => 'Annual inventory audit'
]);
```

## Benefits

### 1. Complete Audit Trail
- Every inventory change is recorded with:
  - Who made the change
  - When it happened
  - Why it happened
  - What the stock was before and after
  - Reference to the source transaction

### 2. Data Integrity
- Cannot have orphaned quantity values
- Stock always matches the movement history
- Easier to identify and fix discrepancies

### 3. Better Reporting
- Historical stock levels at any point in time
- Movement analysis (fast-moving, slow-moving items)
- Trend analysis over time
- Cost tracking per movement

### 4. Simplified Reconciliation
- Stock counts can be compared against movement history
- Discrepancies are easier to trace
- Variance analysis is more accurate

## Migration Path

### Running the Migration
```bash
php artisan migrate
```

This will:
1. Drop the `quantity` column from `products` table
2. Remove related indexes

### Important Notes:
- **Existing stock movements** already contain the quantity_after values
- **No data loss** - current quantities are preserved in the latest stock movements
- **Views and controllers** continue to work because `$product->quantity` accessor provides transparent access

## Code Examples

### Checking Stock Availability:
```php
if ($product->quantity >= $requestedQuantity) {
    // Process order
}
```

### Getting Low Stock Products:
```php
$lowStockProducts = Product::lowStock(10)->get();
```

### Getting Out of Stock Products:
```php
$outOfStockProducts = Product::outOfStock();
```

### Calculating Inventory Value:
```php
$value = $product->quantity * $product->price;  // Still works!
```

## Testing Checklist

- [ ] Create a new product - verify initial stock movement is created
- [ ] Receive a purchase order - verify stock increases via movement
- [ ] Process a sale - verify stock decreases via movement
- [ ] Process a sales return - verify stock increases via movement
- [ ] Process a purchase return - verify stock decreases via movement
- [ ] Perform stock adjustment - verify movement is recorded
- [ ] Transfer stock between products - verify both movements
- [ ] Record waste/damage - verify stock decreases
- [ ] Check low stock alerts - verify they use movement data
- [ ] Run reports - verify quantities are accurate
- [ ] View product details - verify quantity displays correctly

## Troubleshooting

### Issue: Product shows 0 quantity but should have stock
**Solution**: Check if there are any stock movements for the product:
```php
$movements = $product->stockMovements()->orderBy('movement_id', 'desc')->get();
```

### Issue: Quantity not updating after a transaction
**Solution**: Verify that a stock movement was created:
```php
$latestMovement = StockMovement::where('product_id', $productId)
    ->orderBy('movement_id', 'desc')
    ->first();
```

### Issue: Need to set initial stock for a product
**Solution**: Create an initial stock movement:
```php
StockMovement::recordMovement(
    productId: $productId,
    quantityBefore: 0,
    quantityChange: $initialQuantity,
    quantityAfter: $initialQuantity,
    movementType: StockMovement::TYPE_INITIAL_STOCK,
    userId: auth()->id()
);
```

## Future Enhancements

1. **Multi-location inventory** - Add location tracking to stock movements
2. **Batch/Lot tracking** - Track inventory by batch/lot numbers
3. **Expiration management** - Track expiring stock
4. **Reserved stock** - Handle pending orders
5. **Stock forecasting** - Predictive analytics based on movement history

## Conclusion

This architectural change provides a more robust, auditable, and scalable inventory management system. By removing the quantity field from the Product Master and relying entirely on Stock Movements, we ensure:
- Complete traceability
- Data consistency
- Better reporting capabilities
- Simplified troubleshooting

All existing functionality continues to work through the transparent `quantity` accessor on the Product model.
