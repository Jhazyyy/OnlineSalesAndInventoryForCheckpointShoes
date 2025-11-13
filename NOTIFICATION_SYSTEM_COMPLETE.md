# Notification System - Complete Implementation Guide

## Overview
The notification system is now fully functional and allows owners/employees to receive push notifications for:
- Products hitting critical stock levels
- Products needing reordering
- Sales order updates
- Purchase order updates
- System alerts

---

## System Architecture

### 1. Database Structure

**Table**: `notifications`
```sql
- id (Primary Key)
- user_id (nullable) - For user-specific notifications, null for global
- type (string) - e.g., 'inventory.critical_stock', 'sales.order_created'
- title (string) - Notification title
- message (text) - Detailed message
- level (string) - 'info', 'success', 'warning', 'danger'
- link (string, nullable) - URL to relevant page
- read_at (timestamp, nullable) - When notification was read
- created_at, updated_at
```

### 2. Models

**File**: `app/Models/Notification.php`

**Key Methods**:
- `markAsRead()` - Mark notification as read
- `markAsUnread()` - Mark notification as unread
- `isRead()` - Check if notification is read
- `isUnread()` - Check if notification is unread
- `scopeUnread($query)` - Query scope for unread notifications

### 3. Controllers

**File**: `app/Http/Controllers/NotificationsController.php`

**Endpoints**:
- `GET /notifications-list` - View all notifications (paginated)
- `GET /notifications/latest` - Get latest notifications (API)
- `POST /notifications/{id}/read` - Mark single notification as read
- `POST /notifications/mark-all-read` - Mark all notifications as read
- `DELETE /notifications/{id}` - Delete a notification

**Features**:
- Filter by category (all, unread, inventory, orders, system)
- Pagination (20 per page)
- User-specific and global notifications
- Real-time counts by category

### 4. Views

**File**: `resources/views/notifications/index.blade.php`

**Features**:
- Responsive design with Tailwind CSS
- Color-coded notifications by level (danger, warning, success, info)
- Category badges (Inventory, Purchases, Sales, System)
- Unread indicators (blue dot)
- Interactive actions (View Details, Mark as Read, Delete)
- Filter tabs with live counts
- Empty state for when no notifications exist
- Relative timestamps ("5 minutes ago")

---

## Automatic Notification Generation

### Inventory Threshold Alerts

**Service**: `app/Services/InventoryThresholdService.php`

**Auto-generated when**:
1. **Out of Stock** (`inventory.out_of_stock`)
   - Trigger: Product quantity = 0
   - Level: danger
   - Message: "Product '{name}' is out of stock"

2. **Critical Stock** (`inventory.critical_stock`)
   - Trigger: Product quantity ≤ critical_level
   - Level: danger
   - Message: "Product '{name}' has reached critical stock level ({qty} units)"

3. **Low Stock** (`inventory.low_stock`)
   - Trigger: Product quantity ≤ reorder_level
   - Level: warning
   - Message: "Product '{name}' is running low on stock ({qty} units)"

4. **Overstock** (`inventory.overstock`)
   - Trigger: Product quantity > ceiling_level
   - Level: info
   - Message: "Product '{name}' is overstocked ({qty} units)"

5. **Reorder Needed** (`inventory.reorder_needed`)
   - Trigger: auto_reorder_enabled = true AND quantity ≤ reorder_level
   - Level: warning
   - Message: "Auto-reorder triggered for '{name}' - suggested quantity: {qty}"

### Sales Order Notifications

**Service**: `app/Services/SalesOrderService.php`

**Auto-generated when**:
1. **Order Created** (`sales.order_created`)
2. **Order Updated** (`sales.order_updated`)
3. **Order Status Changed** (`sales.order_status_changed`)
   - Confirmed, Delivered, Cancelled, Failed, Processing, Shipped

### Purchase Order Notifications

**Service**: `app/Services/PurchaseOrderService.php`

**Auto-generated when**:
1. **Purchase Order Created** (`purchases.order_created`)
2. **Purchase Order Updated** (`purchases.order_updated`)
3. **Purchase Order Status Changed** (`purchases.order_status_changed`)

**Service**: `app/Services/PurchaseReceiveService.php`

