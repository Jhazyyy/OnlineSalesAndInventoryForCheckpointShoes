<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\ProductCostingService;

class DemoProductCosting extends Command
{
    protected $signature = 'demo:product-costing';
    protected $description = 'Add sample costing data to products for demonstration';

    public function handle()
    {
        $this->info('========================================');
        $this->info('Product Costing Module - Demo Setup');
        $this->info('========================================');
        $this->newLine();

        $costingService = new ProductCostingService();
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->warn('No products found. Please add products first.');
            return 1;
        }

        $this->info("Found {$products->count()} product(s). Adding sample costing data...");
        $this->newLine();

        $costingScenarios = [
            [
                'raw_material_cost' => 500.00,
                'labor_cost' => 150.00,
                'overhead_cost' => 100.00,
                'shipping_cost_per_unit' => 50.00,
                'tax_amount_per_unit' => 30.00,
                'handling_cost' => 20.00,
                'price' => 1200.00,
                'cost_notes' => 'Good profit margin - 41% (Demo data)',
            ],
            [
                'raw_material_cost' => 700.00,
                'labor_cost' => 200.00,
                'overhead_cost' => 150.00,
                'shipping_cost_per_unit' => 80.00,
                'tax_amount_per_unit' => 50.00,
                'handling_cost' => 30.00,
                'price' => 1500.00,
                'cost_notes' => 'Low profit margin - 22% (Demo data)',
            ],
            [
                'raw_material_cost' => 800.00,
                'labor_cost' => 250.00,
                'overhead_cost' => 200.00,
                'shipping_cost_per_unit' => 100.00,
                'tax_amount_per_unit' => 60.00,
                'handling_cost' => 40.00,
                'price' => 1300.00,
                'cost_notes' => 'Negative profit margin - losing money! (Demo data)',
            ],
        ];

        foreach ($products as $index => $product) {
            $scenario = $costingScenarios[$index % count($costingScenarios)];
            
            $this->info("Processing Product {$product->product_id}: {$product->product_name}");
            
            try {
                $updatedProduct = $costingService->updateProductCosting($product, $scenario);
                
                $this->line("  ✓ Costing data added:");
                $this->line("    - Total Cost: ₱" . number_format($updatedProduct->total_cost ?? 0, 2));
                $this->line("    - Selling Price: ₱" . number_format($updatedProduct->price ?? 0, 2));
                $this->line("    - Profit: ₱" . number_format($updatedProduct->profit_amount ?? 0, 2));
                $this->line("    - Margin: " . number_format($updatedProduct->profit_margin ?? 0, 2) . "%");
                
                if (($updatedProduct->profit_margin ?? 0) < 0) {
                    $this->warn("    ⚠ WARNING: Negative margin - losing money!");
                } elseif (($updatedProduct->profit_margin ?? 0) < 20) {
                    $this->warn("    ⚠ WARNING: Low margin - below 20%");
                } else {
                    $this->info("    ✓ Healthy profit margin");
                }
                
                $this->newLine();
            } catch (\Exception $e) {
                $this->error("  ✗ Error: " . $e->getMessage());
                $this->newLine();
            }
        }

        $this->info('========================================');
        $this->info('Demo Setup Complete!');
        $this->info('========================================');
        $this->newLine();

        $stats = $costingService->getCostingStatistics();
        $this->info('Summary:');
        $this->line("- Total Products: {$stats['total_products']}");
        $this->line("- Products with Costing: {$stats['products_with_costing']}");
        $this->line("- Completion: {$stats['costing_completion_percentage']}%");
        $this->line("- Average Profit Margin: " . number_format($stats['average_profit_margin'], 2) . "%");
        $this->line("- Low Margin Products: {$stats['low_margin_products']}");
        $this->line("- Negative Margin Products: {$stats['negative_margin_products']}");
        $this->newLine();

        $this->info('Now you can:');
        $this->line('1. Visit: /inventory/product-costing');
        $this->line('2. View the dashboard with statistics');
        $this->line('3. See products categorized by margin');
        $this->line('4. Edit product costing');
        $this->line('5. View low margin products');
        $this->line('6. View negative margin products');
        $this->newLine();

        $this->info('Enjoy exploring the Product Costing Module! 🎉');

        return 0;
    }
}
