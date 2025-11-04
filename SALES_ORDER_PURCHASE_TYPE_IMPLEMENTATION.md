# Sales Order Purchase Type Implementation

## Overview
This implementation adds a `purchase_type` field to sales orders to distinguish between **in-store** and **online** purchases. The system now automatically handles delivery status based on payment status and purchase type.

## Problem Statement
Previously, when creating a sales order for an in-store customer who paid immediately, the system would still mark the order as "pending" even though:
1. The customer had already paid
2. The customer received the product immediately (no delivery needed)

This caused confusion as the order status didn't reflect the actual transaction flow.

## Solution

### Business Logic
The system now follows these rules:

#### In-Store Purchases
- **Payment Status = Paid**: Order is automatically marked as `delivered` since the customer receives the product immediately
- **Payment Status = Pending/Partial**: Order remains `pending` or at the manually selected status
- No shipping/delivery process needed

#### Online Purchases
- Follows the normal e-commerce flow
- Status progresses through: pending → confirmed → processing → shipped → delivered
- Shipping and delivery tracking are applicable

## Technical Implementation

### 1. Database Migration
**File**: `database/migrations/2025_11_04_134838_add_purchase_type_to_sales_orders_table.php`

Added new column:
```php
$table->enum('purchase_type', ['in_store', 'online'])
      ->default('in_store')
      ->after('payment_method');
```

### 2. Model Update
**File**: `app/Models/SalesOrder.php`

Added `purchase_type` to the `$fillable` array:
```php
protected $fillable = [
    // ... existing fields
    'payment_method',
    'purchase_type',  // NEW
    'shipping_address',
    // ... remaining fields
];
```

### 3. Service Layer Logic
**File**: `app/Services/SalesOrderService.php`

Updated `createOrder()` method with automatic status handling:

```php
public function createOrder(array $data): SalesOrder
{
    // Set defaults
    $data['order_date'] = $data['order_date'] ?? Carbon::today();
    $data['status'] = $data['status'] ?? 'pending';
    $data['priority'] = $data['priority'] ?? 'normal';
    $data['payment_status'] = $data['payment_status'] ?? 'pending';
    $data['purchase_type'] = $data['purchase_type'] ?? 'in_store';

    // Auto-adjust status based on payment status and purchase type
    // For in-store purchases: if paid, customer receives product immediately
    if ($data['purchase_type'] === 'in_store' && $data['payment_status'] === 'paid') {
        // Set status to delivered since customer gets the product immediately
        $data['status'] = 'delivered';
        // Set shipped_date to today since it's instant
        $data['shipped_date'] = $data['shipped_date'] ?? Carbon::today();
    } elseif ($data['purchase_type'] === 'in_store' && $data['payment_status'] !== 'paid') {
        // For in-store but not yet paid, keep as pending or confirmed
        if (!isset($data['status']) || $data['status'] === 'pending') {
            $data['status'] = 'pending';
        }
    }
    // For online purchases, follow normal flow (status set manually or defaults to pending)

    // ... rest of the method
}
```

### 4. Controller Validation
**File**: `app/Http/Controllers/SalesOrderController.php`

Added validation rule:
```php
'purchase_type' => 'nullable|in:in_store,online',
```

### 5. User Interface
**File**: `resources/views/sales/orders/create.blade.php`

#### Added Purchase Type Field
```html
<!-- Purchase Type -->
<div>
    <x-input-label for="purchase_type" :value="__('Purchase Type')" />
    <select id="purchase_type" name="purchase_type" class="...">
        <option value="in_store" selected>In-Store Purchase</option>
        <option value="online">Online Purchase</option>
    </select>
    <p class="mt-1 text-xs text-gray-500" id="purchase_type_help">
        In-Store: Customer receives product immediately upon payment
    </p>
</div>
```

#### Added Dynamic Help Text
JavaScript updates the help text based on selected options:
- **In-Store + Paid**: "✓ In-Store + Paid: Order will be marked as delivered (customer receives product immediately)" (green text)
- **In-Store + Not Paid**: "In-Store: Customer receives product immediately upon payment"
- **Online**: "Online: Order will be processed and shipped to customer"

## Usage Examples

### Example 1: In-Store Cash Sale
1. Customer: John Doe
2. Payment Method: Cash
3. Payment Status: **Paid**
4. Purchase Type: **In-Store**

**Result**: Order is automatically marked as `delivered` with today's date as `shipped_date`

### Example 2: In-Store Layaway
1. Customer: Jane Smith
2. Payment Method: Cash
3. Payment Status: **Pending**
4. Purchase Type: **In-Store**

**Result**: Order remains `pending` until payment is completed

### Example 3: Online Order
1. Customer: Bob Johnson
2. Payment Method: Card
3. Payment Status: Paid
4. Purchase Type: **Online**

**Result**: Order follows normal flow (pending → processing → shipped → delivered)

## Benefits

1. **Accurate Status Tracking**: Order status now reflects the actual transaction state
2. **Reduced Manual Updates**: No need to manually change status from pending to delivered for in-store paid orders
3. **Clear Documentation**: Purchase type is recorded for reporting and analytics
4. **Future-Proof**: System can handle both in-store and online scenarios
5. **User-Friendly**: Dynamic help text guides users on what to expect

## Database Schema

```sql
ALTER TABLE sales_orders 
ADD COLUMN purchase_type ENUM('in_store', 'online') 
DEFAULT 'in_store' 
AFTER payment_method;
```

## Migration Command
```bash
php artisan migrate
```

## Testing Recommendations

1. **Test In-Store Paid Order**
   - Create order with payment_status='paid' and purchase_type='in_store'
   - Verify status is 'delivered'
   - Verify shipped_date is set to today

2. **Test In-Store Unpaid Order**
   - Create order with payment_status='pending' and purchase_type='in_store'
   - Verify status remains 'pending'

3. **Test Online Order**
   - Create order with purchase_type='online'
   - Verify status follows normal workflow

4. **Test UI Interactions**
   - Change payment status and observe help text updates
   - Change purchase type and observe help text updates

## Notes

- Default value is `in_store` to maintain current system behavior
- The logic doesn't override manually set status values unless conditions are met
- Shipped_date is only auto-set for in-store paid orders
- The implementation is backward compatible with existing orders (they default to in_store)

## Files Modified

1. `database/migrations/2025_11_04_134838_add_purchase_type_to_sales_orders_table.php` (NEW)
2. `app/Models/SalesOrder.php`
3. `app/Services/SalesOrderService.php`
4. `app/Http/Controllers/SalesOrderController.php`
5. `resources/views/sales/orders/create.blade.php`

---

**Implementation Date**: November 4, 2025
**Status**: ✅ Completed and Ready for Testing