**Auto-generated when**:
1. **Goods Receipt Created** (`purchases.receive_created`)
2. **Goods Receipt Updated** (`purchases.receive_updated`)
3. **Goods Receipt Status Changed** (`purchases.receive_status_changed`)
4. **Purchase Receive Short Closed** (`purchases.receive_short_closed`)

---

## Scheduled Threshold Checks

**File**: `routes/console.php`

**Schedules**:
1. **Every 30 minutes** (Business hours only: 6 AM - 10 PM, weekdays)
   ```bash
   php artisan inventory:check-thresholds --silent
   ```

2. **Daily at 8:00 AM**
   ```bash
   php artisan inventory:check-thresholds --force
   ```

3. **Weekly on Monday at 9:00 AM**
   ```bash
   php artisan inventory:check-thresholds --force
   ```

**Command**: `app/Console/Commands/CheckInventoryThresholds.php`

**Options**:
- `--force` - Force check even during non-business hours
- `--product=ID` - Check specific product by ID
- `--category=NAME` - Check products in specific category
- `--silent` - Run silently without output

---

## Manual Notification Creation

### In Controllers or Services:

```php
use App\Models\Notification;

Notification::create([
    'title' => 'Your Title',
    'message' => 'Your detailed message',
    'level' => 'warning', // info, success, warning, danger
    'type' => 'inventory.low_stock', // module.action format
    'link' => '/path/to/relevant/page', // optional
    'user_id' => null, // null for global, user ID for specific user
]);
```

### Example - Custom Inventory Alert:

```php
$product = Product::find(1);

Notification::create([
    'title' => 'Low Stock Alert',
    'message' => "Product '{$product->product_name}' is running low ({$product->quantity} units)",
    'level' => 'warning',
    'type' => 'inventory.low_stock',
    'link' => route('inventory.thresholds.show', $product->id),
]);
```

---

## Configuration Settings

### Notification Settings Page
**Route**: `/settings/notifications`
**Controller**: `SettingsController@notifications`

**Configurable Options**:
1. Email Notifications Enabled
2. Administrator Email
3. Low Stock Alerts
4. Order Status Notifications
5. Payment Confirmations

**Database**: Settings stored in `settings` table with category = 'notifications'

---

## Notification Types Reference

### Inventory Module
- `inventory.out_of_stock` - Product out of stock
- `inventory.critical_stock` - Critical stock level reached
- `inventory.low_stock` - Low stock warning
- `inventory.overstock` - Overstocked item
- `inventory.reorder_needed` - Automatic reorder suggestion

### Sales Module
- `sales.order_created` - New sales order
- `sales.order_updated` - Sales order modified
- `sales.order_status_changed` - Order status change

### Purchase Module
- `purchases.order_created` - New purchase order
- `purchases.order_updated` - Purchase order modified
- `purchases.order_status_changed` - Purchase order status change
- `purchases.receive_created` - New goods receipt
- `purchases.receive_updated` - Goods receipt modified
- `purchases.receive_status_changed` - Receipt status change
- `purchases.receive_short_closed` - Receipt short closed

### System Module
- `system.error` - System error
- `system.warning` - System warning
- `system.info` - System information

---

## User Interface Features

### Notification List Page (`/notifications-list`)

**Features**:
1. **Filter Tabs**
   - All - Show all notifications
   - Unread - Show only unread notifications
   - Orders - Sales and purchase orders
   - Inventory - Inventory-related alerts
   - System - System messages

2. **Notification Cards**
   - Color-coded left border (red=danger, yellow=warning, green=success, blue=info)
   - Icon matching the notification level
   - Category badge
   - Timestamp (relative)
   - Unread indicator (blue dot)
   - Action buttons

3. **Actions**
   - View Details - Navigate to related page
   - Mark as Read - Mark single notification
   - Delete - Remove notification
   - Mark All as Read - Bulk action

4. **Live Counts**
   - Badge showing count for each filter category
   - Unread count highlighted in red

---

## Testing

### Test Script
**File**: `test_notifications.php`

**What it does**:
1. Creates test product with thresholds
2. Simulates low stock, critical stock, and out of stock scenarios
3. Generates various notification types
4. Displays summary of notifications

**Run**:
```bash
php test_notifications.php
```

### Manual Testing Steps

