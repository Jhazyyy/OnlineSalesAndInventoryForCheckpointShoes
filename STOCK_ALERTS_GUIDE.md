# Stock Alerts System

## Overview
The stock alerts system automatically monitors all products and creates notifications when stock levels fall below calculated thresholds based on demand formulas.

## How It Works

### Stock Level Formulas
The system uses industry-standard inventory management formulas to calculate thresholds:

1. **Critical Level** = Average Daily Demand × Lead Time Days (default: 5 days)
2. **Reorder Point (ROP)** = (Average Daily Demand × Lead Time) + Safety Stock (default: 10 units)
3. **Safety Stock** = (Max Daily Demand - Average Daily Demand) × Lead Time
4. **Average Daily Demand** = Last 30 days sales / 30

### Stock Status Categories

| Status | Condition | Alert Type | Severity |
|--------|-----------|------------|----------|
| **Out of Stock** | Quantity ≤ 0 | `out_of_stock` | Critical |
| **Critical Stock** | 0 < Quantity ≤ Critical Level | `critical_stock` | Urgent |
| **Low Stock** | Critical Level < Quantity ≤ ROP | `low_stock` | Warning |
| **In Stock** | Quantity > ROP | None | - |

### Automated Monitoring

The stock level check command runs automatically **every hour** via Laravel's task scheduler:

```php
// Scheduled in routes/console.php
Schedule::command('stock:check-levels')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();
```

## Manual Commands

### Check Stock Levels
```bash
php artisan stock:check-levels
```

This command:
- ✅ Checks all products against formula-based thresholds
- ✅ Creates new alerts for products below thresholds
- ✅ Updates existing active alerts with current stock data
- ✅ Resolves alerts when stock returns to normal levels

### Command Output Example
```
Checking stock levels for all products...
 57/57 [============================] 100%

✓ Stock check complete!
  - Products checked: 57
  - New alerts created: 1
  - Alerts updated: 49
  - Alerts resolved: 5
```

## Alert Details

### Alert Data Structure
Each alert includes:
- **Product ID** - The product being monitored
- **Alert Type** - `out_of_stock`, `critical_stock`, or `low_stock`
- **Severity** - `critical`, `urgent`, or `warning`
- **Message** - Human-readable description with current quantities
- **Alert Data** (JSON):
  ```json
  {
    "current_quantity": 0,
    "critical_level": 15.5,
    "reorder_point": 25.5,
    "safety_stock": 10,
    "average_daily_demand": 3.1,
    "checked_at": "2025-11-23 14:39:37"
  }
  ```

### Alert Lifecycle

1. **Active** - Product is below threshold
   - Alert is created or updated hourly
   - Shows in alerts dashboard
   - Can be acknowledged by staff

2. **Acknowledged** - Staff has seen the alert
   - Tracked with user ID and timestamp
   - Still shows as active until resolved

3. **Resolved** - Stock returned to normal (above ROP)
   - Automatically resolved by system
   - No longer shows in active alerts
   - Resolution note: "Stock level returned to normal (above reorder point)"

## Viewing Alerts

Alerts can be viewed in the inventory management dashboard:
- **Active Alerts** - Current stock issues requiring attention
- **Alert History** - Past alerts with resolution details
- **Product Alerts** - Alerts filtered by specific product

## Configuration

### Modifying Default Values

Default values can be adjusted in the Product model (`app/Models/Product.php`):

```php
// Default lead time (days)
$leadTimeDays = 5;

// Default safety stock (units)
$safetyStock = 10;
```

### Changing Schedule Frequency

To run checks more or less frequently, edit `routes/console.php`:

```php
// Every 30 minutes
Schedule::command('stock:check-levels')->everyThirtyMinutes();

// Every 2 hours
Schedule::command('stock:check-levels')->everyTwoHours();

// Daily at 8 AM
Schedule::command('stock:check-levels')->dailyAt('08:00');
```

## Technical Details

### Files Modified/Created

1. **`app/Console/Commands/CheckStockLevels.php`** - Main command
2. **`app/Models/Product.php`** - Added `salesItems()` relationship
3. **`routes/console.php`** - Scheduled hourly execution

### Database
- **Table**: `inventory_alerts`
- **Model**: `InventoryAlert`
- **Indexes**: `product_id`, `status`, `alert_type`

## Troubleshooting

### Alerts Not Appearing

1. Check scheduler is running:
   ```bash
   php artisan schedule:list
   ```

2. Run command manually to test:
   ```bash
   php artisan stock:check-levels
   ```

3. Verify active alerts:
   ```bash
   php artisan tinker
   >>> InventoryAlert::where('status', 'active')->count()
   ```

### Duplicate Alerts

The system prevents duplicates by checking for existing active alerts with the same `product_id` and `alert_type` before creating new ones.

### False Positives

If products show alerts incorrectly:
- Check if sales data is being recorded properly
- Verify the `sales_order_items` table has recent data
- Review formula calculations in Product model methods

## Best Practices

1. **Review alerts daily** - Check dashboard each morning
2. **Acknowledge critical alerts** - Mark as seen to track awareness
3. **Monitor resolution times** - Track how quickly stock is replenished
4. **Adjust formulas** - Tune lead time and safety stock for your business
5. **Use alert data** - Leverage average demand data for purchasing decisions
