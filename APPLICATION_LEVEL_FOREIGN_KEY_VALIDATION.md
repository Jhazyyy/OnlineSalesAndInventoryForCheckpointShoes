# Application-Level Foreign Key Validation

This document explains how we replaced database-level foreign key constraints with application-level validation to maintain data integrity while avoiding migration complexity.

## Overview

Instead of using database foreign key constraints, we use:
1. **Model-level validation** via the `ValidatesForeignKeys` trait
2. **Form request validation** via the `HasForeignKeyValidation` trait
3. **Eloquent relationships** for data access

## Benefits

✅ **No migration order dependencies** - Tables can be created in any order
✅ **Easier testing** - Can truncate/seed tables without constraint errors
✅ **Better error messages** - Application can provide user-friendly validation messages
✅ **Flexibility** - Easy to add/remove validations without database migrations
✅ **Same data integrity** - Validates before saving, preventing orphaned records

## Implementation

### 1. Model-Level Validation

Add the trait to any model with foreign keys:

```php
use App\Traits\ValidatesForeignKeys;

class SalesOrderItem extends Model
{
    use ValidatesForeignKeys;

    // Define foreign keys to validate
    protected array $foreignKeys = [
        'order_id' => ['table' => 'sales_orders', 'column' => 'order_id'],
        'product_id' => ['table' => 'products', 'column' => 'product_id'],
    ];
}
```

**Features:**
- Validates before `creating` and `updating`
- Throws `ValidationException` if foreign key doesn't exist
- Supports nullable foreign keys
- Custom error messages

### 2. Form Request Validation

Use in your Form Request classes:

```php
use App\Traits\HasForeignKeyValidation;

class StoreSalesOrderRequest extends FormRequest
{
    use HasForeignKeyValidation;

    public function rules(): array
    {
        return array_merge([
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ], [
            'product_id' => $this->foreignKeyRule('product_id', required: true),
            'customer_id' => $this->foreignKeyRule('customer_id'),
        ]);
    }

    public function messages(): array
    {
        return $this->foreignKeyMessages();
    }
}
```

**For bulk operations:**
```php
public function rules(): array
{
    return array_merge(
        $this->itemArrayRules(),
        [
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]
    );
}
```

### 3. Cascade Operations

Simulate database cascade operations in your models:

#### CASCADE DELETE
```php
protected static function boot()
{
    parent::boot();
    
    static::deleting(function ($model) {
        // Delete all related items when order is deleted
        $model->cascadeDelete(['items']);
    });
}
```

#### RESTRICT DELETE
```php
protected static function boot()
{
    parent::boot();
    
    static::deleting(function ($model) {
        // Prevent deletion if related records exist
        $model->restrictDelete(['orders']);
    });
}
```

#### SET NULL
```php
protected static function boot()
{
    parent::boot();
    
    static::deleting(function ($model) {
        // Set foreign keys to null in related records
        $model->nullifyRelated(['orders' => 'customer_id']);
    });
}
```

## Examples

### SalesOrderItem Model

```php
namespace App\Models;

use App\Traits\ValidatesForeignKeys;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    use ValidatesForeignKeys;

    protected array $foreignKeys = [
        'order_id' => ['table' => 'sales_orders', 'column' => 'order_id'],
        'product_id' => ['table' => 'products', 'column' => 'product_id'],
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
```

### ExchangeItem Model

```php
namespace App\Models;

use App\Traits\ValidatesForeignKeys;
use Illuminate\Database\Eloquent\Model;

class ExchangeItem extends Model
{
    use ValidatesForeignKeys;

    protected array $foreignKeys = [
        'exchange_id' => ['table' => 'exchanges', 'column' => 'exchange_id'],
        'product_id' => ['table' => 'products', 'column' => 'product_id'],
    ];

    public function exchange()
    {
        return $this->belongsTo(Exchange::class, 'exchange_id', 'exchange_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
```

### PurchaseReturn Model

```php
namespace App\Models;

use App\Traits\ValidatesForeignKeys;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use ValidatesForeignKeys;

    protected array $foreignKeys = [
        'product_id' => ['table' => 'products', 'column' => 'product_id'],
        'supplier_id' => ['table' => 'suppliers', 'column' => 'supplier_id'],
        'purchase_order_id' => ['table' => 'purchase_orders', 'column' => 'order_id', 'nullable' => true],
        'created_by' => ['table' => 'users', 'column' => 'id', 'nullable' => true],
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'order_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
```

## Validation Flow

```
User submits form
    ↓
Form Request validates (HasForeignKeyValidation)
    ↓
Controller creates/updates model
    ↓
Model validates foreign keys (ValidatesForeignKeys)
    ↓
If valid: Save to database
If invalid: Throw ValidationException
    ↓
User sees friendly error message
```

## Testing

```php
// Test model validation
public function test_validates_foreign_keys()
{
    $this->expectException(ValidationException::class);
    
    SalesOrderItem::create([
        'order_id' => 999999, // Non-existent order
        'product_id' => 1,
        'quantity' => 1,
    ]);
}

// Test form request validation
public function test_form_validates_product_exists()
{
    $response = $this->post('/sales-orders', [
        'product_id' => 999999, // Non-existent product
        'quantity' => 1,
    ]);
    
    $response->assertSessionHasErrors('product_id');
}
```

## Migration Comparison

### Before (with constraints)
```php
Schema::create('sales_order_items', function (Blueprint $table) {
    $table->id('item_id');
    $table->foreignId('order_id')->constrained('sales_orders', 'order_id')->onDelete('cascade');
    $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
    // ... other columns
});
```

### After (without constraints)
```php
Schema::create('sales_order_items', function (Blueprint $table) {
    $table->id('item_id');
    $table->foreignId('order_id');  // Just the column, no constraint
    $table->foreignId('product_id'); // Just the column, no constraint
    // ... other columns
    
    $table->index('order_id');    // Keep indexes for performance
    $table->index('product_id');  // Keep indexes for performance
});
```

## Models Already Updated

✅ `SalesOrderItem` - Validates order_id and product_id
✅ `ExchangeItem` - Validates exchange_id and product_id  
✅ `PurchaseReturn` - Validates product_id, supplier_id, purchase_order_id, created_by

## Next Steps

Apply the same pattern to other models:
- `PurchaseReceiveItem`
- `UserTermsAcceptance`
- `PurchaseOrder`
- Any other model with foreign keys

## Troubleshooting

**Q: Model saves without validation**
A: Make sure the trait is imported and the `$foreignKeys` array is defined

**Q: Getting "undefined property" error**
A: The `$foreignKeys` property must be `protected array`, not `private`

**Q: Want to skip validation in certain cases**
A: Use `withoutEvents()` or create a separate method that bypasses validation
```php
$model->withoutEvents(function () use ($model) {
    $model->save();
});
```

## Summary

This implementation provides the same data integrity as database foreign key constraints but with:
- **Better flexibility** for development and testing
- **Clearer error messages** for users
- **No migration complexity** when creating/dropping tables
- **Application-level control** over validation logic

The Eloquent relationships still work exactly the same, and query performance is maintained through proper indexing.
