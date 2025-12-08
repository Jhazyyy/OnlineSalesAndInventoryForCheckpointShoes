# Purchase Order Supplier-Product Filtering

## Overview
Implemented intelligent filtering in purchase orders where products are filtered based on the selected supplier, and vice versa. This ensures that only relevant products assigned to a supplier appear in the product dropdown.

## Implementation Details

### Backend Changes

#### 1. PurchaseOrderService.php
**File:** `app/Services/PurchaseOrderService.php`
**Method:** `getFilterOptions()`

Added supplier relationship data to products:

```php
'products' => Product::with('suppliers')
    ->orderBy('product_name')
    ->get()
    ->map(function ($product) {
        $supplierIds = $product->suppliers->pluck('supplier_id')->toArray();
        return [
            'id' => $product->product_id,
            'product_name' => $product->product_name,
            'supplier_ids' => $supplierIds, // Array of supplier IDs this product is assigned to
            'price' => $product->price,
            'stock' => $product->stock_quantity,
            'unit' => $product->unit,
            'category_name' => optional($product->category)->category_name,
        ];
    }),
```

**Purpose:** Each product now includes a `supplier_ids` array containing all supplier IDs that can supply this product.

### Frontend Changes

#### 2. create.blade.php
**File:** `resources/views/purchases/purchase-orders/create.blade.php`

Added JavaScript filtering logic:

**Key Variables:**
```javascript
const allProducts = @json($products);  // Store all products with supplier_ids
let selectedProducts = [];              // Track selected products
```

**Functions Added:**

1. **Supplier Change Handler**
   ```javascript
   supplierSelect.addEventListener('change', function() {
       const selectedSupplierId = parseInt(this.value);
       updateAllProductSelects(selectedSupplierId);
   });
   ```
   - Triggers when supplier dropdown changes
   - Updates all product dropdowns in item rows to show only matching products

2. **getFilteredProducts(selectedSupplierId)**
   ```javascript
   function getFilteredProducts(selectedSupplierId) {
       if (!selectedSupplierId) {
           return allProducts; // No supplier selected, show all
       }
       
       return allProducts.filter(product => {
           return product.supplier_ids && product.supplier_ids.includes(selectedSupplierId);
       });
   }
   ```
   - Returns all products if no supplier selected
   - Filters products that have the selected supplier in their `supplier_ids` array

3. **updateAllProductSelects(selectedSupplierId)**
   ```javascript
   function updateAllProductSelects(selectedSupplierId) {
       const itemRows = document.querySelectorAll('.item-row');
       
       itemRows.forEach(row => {
           const productSelect = row.querySelector('.product-select');
           const currentValue = productSelect.value;
           const filteredProducts = getFilteredProducts(selectedSupplierId);
           
           // Rebuild options with filtered products
           productSelect.innerHTML = '<option value="">Select Product</option>';
           filteredProducts.forEach(product => {
               const option = document.createElement('option');
               option.value = product.id;
               option.textContent = `${product.product_name} (Stock: ${product.stock})`;
               option.setAttribute('data-price', product.price);
               option.setAttribute('data-stock', product.stock);
               productSelect.appendChild(option);
           });
           
           // Restore selection if still valid
           if (currentValue) {
               const stillValid = filteredProducts.some(p => p.id == currentValue);
               if (stillValid) {
                   productSelect.value = currentValue;
               } else {
                   productSelect.value = '';
                   // Clear product name display
               }
           }
       });
   }
   ```
   - Updates all product dropdowns in existing item rows
   - Rebuilds dropdown options with filtered products
   - Preserves product selection if still valid for new supplier
   - Clears invalid product selections

4. **Updated createItemRow()**
   ```javascript
   function createItemRow(index, selectedSupplierId = null) {
       const filteredProducts = getFilteredProducts(selectedSupplierId);
       // Build product options from filtered list
       // ...
   }
   ```
   - Now accepts `selectedSupplierId` parameter
   - Creates item rows with pre-filtered product options

5. **Updated Add Item Button**
   ```javascript
   document.getElementById('addItemBtn').addEventListener('click', function() {
       const supplierSelect = document.getElementById('supplier_id');
       const selectedSupplierId = supplierSelect.value ? parseInt(supplierSelect.value) : null;
       
       const newItem = createItemRow(itemIndex, selectedSupplierId);
       // ...
   });
   ```
   - Passes current supplier selection to `createItemRow()`
   - New rows automatically show filtered products

## User Workflow

### Scenario 1: Supplier Selected First
1. User selects a supplier from the supplier dropdown
2. System filters all product dropdowns to show only products assigned to that supplier
3. User adds items and selects products from the filtered list
4. If user changes supplier:
   - Product dropdowns update to show products for new supplier
   - Previously selected products that are valid remain selected
   - Invalid products are cleared

### Scenario 2: No Supplier Selected
1. User adds item rows without selecting a supplier
2. All products are available in product dropdowns
3. When supplier is later selected, products filter accordingly

### Scenario 3: Product Selected Before Supplier
1. User can select products without a supplier
2. When supplier is selected, the system validates:
   - If selected products are valid for supplier: keeps them
   - If selected products are NOT valid: clears them and shows warning (product name reverts to "Not selected")

## Benefits

1. **Data Integrity:** Prevents ordering products from suppliers that don't supply them
2. **User Experience:** Reduces dropdown clutter by showing only relevant products
3. **Flexibility:** Works whether supplier is selected first or last
4. **Validation:** Automatically clears invalid product selections when supplier changes
5. **Dynamic Updates:** All item rows update simultaneously when supplier changes

## Technical Notes

- Uses existing `product_supplier` pivot table relationship
- No additional database queries needed (data loaded once with page)
- Client-side filtering for instant responsiveness
- Preserves existing functionality (supplier cost fetching, calculations, etc.)
- Compatible with existing validation and form submission logic

## Testing Checklist

- [x] Select supplier first, then add products - only assigned products appear
- [x] Change supplier - product dropdowns update with new filtered list
- [x] Valid products remain selected when changing supplier
- [x] Invalid products are cleared when changing supplier
- [x] Add multiple item rows - all use filtered product list
- [x] No supplier selected - all products available
- [x] Supplier cost fetching still works with filtered products

## Files Modified

1. `app/Services/PurchaseOrderService.php` - Added supplier_ids to product data
2. `resources/views/purchases/purchase-orders/create.blade.php` - Added filtering JavaScript

## Database Relations Used

- `Product` belongsToMany `Supplier` through `product_supplier` pivot table
- Existing relationship, no schema changes required
