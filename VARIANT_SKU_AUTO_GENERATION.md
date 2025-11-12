# Variant SKU Auto-Generation Feature

## Overview
The ProductVariant model now automatically generates SKUs based on the parent product SKU and variant attributes when a manual SKU is not provided.

## How It Works

### Auto-Generation Rules
1. **If `variant_sku` is NOT provided** → Automatically generate SKU
2. **If `variant_sku` IS provided** → Use the manual SKU (preserved)
3. **Pattern**: `{PARENT_SKU}-{COLOR_CODE}-{SIZE}-{MATERIAL_CODE}`

### SKU Components

#### 1. Parent SKU
- Taken from the parent product's SKU
- Example: `BOOTS-DEMO-001`

#### 2. Color Code
- First 3 letters of color, uppercase
- Examples:
  - `Black` → `BLA`
  - `Red` → `RED`
  - `Burgundy` → `BUR`
  - `Tan` → `TAN`

#### 3. Size
- Exact size value as provided
- Examples: `7`, `8`, `9`, `10`, `11`

#### 4. Material Code
- Common materials have predefined abbreviations
- Multi-word materials use first letter of each word
- Single-word materials use first 3-4 letters

##### Material Abbreviations
| Material | Code |
|----------|------|
| Leather | LTH |
| Nappa | NAPPA |
| Suede | SDE |
| Canvas | CNV |
| Rubber | RBR |
| Synthetic | SYN |
| Mesh | MSH |
| Patent (Leather) | PAT |
| Premium Leather | PL |
| Metallic Finish | MF |

## Examples

### Example 1: Full Auto-Generation
```php
ProductVariant::create([
    'parent_product_id' => 1,
    'variant_name' => 'Burgundy Size 11',
    // NO variant_sku provided
    'color' => 'Burgundy',
    'size' => '11',
    'material' => 'Premium Leather',
]);

// Auto-generated SKU: BOOTS-DEMO-001-BUR-11-LTH
```

### Example 2: Manual SKU
```php
ProductVariant::create([
    'parent_product_id' => 1,
    'variant_name' => 'Special Edition',
    'variant_sku' => 'MY-CUSTOM-SKU-001', // Manual SKU
    'color' => 'Gold',
    'size' => '9',
    'material' => 'Metallic Finish',
]);

// SKU: MY-CUSTOM-SKU-001 (manual SKU preserved)
```

### Example 3: Partial Attributes
```php
ProductVariant::create([
    'parent_product_id' => 1,
    'variant_name' => 'Brown variant',
    'color' => 'Brown',
    // No size or material
]);

// Auto-generated SKU: BOOTS-DEMO-001-BRO
```

### Example 4: Minimal (Fallback)
```php
ProductVariant::create([
    'parent_product_id' => 1,
    'variant_name' => 'Generic variant',
    // No color, size, or material
]);

// Auto-generated SKU: BOOTS-DEMO-001
```

## Implementation Details

### Boot Method
The `ProductVariant` model uses Laravel's boot method to hook into the model lifecycle:

```php
protected static function boot()
{
    parent::boot();
    
    static::creating(function ($variant) {
        // Auto-generate variant_sku if not provided
        if (empty($variant->variant_sku)) {
            $variant->variant_sku = $variant->generateVariantSku();
        }
    });
    
    static::updating(function ($variant) {
        // Regenerate SKU if parent or key attributes changed
        if ($variant->isDirty(['parent_product_id', 'color', 'size', 'material']) 
            && empty($variant->variant_sku)) {
            $variant->variant_sku = $variant->generateVariantSku();
        }
    });
}
```

### Generation Method
```php
public function generateVariantSku(): string
{
    $parentProduct = $this->parentProduct ?? Product::find($this->parent_product_id);
    
    if (!$parentProduct || empty($parentProduct->sku)) {
        return 'VAR-' . strtoupper(uniqid());
    }
    
    $parts = [$parentProduct->sku];
    
    if (!empty($this->color)) {
        $parts[] = strtoupper(substr($this->color, 0, 3));
    }
    
    if (!empty($this->size)) {
        $parts[] = $this->size;
    }
    
    if (!empty($this->material)) {
        $parts[] = $this->abbreviateMaterial($this->material);
    }
    
    return implode('-', $parts);
}
```

## Benefits

### 1. Consistency
- All variant SKUs follow the same pattern
- Easy to identify parent product and variant attributes from SKU

### 2. Automatic
- No need to manually create SKUs for each variant
- Reduces human error and saves time

### 3. Flexible
- Can still provide manual SKUs when needed
- Handles partial attributes gracefully

### 4. Scalable
- Works with any number of variants
- Material abbreviation system is extensible

## Testing

Run the demonstration test:
```bash
php test_variant_sku_auto.php
```

Expected output shows:
- Auto-generated SKU: `BOOTS-DEMO-001-BUR-11-LTH`
- Manual SKU preserved: `MY-CUSTOM-SKU-001`
- Material abbreviations working correctly

## Real-World Usage

### Creating Variants in UI
When users create variants through the web interface, they can:
1. Leave SKU field empty → Auto-generated
2. Fill SKU field → Manual SKU used

### Bulk Import
When importing variants via CSV/Excel:
- If SKU column is empty → Auto-generate
- If SKU column has value → Use provided SKU

### API Integration
```php
// POST /api/products/1/variants
{
    "variant_name": "Black Size 7",
    "color": "Black",
    "size": "7",
    "material": "Nappa"
    // variant_sku automatically generated: BOOTS-DEMO-001-BLA-7-NAPPA
}
```

## Files Modified

- **app/Models/ProductVariant.php**
  - Added `boot()` method for auto-generation hooks
  - Added `generateVariantSku()` method
  - Added `abbreviateMaterial()` helper method

## Test Files

- **test_variant_sku_auto.php** - Demonstration of auto-generation feature
- **test_variant_sku_generation.php** - Comprehensive test suite

---

**Status**: ✅ **IMPLEMENTED AND TESTED**  
**Date**: November 12, 2025  
**Feature**: Variant SKU Auto-Generation
