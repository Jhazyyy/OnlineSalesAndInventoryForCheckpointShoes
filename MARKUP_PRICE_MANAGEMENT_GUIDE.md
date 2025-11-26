# Markup Price Management System - Implementation Guide

## Overview
The Markup Price Management system allows administrators to manage product pricing through multiple methods: manual pricing, markup-based pricing, and costing-based pricing. This gives flexibility in how products are priced across the system.

## Features Implemented

### 1. Database Structure
**Migration**: `2025_11_26_113607_add_markup_price_to_products_table.php`

Added fields to `products` table:
- `markup_percentage` (decimal): The markup percentage to apply to product cost
- `markup_price` (decimal): Calculated selling price based on cost + markup
- `price_source` (enum): Determines pricing method - 'manual', 'markup', or 'costing'

### 2. Product Model Enhancements
**File**: `app/Models/Product.php`

Added methods:
- `calculateMarkupPrice()`: Calculates price from cost and markup percentage
- `getEffectivePrice()`: Returns the appropriate price based on price source
- `updateMarkupPrice()`: Updates markup price when percentage or cost changes

Added to fillable fields:
- `markup_percentage`
- `markup_price`
- `price_source`

### 3. Markup Price Controller
**File**: `app/Http/Controllers/MarkupPriceController.php`

Features:
- **index()**: Display markup price management interface with filters
- **update()**: Update individual product markup settings
- **bulkUpdate()**: Update multiple products at once
- **preview()**: Calculate preview of price changes before applying

### 4. Master Data Views
**File**: `resources/views/master_data/markup_prices/index.blade.php`

Features:
- Product listing with current pricing information
- Filter by brand, category, price source, and markup status
- Individual product editing with live preview
- Bulk update functionality for selected products
- Visual indicators for price sources (manual/markup/costing)

### 5. Routes
**File**: `routes/web.php`

Added routes under `master_data.markup_prices` namespace:
- GET `/master_data/markup_prices` - Main management page
- PUT `/master_data/markup_prices/{product}` - Update single product
- POST `/master_data/markup_prices/bulk-update` - Bulk update
- GET `/master_data/markup_prices/{product}/preview` - Price preview

### 6. Navigation Integration
**File**: `app/Helpers/NavigationHelper.php`

Added breadcrumb support for Markup Prices under Master Data section.

### 7. Product Edit Form Enhancement
**File**: `resources/views/inventory/products/edit.blade.php`

Updated to display:
- Markup price information when available
- Link to markup price management
- Current markup percentage

### 8. POS Integration
**File**: `app/Http/Controllers/POSController.php`

Modified to use `getEffectivePrice()` method, ensuring POS uses the correct price based on the configured price source.

### 9. Product Update Integration
**File**: `app/Http/Controllers/ProductController.php`

Added automatic markup price recalculation when products are updated and price source is set to 'markup'.

## Price Source Options

### 1. Manual (Default)
- Administrator manually sets the selling price
- No automatic calculations
- Full control over pricing

### 2. Markup
- Price calculated as: `Cost × (1 + Markup%/100)`
- Automatically updates when cost or markup percentage changes
- Example: Cost ₱100 + 50% markup = ₱150 selling price

### 3. Costing
- Price calculated from product costing with profit margin
- Uses existing `total_cost` and `profit_margin` fields
- Formula: `Cost × (1 + Profit Margin%/100)`

## Usage Guide

### Setting Up Markup Prices

1. **Navigate to Master Data → Markup Prices**
2. **Filter products** (optional) by brand, category, or price source
3. **Click "Edit"** on a product to configure pricing

### Individual Product Configuration

1. **Select Price Source**:
   - Manual: Enter price directly
   - Markup: Set markup percentage
   - Costing: Use existing profit margin

2. **Preview Changes**:
   - System shows calculated new price
   - Displays price change and percentage difference

3. **Save Changes**:
   - Updates take effect immediately
   - Applies to all sales channels (POS, online, etc.)

### Bulk Update

1. **Select Products**: Check boxes for products to update
2. **Click "Bulk Update"**
3. **Configure Settings**:
   - Choose price source
   - Set markup percentage (if using markup)
4. **Apply Changes**: Updates all selected products

### Price Calculation Examples

