# Markup Price Implementation

## Overview
This document describes the implementation of markup prices in the Sales POS and Inventory Management system. The markup feature allows products with a configured markup price to automatically display and apply the calculated selling price in both the POS and inventory lists.

## Changes Made

### 1. **SalesOrderService** (`app/Services/SalesOrderService.php`)
**File Modified:** `app/Services/SalesOrderService.php`

**Change:** Updated the `getFilterOptions()` method to include markup price information for each product.

**Details:**
- Added `markupPrice` relationship loading via `with('markupPrice')`
- Calculate markup price using the formula: `Cost × (1 + (Markup Percentage / 100))`
- Return additional fields in product array:
  - `markup_price`: Calculated selling price based on markup
  - `markup_percentage`: Percentage markup value
  - `pricing_method`: Method used for pricing (e.g., 'markup')
  - `markup_price_id`: ID of the markup configuration

**Code Changes:**
```php
'products' => Product::with('markupPrice')
    ->orderBy('product_name')
    ->get()
    ->map(function ($product) {
        // Calculate markup price if applicable
        $markupPrice = null;
        $markupPercentage = null;
        if ($product->pricing_method === 'markup' && $product->markupPrice && $product->total_cost) {
            $markupPrice = $product->markupPrice->calculatePrice($product->total_cost);
            $markupPercentage = $product->markupPrice->markup_percentage;
        }
        
        return [
            'id' => $product->product_id,
            'name' => $product->product_name . ' - ' . $product->product_brand,
            'price' => $product->price,
            'markup_price' => $markupPrice,
            'markup_percentage' => $markupPercentage,
            'pricing_method' => $product->pricing_method,
            'markup_price_id' => $product->markup_price_id,
            // ... other fields
        ];
    }),
```

### 2. **Sales POS View** (`resources/views/sales/orders/create.blade.php`)

#### 2.1 Product Card Display
**Location:** Product card in the available products grid

**Changes:**
- Display markup price alongside base price in product cards
- Show markup percentage badge when applicable
- Strike-through base price if markup price exists

**Visual Elements:**
- Base price appears with strikethrough (if markup exists)
- Markup percentage displays as a badge (e.g., "+15%")
- Selling price (markup price) shows in indigo color

#### 2.2 Product Tooltip Enhancement
**Location:** Hover tooltip for products

**Changes:**
- Renamed "Price" to "Base Price"
- Added separate "Selling Price" section (highlighted in indigo)
- Added "Markup" percentage display
- Shows markup configuration name

**Example Display:**
```
Category: Shoes
Brand: Nike
Base Price: ₱500.00
---
Selling Price: ₱575.00  [+15%]
Markup: +15%
Stock: 25 pcs
```

#### 2.3 JavaScript Updates
**Function:** `addToCart(id, name, price, markupPrice, stock, image)`

**Changes:**
- Modified function signature to accept `markupPrice` parameter
- Uses markup price if available, falls back to base price
- Updated `addToCart()` calls to pass 5 parameters instead of 4

**Logic:**
```javascript
// Use markup price if available, otherwise use base price
const finalPrice = markupPrice !== null && markupPrice !== undefined ? markupPrice : price;
```

### 3. **Inventory Products List** (`resources/views/inventory/products/index.blade.php`)

#### 3.1 Table Header
**Location:** Product table headers

**Changes:**
- Added new column header: "Markup / Selling Price"
- Positioned after the "Price" column

#### 3.2 Table Data Cell
**Location:** Product table rows

**Changes Added:**
- Display markup configuration name (if applicable)
- Show markup percentage badge
- Display calculated selling price (markup price)
- Added "Apply to selling price" button

**Visual Display:**
```
Markup Configuration Name
+15% (badge in indigo)
₱575.00 (calculated selling price)
[Apply to selling price] (button link)
```

#### 3.3 JavaScript Function: `applyMarkupPrice()`
**Location:** End of script section

**Functionality:**
- Confirms user action before applying
- Makes POST request to `/inventory/products/{product}/apply-markup`
- Sends markup price as JSON payload
- Reloads page on success to reflect changes

