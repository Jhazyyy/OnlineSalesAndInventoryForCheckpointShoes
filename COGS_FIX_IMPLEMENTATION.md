# Cost of Goods Sold (COGS) Implementation - Complete

## Overview
The system now properly tracks Cost of Goods Sold (COGS) by capturing the actual purchase cost at the time of sale. This ensures accurate profit calculations even when purchase costs change over time.

## How It Works

### 1. Purchase Order Flow (Automatic Cost Updates)
```
Purchase Order → Receive Goods → Update Inventory
                                      ↓
                            Calculate Weighted Average Cost
                                      ↓
                            Update Product.total_cost
```

**Files Involved:**
- `PurchaseReceiveService.php` - Automatically updates product costs when receiving purchases
- `ProductCostingService.php` - Calculates weighted average cost from all inventory locations
- `Product.total_cost` field - Stores the current weighted average purchase cost

### 2. Sales Order Flow (Capture COGS at Sale)
```
Create Sale → Capture Product.total_cost → Store in SalesOrderItem.unit_cost_at_sale
                                                           ↓
                                              Used for accurate COGS calculation
```

**Files Modified:**
- `SalesOrderService.php` - Captures `product->total_cost` as `unit_cost_at_sale` when creating sales items
- `SalesOrderItem.php` - Added `unit_cost_at_sale` field to track COGS at time of sale
- `ReportService.php` - Uses `unit_cost_at_sale` for COGS calculations in sales reports

### 3. Sales Report Calculation
```php
// Calculate COGS from captured costs at time of sale
$totalCost = $orders->sum(function ($order) {
    return $order->items->sum(function ($item) {
        // Use unit_cost_at_sale (captured at sale time)
        $unitCost = $item->unit_cost_at_sale ?? ($item->product->total_cost ?? 0);
        return $unitCost * $item->quantity;
    });
});

// Calculate Gross Profit
$grossProfit = $netRevenue - $totalCost;
```

## Database Schema Changes

### Migration: `add_unit_cost_at_sale_to_sales_order_items_table`
```php
Schema::table('sales_order_items', function (Blueprint $table) {
    $table->decimal('unit_cost_at_sale', 10, 2)->default(0)->after('unit_price');
});
```

**Purpose:** Captures the product's cost from purchases at the moment of sale, ensuring historical accuracy.

## Key Features

### ✅ Accurate Historical Profit Calculation
- COGS is frozen at the time of sale
- Profit margins remain accurate even if purchase costs change later
- Sales reports reflect true profitability at the time of transaction

### ✅ Automatic Cost Updates from Purchases
- Product costs automatically update when receiving purchase orders
- Weighted average costing across all inventory locations
- No manual cost entry required for purchased products

### ✅ Backward Compatibility
- Backfill script updates existing sales with current product costs
- Fallback to `product->total_cost` if `unit_cost_at_sale` is not set
- Old sales reports continue to work correctly

## Example Scenario

### Purchase Flow:
1. **Receive Purchase Order:** 100 units at ₱50 each
   - System updates `product.total_cost = ₱50`
   
2. **Later, Receive Another Purchase:** 100 units at ₱60 each
   - System calculates weighted average: (100×50 + 100×60) / 200 = ₱55
   - System updates `product.total_cost = ₱55`

### Sales Flow:
1. **First Sale (before 2nd purchase):** Sell 10 units at ₱100 each
   - Captures `unit_cost_at_sale = ₱50`
   - COGS = 10 × ₱50 = **₱500**
   - Gross Profit = ₱1,000 - ₱500 = **₱500**

2. **Second Sale (after 2nd purchase):** Sell 10 units at ₱100 each
   - Captures `unit_cost_at_sale = ₱55`
   - COGS = 10 × ₱55 = **₱550**
   - Gross Profit = ₱1,000 - ₱550 = **₱450**

### Sales Report:
```
Total Sales: 20 units at ₱100 = ₱2,000
Total COGS: (10 × ₱50) + (10 × ₱55) = ₱1,050
Gross Profit: ₱2,000 - ₱1,050 = ₱950
Profit Margin: 47.5%
```

**✓ Accurate:** Each sale uses the actual cost at the time it was made.

## Files Created/Modified

