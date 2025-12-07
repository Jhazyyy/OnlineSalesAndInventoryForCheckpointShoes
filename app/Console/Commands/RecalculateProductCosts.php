<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\ProductCostingService;

class RecalculateProductCosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'costs:recalculate {--force : Force recalculation even for products with existing costs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate product costs from purchase history using weighted average';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $costingService = app(ProductCostingService::class);
        $force = $this->option('force');
        
        $this->info('Starting product cost recalculation...');
        
        // Get products that use weighted_average or automatic costing
        $query = Product::whereIn('cost_calculation_method', ['weighted_average', 'automatic', 'latest_purchase']);
        
        if (!$force) {
            // Only recalculate products with no cost or zero cost
            $query->where(function($q) {
                $q->whereNull('total_cost')
                  ->orWhere('total_cost', 0);
            });
        }
        
        $products = $query->get();
        
        $this->info("Found {$products->count()} products to recalculate.");
        
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();
        
        $updated = 0;
        $skipped = 0;
        $failed = 0;
        
        foreach ($products as $product) {
            try {
                $wasUpdated = $costingService->updateCostFromInventory($product);
                
                if ($wasUpdated) {
                    $updated++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $failed++;
                $this->error("\nFailed to update product {$product->product_id}: {$e->getMessage()}");
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        
        $this->newLine(2);
        $this->info("Cost recalculation complete!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Updated', $updated],
                ['Skipped (no inventory data)', $skipped],
                ['Failed', $failed],
                ['Total', $products->count()],
            ]
        );
        
        return Command::SUCCESS;
    }
}
