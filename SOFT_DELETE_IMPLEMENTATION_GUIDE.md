# Soft Delete Implementation Guide

## Overview

The system now implements **Soft Deletes** across all critical models to maintain data integrity and preserve historical records. When a record is "deleted", it's not physically removed from the database - instead, it's marked with a `deleted_at` timestamp and hidden from normal queries.

## Why Soft Deletes?

✅ **Data Integrity** - Sales records remain intact even if a product is deleted  
✅ **Historical Tracking** - Maintain complete audit trails and transaction history  
✅ **Reporting Accuracy** - Generate accurate historical reports without data loss  
✅ **Compliance** - Meet regulatory requirements for data retention  
✅ **Reversibility** - Deleted records can be restored if needed  

## Models with Soft Deletes

The following models now support soft deletes:

### Core Business Models
- **Product** - Products can be removed from active inventory without losing sales history
- **Sale** - Sales records are preserved for reporting and auditing
- **Customer** - Customer data retained for historical transaction records
- **Supplier** - Supplier information kept for purchase history

### Transaction Models
- **SalesOrder** - Order history maintained even after deletion
- **PurchaseOrder** - Purchase records preserved for accounting
- **Invoice** - Invoice history retained for financial records
- **Purchase** - Purchase transactions kept for cost tracking
- **Returns** - Return records maintained for analysis

### Inventory Models
- **Inventory** - Inventory records can be archived without data loss

### Already Implemented
- **StockName** - Stock name records with soft delete
- **TaxDiscount** - Tax and discount configurations
- **TermsAndConditions** - Terms versions preserved
- **Brand** - Brand information retained

## How It Works

### Database Structure

Each table with soft deletes has a `deleted_at` column:
```sql
deleted_at TIMESTAMP NULL
```

- `NULL` = Active record (not deleted)
- `TIMESTAMP` = Soft deleted on this date/time

### Model Implementation

All models use the `SoftDeletes` trait:

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    
    // Model code...
}
```

## Usage Examples

### Soft Deleting Records

```php
// Soft delete a product
$product = Product::find(1);
$product->delete(); // Sets deleted_at timestamp

// Or use query builder
Product::where('product_id', 1)->delete();

// Delete multiple records
Product::where('stock_quantity', 0)->delete();
```

### Retrieving Records

```php
// Get only active (non-deleted) products - DEFAULT BEHAVIOR
$activeProducts = Product::all();
$activeProduct = Product::find(1);

// Include soft deleted records
$allProducts = Product::withTrashed()->get();
$productWithTrashed = Product::withTrashed()->find(1);

// Get ONLY deleted records
$deletedProducts = Product::onlyTrashed()->get();
```

### Restoring Deleted Records

```php
// Restore a single record
$product = Product::withTrashed()->find(1);
$product->restore();

// Restore multiple records
Product::onlyTrashed()
    ->where('product_category', 'Shoes')
    ->restore();

// Restore all deleted records
Product::onlyTrashed()->restore();
```

### Permanently Deleting Records

⚠️ **WARNING**: Force delete permanently removes data from the database!

```php
// Permanently delete a soft-deleted record
$product = Product::withTrashed()->find(1);
$product->forceDelete();

// Permanently delete from query
Product::withTrashed()
    ->where('product_id', 1)
    ->forceDelete();
```

### Checking Deletion Status

```php
$product = Product::withTrashed()->find(1);

// Check if soft deleted
if ($product->trashed()) {
    echo "This product is soft deleted";
}

// Get deletion date
if ($product->deleted_at) {
    echo "Deleted on: " . $product->deleted_at->format('Y-m-d H:i:s');
}
```

## Relationships & Foreign Keys

### Important Notes

1. **Related Records Are NOT Auto-Deleted** - When you soft delete a parent record, related records are NOT automatically deleted. This preserves data integrity.

2. **Querying Relationships** - Be careful when querying relationships with soft-deleted parent records:

```php
// This will only return active products
$sales = Sale::with('product')->get();

// To include sales with deleted products
$sales = Sale::with(['product' => function($query) {
    $query->withTrashed();
}])->get();
```

3. **Cascade Considerations**:

```php
// When deleting a product with sales
$product = Product::find(1);
$product->delete(); // Product is soft deleted

// Sales records remain intact
$sales = Sale::where('product_id', 1)->get(); // Still accessible
```

## Controller Examples

### Product Controller

```php
public function destroy(Product $product)
{
    // Soft delete the product
    $product->delete();
    
    return redirect()->route('products.index')
        ->with('success', 'Product archived successfully');
}

public function restore($id)
{
    $product = Product::onlyTrashed()->findOrFail($id);
    $product->restore();
    
    return redirect()->route('products.index')
        ->with('success', 'Product restored successfully');
}

public function forceDestroy($id)
{
    $product = Product::onlyTrashed()->findOrFail($id);
    
    // Check if product has associated records
    if ($product->sales()->exists()) {
        return back()->with('error', 'Cannot permanently delete product with sales history');
    }
    
    $product->forceDelete();
    
    return redirect()->route('products.index')
        ->with('success', 'Product permanently deleted');
}

public function archived()
{
    $archivedProducts = Product::onlyTrashed()->paginate(20);
    
    return view('products.archived', compact('archivedProducts'));
}
```

### Sale Controller

```php
public function index()
{
    // Only get active sales
    $sales = Sale::with(['product', 'customer'])->latest()->paginate(20);
    
    return view('sales.index', compact('sales'));
}

