# Bank Transfer Payment Module - Implementation Guide

## Overview

This module enables customers to pay for their orders via bank transfer by uploading proof of payment, which admins can then review, confirm, or cancel. Upon confirmation, the system automatically updates inventory and order status.

---

## System Workflow

### Customer Side
1. Customer selects **Bank Transfer** as payment method during checkout
2. Order is created with `payment_status = 'pending'`
3. Customer uploads bank transfer proof with reference number
4. System validates and stores the payment submission
5. Customer waits for admin confirmation

### Admin Side
1. Admin reviews pending bank transfer payments in dashboard
2. Admin views uploaded proof image
3. Admin either:
   - **Confirms** → Order marked as paid, inventory deducted, order processed
   - **Cancels** → Payment rejected with reason, customer notified to resubmit

---

## Installation Steps

### 1. Run Migration

```bash
php artisan migrate
```

This creates the `bank_transfer_payments` table with:
- Payment details (bank name, reference number, amount)
- Proof image path
- Status tracking (pending/confirmed/cancelled)
- Admin review information

### 2. Create Storage Link (if not exists)

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public` for accessing uploaded payment proofs.

### 3. Set File Permissions (Linux/Mac)

```bash
chmod -R 775 storage/app/public/payment_proofs
```

---

## Database Schema

### Table: `bank_transfer_payments`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| order_id | bigint | Foreign key to sales_orders |
| payment_method | string | Always 'bank_transfer' |
| bank_name | string | Name of bank used |
| reference_no | string | Unique transaction reference |
| amount | decimal(15,2) | Payment amount |
| proof | string | Path to uploaded proof image |
| status | enum | pending/confirmed/cancelled |
| reviewed_by | bigint | Admin user who reviewed |
| reviewed_at | timestamp | When reviewed |
| admin_notes | text | Admin comments/reason |
| created_at | timestamp | Submission date |
| updated_at | timestamp | Last update |

### Relationships

- **BankTransferPayment** `belongsTo` **SalesOrder**
- **BankTransferPayment** `belongsTo` **User** (reviewer)
- **SalesOrder** `hasMany` **BankTransferPayment**

---

## Usage Guide

### For Customers

#### Submit Bank Transfer Payment

1. **Access Payment Form**
   - Navigate to order details page
   - Click "Submit Bank Transfer Payment" button
   - Or directly access: `/sales/bank-transfer-payment?order_id={order_id}`

2. **Fill Payment Details**
   - Select bank name from dropdown
   - Enter transaction reference number
   - Amount is auto-filled (must match order total)
   - Upload proof image (JPG, PNG, or PDF, max 5MB)

3. **Submit**
   - Payment marked as "Pending"
   - Admin notified for review
   - Track status on order page

#### View Payment in Order

```blade
<!-- In sales order show page -->
@if($order->payment_method === 'bank_transfer')
    <div class="mt-4">
        @if($order->bankTransferPayments->isEmpty())
            <a href="{{ route('bank-transfer-payments.create', ['order_id' => $order->order_id]) }}"
               class="btn btn-primary">
                Submit Bank Transfer Payment
            </a>
        @else
            @foreach($order->bankTransferPayments as $payment)
                <div class="payment-status">
                    <span class="badge badge-{{ $payment->status_color }}">
                        {{ $payment->status_display }}
                    </span>
                    <p>Reference: {{ $payment->reference_no }}</p>
                </div>
            @endforeach
        @endif
    </div>
@endif
```

### For Admins

#### Access Admin Dashboard

Navigate to: `/admin/bank-transfer-payments`

#### Review Pending Payments

1. **Filter Payments**
   - By status (Pending/Confirmed/Cancelled)
   - By date range
   - Search by reference number or bank name

2. **View Details**
   - Order number (clickable to view full order)
   - Customer information
   - Bank details and reference number
   - Amount paid
   - Proof image (click "View" to open)
   - Submission date/time

3. **Take Action**
   - **Confirm**: Click ✓ icon → Add optional notes → Confirm
   - **Cancel**: Click ✗ icon → Enter reason (required) → Cancel

#### Confirmation Process

When admin confirms payment:
1. Payment status → `confirmed`
2. Order payment_status → `paid`
3. For in-store orders: Order status → `delivered`
4. For online orders: Order status → `confirmed` (ready to ship)
5. **Inventory automatically deducted** for all order items
6. Stock validation performed (prevents over-selling)
7. Activity logged with admin details

#### Cancellation Process

When admin cancels payment:
1. Payment status → `cancelled`
2. Admin must provide cancellation reason
3. Customer notified to resubmit correct proof
4. Order remains in pending state

---

## API Endpoints (Routes)

### Customer Routes

```php
POST /bank-transfer-payments
// Submit bank transfer payment proof
// Requires: order_id, bank_name, reference_no, amount, proof (file)
```

### Admin Routes

```php
GET /admin/bank-transfer-payments
// View all bank transfer payments with filters

