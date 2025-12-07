# Automatic Cost of Goods Sold (COGS) from Purchase Orders

## Overview
The system now automatically updates product costs from purchase orders, ensuring that the Cost of Goods Sold (COGS) in sales reports reflects the actual purchase costs from suppliers.

## How It Works

### 1. Purchase Receiving Process
When products are received from a purchase order:

1. **Inventory is Updated**: The `InventoryService::adjust()` method updates inventory with the purchase price
2. **Unit Cost is Tracked**: Each inventory location tracks the `unit_cost` from the purchase
3. **Weighted Average Calculated**: The system calculates weighted average cost across all inventory locations
4. **Product Cost Updated**: The product's `total_cost` field is automatically updated with the weighted average

### 2. Cost Calculation Methods

The system supports three costing methods (stored in `products.cost_calculation_method`):

- **`weighted_average`** (Default): Automatically calculates cost from all inventory purchases
  - Formula: `(Sum of all inventory value) / (Total quantity on hand)`
  - Updates automatically when receiving new purchases
  - Best for products purchased from suppliers

- **`manual`**: User manually enters costs through the Product Costing interface
  - Used for manufactured products or custom pricing
  - Won't auto-update from purchases

- **`latest_purchase`**: Uses the most recent purchase price
  - Simpler than weighted average
  - Updates with each new purchase

### 3. Sales Report COGS
When generating sales reports:

```php
// COGS is calculated using product's total_cost
$totalCost = $orders->sum(function ($order) {
    return $order->items->sum(function ($item) {
        return ($item->product->total_cost ?? 0) * $item->quantity;
    });
});
```

The `product->total_cost` reflects the weighted average purchase cost, ensuring accurate profit calculations.

## Configuration

### Setting Cost Calculation Method

**Through Purchase Receiving** (Automatic):
- When receiving a purchase, if the product has `manual` or no costing method, it's automatically changed to `weighted_average`
- This happens in `PurchaseReceiveService::updateInventoryAndTracking()`

**Through Product Costing Interface**:
1. Navigate to Inventory → Product Costing
2. Edit a product
3. Select "Cost Calculation Method":
   - Manual (enter costs manually)
   - Weighted Average (auto from purchases)
   - Latest Purchase (use last purchase price)

### Bulk Update Existing Products

To update all products to use weighted average costing:

```bash
php artisan migrate
php artisan costs:recalculate --force
```

This will:
1. Change all products from `manual` to `weighted_average` costing
2. Recalculate costs from existing inventory data

## Implementation Details

### Files Modified

**1. PurchaseReceiveService.php**
```php
// Automatically sets weighted_average method for new products
if (empty($product->cost_calculation_method) || $product->cost_calculation_method === 'manual') {
    $product->update(['cost_calculation_method' => 'weighted_average']);
}

// Always updates cost after receiving
$costingService->updateCostFromInventory($product->fresh());
```

**2. ProductCostingService.php**
```php
// Updates total_cost directly from weighted average
public function updateCostFromInventory(Product $product): bool
{
    // Calculate weighted average from all inventory
    $weightedAverageCost = $totalValue / $totalQuantity;
    
    // Update product cost (used as COGS)
    $product->update([
        'total_cost' => round($weightedAverageCost, 2),
        'cost_notes' => 'Auto-updated from weighted average purchase cost'
    ]);
}
```

**3. ReportService.php**
```php
// Sales report uses product total_cost as COGS
$totalCost = $orders->sum(function ($order) {
    return $order->items->sum(function ($item) {
        return ($item->product->total_cost ?? 0) * $item->quantity;
    });
});

$grossProfit = $netRevenue - $totalCost;
```

### Database Schema

**Products Table** (`cost_calculation_method` field):
```sql
cost_calculation_method VARCHAR(50) DEFAULT 'manual'
COMMENT 'Method: manual, weighted_average, latest_purchase'
```

**Inventory Table** (`unit_cost` field):
```sql
unit_cost DECIMAL(10,2) NULL
COMMENT 'Cost per unit from purchase'
```

## Benefits

1. **Accurate COGS**: Sales reports show true profit based on actual purchase costs
2. **Automatic Updates**: No manual data entry required for purchased products
3. **Weighted Average**: Handles multiple purchases at different prices correctly
4. **Flexible**: Can still use manual costing for manufactured products
5. **Audit Trail**: Cost update history tracked in `last_cost_update` and `cost_notes`

## Example Scenario

### Before Fix
- Purchase 10 units at $50 each
- Sell 5 units at $100 each
- Sales report shows: Revenue = $500, **COGS = $0**, Profit = $500 ❌

### After Fix
- Purchase 10 units at $50 each → `total_cost` automatically updated to $50
- Purchase 10 more units at $60 each → `total_cost` automatically updated to $55 (weighted average)
- Sell 5 units at $100 each
- Sales report shows: Revenue = $500, **COGS = $275** (5 × $55), Profit = $225 ✅

## Maintenance Commands

### Recalculate All Product Costs
```bash
php artisan costs:recalculate --force
```

### Check Product Costs
```bash
php check_product_costs.php
```

### Set All Products to Weighted Average
```bash
php artisan migrate  # Runs set_default_weighted_average_costing_for_products
```

## Troubleshooting

### Product cost not updating?
1. Check `cost_calculation_method` is set to `weighted_average`
2. Verify inventory has `unit_cost` values (check `inventory` table)
3. Run `php artisan costs:recalculate --force`

### Sales report showing zero COGS?
1. Ensure products have been purchased (not just manually added)
2. Check `products.total_cost` field has values
3. Run cost recalculation command

### Want to use manual costing?
1. Go to Product Costing interface
2. Change method to "Manual"
3. Enter custom cost breakdown

## Related Files
- `app/Services/PurchaseReceiveService.php`
- `app/Services/ProductCostingService.php`
- `app/Services/InventoryService.php`
- `app/Services/ReportService.php`
- `app/Console/Commands/RecalculateProductCosts.php`
- `database/migrations/*_set_default_weighted_average_costing_for_products.php`