1. **Set Product Thresholds**
   - Go to Inventory > Threshold Management
   - Set reorder_level = 50, critical_level = 20 for a product
   - Enable threshold alerts

2. **Trigger Alerts**
   - Reduce product quantity below 50 (low stock)
   - Reduce product quantity below 20 (critical stock)
   - Reduce product quantity to 0 (out of stock)

3. **Run Manual Check**
   ```bash
   php artisan inventory:check-thresholds --force
   ```

4. **View Notifications**
   - Navigate to `/notifications-list`
   - Verify notifications appear
   - Test filter tabs
   - Test mark as read
   - Test delete

---

## Integration with Other Modules

### Sales Module
Already integrated - notifications created on:
- Order creation
- Order updates
- Status changes (confirmed, shipped, delivered, cancelled)

### Purchase Module
Already integrated - notifications created on:
- Purchase order creation/updates
- Goods receipt creation/updates
- Short close operations

### Inventory Threshold Service
Fully integrated - automatic alerts for:
- Stock level monitoring
- Threshold violations
- Reorder suggestions

---

## API Endpoints (AJAX)

### Get Latest Notifications
```javascript
fetch('/notifications/latest?limit=10')
    .then(response => response.json())
    .then(data => {
        console.log(data.notifications);
        console.log(data.unread_count);
    });
```

### Mark as Read
```javascript
fetch('/notifications/123/read', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
    }
})
.then(response => response.json())
.then(data => console.log(data.unread_count));
```

### Mark All as Read
```javascript
fetch('/notifications/mark-all-read', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
    }
})
.then(response => response.json())
.then(data => console.log(data.message));
```

### Delete Notification
```javascript
fetch('/notifications/123', {
    method: 'DELETE',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
    }
})
.then(response => response.json())
.then(data => console.log(data.message));
```

---

## Troubleshooting

### No Notifications Appearing
1. Check if products have thresholds set:
   ```bash
   php artisan tinker
   > Product::whereNotNull('reorder_level')->count()
   ```

2. Verify notification table exists:
   ```bash
   php artisan migrate:status
   ```

3. Run manual threshold check:
   ```bash
   php artisan inventory:check-thresholds --force
   ```

4. Check notification count:
   ```bash
   php artisan tinker
   > Notification::count()
   ```

### Scheduled Tasks Not Running
1. Ensure Laravel scheduler is running:
   ```bash
   php artisan schedule:list
   ```

2. Add to cron (Linux) or Task Scheduler (Windows):
   ```bash
   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
   ```

### Notifications Not Updating Real-time
1. Check browser console for JavaScript errors
2. Verify CSRF token is valid
3. Clear browser cache
4. Check network tab for failed API calls

---

## Performance Considerations

### Database Indexes
The notifications table has an index on `user_id` for faster queries.

### Pagination
Notifications are paginated (20 per page) to prevent performance issues.

### Auto-deletion
Consider implementing auto-deletion of old read notifications:
```php
// Delete read notifications older than 30 days
Notification::whereNotNull('read_at')
    ->where('read_at', '<', now()->subDays(30))
    ->delete();
```

---

## Future Enhancements

### Suggested Improvements:
1. **Real-time Push Notifications** - WebSocket integration
2. **Email Notifications** - Send critical alerts via email
3. **SMS Notifications** - Text message alerts for urgent issues
4. **Mobile App** - Push notifications to mobile devices
5. **User Preferences** - Per-user notification settings
6. **Notification Grouping** - Group similar notifications
7. **Snooze Feature** - Temporarily hide notifications
8. **Priority Levels** - Urgent, high, medium, low
9. **Export** - Download notification history
10. **Analytics** - Notification statistics dashboard

---

## Summary

✅ **Fully Functional** - Notification system is complete and operational
✅ **Automatic Alerts** - Inventory thresholds trigger notifications automatically
✅ **Manual Creation** - Easy API for custom notifications
✅ **User Interface** - Clean, responsive notification list page
✅ **Filtering** - Category-based filtering with live counts
✅ **Actions** - Read, delete, and view details functionality
✅ **Integration** - Works with sales, purchases, and inventory modules
✅ **Scheduled Checks** - Automatic threshold monitoring
✅ **Configurable** - Settings page for notification preferences

The notification module is now ready for production use!
