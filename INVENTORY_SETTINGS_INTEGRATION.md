# Inventory Settings Integration

## Overview
The inventory settings are now fully integrated as global configuration options that affect various modules throughout the application. These settings provide system-wide defaults and behavioral controls for inventory management.

## Available Settings

### 1. Low Stock Threshold (`low_stock_threshold`)
- **Type**: Integer
- **Default**: 10 units
- **Description**: Default threshold for low stock alerts
- **Helper Function**: `lowStockThreshold()`
- **Usage**: Used as fallback when products don't have individual reorder levels set

### 2. Critical Stock Level (`critical_stock_level`)
- **Type**: Integer
- **Default**: 5 units
- **Description**: Critical stock level requiring urgent attention
- **Helper Function**: `criticalStockLevel()`
- **Usage**: Used as fallback when products don't have individual critical levels set

### 3. Auto-Reorder Enabled (`auto_reorder_enabled`)
- **Type**: Boolean
- **Default**: false
- **Description**: Enable automatic reorder suggestions system-wide
- **Helper Function**: `isAutoReorderEnabled()`
- **Usage**: Controls whether the system generates automatic reorder suggestions

### 4. Waste Tracking Enabled (`waste_tracking_enabled`)
- **Type**: Boolean
- **Default**: true
- **Description**: Enable waste and damage tracking
- **Helper Function**: `isWasteTrackingEnabled()`
- **Usage**: Controls whether users can record waste/damage inventory adjustments

### 5. Negative Stock Allowed (`negative_stock_allowed`)
- **Type**: Boolean
- **Default**: false
- **Description**: Allow negative stock levels in the system
- **Helper Function**: `isNegativeStockAllowed()`
- **Usage**: Controls whether sales can proceed when stock is insufficient

---

## Helper Functions

All inventory settings can be accessed through helper functions defined in `app/Helpers/settings_helper.php`:

```php
// Get low stock threshold
$threshold = lowStockThreshold(); // Returns: 10 (or configured value)

// Get critical stock level
$critical = criticalStockLevel(); // Returns: 5 (or configured value)

// Check if auto-reorder is enabled
if (isAutoReorderEnabled()) {
    // Generate reorder suggestions
}

// Check if waste tracking is enabled
if (isWasteTrackingEnabled()) {
    // Allow waste/damage recording
}

// Check if negative stock is allowed
if (isNegativeStockAllowed()) {
    // Allow sales even with insufficient stock
}
```

---

## Integration Points

### Product Model (`app/Models/Product.php`)

#### Stock Level Checks
```php
// Check if product is low on stock (uses global setting as fallback)
$product->isLowStock(); // Uses product's reorder_level or lowStockThreshold()

// Check if product is at critical level (uses global setting as fallback)
$product->isCriticalStock(); // Uses product's critical_level or criticalStockLevel()

// Check if product should use auto-reorder
$product->shouldAutoReorder(); // Uses product setting OR global isAutoReorderEnabled()

// Check if negative stock is allowed
$product->allowsNegativeStock(); // Uses global isNegativeStockAllowed()
```

### Sales Order Service (`app/Services/SalesOrderService.php`)

The `checkStockAvailability()` method now respects the global negative stock setting:

```php
public function checkStockAvailability(array $items): array
{
    // If negative stock is allowed globally, skip stock checks
    if (isNegativeStockAllowed()) {
        return []; // No stock issues
    }
    
    // Otherwise, validate stock availability
    // ...
}
```

**Impact**: When "Allow Negative Stock" is enabled in settings, sales orders can be created regardless of stock availability.

### Inventory Threshold Service (`app/Services/InventoryThresholdService.php`)

Auto-reorder suggestions now check both product-level and global settings:

```php
// Generate reorder suggestion if auto-reorder is enabled
if ($product->shouldAutoReorder() && $product->needsReordering()) {
    // Create reorder alert
}
```

**Impact**: Products inherit the global auto-reorder setting unless explicitly configured at product level.

### Stock Controller (`app/Http/Controllers/StockController.php`)

Waste/damage tracking is now controlled by the global setting:

