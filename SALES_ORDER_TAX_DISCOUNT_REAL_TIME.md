# Sales Order Tax & Discount Real-Time Implementation

## Overview
Implemented real-time reactive tax and discount calculations in the Sales Order creation form, mirroring the functionality of Purchase Orders.

## Implementation Details

### Alpine.js Watchers Added
Added an `init()` method to the Alpine.js component with three watchers:

1. **Subtotal Watcher**: Monitors cart changes
   - Triggers when items are added/removed
   - Triggers when quantities change
   - Automatically recalculates both tax and discount

2. **Tax Rule Watcher**: Monitors tax dropdown
   - Triggers when tax rule is selected
   - Triggers when tax rule is cleared
   - Automatically applies the selected tax rule

3. **Discount Rule Watcher**: Monitors discount dropdown
   - Triggers when discount rule is selected
   - Triggers when discount rule is cleared
   - Automatically applies the selected discount rule

### Code Structure
```javascript
init() {
    // Watch subtotal changes to recalculate tax and discount
    this.$watch('subtotal', () => {
        if (this.selectedTaxRule) {
            this.applyCustomerTax();
        }
        if (this.selectedDiscountRule) {
            this.applyCustomerDiscount();
        }
    });
    
    // Watch tax rule changes
    this.$watch('selectedTaxRule', () => {
        this.applyCustomerTax();
    });
    
    // Watch discount rule changes
    this.$watch('selectedDiscountRule', () => {
        this.applyCustomerDiscount();
    });
}
```

## How It Works

### Real-Time Behavior
1. **Adding Items**: When you add an item to cart, subtotal changes → watchers trigger → tax & discount recalculate automatically
2. **Changing Quantity**: When you update quantity, subtotal changes → watchers trigger → recalculation happens
3. **Removing Items**: When you remove an item, subtotal changes → watchers trigger → amounts update
4. **Selecting Tax**: When you select a tax rule → tax watcher triggers → tax applies immediately
5. **Selecting Discount**: When you select a discount rule → discount watcher triggers → discount applies immediately

### Calculation Flow
- **Tax Calculation**: Reads selected dropdown option's data attributes (method, rate, fixed_amount), applies to current subtotal
- **Discount Calculation**: Reads selected dropdown option's data attributes (method, rate, fixed_amount), applies to current subtotal
- **Total Calculation**: Computed property automatically updates: `subtotal + tax - discount`

## Comparison with Purchase Order

### Purchase Order Pattern (Plain JavaScript)
- Uses event listeners on input fields
- Calls `updateOrderSummary()` on every change
- Imperative approach: manually trigger recalculation

### Sales Order Pattern (Alpine.js)
- Uses Alpine.js `$watch` API
- Reactive computed properties
- Declarative approach: automatic recalculation

Both achieve the same real-time behavior, just using different paradigms based on the framework being used.

## Testing Checklist
- [x] Add item to cart → Tax & discount recalculate
- [x] Change item quantity → Tax & discount recalculate
- [x] Remove item from cart → Tax & discount recalculate
- [x] Select tax rule → Tax applies immediately
- [x] Change tax rule → Tax updates immediately
- [x] Clear tax rule (select "No Tax") → Tax resets to 0
- [x] Select discount rule → Discount applies immediately
- [x] Change discount rule → Discount updates immediately
- [x] Clear discount rule (select "No Discount") → Discount resets to 0

## Files Modified
- `resources/views/sales/orders/create.blade.php`
  - Added `init()` method with Alpine.js watchers
  - Watchers monitor: `subtotal`, `selectedTaxRule`, `selectedDiscountRule`
  - Automatic recalculation on any relevant change

## Notes
- The existing `@change` handlers on the dropdowns are now redundant but kept for backwards compatibility
- The watchers provide more comprehensive coverage as they also react to cart changes
- This implementation follows Alpine.js best practices for reactive data
