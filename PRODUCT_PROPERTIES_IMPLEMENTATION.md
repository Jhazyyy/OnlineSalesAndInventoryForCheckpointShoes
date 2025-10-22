# Product Properties Implementation

## Overview
The product quantity system has been refactored to use **Product Properties** (variants) as the primary inventory tracking mechanism. The `quantity` field on the `products` table is now a **reference value only** and is used when a product doesn't have any properties/variants.

## Changes Made

### 1. Database Structure

#### New Table: `product_properties`
- **property_id** - Primary key
- **product_id** - Foreign key to products table
- **property_name** - Variant attribute name (e.g., "Size", "Color", "Style")
- **property_value** - Variant attribute value (e.g., "42", "Red", "Casual")
- **quantity** - Actual inventory quantity for this specific variant
- **sku** - Stock Keeping Unit (unique identifier for the variant)
- **price_adjustment** - Price difference from base product price (+/-)
- **is_active** - Whether this variant is active
- **timestamps** - Created at, Updated at

#### Products Table
- The `quantity` field remains in the table but is now:
  - **Optional** (no longer required)
  - Used as a **reference value only**
  - Only used when products have no properties/variants
  - Defaults to 0 for new products

### 2. Models

#### ProductProperty Model (`app/Models/ProductProperty.php`)
New model with the following features:
- **Relationships**: Belongs to Product
- **Computed Attributes**:
  - `final_price` - Base product price + price_adjustment
  - `display_name` - Formatted property name and value
- **Methods**:
  - `isInStock($quantity)` - Check variant stock availability
  - `decreaseStock($quantity)` - Reduce variant stock
  - `increaseStock($quantity)` - Increase variant stock

#### Product Model Updates (`app/Models/Product.php`)
- **New Relationship**: `properties()` - HasMany relationship to ProductProperty
- **Computed Attributes**:
  - `actual_quantity` - Sum of all active property quantities
  - `display_quantity` - Returns actual_quantity if properties exist, otherwise reference quantity
- **Updated Methods**:
  - `hasProperties()` - Check if product has variants
  - `isInStock()` - Now checks actual_quantity from properties if they exist

### 3. Controllers

#### ProductController Updates (`app/Http/Controllers/ProductController.php`)
- **store() method**:
  - Quantity validation changed to nullable
  - Added validation for properties array
  - Creates product properties after product creation
  - Sets default quantity to 0 if not provided

### 4. Views

#### Product Create Form (`resources/views/master_data/products/create.blade.php`)
- **Quantity Field**:
  - Changed label to "Reference Quantity"
  - Added helper text explaining it's optional
  - Made non-required
  - Shows note that actual inventory is tracked in properties

- **New Product Properties Section**:
  - Dynamic form to add multiple property rows
  - Each row includes:
    - Property Name (e.g., Size, Color)
    - Property Value (e.g., 42, Red)
    - Quantity (actual inventory)
    - SKU (optional unique identifier)
    - Price Adjustment (+/- from base price)
  - Add/Remove buttons for managing property rows

- **JavaScript Functions**:
  - `addPropertyRow()` - Dynamically add new property input row
  - `removePropertyRow()` - Remove a property row

## Usage Examples

### Creating a Product with Properties

```php
// Create a shoe product with size variants
$product = Product::create([
    'product_name' => 'Running Shoes Pro',
    'product_brand' => 'Nike',
    'product_category' => 'Footwear',
    'price' => 5000.00,
    'quantity' => 0, // Reference only
]);

// Add size variants
$product->properties()->create([
    'property_name' => 'Size',
    'property_value' => '40',
    'quantity' => 10,
    'sku' => 'NIKE-RS-40',
    'price_adjustment' => 0,
]);

$product->properties()->create([
    'property_name' => 'Size',
    'property_value' => '42',
    'quantity' => 15,
    'sku' => 'NIKE-RS-42',
    'price_adjustment' => 0,
]);

// Get actual total quantity across all variants
$totalQuantity = $product->actual_quantity; // Returns 25
```

### Creating a Simple Product (No Properties)

```php
// Create a product without variants
$product = Product::create([
    'product_name' => 'Basic T-Shirt',
    'product_brand' => 'Generic',
    'product_category' => 'Apparel',
    'price' => 299.00,
    'quantity' => 50, // Used as actual quantity since no properties exist
]);

// Check stock
$inStock = $product->isInStock(5); // Returns true
$displayQty = $product->display_quantity; // Returns 50
```

### Working with Product Properties

```php
// Find a specific property
$property = $product->properties()
    ->where('property_name', 'Size')
    ->where('property_value', '42')
    ->first();

// Check property stock
if ($property->isInStock(3)) {
    $property->decreaseStock(3);
}

// Get final price for this variant
$finalPrice = $property->final_price; // Base price + price_adjustment
```

## Migration Instructions

1. **Run the migration**:
   ```bash
   php artisan migrate
   ```

2. **Update existing products** (if needed):
   - Existing products will keep their quantity values as reference
   - Optionally create properties for products that need variants

3. **Future enhancements**:
   - Update stock movement tracking to work with properties
   - Update sales/purchase modules to select specific properties
   - Add property management UI in product edit form

## Benefits

1. **Flexible Inventory**: Support products with or without variants
2. **Accurate Tracking**: Each variant has its own inventory count
3. **Price Flexibility**: Variants can have price adjustments
4. **SKU Management**: Each variant can have unique SKU
5. **Backwards Compatible**: Products without properties still work with reference quantity

## Next Steps

- [ ] Update product edit form to manage properties
- [ ] Update sales order to select specific properties
- [ ] Update stock movements to track property-level changes
- [ ] Update reports to show property-level inventory
- [ ] Add bulk property import functionality
