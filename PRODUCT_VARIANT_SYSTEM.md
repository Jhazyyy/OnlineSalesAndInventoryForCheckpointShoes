# Product Variant System Implementation

## Overview

This document describes the implementation of a **Parent-Child Product Variant System** that allows products to have multiple variations based on attributes like color, size, and material.

**Implementation Date:** November 12, 2025  
**Status:** ✅ Complete - Database & Models Ready

---

## Use Case Example

**Scenario:** LEATHER CHELSEA BOOTS SLIP ON

Instead of creating separate unrelated products for each combination, you now have:

- **Parent Product:** "LEATHER CHELSEA BOOTS SLIP ON" (SKU: BOOTS-001)
  - **Variant 1:** Black, Size 7, Black Nappa (SKU: BOOTS-001-BLK-7-NAPPA)
  - **Variant 2:** Tan, Size 7, Tan Nappa (SKU: BOOTS-001-TAN-7-NAPPA)
  - **Variant 3:** Black, Size 8, Black Nappa (SKU: BOOTS-001-BLK-8-NAPPA)
  - **Variant 4:** Tan, Size 8, Tan Nappa (SKU: BOOTS-001-TAN-8-NAPPA)

Each variant:
- Has its own unique SKU
- Tracks its own inventory quantity
- Can have price adjustments (+/- from parent price)
- Has its own barcode and image
- Can be individually activated/deactivated

---

## Database Schema

### 1. `product_variants` Table (NEW)

```sql
CREATE TABLE product_variants (
    variant_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    parent_product_id BIGINT NOT NULL,  -- References products.product_id
    variant_sku VARCHAR(255) UNIQUE,     -- Unique SKU for variant
    variant_name VARCHAR(255),           -- Display name
    
    -- Variant Attributes
    color VARCHAR(255) NULL,             -- e.g., "Black", "Tan"
    size VARCHAR(255) NULL,              -- e.g., "7", "8", "M", "L"
    material VARCHAR(255) NULL,          -- e.g., "Nappa", "Suede"
    additional_attributes JSON NULL,     -- Custom attributes
    
    -- Pricing & Inventory
    price_adjustment DECIMAL(10,2) DEFAULT 0,  -- +/- from parent price
    barcode VARCHAR(255) NULL,
    image VARCHAR(255) NULL,
    quantity INT DEFAULT 0,
    
    -- Thresholds
    reorder_level INT NULL,
    critical_level INT NULL,
    
    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT NULL,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_parent (parent_product_id),
    INDEX idx_parent_color_size (parent_product_id, color, size),
    INDEX idx_parent_active (parent_product_id, is_active)
);
```

### 2. `products` Table Updates

Added two new columns:

```sql
ALTER TABLE products ADD COLUMN (
    is_parent BOOLEAN DEFAULT FALSE,      -- True if product has variants
    has_variants BOOLEAN DEFAULT FALSE,   -- Indicates child variants exist
    
    INDEX idx_variant_support (is_parent, has_variants)
);
```

---

## Model Architecture

### 1. ProductVariant Model

**Location:** `app/Models/ProductVariant.php`

**Key Features:**

```php
class ProductVariant extends Model
{
    use HasFactory, ValidatesForeignKeys;

    protected $primaryKey = 'variant_id';
    
    protected $fillable = [
        'parent_product_id', 'variant_sku', 'variant_name',
        'color', 'size', 'material', 'additional_attributes',
        'price_adjustment', 'barcode', 'image', 'quantity',
        'reorder_level', 'critical_level', 'is_active', 'notes'
    ];
    
    protected $casts = [
        'additional_attributes' => 'array',
        'price_adjustment' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    // Foreign key validation (application-level)
    protected $foreignKeys = [
        'parent_product_id' => [
            'table' => 'products',
            'column' => 'product_id',
            'message' => 'The selected parent product does not exist.',
        ],
    ];
}
```

**Relationships:**

```php
// Belongs to parent product
public function parentProduct(): BelongsTo
{
    return $this->belongsTo(Product::class, 'parent_product_id', 'product_id');
}

// Sales order items
public function salesOrderItems(): HasMany
{
    return $this->hasMany(SalesOrderItem::class, 'variant_id', 'variant_id');
}

// Stock movements
public function stockMovements(): HasMany
{
    return $this->hasMany(StockMovement::class, 'variant_id', 'variant_id');
}
```

**Computed Attributes:**

