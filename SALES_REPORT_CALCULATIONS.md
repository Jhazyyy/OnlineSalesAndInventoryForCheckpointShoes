# Sales Report - Standard Accounting Calculations

## Overview
The sales report now uses **standard accounting formulas** to accurately calculate revenue, profit, and margins. All calculations follow industry-standard practices to prevent conflicts and ensure financial accuracy.

---

## Standard Calculation Formulas

### 1. **Gross Revenue (Gross Sales)**
```
Gross Revenue = Sum of all product line items (Subtotal)
```
- This is the **raw sales amount** from products only
- Calculated from `sales_orders.subtotal`
- Does NOT include: discounts, taxes, or shipping
- **Example**: If you sell 10 items at ₱100 each, Gross Revenue = ₱1,000

### 2. **Net Revenue (Revenue After Discounts)**
```
Net Revenue = Gross Revenue - Total Discounts
```
- Revenue **after applying discounts** but before taxes and shipping
- Calculated as: `subtotal - discount_amount`
- This is the actual product revenue the business earns
- **Example**: ₱1,000 - ₱100 discount = ₱900 Net Revenue

### 3. **Total Revenue (Total Amount Collected)**
```
Total Revenue = Net Revenue + Tax Amount + Shipping Amount
```
Alternative formula (same result):
```
Total Revenue = Gross Revenue - Discounts + Tax + Shipping
```
- This is the **complete amount the customer pays**
- Calculated from `sales_orders.total_amount`
- Formula breakdown:
  - Start with Net Revenue (₱900)
  - Add Tax Amount (e.g., ₱108 for 12% VAT)
  - Add Shipping (e.g., ₱50)
  - **Total Revenue = ₱1,058**

### 4. **Cost of Goods Sold (COGS)**
```
COGS = Sum of (Unit Cost × Quantity) for all items sold
```
- Total cost to acquire/produce the products sold
- Uses `products.total_cost` (or `cost` if total_cost is null)
- **Example**: If 10 items cost ₱60 each to acquire, COGS = ₱600

### 5. **Gross Profit**
```
Gross Profit = Net Revenue - COGS
```
- Profit before operating expenses
- Uses Net Revenue (not Total Revenue) because:
  - Tax goes to government (not profit)
  - Shipping is typically a pass-through cost
  - Discounts reduce actual revenue earned
- **Example**: ₱900 (Net Revenue) - ₱600 (COGS) = ₱300 Gross Profit

### 6. **Profit Margin**
```
Profit Margin = (Gross Profit / Net Revenue) × 100
```
- Expressed as a percentage
- Shows profit as a % of actual product revenue
- **Example**: (₱300 / ₱900) × 100 = 33.3%

---

## Complete Example Calculation

### Sample Order Details:
- **Product Sales**: 10 units @ ₱100 each
- **Discount**: ₱100 (10% off)
- **Tax (VAT 12%)**: Calculated on Net Revenue
- **Shipping**: ₱50
- **Product Cost**: ₱60 per unit

### Step-by-Step Calculation:

1. **Gross Revenue** = 10 × ₱100 = **₱1,000**

2. **Total Discount** = **₱100**

3. **Net Revenue** = ₱1,000 - ₱100 = **₱900**

4. **Tax Amount** = ₱900 × 12% = **₱108**

5. **Shipping Amount** = **₱50**

6. **Total Revenue** = ₱900 + ₱108 + ₱50 = **₱1,058** ← Customer pays this

7. **COGS** = 10 × ₱60 = **₱600**

8. **Gross Profit** = ₱900 - ₱600 = **₱300**

9. **Profit Margin** = (₱300 / ₱900) × 100 = **33.3%**

---

## Database Schema Reference

### Sales Orders Table Structure:
```sql
subtotal          DECIMAL(10,2)  -- Sum of line items (Gross Revenue)
discount_amount   DECIMAL(10,2)  -- Total discounts applied
tax_amount        DECIMAL(10,2)  -- VAT or other taxes
shipping_amount   DECIMAL(10,2)  -- Delivery charges
total_amount      DECIMAL(10,2)  -- Final amount customer pays
```

### Calculation in Code:
```php
// From sales_orders table
$grossRevenue = sum(subtotal);
$totalDiscount = sum(discount_amount);
$netRevenue = $grossRevenue - $totalDiscount;
$totalTax = sum(tax_amount);
$totalShipping = sum(shipping_amount);
$totalRevenue = $netRevenue + $totalTax + $totalShipping;

// From sales_order_items and products
$cogs = sum(product.total_cost * item.quantity);

// Calculate profit
$grossProfit = $netRevenue - $cogs;
$profitMargin = ($grossProfit / $netRevenue) * 100;
```