#### Markup Pricing
```
Product Cost: ₱1,000
Markup: 50%
Calculation: ₱1,000 × (1 + 50/100) = ₱1,500
Selling Price: ₱1,500
```

#### Costing Pricing
```
Total Cost: ₱1,200
Profit Margin: 30%
Calculation: ₱1,200 × (1 + 30/100) = ₱1,560
Selling Price: ₱1,560
```

## Integration Points

### POS System
- Uses `getEffectivePrice()` to get correct price
- Honors price source settings
- Displays effective price to cashier

### Product Management
- Shows markup information in product edit form
- Link to markup management from product pages
- Automatic recalculation on updates

### Sales Orders
- Uses effective price for all order types
- Maintains pricing consistency across channels

### Reporting
- Can filter by price source
- Track products using different pricing methods
- Monitor markup effectiveness

## API Methods

### Product Model
```php
// Get the effective selling price based on price source
$product->getEffectivePrice();

// Calculate markup price from cost and percentage
$product->calculateMarkupPrice();

// Update markup price when values change
$product->updateMarkupPrice();
```

### Controller Endpoints
```php
// Get markup price management page
GET /master_data/markup_prices

// Update single product
PUT /master_data/markup_prices/{product}
Body: {
    "price_source": "markup",
    "markup_percentage": 50,
    "manual_price": 1500
}

// Bulk update products
POST /master_data/markup_prices/bulk-update
Body: {
    "product_ids": [1, 2, 3],
    "price_source": "markup",
    "markup_percentage": 50
}

// Preview price calculation
GET /master_data/markup_prices/{product}/preview?markup_percentage=50&price_source=markup
```

## Best Practices

### 1. Cost Management
- Keep product costs up to date
- Review costs regularly for accuracy
- Use product costing feature for detailed cost tracking

### 2. Markup Strategy
- Set consistent markups by category
- Review competitive pricing regularly
- Adjust markups based on product movement

### 3. Price Source Selection
- **Manual**: For special pricing, promotions, or unique products
- **Markup**: For standard products with consistent margins
- **Costing**: For products with detailed cost tracking

### 4. Bulk Updates
- Test on a few products first
- Review preview before applying
- Document pricing changes

### 5. Regular Reviews
- Monitor products without markup
- Check for pricing inconsistencies
- Update costs and markups quarterly

## Troubleshooting

### Issue: Markup price not calculating
**Solution**: Ensure product has `total_cost` set. Configure costing first if needed.

### Issue: Price not updating in POS
**Solution**: Check that `price_source` is properly set. Verify `getEffectivePrice()` is being used.

### Issue: Bulk update not working
**Solution**: Ensure products are selected. Check that products have required cost data.

### Issue: Preview showing incorrect values
**Solution**: Verify product cost is set. Check markup percentage input.

## Database Migration

To run the migration:
```bash
php artisan migrate
```

To rollback:
```bash
php artisan migrate:rollback
```

## Permissions

Access to Markup Price Management requires:
- Role: Super Admin or Admin
- Route middleware: `role:super_admin,admin`

## Future Enhancements

Potential improvements:
1. **Category-level markup defaults**: Set default markups by category
2. **Markup templates**: Save and apply markup profiles
3. **Price history tracking**: Track price changes over time
4. **Competitive pricing**: Compare with market rates
5. **Dynamic pricing**: Time-based or inventory-based pricing
6. **Multi-currency support**: Handle different currencies
7. **Wholesale pricing**: Separate pricing tiers
8. **Scheduled price changes**: Plan future price updates

## Related Files

### Core Implementation
- `app/Models/Product.php`
- `app/Http/Controllers/MarkupPriceController.php`
- `database/migrations/2025_11_26_113607_add_markup_price_to_products_table.php`

### Views
- `resources/views/master_data/markup_prices/index.blade.php`
- `resources/views/inventory/products/edit.blade.php`

### Integration
- `app/Http/Controllers/POSController.php`
- `app/Http/Controllers/ProductController.php`
- `app/Helpers/NavigationHelper.php`

### Routes
- `routes/web.php`

## Support

For issues or questions:
1. Check this documentation
2. Review product costing documentation
3. Verify database migrations are up to date
4. Check Laravel logs for errors
5. Contact system administrator

---

**Last Updated**: November 26, 2025
**Version**: 1.0.0
**Status**: Production Ready