POST /admin/bank-transfer-payments/{id}/confirm
// Confirm a pending payment
// Optional: admin_notes

POST /admin/bank-transfer-payments/{id}/cancel
// Cancel a pending payment
// Required: admin_notes (reason)

GET /admin/bank-transfer-payments/{id}/proof
// View/download payment proof image
```

---

## Controller Methods

### BankTransferPaymentController

#### `index()` - Admin Dashboard
- Lists all bank transfer payments with pagination
- Filters by status, date range, search
- Shows summary statistics
- Returns: `admin.bank-transfer-payments.index` view

#### `store()` - Submit Payment
- Validates payment details and proof upload
- Stores proof image in `storage/app/public/payment_proofs`
- Creates payment record with status 'pending'
- Updates order payment status
- Returns: Success message with reference number

#### `confirm()` - Confirm Payment
- Validates admin has permission
- Updates payment status to 'confirmed'
- Updates order payment and delivery status
- **Deducts inventory for each order item**
- Validates sufficient stock before deduction
- Logs admin action
- Returns: Success message

#### `cancel()` - Cancel Payment
- Requires cancellation reason
- Updates payment status to 'cancelled'
- Logs admin action with reason
- Returns: Success message

#### `showProof()` - View Proof Image
- Returns stored proof image file
- Protected route (admin only)

---

## Validation Rules

### Payment Submission (store)

```php
'order_id' => 'required|exists:sales_orders,order_id',
'bank_name' => 'required|string|max:255',
'reference_no' => 'required|string|max:255|unique:bank_transfer_payments',
'amount' => 'required|numeric|min:0.01',
'proof' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB
```

### Payment Confirmation

```php
'admin_notes' => 'nullable|string|max:1000'
```

### Payment Cancellation

```php
'admin_notes' => 'required|string|max:1000' // Reason required
```

---

## Business Logic

### Inventory Management

When payment is confirmed:
1. System loops through order items
2. Checks available stock for each product
3. If insufficient stock → Transaction rolled back, error returned
4. If sufficient stock → Deducts quantity from product.quantity
5. Logs inventory change for each product
6. All operations wrapped in database transaction (atomic)

### Order Status Updates

| Purchase Type | Payment Status | Result Order Status |
|--------------|----------------|---------------------|
| in_store | paid | delivered (immediate) |
| online | paid | confirmed (ready to ship) |
| Any | cancelled | pending (unchanged) |

### Payment Status Validation

- Amount must **exactly match** order total (within 0.01 tolerance)
- Reference number must be **unique** across all payments
- Order must have `payment_method = 'bank_transfer'`
- Cannot confirm already confirmed payment
- Cannot confirm cancelled payment
- Cannot cancel already confirmed payment

---

## Security Features

1. **File Upload Protection**
   - Only images and PDFs allowed
   - Maximum file size: 5MB
   - Files stored in protected storage directory
   - Unique filenames prevent overwriting

2. **Authorization**
   - All routes require authentication
   - Admin routes should be protected by role/permission middleware
   - Payment proof viewing restricted to authenticated users

3. **Data Validation**
   - Strict validation rules on all inputs
   - CSRF protection on all POST requests
   - SQL injection protection via Eloquent ORM
   - XSS protection via Blade templating

4. **Transaction Safety**
   - Database transactions ensure data consistency
   - Rollback on any error during confirmation
   - Stock validation prevents over-selling

---

## Error Handling

### Common Errors and Solutions

**"Payment amount does not match order total"**
- Solution: Ensure uploaded amount exactly matches order total

**"Reference number already exists"**
- Solution: Use unique transaction reference from bank

**"Insufficient stock for product: {name}"**
- Solution: Admin must restock before confirming payment

**"Cannot confirm a cancelled payment"**
- Solution: Customer must create new payment submission

**"Payment proof not found"**
- Solution: Ensure storage link exists and file permissions are correct

---

## Testing

### Test Scenarios

#### 1. Successful Payment Flow
```
1. Create order with bank_transfer payment method
2. Submit payment with valid proof
3. Verify payment status = pending
4. Admin confirms payment
5. Verify:
   - Payment status = confirmed
   - Order status = paid/delivered
   - Inventory deducted
