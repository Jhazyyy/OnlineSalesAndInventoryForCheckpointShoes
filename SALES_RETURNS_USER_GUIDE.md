# Sales Returns - Quick Reference Guide

## ✅ Problem Fixed

**Issue:** When approving returns, inventory and product quantities were NOT being updated.

**Solution:** The system now properly updates both `Inventory` and `Product` tables when a return is approved.

---

## How to Use Sales Returns

### 1. **Create a Return**
1. Go to **Sales → Returns**
2. Click **"Create New Return"**
3. Fill in the details:
   - **Product** (required)
   - **Customer** (optional)
   - **Sales Order** (optional - can search and link)
   - **Quantity** (required)
   - **Price** (required)
   - **Return Date** (required)
   - **Reason** (optional)
4. Click **"Create Return"**

✅ Return is created with **Pending** status
❌ Inventory is NOT changed yet (remains unchanged until approval)

---

### 2. **Approve a Return**
1. Go to **Sales → Returns**
2. Click on a **Pending** return
3. Click **"Approve Return"** button
4. Confirm the action

✅ Return status changes to **Approved**
✅ **Inventory quantity increases** by the return quantity
✅ **Product quantity increases** by the return quantity
✅ **Stock movement** is recorded for audit trail

**Example:**
- Product had: 50 units
- Customer returns: 5 units
- After approval: **55 units** ✅

---

### 3. **Reject a Return**
1. Go to **Sales → Returns**
2. Click on a **Pending** return
3. Click **"Reject Return"** button

✅ Return status changes to **Rejected**
❌ Inventory is NOT changed (no stock adjustment)

---

### 4. **Mark as Processed**
1. Go to **Sales → Returns**
2. Click on an **Approved** return
3. Click **"Mark as Processed"** button

✅ Return status changes to **Processed**
❌ Inventory is NOT changed again (already updated during approval)

---

### 5. **Bulk Approve Returns**
1. Go to **Sales → Returns**
2. Select multiple **Pending** returns using checkboxes
3. Click **"Bulk Approve"** button
4. Confirm the action

✅ All selected returns are approved
✅ Inventory is updated for each return
✅ All done in a single transaction (all or nothing)

---

## Status Workflow

```
PENDING → APPROVED → PROCESSED
    ↓
REJECTED
```

### Status Definitions

| Status | Description | Inventory Impact |
|--------|-------------|------------------|
| **Pending** | Return requested, awaiting approval | No change |
| **Approved** | Return approved, items received | ✅ **Inventory +** |
| **Rejected** | Return denied | No change |
| **Processed** | Return fully processed | No change (already updated) |

---

## Important Rules

### ✅ What You CAN Do

- **Create** returns with pending status
- **Approve** pending returns (updates inventory)
- **Reject** pending returns (no inventory change)
- **Mark as processed** approved returns
- **Delete** pending or rejected returns
- **Edit** pending returns
- **Bulk approve** multiple pending returns

### ❌ What You CANNOT Do

- **Approve** a return twice (prevented automatically)
- **Edit** approved or processed returns
- **Delete** approved or processed returns
- **Change status** back to pending after approval
- **Reject** an already approved return

---

## Inventory Impact Summary

### When a Return is **APPROVED**:
1. **Inventory Table** (`quantity_on_hand`) ⬆️ increased
2. **Product Table** (`quantity`) ⬆️ increased
3. **Stock Movement** record created
4. All changes are atomic (transaction)

### When a Return is **REJECTED**:
- ❌ No inventory changes
- Status updated to "rejected"

### When Marked as **PROCESSED**:
- ❌ No inventory changes (already updated during approval)
- Status updated to "processed"

---

## Linking Returns to Sales Orders (Optional)

You can optionally link a return to a specific sales order:

1. In the return form, use the **"Search Sales Order"** field
2. Type the order number or customer name
3. Select the sales order from the dropdown
4. **Customer** is auto-filled
5. **Order items** are shown - click to quick-fill product details

✅ This helps track which sales the returns came from
✅ Better reporting and analytics

---

## Viewing Inventory Changes

### Method 1: Product Stock Movements
1. Go to **Inventory → Products**
2. Click on a product
3. View the **Stock Movements** tab
4. Look for entries with type **"Return"**

### Method 2: Stock Movement List
1. Go to **Inventory → Stock Movements**
2. Filter by **Movement Type: "Return"**
3. View all return-related inventory changes

---

## Troubleshooting

### ❓ Return was approved but inventory didn't update
**Fixed!** This was the original issue. After the fix:
- ✅ Inventory updates automatically when approved
- ✅ Both Inventory and Product tables are synchronized
- ✅ Stock movement is recorded

### ❓ Can I reverse an approved return?
**No.** Once approved, the return cannot be changed back to pending.
- If you need to reverse it, create a new sales order for the same quantity
- This maintains proper audit trail

### ❓ Can I delete an approved return?
**No.** Only pending or rejected returns can be deleted.
- This prevents inventory inconsistencies
- Approved returns must remain for audit purposes

---

## Summary of Changes Made

### What Was Fixed:
1. **Returns now update inventory** when approved ✅
2. **Product quantities sync** with inventory ✅
3. **Stock movements tracked** properly ✅
4. **No constraints or blocking issues** ✅

### Files Modified:
1. `app/Models/Returns.php` - Updated `approve()` method
2. `app/Http/Controllers/ReturnsController.php` - Enhanced `update()` method

### How It Works Now:
- Uses `InventoryService::adjust()` for proper inventory updates
- All updates wrapped in database transactions
- Maintains data consistency between Inventory and Product tables
- Creates proper audit trail via stock movements

---

## Need Help?

If you encounter any issues:
1. Check that the return is in **Pending** status before approving
2. Verify you have the necessary permissions
3. Check the error message displayed
4. Review the stock movements for audit trail

---

**Status: ✅ FULLY FUNCTIONAL**

Last Updated: November 11, 2025
