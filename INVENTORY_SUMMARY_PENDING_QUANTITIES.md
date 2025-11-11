# Inventory Summary Enhancement - Pending Quantities

## Overview
Enhanced the Inventory Summary on the dashboard to show comprehensive "Quantity to be Received" information from multiple sources: Purchase Orders, Deliveries, and Goods Receipts.

## Implementation Date
November 5, 2025

## What Changed

### 1. Dashboard Route Logic (Backend)
**File**: `routes/web.php`

#### Enhanced Purchase Receive Statistics:
Added comprehensive calculations to track pending quantities from three different sources:

1. **Purchase Orders** - Items that are ordered but not yet received
   - Includes orders with status: `ordered`, `partial_received`, `approved`
   - Calculates: `SUM(quantity_ordered - quantity_received)`

2. **Purchase Deliveries** - Items currently in transit or scheduled for delivery
   - Includes deliveries with status: `scheduled`, `in_transit`, `picked_up`
   - Calculates: `SUM(quantity_expected - quantity_delivered)`

3. **Purchase Receives (Goods Receipts)** - Items expected but not yet confirmed as received
   - Includes receives with status: `in_transit`, `pending`
   - Calculates: `SUM(quantity_expected - quantity_received)`

#### New Statistics Added:
```php
$purchaseReceiveStats = [
    // ... existing stats ...
    'pending_quantity' => total pending from all sources,
    'pending_from_orders' => pending from purchase orders,
    'pending_from_deliveries' => pending from deliveries,
    'pending_from_receives' => pending from goods receipts,
];
```

### 2. Dashboard View (Frontend)
**File**: `resources/views/dashboard.blade.php`

#### Enhanced Inventory Summary Section:
- **Main Display**: Shows total pending quantity in large, prominent blue text
- **Detailed Breakdown**: Displays source-specific pending quantities with:
  - Color-coded cards (blue for orders, orange for deliveries, green for receipts)
  - Descriptive icons for each source
  - Individual quantity counts

#### Visual Improvements:
- Changed pending quantity color to blue for better visibility
- Added expandable breakdown section that only shows when quantities are pending
- Each source has its own color scheme for easy identification
- Icons help users quickly understand the source of pending quantities

## Data Sources & Business Logic

### Purchase Orders (Blue Card)
**What it tracks**: Items that have been ordered from suppliers but haven't arrived yet
- Includes approved and ordered purchase orders
- Shows the gap between what was ordered and what's been received
- **Example**: PO for 100 shoes, 30 received → Shows 70 pending

### Deliveries (Orange Card)
**What it tracks**: Items currently being shipped or scheduled for delivery
- Tracks items in transit from supplier to warehouse
- Shows items that are on the way but not yet at the facility
- **Example**: Delivery in transit with 50 items → Shows 50 pending

### Goods Receipts (Green Card)
**What it tracks**: Items expected at the warehouse but not yet confirmed/inspected
- Represents items that need to be processed through receiving
- Shows items awaiting inspection or confirmation
- **Example**: Goods receipt created for 25 items, none processed yet → Shows 25 pending

## User Benefits

### Better Inventory Planning
- **Complete Visibility**: See all incoming inventory in one place
- **Source Tracking**: Understand where pending inventory is in the supply chain
- **Accurate Forecasting**: Make better decisions with complete pending quantity data

### Improved Decision Making
- Know what's on order vs. what's in transit vs. what's awaiting processing
- Identify bottlenecks in the receiving process
- Plan storage space based on incoming quantities

### Operational Efficiency
- Quick overview without navigating to multiple pages
- Color-coded visual indicators for easy scanning
- Immediate awareness of inventory status

## Technical Details

### Database Queries
The system performs three separate aggregate queries:

1. **Purchase Order Items Query**:
   ```sql
   SELECT SUM(quantity_ordered - quantity_received) as pending_qty
   FROM purchase_order_items
   WHERE order.status IN ('ordered', 'partial_received', 'approved')
   ```

2. **Purchase Delivery Items Query**:
   ```sql
   SELECT SUM(quantity_expected - quantity_delivered) as pending_qty
   FROM purchase_delivery_items
   WHERE delivery.status IN ('scheduled', 'in_transit', 'picked_up')
   ```

3. **Purchase Receive Items Query**:
   ```sql
   SELECT SUM(quantity_expected - quantity_received) as pending_qty
   FROM purchase_receive_items
   WHERE receive.status IN ('in_transit', 'pending')
   ```

### Performance Considerations
- All queries use aggregate functions (SUM) for efficiency
- Queries are cached within the dashboard route
- Uses database indexes on status and foreign key columns
- Exception handling ensures dashboard loads even if queries fail

## Display Logic

