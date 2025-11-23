# Product Movement Report - Implementation Guide

## Overview
The Product Movement Report is now fully functional and synced with your inventory system. It automatically analyzes products and categorizes them as Fast Moving, Slow Moving, or Non-Moving based on actual sales data.

## Features

### 1. **Automatic Movement Analysis**
- **Fast Moving Products**: ≥5 units sold per day
- **Slow Moving Products**: 1-5 units sold per day  
- **Non-Moving Products**: 0 sales in 30+ days

### 2. **Report Access**
Navigate to: **Reports → Product Movement Report**
- URL: `/reports/product-movement`

### 3. **Key Metrics Displayed**

#### Summary Cards
- **Fast Moving**: Products with high sales velocity
- **Slow Moving**: Products with moderate sales velocity
- **Non-Moving**: Products with no recent sales
- **Uncategorized**: Products not yet analyzed

#### Value Analysis
- Total quantity and stock value for each movement category
- Helps identify capital tied up in slow/non-moving inventory

#### Top Products Lists
- **Top 10 Fast Moving**: Your best-selling products
- **Products Needing Attention**: Slow/non-moving items with high stock value (>₱1,000)
- **Category Breakdown**: Movement distribution by product category

### 4. **Filters Available**

#### Analysis Period
- Last 30 Days
- Last 60 Days
- Last 90 Days (default)
- Last 6 Months
- Last Year

#### Movement Category
- All Categories
- Fast Moving
- Slow Moving
- Non-Moving

#### Product Category
- Filter by specific product categories

### 5. **Manual Calculation**
Click the **"Calculate Movement"** button to manually trigger analysis:
- Updates all products with current sales data
- Useful when you want fresh data immediately
- Shows how many products were categorized

### 6. **Automated Scheduling**
Movement calculations run automatically:
- **Daily at 2:00 AM**: Analyzes all products for the last 90 days
- Keeps data fresh without manual intervention

## How It Works

### Movement Velocity Calculation
```
Velocity = Total Sales Quantity / Number of Days Analyzed
```

### Categorization Logic
1. **Non-Moving**: 
   - No sales in 30+ days OR
   - Velocity = 0 units/day

2. **Fast Moving**:
   - Velocity ≥ 5 units/day

3. **Slow Moving**:
   - Velocity between 1-5 units/day

### Data Tracked per Product
- `movement_category`: fast, slow, or non-moving
- `movement_velocity`: Units sold per day
- `total_sales_quantity`: Total units sold in analysis period
- `days_since_last_sale`: Days since last confirmed sale
- `last_sale_date`: Date of most recent sale
- `last_movement_check`: When analysis was last performed

## Usage Scenarios

### 1. **Inventory Optimization**
Identify slow/non-moving products to:
- Mark for promotional pricing
- Reduce reorder quantities
- Avoid overstocking

### 2. **Stock Planning**
Use fast-moving product data to:
- Increase stock levels
- Ensure adequate safety stock
- Prioritize reorders

### 3. **Financial Analysis**
Review stock value by movement category:
- See capital tied up in non-moving inventory
- Identify opportunities to free up cash flow
- Make informed purchasing decisions

### 4. **Promotional Strategy**
The report highlights products needing attention:
- High stock value + slow/non-moving = promotion candidate
- Direct link to mark items as promotional
- Helps clear stagnant inventory

## Commands Available

### Calculate Movement for All Products
```bash
php artisan products:calculate-movements --days=90
```

Options:
- `--days=30`: Analyze last 30 days
- `--days=60`: Analyze last 60 days
- `--days=90`: Analyze last 90 days (default)
- `--days=180`: Analyze last 6 months
- `--days=365`: Analyze last year

### Check Schedule Status
```bash
php artisan schedule:list
```
Look for: `products:calculate-movements --days=90` (runs daily at 02:00)

### Run Scheduled Task Manually
```bash
php artisan schedule:run
```

## Integration with Other Modules

### 1. **Sales Module**
- Pulls data from `sales_orders` and `sales_order_items` tables
- Only counts confirmed, processing, shipped, and delivered orders
- Ignores pending, cancelled, and returned orders

### 2. **Inventory Module**
- Links to Product Movement Management (`/inventory/product-movement`)
- Shows current stock quantities
- Integrates with promotional product marking

### 3. **Stock Alerts**
- Works alongside the stock alert system
- Provides context for reorder decisions
- Helps distinguish between urgent and non-urgent low stock

## Report Columns Explained

### Top Fast Moving Products Table
- **Product**: Name and brand
- **Category**: Product category
- **Stock**: Current quantity in stock
- **Velocity**: Average units sold per day
- **Total Sales**: Total units sold in analysis period
- **Last Sale**: Date of most recent sale

### Products Needing Attention Table
- **Product**: Name and brand
- **Category**: Product category
- **Movement**: Current movement classification
- **Stock Qty**: Current quantity in stock
- **Stock Value**: Total value of stock (Qty × Cost)
- **Days Since Sale**: Days since last confirmed sale
- **Action**: Link to mark as promotional

## Troubleshooting

### No Products Showing?
1. Click "Calculate Movement" button to trigger analysis
2. Ensure you have sales data in the system
3. Check that sales orders have proper status (not all pending)

### All Products Show as Non-Moving?
- This is normal if you have limited sales history
- Sales must be in confirmed/processing/shipped/delivered status
- Pending orders don't count toward movement

### Uncategorized Products?
- Products haven't been analyzed yet
- Click "Calculate Movement" to categorize them
- New products are uncategorized until first analysis

### Wrong Movement Classification?
- Adjust the analysis period (30, 60, 90 days, etc.)
- Recent high sales might not show in longer periods
- Seasonal products may vary by period analyzed

## Performance Considerations

### For Large Inventories (1000+ products)
- Movement calculation takes 1-5 minutes
- Runs in background when scheduled
- Manual calculation button shows progress
- Database indexes on `product_id` and `order_date` optimize queries

### Database Columns Added
The following columns were added to the `products` table:
- `movement_category` (string)
- `movement_velocity` (decimal)
- `total_sales_quantity` (integer)
- `days_since_last_sale` (integer)
- `last_sale_date` (date)
- `movement_analysis_start_date` (date)
- `movement_analysis_end_date` (date)
- `last_movement_check` (timestamp)

## Best Practices

1. **Regular Analysis**: Let the scheduled task run daily for accurate data
2. **Period Selection**: Use 90 days for balanced view, 30 days for recent trends
3. **Action on Insights**: 
   - Review "Products Needing Attention" weekly
   - Adjust reorder points based on movement velocity
   - Mark slow/non-moving items for promotion
4. **Combined with Alerts**: Use alongside stock alert system for comprehensive inventory management

## Support

If you encounter issues:
1. Check the console for error messages
2. Verify database columns exist (`products` table)
3. Ensure sales orders have proper status values
4. Run `php artisan optimize` to clear caches

---

**Last Updated**: November 23, 2025
**Version**: 1.0
