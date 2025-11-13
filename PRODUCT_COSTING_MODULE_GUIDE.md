# Product Costing Module - Complete Guide

## Overview
The **Product Costing Module** is a comprehensive system for managing and calculating product costs, including raw materials, labor, overhead allocation, shipping, taxes, and handling costs. This module helps you determine accurate product pricing, analyze profit margins, and identify products that need attention.

---

## ✅ Module Status: FULLY IMPLEMENTED

The Product Costing Module is **already implemented** in your system with:
- ✅ Complete backend logic (Controller, Service, Model)
- ✅ Database structure with all cost fields
- ✅ User Interface (Views created)
- ✅ Navigation menu integration
- ✅ Routes configured
- ✅ Automatic calculations
- ✅ Analytics and reporting

---

## Features

### 1. **Cost Components Tracking**
The module tracks the following cost components for each product:

- **Raw Material Cost** - Cost of materials per unit
- **Labor Cost** - Direct labor cost per unit
- **Overhead Cost** - Overhead/indirect costs per unit
- **Manufacturing Cost** - Automatically calculated (Raw Material + Labor + Overhead)
- **Shipping Cost** - Shipping/freight cost per unit
- **Tax Amount** - Tax amount per unit
- **Handling Cost** - Handling and packaging cost per unit
- **Total Cost** - Automatically calculated sum of all costs

### 2. **Profit Analysis**
- **Profit Amount** - Selling price minus total cost
- **Profit Margin %** - Percentage profit relative to cost
- **Visual indicators** for profit margins (green for good, yellow for low, red for negative)

### 3. **Pricing Support**
- Manual price setting
- Price suggestion based on desired profit margin
- Cost breakdown visualization
- Real-time calculation updates

### 4. **Analytics & Insights**
- Dashboard with key statistics
- Low margin products identification (<20%)
- Negative margin products (losing money)
- Average profit margin across all products
- Costing completion percentage

---

## How to Access

### From the Navigation Menu:
1. Log in to your system
2. Click the **sidebar menu** button (☰) in the top left
3. Navigate to **Inventory** section
4. Click **"Product Costing"**

### Direct URL:
```
http://your-domain.com/inventory/product-costing
```

---

## Using the Module

### Dashboard View (`/inventory/product-costing`)

The dashboard provides an overview of all products with their costing information:

**Statistics Cards:**
- Total products count
- Products with costing data
- Average profit margin
- Low/Negative margin product counts

**Filter Options:**
- All Products
- With Costing
- Without Costing
- Low Margin (<20%)
- Negative Margin

**Search:**
- Search by product name, brand, or category

**Product Table Columns:**
- Product name and brand
- Category
- Selling price
- Total cost
- Profit amount
- Profit margin %
- Action buttons (Edit Costing)

### Editing Product Costing (`/inventory/product-costing/{product}/edit`)

**Product Information Section:**
- Displays current product details
- Shows current price, total cost, and profit margin

**Current Cost Breakdown:**
- Visual breakdown of cost components
- Percentage contribution of each component

**Cost Components Form:**
1. **Raw Material Cost** - Enter cost of materials
2. **Labor Cost** - Enter direct labor cost
3. **Overhead Cost** - Enter overhead/indirect costs
4. **Shipping Cost** - Enter shipping/freight cost
5. **Tax Amount** - Enter tax amount per unit
6. **Handling Cost** - Enter handling and packaging cost

**Live Calculation Display:**
- Manufacturing Cost (auto-calculated)
- Total Cost (auto-calculated)
- Profit Amount (auto-calculated)
- Profit Margin % (auto-calculated)
- Updates in real-time as you enter values

**Pricing Section:**
- **Selling Price** - Set or adjust the selling price
- **Calculation Method** - Choose: Standard, Average, FIFO, LIFO
- **Price Suggestion** - System suggests optimal price for desired margin

**Cost Notes:**
- Add notes about costing calculations
- Track reasoning or special considerations

### Low Margin Products View (`/inventory/product-costing/low-margin`)

Shows products with profit margins below 20%:
- Warning alert about attention needed
- Statistics: Total count, average margin, potential revenue impact
- Detailed table with all low-margin products
- Stock quantities to assess inventory impact
- Direct links to edit costing

### Negative Margin Products View (`/inventory/product-costing/negative-margin`)

Shows products being sold at a loss:
- Critical alert indicating money loss
- Statistics: Total count, average margin, total loss value
- Detailed table highlighting loss per unit and total loss
- Recommendations for corrective actions
- Direct links to fix costing

---

## Database Structure

### Products Table - Costing Fields