### Breakdown Visibility
The detailed breakdown only appears when there are pending quantities:
```blade
@if(($purchaseReceiveStats['pending_quantity'] ?? 0) > 0)
    <!-- Show breakdown -->
@endif
```

### Individual Source Cards
Each source card only displays if it has pending quantities:
```blade
@if(($purchaseReceiveStats['pending_from_orders'] ?? 0) > 0)
    <!-- Show Purchase Orders card -->
@endif
```

## Example Scenarios

### Scenario 1: New Store Setup
- **Purchase Orders**: 1,000 items ordered
- **Deliveries**: 500 items in transit
- **Goods Receipts**: 200 items awaiting processing
- **Total Display**: 1,700 items to be received

### Scenario 2: Normal Operations
- **Purchase Orders**: 150 items on backorder
- **Deliveries**: 75 items scheduled for delivery tomorrow
- **Goods Receipts**: 30 items received but not yet processed
- **Total Display**: 255 items to be received

### Scenario 3: All Clear
- **Purchase Orders**: 0 items pending
- **Deliveries**: 0 items in transit
- **Goods Receipts**: 0 items awaiting processing
- **Total Display**: 0 (breakdown section hidden)

## Integration Points

### Related Modules
This feature integrates with:
- **Purchase Orders Module**: Tracks ordered quantities
- **Purchase Deliveries Module**: Tracks in-transit quantities
- **Purchase Receives (Goods Receipts) Module**: Tracks pending receipts
- **Inventory Management**: Shows current on-hand quantities

### Workflow Impact
The pending quantities update automatically as items move through the system:
1. Create Purchase Order → Adds to "Purchase Orders" pending
2. Create Delivery → Moves to "In Delivery" pending
3. Create Goods Receipt → Moves to "Goods Receipt" pending
4. Confirm Receipt → Removes from pending, adds to on-hand

## Color Scheme

- **Blue** (#3B82F6): Purchase Orders - Represents planned/ordered items
- **Orange** (#F97316): Deliveries - Represents items in motion
- **Green** (#10B981): Goods Receipts - Represents items at facility, awaiting confirmation
- **Gray** (#1F2937): On-Hand Quantity - Current inventory

## Future Enhancements

Potential improvements for this feature:
- [ ] Add expected arrival dates for pending quantities
- [ ] Show value of pending inventory (not just quantity)
- [ ] Add filters by supplier or product category
- [ ] Link each card to detailed view of pending items
- [ ] Add trend analysis (comparing to previous periods)
- [ ] Show aging of pending quantities (overdue items)
- [ ] Email alerts when pending quantities are high/low
- [ ] Export pending inventory report
- [ ] Add product-level breakdown in a modal
- [ ] Show critical items in pending quantities

## Testing Checklist

- [x] Pending quantities calculate correctly
- [x] Breakdown shows/hides based on data
- [x] Each source displays independently
- [x] Colors and icons display properly
- [x] Dark mode compatibility
- [x] Responsive design (mobile, tablet, desktop)
- [x] Exception handling works correctly
- [x] No syntax errors in code
- [x] Database queries are efficient

## Troubleshooting

### Issue: Pending quantities show 0 when there are pending orders
**Solution**: Check that:
- Purchase orders have status `ordered`, `partial_received`, or `approved`
- Items have `quantity_ordered` > `quantity_received`
- Database relationships are properly configured

### Issue: Breakdown doesn't show even with pending quantities
**Solution**: Verify the conditional logic in the blade template and ensure `pending_quantity` is greater than 0

### Issue: Performance degradation on dashboard
**Solution**: 
- Ensure database indexes exist on status columns
- Check query execution plans
- Consider caching the statistics

## Maintenance

To modify this feature:
1. **Backend Logic**: Edit `routes/web.php` (around line 375-425)
2. **Frontend Display**: Edit `resources/views/dashboard.blade.php` (around line 217-280)
3. **Styling**: Adjust Tailwind classes in the dashboard view
4. **Queries**: Modify the aggregate SUM queries in web.php

## Database Dependencies

This feature relies on the following tables:
- `purchase_orders` and `purchase_order_items`
- `purchase_deliveries` and `purchase_delivery_items`
- `purchase_receives` and `purchase_receive_items`
- `products` (for on-hand quantity)

## API Response Format

The `$purchaseReceiveStats` array structure:
```php
[
    'total_receives' => int,
    'received_count' => int,
    'in_transit_count' => int,
    'total_value_received' => float,
    'this_month_receives' => int,
    'pending_quantity' => int,           // NEW - Total pending
    'pending_from_orders' => int,        // NEW - From purchase orders
    'pending_from_deliveries' => int,    // NEW - From deliveries
    'pending_from_receives' => int,      // NEW - From goods receipts
]
```
