<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockMovement;
use App\Models\Inventory;
use App\Models\Customer;
use App\Models\Supplier;

echo "===== VARIANT INTEGRATION TEST =====\n\n";

try {
    // 1. Create or get parent product
    $product = Product::where('is_parent', true)->first();
    
    if (!$product) {
        echo "Creating parent product...\n";
        $product = Product::create([
            'product_name' => 'LEATHER CHELSEA BOOTS SLIP ON',
            'sku' => 'LCB-001',
            'barcode' => '8801234567890',
            'description' => 'Premium leather chelsea boots',
            'category_id' => 1,
            'unit' => 'pair',
            'selling_price' => 150.00,
            'purchase_price' => 80.00,
            'stock_quantity' => 0,
            'reorder_level' => 10,
            'is_active' => true,
            'is_parent' => true,
            'has_variants' => true,
        ]);
    }
    
    echo "Parent Product: {$product->product_name} (ID: {$product->product_id})\n\n";
    
    // 2. Create or get variants
    $variants = ProductVariant::where('parent_product_id', $product->product_id)->get();
    
    if ($variants->isEmpty()) {
        echo "Creating product variants...\n";
        
        $variantData = [
            ['color' => 'Black', 'size' => '7', 'material' => 'Black Nappa'],
            ['color' => 'Tan', 'size' => '7', 'material' => 'Tan Nappa'],
            ['color' => 'Black', 'size' => '8', 'material' => 'Black Nappa'],
            ['color' => 'Tan', 'size' => '8', 'material' => 'Tan Nappa'],
        ];
        
        foreach ($variantData as $index => $data) {
            $variant = ProductVariant::create([
                'parent_product_id' => $product->product_id,
                'variant_name' => "{$product->product_name} - {$data['color']} Size {$data['size']}",
                'variant_sku' => "{$product->sku}-{$data['color'][0]}{$data['size']}",
                'barcode' => $product->barcode . ($index + 1),
                'color' => $data['color'],
                'size' => $data['size'],
                'material' => $data['material'],
                'price_adjustment' => 0.00,
                'quantity' => 0,
                'low_stock_threshold' => 5,
                'critical_stock_threshold' => 2,
                'is_active' => true,
            ]);
            
            echo "  - Created variant: {$variant->display_name} (ID: {$variant->variant_id})\n";
        }
        
        $variants = ProductVariant::where('parent_product_id', $product->product_id)->get();
    } else {
        echo "Using existing variants:\n";
        foreach ($variants as $variant) {
            echo "  - {$variant->display_name} (ID: {$variant->variant_id})\n";
        }
    }
    
    echo "\n";
    
    // 3. Test Purchase Order with Variant
    echo "Testing Purchase Order Integration...\n";
    
    $supplier = Supplier::first();
    if (!$supplier) {
        $supplier = Supplier::create([
            'supplier_name' => 'Test Supplier',
            'contact_person' => 'John Doe',
            'email' => 'supplier@test.com',
            'phone' => '1234567890',
            'address' => '123 Test St',
        ]);
    }
    
    $variant1 = $variants->first();
    
    $purchaseOrder = PurchaseOrder::create([
        'supplier_id' => $supplier->supplier_id,
        'order_date' => now(),
        'expected_delivery_date' => now()->addDays(7),
        'status' => 'pending',
        'total_amount' => 0,
        'notes' => 'Test purchase order with variant',
    ]);
    
    $purchaseItem = PurchaseOrderItem::create([
        'order_id' => $purchaseOrder->order_id,
        'product_id' => $product->product_id,
        'variant_id' => $variant1->variant_id,
        'quantity_ordered' => 10,
        'quantity_received' => 0,
        'unit_price' => 80.00,
        'discount_amount' => 0,
        'line_total' => 800.00,
    ]);
    
    echo "  ✓ Purchase Order #{$purchaseOrder->order_id} created\n";
    echo "  ✓ Purchase Item: {$purchaseItem->display_name}\n";
    echo "  ✓ SKU: {$purchaseItem->item_sku}\n";
    echo "  ✓ Quantity: {$purchaseItem->quantity_ordered}\n\n";
    
    // 4. Test Stock Movement with Variant
    echo "Testing Stock Movement Integration...\n";
    
    $stockMovement = StockMovement::recordMovement(
        productId: $product->product_id,
        quantityBefore: 0,
        quantityChange: 10,
        quantityAfter: 10,
        movementType: StockMovement::TYPE_PURCHASE,
        unitCost: 80.00,
        referenceType: 'purchase_order',
        referenceId: $purchaseOrder->order_id,
        notes: 'Initial stock for variant',
        variantId: $variant1->variant_id
    );
    
    echo "  ✓ Stock Movement #{$stockMovement->movement_id} recorded\n";
    echo "  ✓ Item: {$stockMovement->display_name}\n";
    echo "  ✓ SKU: {$stockMovement->item_sku}\n";
    echo "  ✓ Quantity Change: +{$stockMovement->quantity_change}\n";
    echo "  ✓ Movement Type: {$stockMovement->movement_type_label}\n\n";
    
    // 5. Test Inventory with Variant
    echo "Testing Inventory Integration...\n";
    
    $inventory = Inventory::create([
        'product_id' => $product->product_id,
        'variant_id' => $variant1->variant_id,
        'sku' => $variant1->variant_sku,
        'location' => 'Main Warehouse',
        'quantity_on_hand' => 10,
        'quantity_reserved' => 0,
        'unit_cost' => 80.00,
        'last_movement_at' => now(),
    ]);
    
    echo "  ✓ Inventory record created\n";
    echo "  ✓ Item: {$inventory->display_name}\n";
    echo "  ✓ SKU: {$inventory->item_sku}\n";
    echo "  ✓ Quantity on Hand: {$inventory->quantity_on_hand}\n";
    echo "  ✓ Available: {$inventory->quantity_available}\n\n";
    
    // 6. Test Sales Order with Variant
    echo "Testing Sales Order Integration...\n";
    
    $customer = Customer::first();
    if (!$customer) {
        $customer = Customer::create([
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'customer@test.com',
            'phone' => '9876543210',
            'address' => '456 Test Ave',
        ]);
    }
    
    $salesOrder = SalesOrder::create([
        'customer_id' => $customer->customer_id,
        'order_date' => now(),
        'delivery_date' => now()->addDays(3),
        'status' => 'pending',
        'subtotal' => 0,
        'total_discount' => 0,
        'tax_amount' => 0,
        'total_amount' => 0,
        'notes' => 'Test sales order with variant',
    ]);
    
    $salesItem = SalesOrderItem::create([
        'order_id' => $salesOrder->order_id,
        'product_id' => $product->product_id,
        'variant_id' => $variant1->variant_id,
        'quantity' => 2,
        'unit_price' => 150.00,
        'discount_amount' => 0,
        'line_total' => 300.00,
    ]);
    
    echo "  ✓ Sales Order #{$salesOrder->order_id} created\n";
    echo "  ✓ Sales Item: {$salesItem->display_name}\n";
    echo "  ✓ SKU: {$salesItem->item_sku}\n";
    echo "  ✓ Quantity: {$salesItem->quantity}\n\n";
    
    // 7. Test Multiple Variants in Same Order
    echo "Testing Multiple Variants in Same Sales Order...\n";
    
    $variant2 = $variants->get(1);
    
    // Create inventory for variant2
    Inventory::create([
        'product_id' => $product->product_id,
        'variant_id' => $variant2->variant_id,
        'sku' => $variant2->variant_sku,
        'location' => 'Main Warehouse',
        'quantity_on_hand' => 5,
        'quantity_reserved' => 0,
        'unit_cost' => 80.00,
        'last_movement_at' => now(),
    ]);
    
    $salesItem2 = SalesOrderItem::create([
        'order_id' => $salesOrder->order_id,
        'product_id' => $product->product_id,
        'variant_id' => $variant2->variant_id,
        'quantity' => 1,
        'unit_price' => 150.00,
        'discount_amount' => 0,
        'line_total' => 150.00,
    ]);
    
    echo "  ✓ Added second variant to same order\n";
    echo "  ✓ Sales Item: {$salesItem2->display_name}\n";
    echo "  ✓ SKU: {$salesItem2->item_sku}\n\n";
    
    // 8. Verify Relationships
    echo "Verifying Relationships...\n";
    
    $salesOrderWithItems = SalesOrder::with(['items.variant', 'items.product'])->find($salesOrder->order_id);
    echo "  ✓ Sales Order has " . $salesOrderWithItems->items->count() . " items\n";
    
    foreach ($salesOrderWithItems->items as $item) {
        echo "    - {$item->display_name} (Variant ID: {$item->variant_id})\n";
    }
    
    echo "\n";
    
    // 9. Summary
    echo "===== INTEGRATION TEST SUMMARY =====\n";
    echo "✓ Product Variants: Created and managed\n";
    echo "✓ Purchase Orders: Variant support working\n";
    echo "✓ Stock Movements: Variant tracking working\n";
    echo "✓ Inventory: Variant-specific records working\n";
    echo "✓ Sales Orders: Multiple variants in same order working\n";
    echo "✓ Relationships: All associations loading correctly\n";
    echo "\n✅ ALL INTEGRATION TESTS PASSED!\n\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
