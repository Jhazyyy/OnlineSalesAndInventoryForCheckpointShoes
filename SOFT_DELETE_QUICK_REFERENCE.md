# Soft Delete Quick Reference

## Quick Commands

### Delete (Soft)
```php
$product->delete();                    // Soft delete single record
Product::where('id', 1)->delete();    // Soft delete by query
```

### Retrieve
```php
Product::all();                       // Active records only (default)
Product::withTrashed()->get();       // Include soft deleted
Product::onlyTrashed()->get();       // Only soft deleted records
```

### Restore
```php
$product->restore();                      // Restore single record
Product::onlyTrashed()->restore();       // Restore all
Product::onlyTrashed()                   // Restore by condition
    ->where('category', 'Shoes')
    ->restore();
```

### Force Delete (Permanent)
```php
$product->forceDelete();              // Permanently delete
Product::withTrashed()                // Force delete by query
    ->where('id', 1)
    ->forceDelete();
```

### Check Status
```php
$product->trashed();                  // Returns true if soft deleted
$product->deleted_at;                 // Timestamp of deletion or null
```

## Models with Soft Deletes

✅ Product  
✅ Sale  
✅ Customer  
✅ Supplier  
✅ SalesOrder  
✅ PurchaseOrder  
✅ Invoice  
✅ Purchase  
✅ Returns  
✅ Inventory  
✅ StockName  
✅ TaxDiscount  
✅ TermsAndConditions  
✅ Brand  

## Common Patterns

### In Controllers

```php
// Delete
public function destroy($id)
{
    Product::findOrFail($id)->delete();
    return redirect()->back()->with('success', 'Archived');
}

// Restore
public function restore($id)
{
    Product::onlyTrashed()->findOrFail($id)->restore();
    return redirect()->back()->with('success', 'Restored');
}

// View archived
public function archived()
{
    return view('products.archived', [
        'products' => Product::onlyTrashed()->paginate(20)
    ]);
}
```

### In Queries with Relations

```php
// Include deleted parent
Sale::with(['product' => function($query) {
    $query->withTrashed();
}])->get();

// Include deleted children
Product::withTrashed()
    ->with(['sales' => function($query) {
        $query->withTrashed();
    }])
    ->get();
```

### In Blade Templates

```blade
@if($product->trashed())
    <span class="badge-danger">Archived</span>
    <button wire:click="restore({{ $product->id }})">Restore</button>
@else
    <button wire:click="delete({{ $product->id }})">Archive</button>
@endif
```

## When to Use Each Method

| Scenario | Method | Reason |
|----------|--------|--------|
| Remove product from active inventory | `delete()` | Preserves history |
| Archive old sales records | `delete()` | Keeps transaction data |
| Remove test/duplicate data | `forceDelete()` | Clean permanent removal |
| Restore accidentally deleted item | `restore()` | Undo deletion |
| Show all historical records | `withTrashed()` | Complete data view |
| Manage archived items | `onlyTrashed()` | View deletion candidates |

## Important Notes

⚠️ **Default Behavior**: All queries automatically exclude soft-deleted records  
⚠️ **Force Delete**: Permanently removes data - use with caution  
⚠️ **Relationships**: Don't cascade on delete for soft-deleted models  
⚠️ **Reports**: Use `withTrashed()` for accurate historical data  

## Example Use Cases

### Product Deletion
When you delete a product, its sales history remains intact:
```php
$product = Product::find(1);
$product->delete(); // Product archived

// Sales still exist
Sale::where('product_id', 1)->count(); // Returns count

// View product in sales
Sale::with(['product' => function($q) {
    $q->withTrashed();
}])->first()->product; // Returns deleted product
```

### Customer Deletion
Customer data preserved for transaction records:
```php
$customer = Customer::find(1);
$customer->delete(); // Customer archived

// Orders still accessible
$orders = SalesOrder::where('customer_id', 1)->get();
```

### Reporting
Generate accurate reports including deleted items:
```php
// Current inventory value (active only)
$currentValue = Product::sum('price');

// Total historical inventory value
$totalValue = Product::withTrashed()->sum('price');

// Archived product count
$archivedCount = Product::onlyTrashed()->count();
```

## See Full Documentation
For detailed information, see `SOFT_DELETE_IMPLEMENTATION_GUIDE.md`