```sql
-- Cost Components
raw_material_cost         DECIMAL(10,2)  -- Raw materials per unit
labor_cost               DECIMAL(10,2)  -- Direct labor per unit
overhead_cost            DECIMAL(10,2)  -- Overhead/indirect costs
manufacturing_cost       DECIMAL(10,2)  -- Calculated: raw + labor + overhead
shipping_cost_per_unit   DECIMAL(10,2)  -- Shipping/freight per unit
tax_amount_per_unit      DECIMAL(10,2)  -- Tax per unit
handling_cost            DECIMAL(10,2)  -- Handling and packaging
total_cost               DECIMAL(10,2)  -- Calculated: all costs combined

-- Profit Analysis
profit_margin            DECIMAL(10,2)  -- Percentage
profit_amount            DECIMAL(10,2)  -- Price - Total Cost

-- Metadata
cost_calculation_method  VARCHAR        -- standard, average, fifo, lifo
last_cost_update         TIMESTAMP      -- Last update time
cost_notes               TEXT           -- Additional notes
```

---

## Backend Components

### Controller
**File:** `app/Http/Controllers/ProductCostingController.php`

**Methods:**
- `index()` - Dashboard with filters and search
- `edit($product)` - Edit form with breakdown
- `update($product)` - Update costing data
- `bulkUpdate()` - Bulk update multiple products
- `lowMargin()` - Low margin products list
- `negativeMargin()` - Negative margin products list
- `suggestPrice($product)` - AJAX price suggestion
- `costBreakdown($product)` - AJAX cost breakdown
- `analytics()` - AJAX analytics data

### Service
**File:** `app/Services/ProductCostingService.php`

**Key Methods:**
- `calculateTotalCost($product, $costData)` - Calculate all costs
- `updateProductCosting($product, $costData)` - Update product
- `bulkUpdateCosting($productsData)` - Bulk operations
- `suggestOptimalPrice($product, $desiredMargin)` - Price suggestions
- `getCostBreakdown($product)` - Component breakdown
- `getCostingStatistics()` - Overall statistics
- `getLowMarginProducts($threshold)` - Find low margin items
- `getNegativeMarginProducts()` - Find negative margin items

---

## Routes

```php
// Product Costing Routes Group
Route::prefix('inventory/product-costing')->name('inventory.product-costing.')->group(function () {
    // Main views
    Route::get('/', 'index')->name('index');
    Route::get('/{product}/edit', 'edit')->name('edit');
    Route::put('/{product}', 'update')->name('update');
    
    // Bulk operations
    Route::post('/bulk-update', 'bulkUpdate')->name('bulk-update');
    
    // Special views
    Route::get('/low-margin', 'lowMargin')->name('low-margin');
    Route::get('/negative-margin', 'negativeMargin')->name('negative-margin');
    
    // AJAX endpoints
    Route::get('/{product}/suggest-price', 'suggestPrice')->name('suggest-price');
    Route::get('/{product}/cost-breakdown', 'costBreakdown')->name('cost-breakdown');
    Route::get('/analytics', 'analytics')->name('analytics');
});
```

---

## Calculation Logic

### Manufacturing Cost
```
Manufacturing Cost = Raw Material Cost + Labor Cost + Overhead Cost
```

### Total Cost
```
Total Cost = Manufacturing Cost + Shipping Cost + Tax Amount + Handling Cost
```

### Profit Amount
```
Profit Amount = Selling Price - Total Cost
```

### Profit Margin %
```
Profit Margin % = (Profit Amount / Total Cost) × 100
```

### Suggested Price (for desired margin)
```
Suggested Price = Total Cost × (1 + Desired Margin / 100)
```

---

## Usage Examples

### Example 1: Setting Product Costing

**Scenario:** You have a shoe product that needs costing

**Steps:**
1. Navigate to **Inventory → Product Costing**
2. Find the product and click **"Edit Costing"**
3. Enter costs:
   - Raw Material Cost: ₱500.00
   - Labor Cost: ₱150.00
   - Overhead Cost: ₱100.00
   - Shipping Cost: ₱50.00
   - Tax Amount: ₱30.00
   - Handling Cost: ₱20.00
4. Set Selling Price: ₱1,200.00
5. Click **"Update Costing"**

**Results:**
- Manufacturing Cost: ₱750.00 (auto-calculated)
- Total Cost: ₱850.00 (auto-calculated)
- Profit Amount: ₱350.00 (auto-calculated)
- Profit Margin: 41.18% (auto-calculated)

### Example 2: Identifying Problem Products

**Scenario:** Find products with negative margins

