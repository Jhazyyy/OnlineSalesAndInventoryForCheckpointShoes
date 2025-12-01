# Soft Delete Usage Examples

## Real-World Scenarios

### Scenario 1: Deleting a Product

**Problem**: You want to remove a product from active inventory, but it has existing sales records.

**Solution with Soft Delete**:
```php
// In ProductController.php
public function destroy($id)
{
    $product = Product::findOrFail($id);
    
    // Check if product has sales
    $salesCount = $product->sales()->count();
    
    if ($salesCount > 0) {
        // Soft delete - preserves integrity
        $product->delete();
        
        return redirect()->route('products.index')
            ->with('success', "Product archived. {$salesCount} sales records preserved.");
    } else {
        // Can safely force delete if no sales
        $product->forceDelete();
        
        return redirect()->route('products.index')
            ->with('success', 'Product permanently deleted.');
    }
}
```

**Before Soft Delete (Problems)**:
```php
// OLD WAY - Would cause problems!
$product->delete(); // If cascading, would delete all sales!
// OR
if ($product->sales()->count() > 0) {
    return back()->with('error', 'Cannot delete product with sales');
}
```

### Scenario 2: Sales Report with Deleted Products

**Problem**: Generate a sales report, but some products have been deleted.

**Solution with Soft Delete**:
```php
// In ReportController.php
public function salesReport(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    
    // Include sales of deleted products
    $sales = Sale::withTrashed()
        ->with(['product' => function($query) {
            $query->withTrashed(); // Include deleted products
        }])
        ->whereBetween('date', [$startDate, $endDate])
        ->get();
    
    $report = $sales->map(function($sale) {
        return [
            'product_name' => $sale->product->product_name,
            'is_archived' => $sale->product->trashed(),
            'quantity' => $sale->quantity,
            'total' => $sale->total_amount,
            'date' => $sale->date->format('Y-m-d'),
        ];
    });
    
    return view('reports.sales', [
        'sales' => $report,
        'total' => $sales->sum('total_amount')
    ]);
}
```

**In the View (reports/sales.blade.php)**:
```blade
<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $sale)
        <tr>
            <td>
                {{ $sale['product_name'] }}
                @if($sale['is_archived'])
                    <span class="badge badge-secondary">Archived</span>
                @endif
            </td>
            <td>{{ $sale['quantity'] }}</td>
            <td>₱{{ number_format($sale['total'], 2) }}</td>
            <td>{{ $sale['date'] }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Total</th>
            <th colspan="2">₱{{ number_format($total, 2) }}</th>
        </tr>
    </tfoot>
</table>
```

### Scenario 3: Archived Products Management

**Problem**: Need a way to view and restore deleted products.

**Solution with Soft Delete**:
```php
// In ProductController.php
public function archived()
{
    $archivedProducts = Product::onlyTrashed()
        ->withCount('sales') // Count of sales records
        ->orderBy('deleted_at', 'desc')
        ->paginate(20);
    
    return view('products.archived', compact('archivedProducts'));
}

public function restore($id)
{
    $product = Product::onlyTrashed()->findOrFail($id);
    
    // Log the restoration
    Log::info("Product restored", [
        'product_id' => $product->product_id,
        'product_name' => $product->product_name,
        'restored_by' => auth()->id(),
        'deleted_at' => $product->deleted_at,
    ]);
    
    $product->restore();
    
    return redirect()->route('products.archived')
        ->with('success', 'Product restored successfully!');
}

public function bulkRestore(Request $request)
{
    $productIds = $request->input('product_ids', []);
    
    $restored = Product::onlyTrashed()
        ->whereIn('product_id', $productIds)
        ->restore();
    
    return redirect()->route('products.archived')
        ->with('success', "{$restored} products restored.");
}
```

**Archived View (products/archived.blade.php)**:
```blade
<div class="container">
    <h2>Archived Products</h2>
    
    <form method="POST" action="{{ route('products.bulk-restore') }}">
        @csrf
        
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">
                Restore Selected
            </button>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>Product Name</th>
                    <th>SKU</th>
                    <th>Sales Count</th>
                    <th>Deleted Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($archivedProducts as $product)
                <tr>
                    <td>
                        <input type="checkbox" name="product_ids[]" 
                               value="{{ $product->product_id }}">
                    </td>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>
                        {{ $product->sales_count }}
                        @if($product->sales_count > 0)
                            <small class="text-muted">(has history)</small>
                        @endif
                    </td>
                    <td>{{ $product->deleted_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <form method="POST" 
                              action="{{ route('products.restore', $product->product_id) }}" 
                              style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                Restore
                            </button>
                        </form>
                        
                        @if($product->sales_count == 0)
                        <form method="POST" 
                              action="{{ route('products.force-delete', $product->product_id) }}" 
                              style="display: inline;"
                              onsubmit="return confirm('Permanently delete? This cannot be undone!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                Permanent Delete
                            </button>
                        </form>
                        @else
                        <button class="btn btn-sm btn-secondary" disabled 
                                title="Cannot permanently delete product with sales history">
                            Permanent Delete
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        {{ $archivedProducts->links() }}
    </form>
</div>

<script>
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="product_ids[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
```

### Scenario 4: Customer Deletion with Order Preservation

**Problem**: A customer requests account deletion, but you need to keep order records.