---

## Why These Formulas Matter

### 1. **Industry Standard**
These calculations follow GAAP (Generally Accepted Accounting Principles) and are universally recognized in financial reporting.

### 2. **Tax Compliance**
- Tax is calculated on Net Revenue (after discounts)
- Tax amount is NOT included in profit calculations
- Proper separation for BIR reporting

### 3. **Accurate Profitability**
- Using Net Revenue (not Total Revenue) for profit margin gives accurate product profitability
- Excludes pass-through costs (tax, shipping)
- Discounts properly reduce revenue before calculating profit

### 4. **Business Decisions**
- **Gross Profit** tells you actual profit from product sales
- **Profit Margin** helps with pricing decisions
- **Net Revenue** shows true business income from operations

---

## Report Display Breakdown

### Summary Cards:

1. **Total Orders**: Count of all sales orders
2. **Total Revenue**: ₱1,058 (What customer actually pays)
3. **Gross Revenue**: ₱1,000 (Product sales before deductions)
4. **Gross Profit**: ₱300 (Net Revenue - COGS)
5. **Profit Margin**: 33.3% (Profit as % of Net Revenue)

### Detailed Breakdown (Info Box):
```
Gross Revenue     = ₱1,000.00  (Sum of all product line items)
- Discounts       = ₱100.00
─────────────────────────────
= Net Revenue     = ₱900.00

+ Tax             = ₱108.00
+ Shipping        = ₱50.00
─────────────────────────────
= Total Revenue   = ₱1,058.00  (Customer Payment)

Net Revenue - COGS (₱600.00) = Gross Profit (₱300.00)
Profit Margin = (₱300 / ₱900) × 100 = 33.3%
```

---

## Common Questions

### Q: Why not include tax in profit calculation?
**A**: Tax is collected on behalf of the government and must be remitted. It's not part of your business revenue or profit.

### Q: Why use Net Revenue instead of Total Revenue for profit margin?
**A**: Net Revenue represents your actual product earnings. Including tax and shipping (which aren't profit sources) would artificially deflate your margin percentage.

### Q: What if discount_amount is negative or null?
**A**: The code handles this safely. NULL is treated as 0, and negative values (should never occur) would be caught during data validation.

### Q: How are discounts calculated?
**A**: Discounts are applied through the tax/discount rules system. The `discount_amount` is automatically calculated and stored when an order is created or updated.

---

## Conflict Prevention

### No More Issues With:
✅ Tax included in profit calculations
✅ Shipping affecting profit margins
✅ Discounts not properly deducted from revenue
✅ Using wrong revenue base for margin calculations
✅ Inconsistent formulas across different reports

### Data Integrity Checks:
- All calculations use the same source fields
- Formulas are documented and standardized
- Revenue flow: Gross → Net → Total is clearly defined
- COGS accurately pulled from product costs

---

## Testing the Calculations

### Manual Verification:
1. Create a test order with known values
2. Check each calculation step:
   - Verify subtotal matches line item sum
   - Confirm discount is applied
   - Check tax calculated on net amount
   - Verify shipping added correctly
   - Confirm COGS matches product costs
   - Validate profit = net revenue - COGS
   - Check margin percentage calculation

### Example Test Order:
```php
// Create order with:
- 5 items @ ₱200 each = ₱1,000 subtotal
- 10% discount = ₱100
- 12% VAT on ₱900 = ₱108
- ₱75 shipping
- Product cost ₱120 per unit

Expected Results:
- Gross Revenue: ₱1,000
- Net Revenue: ₱900
- Total Revenue: ₱1,083
- COGS: ₱600
- Gross Profit: ₱300
- Profit Margin: 33.3%
```

---

## File Locations

### Updated Files:
1. **app/Services/ReportService.php** (Line 319-439)
   - `generateSalesReport()` method
   - Contains all calculation logic

2. **resources/views/reports/sales.blade.php** (Lines 60-150)
   - Summary cards with updated labels
   - Detailed calculation breakdown display

### Related Files:
- **app/Models/SalesOrder.php**: `calculateTotals()` method
- **app/Services/TaxDiscountService.php**: Tax and discount calculation
- **database/migrations/*_create_sales_orders_table.php**: Schema definition

---

**Last Updated**: November 23, 2025  
**Version**: 2.0 - Standard Accounting Formulas