public function indexWithDeleted()
{
    // Include deleted sales for reporting
    $sales = Sale::withTrashed()
        ->with(['product' => function($query) {
            $query->withTrashed();
        }])
        ->latest()
        ->paginate(20);
    
    return view('sales.index_all', compact('sales'));
}
```

## Query Scopes

### Custom Scopes with Soft Deletes

```php
// In Product model
public function scopeActive($query)
{
    return $query->where('status', 'active');
}

// Usage - automatically excludes soft deleted
$activeProducts = Product::active()->get();

// Include soft deleted with custom scope
$allActiveProducts = Product::withTrashed()->active()->get();
```

## Reports & Analytics

### Handling Soft Deletes in Reports

```php
// Sales report - exclude deleted sales by default
$totalSales = Sale::sum('total_amount');

// Include all sales for historical accuracy
$historicalSales = Sale::withTrashed()->sum('total_amount');

// Sales by product - handle deleted products
$salesByProduct = Sale::withTrashed()
    ->with(['product' => function($query) {
        $query->withTrashed();
    }])
    ->get()
    ->groupBy('product_id');
```

## Best Practices

### ✅ DO

1. **Always use soft delete for business-critical data** (products, sales, customers)
2. **Check relationships before force delete**
3. **Include restoration functionality in your UI**
4. **Log soft delete operations** in audit trails
5. **Use `withTrashed()` in reports** that need historical data
6. **Add "archived" views** to show deleted records
7. **Implement permissions** for restore and force delete operations

### ❌ DON'T

1. **Don't force delete records with relationships** without checking
2. **Don't forget to use `withTrashed()`** when querying related soft-deleted records
3. **Don't automatically cascade deletes** on important relationships
4. **Don't expose force delete** to regular users
5. **Don't forget to update reports** to handle soft-deleted records properly

## UI Considerations

### Display Deleted Records

```blade
{{-- In product list view --}}
@if($product->trashed())
    <span class="badge badge-danger">Archived</span>
    <button wire:click="restore({{ $product->product_id }})">Restore</button>
@else
    <button wire:click="delete({{ $product->product_id }})">Archive</button>
@endif
```

### Archived Items View

```blade
{{-- archived.blade.php --}}
<h2>Archived Products</h2>
<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Deleted Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($archivedProducts as $product)
        <tr>
            <td>{{ $product->product_name }}</td>
            <td>{{ $product->deleted_at->format('Y-m-d H:i') }}</td>
            <td>
                <button onclick="restore({{ $product->product_id }})">Restore</button>
                <button onclick="forceDelete({{ $product->product_id }})" class="btn-danger">
                    Permanently Delete
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
```

## Testing Soft Deletes

```php
// In your tests
public function test_product_can_be_soft_deleted()
{
    $product = Product::factory()->create();
    
    $product->delete();
    
    // Product should be soft deleted
    $this->assertSoftDeleted('products', [
        'product_id' => $product->product_id
    ]);
    
    // Product should not appear in normal queries
    $this->assertNull(Product::find($product->product_id));
    
    // Product should appear with withTrashed
    $this->assertNotNull(Product::withTrashed()->find($product->product_id));
}

public function test_product_can_be_restored()
{
    $product = Product::factory()->create();
    $product->delete();
    
    $product->restore();
    
    // Product should be accessible again
    $this->assertNotNull(Product::find($product->product_id));
    $this->assertNull($product->fresh()->deleted_at);
}
```

## Migration Reference

### Adding Soft Deletes to Existing Tables

```php
// Migration
public function up(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->softDeletes(); // Adds deleted_at column
    });
}

public function down(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropSoftDeletes(); // Removes deleted_at column
    });
}
```

## Performance Considerations

### Indexing

Consider adding indexes for better query performance:

```php
Schema::table('products', function (Blueprint $table) {
    $table->index('deleted_at');
});
```

### Archiving Old Deleted Records

For tables with many soft-deleted records, consider archiving old deletions:

```php
// Archive records deleted more than 1 year ago
Product::onlyTrashed()
    ->where('deleted_at', '<', now()->subYear())
    ->chunk(1000, function ($products) {
        // Move to archive table or export
        foreach ($products as $product) {
            ArchiveService::archive($product);
            $product->forceDelete();
        }
    });
```

## Troubleshooting

### Issue: Related records not showing deleted parent

**Solution**: Use `withTrashed()` in relationship queries

```php
// Before
$sale = Sale::with('product')->find(1); // product might be null if deleted

// After
$sale = Sale::with(['product' => function($query) {
    $query->withTrashed();
}])->find(1); // product shows even if deleted
```

### Issue: Unique constraint errors on soft-deleted records

**Solution**: Make unique indexes conditional

```php
// Migration
$table->unique(['sku', 'deleted_at']); // Allows same SKU if deleted
```

### Issue: Accidentally restored many records

**Solution**: Implement safeguards in restore operations

```php
public function restore($id)
{
    // Add confirmation and logging
    Log::info("Restoring product ID: $id by user: " . auth()->id());
    
    $product = Product::onlyTrashed()->findOrFail($id);
    $product->restore();
}
```

## Summary

Soft deletes are now implemented across all critical models in the system:
- ✅ Database migrations completed
- ✅ Models updated with SoftDeletes trait
- ✅ Relationships preserved
- ✅ Data integrity maintained

**Next Steps:**
1. Update controller methods to include restore functionality
2. Add "Archived" views in the UI
3. Implement proper permissions for restore/force delete
4. Update reports to use `withTrashed()` where appropriate
5. Add soft delete functionality to Livewire components
6. Create unit tests for soft delete operations

## Support

When a product is deleted, its sales history, purchase history, and all related transaction records remain intact in the database. This ensures:
- Accurate financial reporting
- Complete audit trails
- Data recovery capabilities
- Regulatory compliance

For questions or issues, contact the development team.