```php
// Get full price (parent price + adjustment)
$variant->full_price;  // $100 + $10 = $110

// Get display name with attributes
$variant->display_name;  // "Chelsea Boot (Black, Size 7, Nappa)"
```

**Helper Methods:**

```php
// Check stock levels
$variant->isLowStock();      // quantity <= reorder_level
$variant->isCriticalStock(); // quantity <= critical_level
```

**Query Scopes:**

```php
ProductVariant::active()->get();                // Active variants only
ProductVariant::byColor('Black')->get();        // Filter by color
ProductVariant::bySize('7')->get();             // Filter by size
ProductVariant::lowStock()->get();              // Low stock variants
ProductVariant::criticalStock()->get();         // Critical stock variants
```

---

### 2. Product Model Updates

**Location:** `app/Models/Product.php`

**New Fillable Fields:**

```php
protected $fillable = [
    // ... existing fields
    'is_parent',
    'has_variants',
];
```

**New Relationships:**

```php
// Get all variants for parent product
public function variants(): HasMany
{
    return $this->hasMany(ProductVariant::class, 'parent_product_id', 'product_id');
}

// Get only active variants
public function activeVariants(): HasMany
{
    return $this->variants()->where('is_active', true);
}
```

**New Methods:**

```php
// Check if product has variants
$product->hasVariants();  // Returns bool

// Get total quantity across all variants
$product->total_variant_quantity;  // Sum of all variant quantities

// Create a new variant
$variant = $product->createVariant([
    'variant_sku' => 'BOOTS-001-BLK-7',
    'variant_name' => 'Black Size 7',
    'color' => 'Black',
    'size' => '7',
    'material' => 'Nappa',
    'quantity' => 50,
    'price_adjustment' => 0,
]);

// Get available variant options
$product->variant_options;
// Returns: [
//     'colors' => ['Black', 'Tan', 'Brown'],
//     'sizes' => ['7', '8', '9', '10'],
//     'materials' => ['Nappa', 'Suede']
// ]
```

---

## Usage Examples

### Creating a Parent Product with Variants

```php
// Step 1: Create parent product
$parentProduct = Product::create([
    'product_name' => 'LEATHER CHELSEA BOOTS SLIP ON',
    'sku' => 'BOOTS-001',
    'product_brand' => 'Premium Footwear',
    'product_category' => 'Boots',
    'price' => 5000.00,  // Base price
    'description' => 'Premium leather chelsea boots',
    'is_parent' => true,
    'has_variants' => true,
]);

// Step 2: Create variants
$variants = [
    [
        'variant_sku' => 'BOOTS-001-BLK-7-NAPPA',
        'variant_name' => 'Black Size 7 Nappa',
        'color' => 'Black',
        'size' => '7',
        'material' => 'Black Nappa',
        'quantity' => 25,
        'price_adjustment' => 0,
    ],
    [
        'variant_sku' => 'BOOTS-001-TAN-7-NAPPA',
        'variant_name' => 'Tan Size 7 Nappa',
        'color' => 'Tan',
        'size' => '7',
        'material' => 'Tan Nappa',
        'quantity' => 20,
        'price_adjustment' => 200.00,  // ₱200 more expensive
    ],
    [
        'variant_sku' => 'BOOTS-001-BLK-8-NAPPA',
        'variant_name' => 'Black Size 8 Nappa',
        'color' => 'Black',
        'size' => '8',
        'material' => 'Black Nappa',
        'quantity' => 30,
        'price_adjustment' => 0,
    ],
];

foreach ($variants as $variantData) {
    $parentProduct->createVariant($variantData);
}
```

### Querying Variants

```php
// Get all variants for a product
$product = Product::find(1);
$allVariants = $product->variants;

// Get only active variants
$activeVariants = $product->activeVariants;

// Get specific variant
$blackSize7 = ProductVariant::where('parent_product_id', $product->product_id)
    ->where('color', 'Black')
    ->where('size', '7')
    ->first();

// Get available colors for product
$colors = $product->variant_options['colors'];

// Find low stock variants
$lowStockVariants = $product->variants()->lowStock()->get();
```

### Using Variants in Sales Orders

```php
// When creating sales order, reference variant instead of product
SalesOrderItem::create([
    'order_id' => $order->order_id,
    'variant_id' => $variant->variant_id,  // Use variant
    'quantity' => 2,
    'unit_price' => $variant->full_price,  // Parent price + adjustment
]);

// Stock will be deducted from variant's quantity
$variant->decrement('quantity', 2);
```

---

## Migration Files

