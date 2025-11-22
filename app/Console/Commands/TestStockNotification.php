<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class TestStockNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:stock-notification {product_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test stock notification system by updating product quantity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productId = $this->argument('product_id');
        
        if ($productId) {
            $product = Product::find($productId);
            
            if (!$product) {
                $this->error("Product with ID {$productId} not found.");
                return 1;
            }
            
            $this->testProduct($product);
        } else {
            // Find a product with stock > 20 to test
            $product = Product::where('quantity', '>', 20)->first();
            
            if (!$product) {
                $this->error("No products found with quantity > 20 to test.");
                return 1;
            }
            
            $this->info("Testing with product: {$product->product_name} (ID: {$product->product_id})");
            $this->testProduct($product);
        }
        
        return 0;
    }
    
    protected function testProduct(Product $product)
    {
        $originalQuantity = $product->quantity;
        $this->info("Original quantity: {$originalQuantity}");
        
        // Test different stock levels
        $testLevels = [
            ['quantity' => 18, 'expected' => 'Reorder Needed'],
            ['quantity' => 9, 'expected' => 'Low Stock'],
            ['quantity' => 4, 'expected' => 'Critical'],
            ['quantity' => 0, 'expected' => 'Out of Stock'],
        ];
        
        foreach ($testLevels as $test) {
            $this->info("\n--- Testing: {$test['expected']} ---");
            $this->info("Setting quantity to: {$test['quantity']}");
            
            $product->quantity = $test['quantity'];
            $product->save();
            
            $this->info("✓ Product updated. Check notifications!");
            sleep(1); // Wait a moment between updates
        }
        
        // Restore original quantity
        $this->info("\n--- Restoring original quantity ---");
        $product->quantity = $originalQuantity;
        $product->save();
        
        $this->info("✓ Test completed! Check your notifications at /notifications-list");
        $this->info("✓ Check inventory alerts at /inventory/thresholds/alerts");
    }
}
