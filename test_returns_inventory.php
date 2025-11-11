<?php

/**
 * Test script to verify that approving a return properly updates inventory
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Returns;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

echo "=== Testing Sales Return Inventory Update ===\n\n";

try {
    DB::beginTransaction();

    // Find a product to test with
    $product = Product::first();
    
    if (!$product) {
        echo "❌ No products found in the database.\n";
        exit(1);
    }

    echo "📦 Testing with Product: {$product->product_name} (ID: {$product->product_id})\n";
    echo "   Current Product Quantity: {$product->quantity}\n";

    // Get current inventory
    $inventory = Inventory::where('product_id', $product->product_id)->first();
    $initialInventoryQty = $inventory ? $inventory->quantity_on_hand : 0;
    echo "   Current Inventory Quantity: {$initialInventoryQty}\n\n";

    // Create a pending return
    echo "📝 Creating a pending return...\n";
    $return = Returns::create([
        'product_id' => $product->product_id,
        'quantity' => 5,
        'price' => 100.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Test return for inventory verification',
    ]);

    echo "✅ Return created (ID: {$return->return_id})\n";
    echo "   Return Quantity: {$return->quantity}\n";
    echo "   Return Status: {$return->return_status}\n\n";

    // Refresh product to check that inventory hasn't changed yet
    $product->refresh();
    $inventory = $inventory ? $inventory->fresh() : Inventory::where('product_id', $product->product_id)->first();
    $afterCreateProductQty = $product->quantity;
    $afterCreateInventoryQty = $inventory ? $inventory->quantity_on_hand : 0;

    echo "📊 After creating pending return:\n";
    echo "   Product Quantity: {$afterCreateProductQty} (should be unchanged)\n";
    echo "   Inventory Quantity: {$afterCreateInventoryQty} (should be unchanged)\n\n";

    // Now approve the return
    echo "✅ Approving the return...\n";
    $approved = $return->approve();

    if (!$approved) {
        echo "❌ Failed to approve return!\n";
        DB::rollBack();
        exit(1);
    }

    echo "✅ Return approved successfully!\n\n";

    // Refresh and check inventory updates
    $product->refresh();
    $inventory = $inventory ? $inventory->fresh() : Inventory::where('product_id', $product->product_id)->first();
    $afterApproveProductQty = $product->quantity;
    $afterApproveInventoryQty = $inventory ? $inventory->quantity_on_hand : 0;

    echo "📊 After approving return:\n";
    echo "   Product Quantity: {$afterApproveProductQty}\n";
    echo "   Expected: " . ($afterCreateProductQty + $return->quantity) . "\n";
    echo "   Inventory Quantity: {$afterApproveInventoryQty}\n";
    echo "   Expected: " . ($afterCreateInventoryQty + $return->quantity) . "\n\n";

    // Check stock movement was recorded
    $stockMovement = StockMovement::where('reference_type', 'sales_return')
        ->where('reference_id', $return->return_id)
        ->first();

    if ($stockMovement) {
        echo "📋 Stock Movement Record:\n";
        echo "   Movement Type: {$stockMovement->movement_type}\n";
        echo "   Quantity Change: {$stockMovement->quantity_change}\n";
        echo "   Quantity Before: {$stockMovement->quantity_before}\n";
        echo "   Quantity After: {$stockMovement->quantity_after}\n";
        echo "   Unit Cost: {$stockMovement->unit_cost}\n";
        echo "   Status: {$stockMovement->status}\n\n";
    } else {
        echo "⚠️ No stock movement record found!\n\n";
    }

    // Verify the updates
    $productQtyIncreased = ($afterApproveProductQty == $afterCreateProductQty + $return->quantity);
    $inventoryQtyIncreased = ($afterApproveInventoryQty == $afterCreateInventoryQty + $return->quantity);

    echo "=== VERIFICATION RESULTS ===\n";
    echo ($productQtyIncreased ? "✅" : "❌") . " Product quantity increased correctly\n";
    echo ($inventoryQtyIncreased ? "✅" : "❌") . " Inventory quantity increased correctly\n";
    echo ($stockMovement ? "✅" : "❌") . " Stock movement recorded\n";
    echo ($return->isApproved() ? "✅" : "❌") . " Return status is approved\n\n";

    if ($productQtyIncreased && $inventoryQtyIncreased && $stockMovement && $return->isApproved()) {
        echo "🎉 All tests passed! Sales returns are properly updating inventory.\n";
    } else {
        echo "⚠️ Some tests failed. Please review the output above.\n";
    }

    // Rollback the transaction to keep database clean
    DB::rollBack();
    echo "\n✅ Database rolled back (test data removed)\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