**Steps:**
1. Navigate to **Inventory → Product Costing**
2. Click **"Negative Margin Products"** link or filter
3. Review the list of products losing money
4. Click **"Fix Costing"** on each product
5. Either:
   - Increase the selling price, OR
   - Reduce cost components

### Example 3: Using Price Suggestions

**Scenario:** You want a 30% profit margin

**Steps:**
1. Edit a product's costing
2. Enter all cost components
3. Look at the **"Pricing Suggestion"** section
4. System shows suggested price for 30% margin
5. Use suggested price or adjust as needed

---

## Best Practices

### 1. **Regular Updates**
- Update costing data when supplier prices change
- Review quarterly for accuracy
- Adjust for seasonal variations

### 2. **Cost Component Accuracy**
- Include all actual costs
- Don't underestimate overhead
- Factor in shipping variations
- Account for taxes accurately

### 3. **Profit Margin Targets**
- Set minimum acceptable margins (e.g., 20%)
- Review low-margin products monthly
- Act quickly on negative margins

### 4. **Documentation**
- Use Cost Notes field for:
  - Special pricing considerations
  - Seasonal adjustments
  - Supplier information
  - Last update reasons

### 5. **Analysis**
- Monitor average profit margin trends
- Compare categories
- Identify improvement opportunities
- Track costing completion percentage

---

## Troubleshooting

### Problem: Negative Profit Margin

**Possible Causes:**
1. Selling price is too low
2. Costs are too high
3. Cost data is incorrect

**Solutions:**
1. Increase selling price to cover costs
2. Negotiate better supplier rates
3. Optimize overhead allocation
4. Review and verify all cost entries

### Problem: Low Margin Products

**Possible Causes:**
1. Competitive pricing pressure
2. High cost structure
3. Outdated costing data

**Solutions:**
1. Analyze competitor pricing
2. Look for cost reduction opportunities
3. Update costing data regularly
4. Consider product bundling

### Problem: Missing Costing Data

**Possible Causes:**
1. New products not yet costed
2. Import didn't include costs
3. Manual entry oversight

**Solutions:**
1. Filter by "Without Costing"
2. Update products systematically
3. Set up costing process for new products
4. Train staff on importance

---

## Keyboard Shortcuts & Tips

### Navigation
- Use search to quickly find products
- Use filters to segment products
- Sort by margin to prioritize updates

### Data Entry
- Tab through form fields efficiently
- Watch live calculations update
- Use price suggestions as starting points

---

## Reports Available

### Dashboard Statistics
- Total products count
- Products with/without costing
- Average profit margin
- Low margin count
- Negative margin count
- Costing completion percentage

### Low Margin Report
- Total count
- Average margin
- Potential revenue impact
- Individual product details

### Negative Margin Report
- Total count
- Average margin (negative)
- Total loss value
- Loss per unit
- Total loss with stock

---

## Integration Points

### Connected Modules
- **Inventory Management** - Product data source
- **Sales Module** - Uses pricing data
- **Purchase Orders** - Cost data input
- **Reports** - Profitability analysis

### Data Flow
1. Products created in Inventory
2. Costs entered in Product Costing
3. Prices used in Sales
4. Profit analyzed in Reports

---

## Security & Permissions

The Product Costing module respects your system's role-based access control:
- View access for reviewing costing data
- Edit access for updating costs
- Admin access for bulk operations

---

## Future Enhancements (Potential)

- Historical cost tracking
- Cost trend analysis
- Multi-currency support
- Automated cost updates from purchase orders
- Competitor price comparison
- Bulk import/export of costing data
- Cost variance alerts
- Budget vs actual analysis

---

## Support & Assistance

If you need help with the Product Costing Module:
1. Review this documentation
2. Check the in-app tooltips
3. Contact your system administrator
4. Refer to backend code comments

---

## Technical Notes

### Performance Considerations
- Costing calculations are done server-side
- Live calculations in UI use JavaScript
- Database indexes on profit_margin and total_cost
- Pagination for large product lists

### Data Validation
- All cost fields accept decimal values (10,2 precision)
- Minimum value: 0
- Maximum value: 99,999,999.99
- Price field is required
- Cost fields are optional (default to 0)

---

## Summary

The **Product Costing Module** provides a complete solution for:
✅ Tracking all product cost components
✅ Calculating profit margins automatically
✅ Identifying problematic products
✅ Supporting pricing decisions
✅ Analyzing profitability

**Access it now:**
1. Open your sidebar menu
2. Go to Inventory → Product Costing
3. Start managing your product costs!

---

**Last Updated:** November 13, 2025
**Version:** 1.0
**Status:** Production Ready ✅