```

#### 2. Invalid Amount
```
1. Create order with total = 1000
2. Submit payment with amount = 900
3. Verify: Error "amount does not match"
```

#### 3. Duplicate Reference
```
1. Submit payment with ref = "ABC123"
2. Submit another payment with ref = "ABC123"
3. Verify: Error "reference number already exists"
```

#### 4. Insufficient Stock
```
1. Product has stock = 5
2. Order has quantity = 10
3. Admin confirms payment
4. Verify: Error "insufficient stock"
```

#### 5. Cancel Payment
```
1. Submit payment
2. Admin cancels with reason
3. Verify: Payment status = cancelled
4. Customer can resubmit new payment
```

---

## Integration with Existing System

### POS Integration

For POS orders with bank_transfer:
1. Order created with `purchase_type = 'in_store'`
2. Customer submits payment proof
3. Upon confirmation → Status automatically set to `delivered`
4. Inventory deducted immediately

### Sales Order Integration

For regular sales orders with bank_transfer:
1. Order created with `purchase_type = 'online'`
2. Customer submits payment proof
3. Upon confirmation → Status set to `confirmed`
4. Inventory deducted, ready for shipping

### Notification Integration (Future Enhancement)

```php
// After payment confirmation
Mail::to($order->customer->email)->send(
    new PaymentConfirmedMail($payment)
);

// After payment cancellation
Mail::to($order->customer->email)->send(
    new PaymentCancelledMail($payment, $reason)
);
```

---

## Customization Options

### Add More Banks

Edit `resources/views/sales/bank-transfer-payment.blade.php`:

```blade
<option value="New Bank Name">New Bank Name</option>
```

### Change File Size Limit

Edit validation in `BankTransferPaymentController@store`:

```php
'proof' => 'required|image|mimes:jpeg,png,jpg,pdf|max:10240', // 10MB
```

### Add Role-Based Access Control

Edit `routes/web.php`:

```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin routes here
});
```

### Customize Inventory Logic

Edit `BankTransferPaymentController@confirm`:

```php
// Add custom inventory deduction logic
$product->quantity -= $item->quantity_ordered;
// Add your custom logic here
$product->save();
```

---

## File Structure

```
app/
├── Http/Controllers/
│   └── BankTransferPaymentController.php
├── Models/
│   ├── BankTransferPayment.php
│   └── SalesOrder.php (updated)
database/
└── migrations/
    └── 2025_11_13_000001_create_bank_transfer_payments_table.php
resources/views/
├── admin/
│   └── bank-transfer-payments/
│       └── index.blade.php
└── sales/
    └── bank-transfer-payment.blade.php
routes/
└── web.php (updated)
storage/app/public/
└── payment_proofs/
    └── (uploaded images)
```

---

## Maintenance

### Clear Old Payment Proofs

```bash
# Delete payment proofs older than 90 days
php artisan schedule:run

# Or create artisan command:
php artisan payments:cleanup --days=90
```

### Backup Payment Proofs

```bash
# Backup payment proofs directory
tar -czf payment_proofs_backup_$(date +%Y%m%d).tar.gz storage/app/public/payment_proofs/
```

### Monitor Storage Usage

```bash
du -sh storage/app/public/payment_proofs
```

---

## Support & Troubleshooting

### Debug Mode

Enable logging in controller:
```php
Log::info('Payment submission', ['data' => $request->all()]);
```

### Check Permissions

```bash
ls -la storage/app/public/payment_proofs
# Should show write permissions
```

### Verify Storage Link

```bash
ls -la public/storage
# Should be symlink to ../storage/app/public
```

---

## Conclusion

This Bank Transfer Payment module provides a complete workflow for handling offline payment verification in your POS and Sales & Inventory System. It ensures inventory accuracy, prevents fraud, and provides clear audit trails for all payment transactions.

For questions or issues, refer to the code comments or contact the development team.
