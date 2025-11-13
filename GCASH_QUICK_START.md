# GCash Scan-to-Pay - Quick Start Guide

## 🚀 Quick Setup (5 Minutes)

### Step 1: Run Migration
```bash
php artisan migrate
```

### Step 2: Upload Your GCash QR Code
1. Save your GCash merchant QR code as `gcash_qr.png`
2. Place it in: `storage/app/public/gcash_qr.png`
3. Run: `php artisan storage:link`

### Step 3: Test the Feature
1. Go to POS: `/pos/create`
2. Add items to cart
3. Select **"GCash"** as payment method
4. Click **"Show GCash QR Code"** button
5. Enter test reference: `1234567890123`
6. Click **"Confirm Payment"**
7. Complete the sale

---

## 💡 How It Works

### For Cashiers:
1. Customer wants to pay with GCash
2. Select "GCash" from payment dropdown
3. Click "Show GCash QR Code" - modal opens
4. Customer scans QR with their GCash app
5. Customer pays and receives reference number
6. Enter the reference number in the modal
7. Click "Confirm Payment"
8. Complete the transaction as normal

### What Happens Behind the Scenes:
- Order created with `payment_method = 'gcash'`
- GCash payment record saved with reference number
- Payment automatically marked as "verified"
- Inventory deducted immediately
- Receipt shows GCash payment details

---

## 📱 POS Interface Changes

### Payment Method Dropdown
```
Cash
GCash ← NEW
Bank Transfer
```

### When GCash Selected
- Blue "Show GCash QR Code" button appears
- Modal shows QR, instructions, reference input

### After Reference Entered
- Green badge confirms: "✓ GCash Reference Number Entered: 1234567890123"

---

## 🎯 Business Rules

| Rule | Value |
|------|-------|
| **Reference Length** | Minimum 10 characters |
| **Payment Status** | Automatically "Paid" |
| **Verification** | Instant (no admin approval needed) |
| **Inventory** | Deducted immediately |
| **Receipt** | Shows GCash details automatically |

---

## 🖼️ QR Code Requirements

- **Format**: PNG or JPG
- **Size**: 256x256px (recommended)
- **Location**: `storage/app/public/gcash_qr.png`
- **Fallback**: Gray placeholder if missing

---

## 📊 Database Tables

### New Table: `gcash_payments`
Stores all GCash transactions with:
- Reference numbers (unique)
- Payment amounts
- Verification status
- Timestamps

### Relationship
```
SalesOrder → hasMany → GcashPayment
GcashPayment → belongsTo → SalesOrder
```

---

## ✅ Testing Checklist

**Before Going Live:**
- [ ] Migration successful
- [ ] QR code displays correctly
- [ ] Reference input works
- [ ] Payment records created
- [ ] Receipt shows GCash details
- [ ] No duplicate reference errors

**Test Reference Numbers:**
- `1234567890123` (13 digits)
- `GCASH12345678` (14 chars)
- `REF-9876543210` (15 chars)

---

## 🐛 Common Issues

### "QR Code shows placeholder"
→ Upload `gcash_qr.png` to `storage/app/public/`  
→ Run `php artisan storage:link`

### "Reference number required error"
→ Must be at least 10 characters  
→ Check that modal confirmed before submitting form

### "Duplicate reference number"
→ Reference already used in system  
→ Each reference must be unique

---

## 📞 Need Help?

Check the full documentation: `GCASH_SCAN_TO_PAY_IMPLEMENTATION.md`

---

## 🎉 You're Ready!

The GCash Scan-to-Pay feature is now live and ready for customer transactions.

**Next Steps:**
1. Train cashiers on the new flow
2. Test with a few transactions
3. Monitor payment records in database
4. Consider adding admin dashboard for reconciliation

---

**Happy Selling! 🛍️💙**