```php
public function showWasteForm()
{
    // Check if waste tracking is enabled
    if (!isWasteTrackingEnabled()) {
        return redirect()->back()
            ->with('error', 'Waste tracking is currently disabled.');
    }
    // ...
}
```

**Impact**: When waste tracking is disabled, users cannot access waste/damage forms.

---

## Usage Examples

### Example 1: Configuring Global Defaults
Navigate to **Settings → Inventory Settings** and configure:
- Low Stock Threshold: 20 units
- Critical Stock Level: 5 units
- Enable Auto-Reorder: Yes
- Enable Waste Tracking: Yes
- Allow Negative Stock: No

All products without specific threshold settings will use these values.

### Example 2: Product-Level Override
A product can override global settings:
```php
$product->reorder_level = 50; // Product-specific reorder level
$product->critical_level = 10; // Product-specific critical level
$product->auto_reorder_enabled = false; // Disable auto-reorder for this product
$product->save();
```

### Example 3: System-Wide Behavior
```php
// Enable negative stock globally
// Settings → Inventory Settings → Allow Negative Stock: Yes

// Now all sales can proceed regardless of stock
$order = SalesOrder::create([...]); // No stock validation errors
```

### Example 4: Disabling Waste Tracking
```php
// Disable waste tracking globally
// Settings → Inventory Settings → Enable Waste Tracking: No

// Users trying to access waste forms will see an error
// Route: /inventory/waste -> "Waste tracking is currently disabled"
```

---

## Module Impact Summary

| Module | Setting Used | Impact |
|--------|-------------|--------|
| **Product Model** | Low Stock Threshold<br>Critical Stock Level | Fallback values for stock status checks |
| **Product Model** | Auto-Reorder Enabled | Default behavior for auto-reorder suggestions |
| **Sales Orders** | Negative Stock Allowed | Whether to validate stock before order creation |
| **Inventory Alerts** | Low Stock Threshold<br>Critical Stock Level<br>Auto-Reorder Enabled | Alert generation and thresholds |
| **Stock Adjustments** | Waste Tracking Enabled | Access control for waste/damage recording |
| **Inventory Reports** | All Settings | Dashboard metrics and threshold displays |

---

## Settings Location

- **UI Path**: Settings → Inventory Settings (`/settings/inventory`)
- **Controller**: `SettingsController@inventory`
- **View**: `resources/views/settings/inventory.blade.php`
- **Service**: `SettingsService`
- **Database**: `settings` table (category: `inventory`)

---

## Best Practices

1. **Set Global Defaults First**: Configure inventory settings before adding products
2. **Product-Specific Overrides**: Use product-level settings for items with unique requirements
3. **Negative Stock**: Only enable for special business cases (e.g., dropshipping)
4. **Waste Tracking**: Keep enabled unless your business doesn't track damaged goods
5. **Auto-Reorder**: Enable for products with consistent demand patterns

---

## Future Enhancements

Potential additions to inventory settings:
- Automatic reorder execution (not just suggestions)
- Multi-location stock thresholds
- Seasonal threshold adjustments
- Integration with supplier lead times
- Stock valuation methods (FIFO, LIFO, Average)

---

## Testing

To test the integration:

1. **Test Low Stock Threshold**:
   - Set global threshold to 20
   - Create product without reorder_level
   - Reduce stock to 15
   - Verify low stock alert appears

2. **Test Negative Stock**:
   - Disable negative stock
   - Try to create sale order exceeding stock
   - Verify error message
   - Enable negative stock
   - Retry - should succeed

3. **Test Waste Tracking**:
   - Disable waste tracking
   - Try to access `/inventory/waste`
   - Verify error message
   - Enable waste tracking
   - Verify form is accessible

4. **Test Auto-Reorder**:
   - Enable global auto-reorder
   - Create product with reorder level
   - Reduce stock below reorder level
   - Verify auto-reorder alert is generated

---

## Support

For questions or issues with inventory settings integration:
1. Check this documentation
2. Review helper function definitions in `app/Helpers/settings_helper.php`
3. Examine the `SettingsService` for validation rules
4. Test in development environment before applying to production