### 1. Create Product Variants Table

**File:** `database/migrations/2025_11_12_105806_create_product_variants_table.php`

**Status:** ✅ Migrated

**What it does:**
- Creates `product_variants` table
- Sets up indexes for performance
- No foreign key constraints (application-level validation)

### 2. Add Variant Support to Products

**File:** `database/migrations/2025_11_12_105854_add_variant_support_to_products_table.php`

**Status:** ✅ Migrated

**What it does:**
- Adds `is_parent` column to products
- Adds `has_variants` column to products
- Creates index for variant queries

---

## Application-Level Validation

The system uses the `ValidatesForeignKeys` trait instead of database foreign keys:

```php
// In ProductVariant model
protected $foreignKeys = [
    'parent_product_id' => [
        'table' => 'products',
        'column' => 'product_id',
        'message' => 'The selected parent product does not exist.',
    ],
];
```

**Benefits:**
- No migration dependency issues
- Flexible validation rules
- Custom error messages
- Can handle nullable relationships

---

## Next Steps (UI Implementation)

To complete the variant system, you'll need:

### 1. Product Variant Management Views

**Location:** `resources/views/master_data/products/variants/`

**Required Views:**
- `index.blade.php` - List all variants for a product
- `create.blade.php` - Create new variant form
- `edit.blade.php` - Edit variant form
- `_variant_row.blade.php` - Reusable variant table row

### 2. Controller Methods

**Location:** `app/Http/Controllers/ProductVariantController.php`

**Required Methods:**
```php
public function index(Product $product)         // List variants
public function create(Product $product)        // Show create form
public function store(Request $request)         // Store new variant
public function edit(ProductVariant $variant)   // Show edit form
public function update(Request $request)        // Update variant
public function destroy(ProductVariant $variant) // Delete variant
```

### 3. Routes

**Location:** `routes/web.php`

```php
Route::prefix('products/{product}/variants')->group(function () {
    Route::get('/', [ProductVariantController::class, 'index'])->name('products.variants.index');
    Route::get('/create', [ProductVariantController::class, 'create'])->name('products.variants.create');
    Route::post('/', [ProductVariantController::class, 'store'])->name('products.variants.store');
    Route::get('/{variant}/edit', [ProductVariantController::class, 'edit'])->name('products.variants.edit');
    Route::put('/{variant}', [ProductVariantController::class, 'update'])->name('products.variants.update');
    Route::delete('/{variant}', [ProductVariantController::class, 'destroy'])->name('products.variants.destroy');
});
```

### 4. Update Sales Module

**Files to Update:**
- Sales order creation forms (to select variants)
- Inventory management (to show variant stock)
- Reports (to include variant data)

---

## Testing the System

### Via Tinker

```bash
php artisan tinker
```

```php
// Create parent product
$product = Product::create([
    'product_name' => 'Test Boots',
    'sku' => 'TEST-001',
    'price' => 1000,
    'is_parent' => true,
    'has_variants' => true
]);

// Create variant
$variant = $product->createVariant([
    'variant_sku' => 'TEST-001-BLK-7',
    'variant_name' => 'Black Size 7',
    'color' => 'Black',
    'size' => '7',
    'quantity' => 50,
    'price_adjustment' => 100
]);

// Test relationships
$product->variants;           // Should show the variant
$variant->parentProduct;      // Should show parent product
$variant->full_price;         // Should be 1100 (1000 + 100)
```

---

## Benefits of This Implementation

✅ **Organized Inventory:** Related products grouped under parent  
✅ **Individual SKUs:** Each variant has unique SKU for tracking  
✅ **Flexible Pricing:** Variants can have price adjustments  
✅ **Separate Stock:** Each variant tracks its own inventory  
✅ **Scalable:** Easy to add new attributes (e.g., width, style)  
✅ **Application-Level Validation:** No foreign key constraint issues  
✅ **Query Optimization:** Indexed for fast variant lookups  
✅ **Business Logic:** Built-in stock checking and price calculation

---

## Conclusion

The Product Variant System is now **fully implemented at the database and model level**. 

**What's Ready:**
- ✅ Database tables created
- ✅ ProductVariant model with relationships
- ✅ Product model updated with variant support
- ✅ Application-level validation
- ✅ Helper methods and scopes

**What's Next:**
- Create UI for managing variants
- Update sales order forms to support variant selection
- Add variant reporting features

You can now create products with multiple variants using different SKUs, colors, sizes, and materials! 🎉
