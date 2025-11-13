# Notification System - Quick Start Guide

## For Owners/Employees

### Accessing Notifications

1. **View Notifications**
   - Click on the bell icon 🔔 in the top navigation bar (if available)
   - Or navigate directly to `/notifications-list`

2. **Filter Notifications**
   - **All** - View all notifications
   - **Unread** - See only unread notifications (highlighted with blue dot)
   - **Orders** - Sales and purchase order updates
   - **Inventory** - Stock alerts and inventory notifications
   - **System** - System messages and alerts

### Understanding Notification Levels

- 🔴 **Red Border (Danger)** - Critical alerts requiring immediate attention
  - Out of stock products
  - Critical stock levels
  - Failed orders

- 🟡 **Yellow Border (Warning)** - Important notifications
  - Low stock alerts
  - Products needing reorder
  - Cancelled orders

- 🟢 **Green Border (Success)** - Positive updates
  - Orders confirmed
  - Orders delivered
  - Successful operations

- 🔵 **Blue Border (Info)** - General information
  - New orders created
  - Status updates
  - General notifications

### Taking Action

1. **View Details**
   - Click "View Details" button to see more information
   - Opens the related page (order, product, etc.)

2. **Mark as Read**
   - Click "Mark as Read" on individual notifications
   - Or click "Mark all as read" at the top to clear all unread notifications

3. **Delete**
   - Click "Delete" to remove a notification
   - Deleted notifications cannot be recovered

---

## Setting Up Inventory Alerts

### Step 1: Configure Notification Settings

1. Go to **Settings > Notification Settings**
2. Enable these options:
   - ✅ Email Notifications Enabled
   - ✅ Low Stock Alerts
   - ✅ Order Status Notifications
3. Enter Administrator Email address
4. Click **Save**

### Step 2: Set Product Thresholds

**Option A: Individual Product**
1. Go to **Inventory > Threshold Management**
2. Find the product you want to monitor
3. Click **Edit** or set thresholds directly
4. Set the following:
   - **Reorder Level**: When to reorder (e.g., 50 units)
   - **Critical Level**: Critical stock level (e.g., 20 units)
   - **Ceiling Level**: Maximum stock (optional)
5. Enable **Threshold Alerts**
6. Click **Save**

**Option B: Apply to All Products**
1. Go to **Settings > Inventory Settings**
2. Set:
   - **Low Stock Threshold**: 50 (or your preferred value)
   - **Critical Stock Level**: 20 (or your preferred value)
3. Check ✅ **Apply thresholds to all products**
4. Click **Save**

### Step 3: Verify Automatic Checks

The system automatically checks inventory levels:
- **Every 30 minutes** during business hours (6 AM - 10 PM, weekdays)
- **Daily at 8:00 AM** (comprehensive check)
- **Weekly on Monday at 9:00 AM** (full system check)

### Step 4: Monitor Notifications

1. Visit `/notifications-list` regularly
2. Look for:
   - 🔴 **Out of Stock** - Restock immediately
   - 🔴 **Critical Stock** - Urgent reorder needed
   - 🟡 **Low Stock** - Plan to reorder soon
   - 🟡 **Reorder Needed** - Suggested order quantities

---

## Common Scenarios

### Scenario 1: Product Hits Low Stock

**What Happens:**
1. Product quantity drops to or below reorder level
2. System creates "Low Stock Alert" notification
3. Notification appears in your notification list
4. Level: Warning (yellow)

**What to Do:**
1. Click "View Details" to see product information
2. Review suggested order quantity
3. Create purchase order to restock
4. Mark notification as read when handled

---

### Scenario 2: Product Hits Critical Level

**What Happens:**
1. Product quantity drops to or below critical level
2. System creates "Critical Stock Level" notification
3. Notification appears with danger level (red)
4. Marked as urgent priority

**What to Do:**
1. **Immediate action required**
2. Check current stock and pending orders
3. Create emergency purchase order if needed
4. Consider placing express order with supplier
5. Mark notification as read when resolved

---

### Scenario 3: Product Out of Stock

**What Happens:**
1. Product quantity reaches 0
2. System creates "Out of Stock Alert" notification
3. Highest priority notification (red, danger)
4. May affect sales and customer orders

**What to Do:**
1. **Urgent**: Create purchase order immediately
2. Contact supplier for fastest delivery
3. Check if backorders exist
4. Update customers on expected restock date
5. Mark notification as read when stock arrives

---

## Manual Threshold Check

If you want to check thresholds manually:

1. **Using Command Line:**
   ```bash
   php artisan inventory:check-thresholds --force
   ```

2. **Using Test Script:**
   ```bash
   php test_notifications.php
   ```

---

## Tips for Effective Use

### Best Practices:

1. **Check Notifications Daily**
   - Make it part of your morning routine
   - Review all unread notifications
   - Take action on critical alerts immediately

2. **Set Appropriate Thresholds**
   - Consider lead time from suppliers
   - Account for sales velocity
   - Set critical level lower than reorder level
   - Example: If weekly sales = 40 units, set reorder at 100, critical at 40

3. **Clean Up Read Notifications**
   - Delete old notifications periodically
   - Keep unread count manageable
   - Focus on current issues

4. **Enable Email Alerts**
   - Get notified even when not logged in
   - Useful for critical alerts
   - Configure in notification settings

5. **Regular Threshold Review**
   - Update thresholds quarterly
   - Adjust based on sales trends
   - Consider seasonal variations

---

## Troubleshooting

### Not Receiving Notifications?

**Check:**
1. Product has thresholds set (reorder_level, critical_level)
2. Threshold alerts enabled for product
3. Product quantity is actually below threshold
4. Scheduled tasks are running
5. Browser cache cleared

**Test:**
1. Manually set product quantity below threshold
2. Run: `php artisan inventory:check-thresholds --force`
3. Visit `/notifications-list`
4. Notification should appear

### Too Many Notifications?

**Solutions:**
1. Increase threshold values
2. Disable alerts for slow-moving products
3. Mark all as read regularly
4. Delete old notifications
5. Adjust scheduled check frequency

### Missing Critical Alerts?

**Actions:**
1. Enable email notifications
2. Check notification settings enabled
3. Review threshold values (may be too low)
4. Verify admin email is correct
5. Check spam folder for emails

---

## Quick Reference Card

### Notification Types:
- 🔴 **Out of Stock** - Quantity = 0
- 🔴 **Critical Stock** - Quantity ≤ Critical Level
- 🟡 **Low Stock** - Quantity ≤ Reorder Level
- 🟡 **Reorder Needed** - Auto-reorder triggered
- 🔵 **Overstock** - Quantity > Ceiling Level

### Actions:
- ✅ View Details
- ✅ Mark as Read
- ✅ Delete
- ✅ Mark All as Read

### URLs:
- Notifications: `/notifications-list`
- Settings: `/settings/notifications`
- Inventory Settings: `/settings/inventory`
- Threshold Management: `/inventory/thresholds`

### Commands:
```bash
# Manual threshold check
php artisan inventory:check-thresholds --force

# Check specific product
php artisan inventory:check-thresholds --product=123

# Silent mode (no output)
php artisan inventory:check-thresholds --silent

# Test script
php test_notifications.php
```

---

## Support

For technical issues or questions:
1. Check the full documentation: `NOTIFICATION_SYSTEM_COMPLETE.md`
2. Review test script: `test_notifications.php`
3. Contact system administrator
4. Check application logs: `storage/logs/laravel.log`

---

**The notification system is ready to help you manage inventory efficiently!** 🎉
