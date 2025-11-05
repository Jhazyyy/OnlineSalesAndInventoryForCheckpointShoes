# Tax & Discount UI Integration

## Overview
This document describes the UI integration of the Tax & Discount module with Sales Orders and Purchase Orders, allowing users to automatically apply active tax and discount rules when creating orders.

## Features Implemented

### 1. Purchase Order Create View Enhancement
**File**: `resources/views/purchases/purchase-orders/create.blade.php`

#### Changes Made:
- Added **Auto Calculate** button next to Tax Amount field
- Added **Auto Calculate** button next to Discount Amount field
- Display active tax rules with their rates below the tax field
- Display active discount rules with their rates below the discount field
- Added JavaScript functions to handle auto-calculation

#### UI Elements:
```blade
<!-- Tax Amount with Auto-Calculate -->
<div class="flex items-center justify-between mb-1">
    <x-input-label for="tax_amount" :value="__('Tax Amount')" />
    @if(isset($activeTaxes) && $activeTaxes->isNotEmpty())
    <button type="button" onclick="calculateAutoTaxes()"
        class="text-xs px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded">
        Auto Calculate
    </button>
    @endif
</div>
<x-text-input id="tax_amount" name="tax_amount" type="number" step="0.01" />
@if(isset($activeTaxes) && $activeTaxes->isNotEmpty())
<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
    Active taxes: 
    @foreach($activeTaxes as $tax)
        <span>{{ $tax->name }} ({{ $tax->rate }}{{ $tax->calculation_method === 'percentage' ? '%' : ' fixed' }})</span>
    @endforeach
</p>
@endif
```

### 2. Sales Order Create View Enhancement
**File**: `resources/views/sales/orders/create.blade.php`

#### Changes Made:
- Added **Apply Auto Discount** button in the custom discount section
- Display active discount rules with their rates
- Added JavaScript function to auto-calculate discounts
- Integrated with existing Alpine.js reactive data

#### UI Elements:
```blade
<!-- Auto-Calculate Discount -->
@if(isset($activeDiscounts) && $activeDiscounts->isNotEmpty())
<div class="flex items-center justify-between">
    <span class="text-xs font-medium">Auto-Calculate Discount</span>
    <button type="button" onclick="calculateAutoDiscounts()"
        class="text-xs px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded">
        Apply Auto Discount
    </button>
</div>
<p class="text-xs text-gray-500 dark:text-gray-400">
    Active discounts: 
    @foreach($activeDiscounts as $discount)
        <span>{{ $discount->name }} ({{ $discount->rate }}{{ $discount->calculation_method === 'percentage' ? '%' : ' fixed' }})</span>
    @endforeach
</p>
@endif
```

### 3. Controller Updates

#### PurchaseOrderController
**File**: `app/Http/Controllers/PurchaseOrderController.php`

Added to the `create()` method:
```php
$activeTaxes = \App\Models\TaxDiscount::active()
    ->taxes()
    ->orderBy('priority')
    ->get();

$activeDiscounts = \App\Models\TaxDiscount::active()
    ->discounts()
    ->orderBy('priority')
    ->get();

return view('purchases.purchase-orders.create', compact(
    'suppliers', 
    'products', 
    'statuses',
    'activeTaxes',
    'activeDiscounts'
));
```

#### SalesOrderController
**File**: `app/Http/Controllers/SalesOrderController.php`

Added to the `create()` method:
```php
$activeTaxes = \App\Models\TaxDiscount::active()
    ->taxes()
    ->orderBy('priority')
    ->get();

$activeDiscounts = \App\Models\TaxDiscount::active()
    ->discounts()
    ->orderBy('priority')
    ->get();

return [
    'customers' => $customers,
    'products' => $products,
    'activeTaxes' => $activeTaxes,
    'activeDiscounts' => $activeDiscounts,
];
```

### 4. API Endpoint for Auto-Calculation

#### New Controller Method
**File**: `app/Http/Controllers/TaxDiscountController.php`

```php
/**
 * Calculate taxes and discounts for an order
 */
public function calculateForOrder(Request $request)
{
    $validator = Validator::make($request->all(), [
        'type' => 'required|in:sales_order,purchase_order',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|numeric|min:0',
        'items.*.unit_price' => 'required|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Create items collection
    $items = collect($request->items)->map(function ($item) {
        return (object) [
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
        ];
    });

    // Calculate subtotal
    $subtotal = (float) $items->sum(function ($item) {
        return (float) $item->quantity * (float) $item->unit_price;
    });

    $orderType = $request->type === 'purchase_order' ? 'purchase' : 'sales';
    $taxDiscountService = new \App\Services\TaxDiscountService();
    $result = $taxDiscountService->calculateForOrder($subtotal, $items, $orderType);

    return response()->json([
        'success' => true,
        'taxes' => $result['total_tax'],
        'discounts' => $result['total_discount'],
        'breakdown' => $result
    ]);
}
```

#### Route Registration
**File**: `routes/web.php`

```php
// API route for tax/discount calculation
Route::post('/api/tax-discounts/calculate', 
    [\App\Http\Controllers\TaxDiscountController::class, 'calculateForOrder'])
    ->name('api.tax-discounts.calculate');
```

### 5. JavaScript Auto-Calculation Functions