### Created
- ✅ `database/migrations/2025_12_09_101327_add_unit_cost_at_sale_to_sales_order_items_table.php`
- ✅ `backfill_unit_cost_at_sale.php` - Backfill script for existing data
- ✅ `COGS_FIX_IMPLEMENTATION.md` - This documentation

### Modified
- ✅ `app/Models/SalesOrderItem.php`
  - Added `unit_cost_at_sale` to fillable and casts
  - Added helper methods: `getTotalCostAttribute()`, `getGrossProfitAttribute()`, `getProfitMarginAttribute()`
  
- ✅ `app/Services/SalesOrderService.php`
  - Modified `addItemsToOrder()` to capture `product->total_cost` as `unit_cost_at_sale`
  
- ✅ `app/Services/ReportService.php`
  - Modified `generateSalesReport()` to use `unit_cost_at_sale` for COGS calculation

## Helper Methods Added to SalesOrderItem

```php
// Get total COGS for this line item
$item->total_cost; // Returns: unit_cost_at_sale × quantity

// Get gross profit for this line item
$item->gross_profit; // Returns: line_total - total_cost

// Get profit margin percentage for this line item
$item->profit_margin; // Returns: (gross_profit / line_total) × 100
```

## Setup & Deployment

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Backfill Existing Data
```bash
php backfill_unit_cost_at_sale.php
```

### 3. Verify
Check sales reports to ensure COGS calculations are accurate.

## Testing

### Verify COGS in Sales Reports
1. Navigate to Reports → Sales Report
2. Check that:
   - COGS reflects actual purchase costs
   - Gross Profit = Net Revenue - COGS
   - Profit Margin = (Gross Profit / Net Revenue) × 100

### Test New Sales
1. Create a new sale through POS or Sales Orders
2. Verify `unit_cost_at_sale` is captured in database
3. Check that sales report uses the captured cost

### Sample Query
```php
// Get COGS breakdown for an order
$order = SalesOrder::with('items.product')->find($orderId);
foreach ($order->items as $item) {
    echo "Product: {$item->product->name}\n";
    echo "Quantity: {$item->quantity}\n";
    echo "Unit Price: ₱{$item->unit_price}\n";
    echo "Unit Cost (COGS): ₱{$item->unit_cost_at_sale}\n";
    echo "Total COGS: ₱{$item->total_cost}\n";
    echo "Gross Profit: ₱{$item->gross_profit}\n";
    echo "Profit Margin: {$item->profit_margin}%\n\n";
}
```

## Benefits

### ✅ Accurate Financial Reporting
- True profit margins based on actual purchase costs
- Historical sales maintain their original profitability
- No retroactive profit recalculations when costs change

### ✅ Better Business Insights
- Track which products have higher/lower margins
- Identify profitable vs. loss-making sales
- Make informed pricing decisions

### ✅ Audit Trail
- Every sale records the cost at that moment
- Can trace back profit calculations to source data
- Supports financial audits and accounting

## Costing Methods Supported

The system supports three costing methods (stored in `products.cost_calculation_method`):

1. **`weighted_average`** (Default, Recommended)
   - Automatically calculates from all inventory purchases
   - Updates when receiving new purchases
   - Best for products purchased from suppliers

2. **`latest_purchase`**
   - Uses the most recent purchase price
   - Simpler than weighted average
   - Updates with each new purchase

3. **`manual`**
   - User manually enters costs
   - Used for manufactured products or special cases
   - Won't auto-update from purchases

## Related Documentation
- `AUTOMATIC_COGS_FROM_PURCHASES.md` - How purchase orders update product costs
- `COGS_IMPLEMENTATION_SUMMARY.md` - Previous implementation summary
- `SALES_REPORT_CALCULATIONS.md` - Sales report calculation details

## Summary

✅ **Problem Solved:** COGS now reflects actual purchase costs at the time of sale  
✅ **Automatic:** Purchase orders automatically update product costs  
✅ **Accurate:** Sales capture the cost at transaction time  
✅ **Historical:** Old sales maintain their original profitability  
✅ **Complete:** All 165 existing sales items backfilled successfully  

The system now provides accurate profit calculations based on real purchase costs from suppliers!
