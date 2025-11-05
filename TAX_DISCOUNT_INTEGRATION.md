# Tax and Discount Integration with Sales and Purchases

## Overview

The Tax and Discount module has been successfully integrated with both Sales Orders and Purchase Orders. The system now automatically calculates and applies taxes and discounts based on the rules defined in the Tax & Discount Master Data.

## How It Works

### 1. Automatic Calculation

When you call `calculateTotals()` on a SalesOrder or PurchaseOrder, the system will:
- Calculate the subtotal from all order items
- Apply all active taxes (in priority order)
- Apply all active discounts (in priority order)
- Update the order's `tax_amount` and `discount_amount` fields
- Calculate the final `total_amount`

### 2. Tax and Discount Application Logic

#### Applicability Rules:
- **All Products**: Tax/discount applies to any order
- **Specific Categories**: Applies if any order item belongs to the specified categories
- **Specific Products**: Applies if any order item is one of the specified products

#### Calculation Method:
- **Percentage**: Calculated as a percentage of the subtotal
- **Fixed Amount**: A flat fee added to the order
- **Compound**: Calculated on top of previous taxes/discounts (priority matters!)

### 3. Priority System

Lower priority numbers are calculated first (0 is highest priority). This is important for compound taxes/discounts.

**Example:**
- Priority 0: VAT Tax 12% (applied first)
- Priority 1: Senior Discount 20% (can be compound to apply after VAT)

## Usage Examples

### Creating a Tax/Discount

```php
use App\Models\TaxDiscount;

// Create a 12% VAT tax
TaxDiscount::create([
    'code' => 'VAT-12',
    'name' => 'Value Added Tax',
    'type' => 'tax',
    'rate' => 12.0,
    'calculation_method' => 'percentage',
    'applies_to' => 'all',
    'priority' => 0,
    'is_active' => true,
]);

// Create a 10% discount for specific category
TaxDiscount::create([
    'code' => 'DISC-SHOES',
    'name' => 'Shoes Discount',
    'type' => 'discount',
    'rate' => 10.0,
    'calculation_method' => 'percentage',
    'applies_to' => 'specific',
    'applicable_categories' => [1, 2], // Category IDs
    'priority' => 1,
    'is_active' => true,
    'valid_from' => now(),
    'valid_to' => now()->addDays(30),
]);
```

### Using with Sales Orders

```php
use App\Models\SalesOrder;

$order = SalesOrder::find($orderId);

// Automatically calculate taxes and discounts
$order->calculateTotals(); // Tax & discount applied automatically

// Calculate without auto tax/discount (use manual amounts)
$order->calculateTotals(false);

// Get breakdown details
$breakdown = $order->getTaxDiscountBreakdown();
/*
Returns:
[
    'taxes' => [
        ['name' => 'VAT', 'rate' => 12, 'amount' => 120.00, ...]
    ],
    'discounts' => [
        ['name' => 'Promo', 'rate' => 10, 'amount' => 100.00, ...]
    ],
    'total_tax' => 120.00,
    'total_discount' => 100.00,
    'breakdown' => [
        'subtotal' => 1000.00,
        'total_tax' => 120.00,
        'total_discount' => 100.00,
        'net_amount' => 1020.00
    ]
]
*/
```

### Using with Purchase Orders

```php
use App\Models\PurchaseOrder;

$order = PurchaseOrder::find($orderId);

// Same API as Sales Orders
$order->calculateTotals(); // Taxes & discounts applied automatically
$breakdown = $order->getTaxDiscountBreakdown();
```

### Using the Service Directly

```php
use App\Services\TaxDiscountService;

$service = new TaxDiscountService();

// Calculate for any order
$calculation = $service->calculateForOrder(
    $subtotal = 1000.00,
    $items = $order->items,
    $orderType = 'sales' // or 'purchase'
);

// Get only taxes
$taxAmount = $service->calculateTaxes($subtotal, $items);

// Get only discounts
$discountAmount = $service->calculateDiscounts($subtotal, $items);

// Get active taxes and discounts
$activeTaxes = $service->getActiveTaxes();
$activeDiscounts = $service->getActiveDiscounts();
```

## Integration Points

### Models Updated:
1. **SalesOrder** (`app/Models/SalesOrder.php`)
   - `calculateTotals($applyAutoTaxDiscount = true)`
   - `getTaxDiscountBreakdown()`

2. **PurchaseOrder** (`app/Models/PurchaseOrder.php`)
   - `calculateTotals($applyAutoTaxDiscount = true)`
   - `getTaxDiscountBreakdown()`

### New Service:
- **TaxDiscountService** (`app/Services/TaxDiscountService.php`)
  - Handles all tax and discount calculations
  - Determines applicability based on order items
  - Supports compound calculations

## Best Practices

### 1. Always Set Priority Correctly
```php
// Base taxes first (priority 0-9)
VAT: priority 0
Sales Tax: priority 1

// Discounts next (priority 10-19)
Volume Discount: priority 10
Promo Code: priority 11

// Compound items last (priority 20+)
Senior Discount (compound): priority 20
```

### 2. Use Validity Periods for Promotions
```php
TaxDiscount::create([
    // ... other fields
    'valid_from' => now(),
    'valid_to' => now()->addDays(7), // Week-long promotion
]);
```

### 3. Test Before Activating
Create taxes/discounts as inactive first, test the calculations, then activate:
```php
$taxDiscount->is_active = false; // Test first
$taxDiscount->save();

// After testing...
$taxDiscount->is_active = true;
$taxDiscount->save();
```

### 4. Monitor Active Rules
Regularly check which taxes and discounts are active:
```php
$activeTaxes = TaxDiscount::active()->taxes()->get();
$activeDiscounts = TaxDiscount::active()->discounts()->get();
```

## Database Fields

Both `sales_orders` and `purchase_orders` tables have:
- `subtotal`: Sum of all line items before taxes/discounts
- `tax_amount`: Total tax applied (auto-calculated or manual)
- `discount_amount`: Total discount applied (auto-calculated or manual)
- `shipping_amount`: Shipping cost (not affected by auto-calc)
- `total_amount`: Final amount (subtotal + tax + shipping - discount)

## Backwards Compatibility

The system is fully backwards compatible:
- If you call `calculateTotals(false)`, it works like before (no auto tax/discount)
- Existing orders are not affected unless you recalculate them
- Manual tax and discount amounts are preserved if auto-calculation is disabled

## Future Enhancements

Potential improvements:
1. Tax/discount history tracking per order
2. API endpoints for real-time calculation previews
3. Bulk tax/discount updates
4. Tax exemption support for specific customers
5. Multi-tier discount calculations
6. Integration with accounting modules

## Navigation

Access the Tax & Discount module via:
**Sidebar → Master Data → Tax & Discount**

From there you can:
- View all taxes and discounts
- Create new markups
- Edit existing rules
- Toggle active/inactive status
- Test calculations with the profit breakdown calculator

## Support

For questions or issues, refer to the inline documentation in:
- `app/Models/TaxDiscount.php`
- `app/Services/TaxDiscountService.php`
- `app/Http/Controllers/TaxDiscountController.php`