#### Purchase Order JavaScript
```javascript
// Auto-calculate taxes based on active tax rules
async function calculateAutoTaxes() {
    const items = [];
    document.querySelectorAll('.item-row').forEach(row => {
        const productSelect = row.querySelector('.product-select');
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
        
        if (productSelect && productSelect.value && quantity > 0) {
            items.push({
                product_id: productSelect.value,
                quantity: quantity,
                unit_price: price
            });
        }
    });

    if (items.length === 0) {
        alert('Please add at least one item to calculate taxes.');
        return;
    }

    try {
        const response = await fetch('/api/tax-discounts/calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                type: 'purchase_order',
                items: items
            })
        });

        const data = await response.json();
        if (data.success && data.taxes) {
            document.getElementById('tax_amount').value = data.taxes.toFixed(2);
            updateOrderSummary();
        }
    } catch (error) {
        console.error('Error calculating taxes:', error);
        alert('Failed to calculate taxes. Please try again.');
    }
}

// Auto-calculate discounts based on active discount rules
async function calculateAutoDiscounts() {
    // Similar implementation to calculateAutoTaxes
    // But updates discount_amount field instead
}
```

#### Sales Order JavaScript
```javascript
// Auto-calculate discounts based on active discount rules
async function calculateAutoDiscounts() {
    // Get items from Alpine.js component
    const orderData = Alpine.$data(document.querySelector('[x-data]'));
    
    if (!orderData || !orderData.items || orderData.items.length === 0) {
        alert('Please add at least one item to calculate discounts.');
        return;
    }

    const items = orderData.items.map(item => ({
        product_id: item.product_id,
        quantity: item.quantity,
        unit_price: item.unit_price
    }));

    try {
        const response = await fetch('/api/tax-discounts/calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                type: 'sales_order',
                items: items
            })
        });

        const data = await response.json();
        if (data.success && data.discounts) {
            // Update Alpine.js data
            orderData.discountType = 'fixed';
            orderData.customDiscount = data.discounts;
        }
    } catch (error) {
        console.error('Error calculating discounts:', error);
        alert('Failed to calculate discounts. Please try again.');
    }
}
```

## How It Works

### User Workflow

1. **Creating a Purchase Order:**
   - User navigates to Create Purchase Order page
   - Adds items to the order (products, quantities, prices)
   - Sees active tax/discount rules displayed below the respective fields
   - Clicks "Auto Calculate" button next to Tax Amount
   - System calculates applicable taxes based on:
     - Products in the order
     - Product categories
     - Active tax rules
     - Rule priorities and compound settings
   - Tax amount is automatically populated
   - Clicks "Auto Calculate" button next to Discount Amount
   - System calculates applicable discounts similarly
   - Order totals are updated automatically

2. **Creating a Sales Order:**
   - User navigates to Create Sales Order page
   - Adds items using the existing interface
   - Sees active discount rules displayed in the discount section
   - Clicks "Apply Auto Discount" button
   - System calculates applicable discounts
   - Discount is automatically applied to the order
   - Totals update reactively via Alpine.js

### Backend Processing

1. **Data Collection:**
   - JavaScript collects all order items (product_id, quantity, unit_price)
   - Sends POST request to `/api/tax-discounts/calculate`

2. **Calculation Process:**
   - TaxDiscountController validates the request
   - Creates temporary items collection
   - Calculates order subtotal
   - Calls TaxDiscountService.calculateForOrder()
   - Service applies all active, valid tax/discount rules
   - Checks product/category applicability
   - Handles compound calculations with proper priority
   - Returns calculated amounts

3. **UI Update:**
   - JavaScript receives response with calculated values
   - Updates form fields
   - Triggers order summary recalculation
   - User can review and adjust if needed

## Benefits

1. **Accuracy**: Automatic calculation reduces manual entry errors
2. **Consistency**: Same business rules applied across all orders
3. **Transparency**: Users can see which rules are active
4. **Flexibility**: Users can still manually adjust amounts if needed
5. **Efficiency**: Saves time by automating complex calculations
6. **Audit Trail**: All applied rules are tracked in the system

## Future Enhancements

1. **Edit Views**: Add similar functionality to order edit pages
2. **Rule Preview**: Show breakdown of which rules were applied
3. **Manual Override**: Add UI to select specific rules to apply/exclude
4. **Bulk Operations**: Apply tax/discount calculations to multiple orders
5. **Saved Presets**: Allow users to save common tax/discount combinations
6. **Report Integration**: Include tax/discount analysis in reports

## Testing Checklist

- [ ] Create purchase order with auto-calculated taxes
- [ ] Create purchase order with auto-calculated discounts
- [ ] Create sales order with auto-calculated discounts
- [ ] Verify correct calculation for percentage-based rules
- [ ] Verify correct calculation for fixed amount rules
- [ ] Test with compound tax rules
- [ ] Test with priority ordering
- [ ] Test with category-specific rules
- [ ] Test with product-specific rules
- [ ] Test with date-valid rules (before/after validity period)
- [ ] Verify manual override still works
- [ ] Test error handling for invalid data
- [ ] Verify CSRF token protection
- [ ] Test with empty orders (validation)
- [ ] Test API response format

## Related Documentation

- [TAX_DISCOUNT_INTEGRATION.md](./TAX_DISCOUNT_INTEGRATION.md) - Backend integration details
- [IMPLEMENTATION_SUMMARY.md](./IMPLEMENTATION_SUMMARY.md) - Overall system documentation