**Solution with Soft Delete**:
```php
// In CustomerController.php
public function destroy($id)
{
    $customer = Customer::findOrFail($id);
    
    // Check for active orders
    $activeOrders = $customer->orders()
        ->whereIn('status', ['pending', 'processing', 'shipped'])
        ->count();
    
    if ($activeOrders > 0) {
        return back()->with('error', 
            "Cannot archive customer with {$activeOrders} active orders. " .
            "Please complete or cancel orders first."
        );
    }
    
    // Anonymize sensitive data before soft delete
    $customer->update([
        'first_name' => 'Deleted',
        'last_name' => 'Customer',
        'email' => 'deleted_' . $customer->customer_id . '@example.com',
        'phone' => null,
        'address' => '[DELETED]',
    ]);
    
    // Soft delete to preserve order history
    $customer->delete();
    
    Log::info("Customer archived", [
        'customer_id' => $customer->customer_id,
        'order_count' => $customer->orders()->count(),
        'deleted_by' => auth()->id(),
    ]);
    
    return redirect()->route('customers.index')
        ->with('success', 'Customer account archived. Order history preserved.');
}
```

### Scenario 5: Inventory Report with Historical Data

**Problem**: Generate inventory movement report including discontinued products.

**Solution with Soft Delete**:
```php
// In InventoryController.php
public function movementReport(Request $request)
{
    $startDate = $request->input('start_date', now()->subMonth());
    $endDate = $request->input('end_date', now());
    $includeArchived = $request->boolean('include_archived', true);
    
    $query = StockMovement::query()
        ->whereBetween('created_at', [$startDate, $endDate])
        ->with(['product' => function($query) use ($includeArchived) {
            if ($includeArchived) {
                $query->withTrashed();
            }
        }]);
    
    if (!$includeArchived) {
        // Only show movements for active products
        $query->whereHas('product', function($q) {
            $q->whereNull('deleted_at');
        });
    }
    
    $movements = $query->orderBy('created_at', 'desc')->get();
    
    $summary = [
        'total_movements' => $movements->count(),
        'active_products' => $movements->where('product.deleted_at', null)->count(),
        'archived_products' => $movements->whereNotNull('product.deleted_at')->count(),
        'total_in' => $movements->where('movement_type', 'in')->sum('quantity'),
        'total_out' => $movements->where('movement_type', 'out')->sum('quantity'),
    ];
    
    return view('inventory.movement-report', [
        'movements' => $movements,
        'summary' => $summary,
    ]);
}
```

### Scenario 6: API Response with Soft-Deleted Data

**Problem**: API should indicate which products are archived.

**Solution with Soft Delete**:
```php
// In API\ProductController.php
public function index(Request $request)
{
    $includeArchived = $request->boolean('include_archived', false);
    
    $query = Product::query();
    
    if ($includeArchived) {
        $query->withTrashed();
    }
    
    $products = $query->get()->map(function($product) {
        return [
            'id' => $product->product_id,
            'name' => $product->product_name,
            'sku' => $product->sku,
            'price' => $product->price,
            'status' => $product->trashed() ? 'archived' : 'active',
            'archived_at' => $product->deleted_at?->toIso8601String(),
        ];
    });
    
    return response()->json([
        'success' => true,
        'data' => $products,
    ]);
}

public function show($id)
{
    $product = Product::withTrashed()->findOrFail($id);
    
    return response()->json([
        'success' => true,
        'data' => [
            'id' => $product->product_id,
            'name' => $product->product_name,
            'status' => $product->trashed() ? 'archived' : 'active',
            'can_purchase' => !$product->trashed(),
            'archived_at' => $product->deleted_at?->toIso8601String(),
            'sales_count' => $product->sales()->count(),
        ],
    ]);
}
```

## Routes Configuration

Add these routes to handle soft delete operations:

```php
// In routes/web.php

Route::middleware(['auth'])->group(function () {
    
    // Products
    Route::resource('products', ProductController::class);
    Route::get('products/archived/list', [ProductController::class, 'archived'])
        ->name('products.archived');
    Route::post('products/{id}/restore', [ProductController::class, 'restore'])
        ->name('products.restore');
    Route::delete('products/{id}/force', [ProductController::class, 'forceDestroy'])
        ->name('products.force-delete')
        ->middleware('can:force-delete-products');
    Route::post('products/bulk-restore', [ProductController::class, 'bulkRestore'])
        ->name('products.bulk-restore');
    
    // Similar for other models
    Route::get('customers/archived/list', [CustomerController::class, 'archived'])
        ->name('customers.archived');
    Route::post('customers/{id}/restore', [CustomerController::class, 'restore'])
        ->name('customers.restore');
    
    Route::get('sales/archived/list', [SaleController::class, 'archived'])
        ->name('sales.archived');
    Route::post('sales/{id}/restore', [SaleController::class, 'restore'])
        ->name('sales.restore');
});
```

## Livewire Component Example

```php
// app/Livewire/ProductManager.php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductManager extends Component
{
    public $showArchived = false;
    
    public function toggleArchived()
    {
        $this->showArchived = !$this->showArchived;
    }
    
    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        
        $this->dispatch('product-deleted', productId: $id);
        session()->flash('message', 'Product archived successfully.');
    }
    
    public function restoreProduct($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        
        $this->dispatch('product-restored', productId: $id);
        session()->flash('message', 'Product restored successfully.');
    }
    
    public function render()
    {
        $products = $this->showArchived 
            ? Product::onlyTrashed()->get()
            : Product::all();
        
        return view('livewire.product-manager', [
            'products' => $products
        ]);
    }
}
```

## Summary

Soft deletes provide:
- ✅ Data integrity preservation
- ✅ Complete audit trails
- ✅ Reversible deletions
- ✅ Historical reporting accuracy
- ✅ GDPR compliance (with data anonymization)
- ✅ Better user experience (undo capability)

All examples above are now possible in your system!
