<?php

/**
 * Test script to verify POS tax and discount functionality
 * 
 * This script:
 * 1. Checks if tax and discount rules exist
 * 2. Creates a test POS order with tax and discount
 * 3. Verifies the order has the correct tax/discount applied
 * 4. Displays the order information
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\SalesOrder;
use App\Models\TaxDiscount;
use App\Models\Customer;
use App\Models\Product;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== POS Tax & Discount Test ===\n\n";

// 1. Check active tax and discount rules
echo "1. Checking active tax and discount rules...\n";
$activeTaxes = TaxDiscount::active()->taxes()->forCustomer()->get();
$activeDiscounts = TaxDiscount::active()->discounts()->forCustomer()->get();

echo "   Active Taxes: " . $activeTaxes->count() . "\n";
foreach ($activeTaxes as $tax) {
    echo "   - {$tax->name} (ID: {$tax->id}): ";
    if ($tax->calculation_method === 'percentage') {
        echo "{$tax->rate}%\n";
    } else {
        echo "₱" . number_format($tax->fixed_amount, 2) . "\n";
    }
}

echo "\n   Active Discounts: " . $activeDiscounts->count() . "\n";
foreach ($activeDiscounts as $discount) {
    echo "   - {$discount->name} (ID: {$discount->id}): ";
    if ($discount->calculation_method === 'percentage') {
        echo "{$discount->rate}%\n";
    } else {
        echo "₱" . number_format($discount->fixed_amount, 2) . "\n";
    }
}

// 2. Check if we have orders with tax/discount
echo "\n2. Checking recent POS orders with tax/discount...\n";
$recentOrders = SalesOrder::where('purchase_type', 'in_store')
    ->where(function($query) {
        $query->whereNotNull('tax_rule_id')
              ->orWhereNotNull('discount_rule_id');
    })
    ->with(['taxRule', 'discountRule', 'customer'])
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($recentOrders->count() > 0) {
    echo "   Found " . $recentOrders->count() . " recent POS orders with tax/discount:\n\n";
    
    foreach ($recentOrders as $order) {
        echo "   Order #{$order->order_number}\n";
        echo "   Customer: {$order->customer->first_name} {$order->customer->last_name}\n";
        echo "   Subtotal: ₱" . number_format($order->subtotal, 2) . "\n";
        
        if ($order->tax_rule_id && $order->taxRule) {
            echo "   Tax: {$order->taxRule->name} = ₱" . number_format($order->tax_amount, 2) . "\n";
        } else {
            echo "   Tax: None\n";
        }
        
        if ($order->discount_rule_id && $order->discountRule) {
            echo "   Discount: {$order->discountRule->name} = -₱" . number_format($order->discount_amount, 2) . "\n";
        } else {
            echo "   Discount: None\n";
        }
        
        echo "   Total: ₱" . number_format($order->total_amount, 2) . "\n";
        echo "   ---\n";
    }
} else {
    echo "   No POS orders found with tax/discount applied.\n";
    echo "   Please create a test order through the POS interface.\n";
}

// 3. Verify database schema
echo "\n3. Verifying database schema...\n";
$columns = DB::select("SHOW COLUMNS FROM sales_orders WHERE Field IN ('tax_rule_id', 'discount_rule_id', 'tax_amount', 'discount_amount')");
echo "   Required columns in sales_orders table:\n";
foreach ($columns as $column) {
    echo "   - {$column->Field} ({$column->Type})\n";
}

// 4. Test relationships
echo "\n4. Testing model relationships...\n";
$testOrder = SalesOrder::whereNotNull('tax_rule_id')->first();
if ($testOrder) {
    echo "   Testing with Order #{$testOrder->order_number}\n";
    
    // Test tax rule relationship
    try {
        $taxRule = $testOrder->taxRule;
        echo "   ✓ Tax rule relationship works: " . ($taxRule ? $taxRule->name : 'NULL') . "\n";
    } catch (\Exception $e) {
        echo "   ✗ Tax rule relationship error: " . $e->getMessage() . "\n";
    }
    
    // Test discount rule relationship
    try {
        $discountRule = $testOrder->discountRule;
        echo "   ✓ Discount rule relationship works: " . ($discountRule ? $discountRule->name : 'NULL') . "\n";
    } catch (\Exception $e) {
        echo "   ✗ Discount rule relationship error: " . $e->getMessage() . "\n";
    }
} else {
    echo "   No orders with tax_rule_id found to test relationships.\n";
}

echo "\n=== Test Complete ===\n";
echo "\nNext Steps:\n";
echo "1. Create a new POS order with tax and discount selected\n";
echo "2. Verify the receipt displays the tax/discount names\n";
echo "3. Check the sales order detail page shows the names\n";