**Code:**
```javascript
function applyMarkupPrice(productId, markupPrice) {
    if (!confirm(`Apply markup price ₱${markupPrice.toFixed(2)} to this product's selling price?`)) {
        return;
    }

    fetch(`/inventory/products/${productId}/apply-markup`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            selling_price: markupPrice
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Markup price applied successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to apply markup price'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred: ' + error.message);
    });
}
```

### 4. **ProductController** (`app/Http/Controllers/ProductController.php`)

#### 4.1 Import Addition
**File:** `app/Http/Controllers/ProductController.php`

**Change:** Added `Log` facade import:
```php
use Illuminate\Support\Facades\Log;
```

#### 4.2 New Method: `applyMarkup()`
**Location:** End of ProductController class

**Functionality:**
- Validates incoming `selling_price` parameter
- Updates product's selling price
- Logs the action to audit trail with before/after values
- Returns JSON response with success status

**Validation:**
- `selling_price` is required, numeric, and >= 0

**Audit Logging:**
- Records old and new prices
- Indicates markup was applied
- Stores markup_price_id for reference

**Response:**
- Success: Returns `{ success: true, message: "...", new_price: ... }`
- Error: Returns `{ success: false, message: "...", errors: ... }` with HTTP 422 or 500

### 5. **Routes** (`routes/web.php`)

**Location:** Inventory products route group

**Addition:**
```php
Route::post('/{product}/apply-markup', [ProductController::class, 'applyMarkup'])
    ->middleware('permission:edit products')
    ->name('apply-markup');
```

**Full Route:** `POST /inventory/products/{product}/apply-markup`
**Route Name:** `inventory.products.apply-markup`
**Middleware:** Requires `edit products` permission

## Workflow

### In Sales POS (Create Order)
1. User views available products grid
2. Each product shows:
   - Base price (₱500.00)
   - Markup badge (+15%) if markup configured
   - Selling price (₱575.00) if markup exists
3. When hovering, tooltip shows detailed pricing info
4. User clicks to add product to cart
5. If markup price exists, that price is added to cart (not base price)
6. Checkout calculates totals using markup prices

### In Inventory Management (Products List)
1. Admin views product inventory list
2. "Price" column shows base selling price
3. New "Markup / Selling Price" column shows:
   - Markup configuration name
   - Markup percentage (e.g., +15%)
   - Calculated selling price from markup
   - "Apply to selling price" button
4. Clicking button:
   - Confirms action
   - Sends request to update price
   - Reloads page to show changes
5. Price column now reflects the markup-applied price

## Database Requirements

### Tables Used (No changes needed)
- `products` - Already has `pricing_method`, `markup_price_id`, `price` columns
- `markup_prices` - Already has `markup_percentage` column
- `audit_logs` - Records markup price applications

### Fields Utilized
- `Product.pricing_method` - Determines if markup pricing is used
- `Product.markup_price_id` - Foreign key to MarkupPrice
- `Product.total_cost` - Used to calculate markup price
- `Product.price` - Updated when markup is applied
- `MarkupPrice.markup_percentage` - The percentage to apply

## API Endpoints

### Apply Markup Price
- **Method:** POST
- **URL:** `/inventory/products/{product}/apply-markup`
- **Route Name:** `inventory.products.apply-markup`
- **Middleware:** `auth`, `permission:edit products`
- **Request Body:**
  ```json
  {
    "selling_price": 575.00
  }
  ```
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Markup price applied successfully",
    "new_price": 575.00
  }
  ```
- **Error Response (422):**
  ```json
  {
    "success": false,
    "message": "Invalid selling price",
    "errors": { "selling_price": [...] }
  }
  ```

## Testing Checklist

- [ ] Markup prices display in POS product cards
- [ ] Strikethrough on base price when markup exists
- [ ] Markup percentage badge shows correctly
- [ ] POS tooltip shows full pricing details
- [ ] Products with markup add correct price to cart
- [ ] Cart totals calculate correctly with markup prices
- [ ] Inventory products list shows markup column
- [ ] "Apply to selling price" button displays correctly
- [ ] Button click triggers confirmation dialog
- [ ] Price updates successfully when markup applied
- [ ] Audit log records markup price application
- [ ] Non-marked-up products don't show markup info
- [ ] Responsive design works on mobile

## Performance Considerations

1. **SalesOrderService.getFilterOptions()**
   - Added `with('markupPrice')` to eager load relationship
   - Calculation done in memory (no extra DB queries)

2. **Inventory List**
   - No extra database queries (markup calculated from existing data)
   - Minimal performance impact

3. **API Endpoint**
   - Single database update
   - Single audit log insert
   - Reasonable response time

## Security Considerations

1. **Middleware:** Both views/features protected by permission middleware
2. **Validation:** Input validated on server-side
3. **CSRF Protection:** POST request requires valid CSRF token
4. **Authorization:** `edit products` permission required
5. **Audit Trail:** All changes logged with user context

## Future Enhancements

1. Batch apply markup to multiple products
2. Scheduled markup recalculation
3. Markup history tracking per product
4. Margin profit display (selling price - cost)
5. Bulk discount based on markup tier
6. Price override protection settings
