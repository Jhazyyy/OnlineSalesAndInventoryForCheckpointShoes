<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class SetTestReorderLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:set-reorder-level';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set reorder levels for test products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $product = Product::first();
        
        if ($product) {
            $product->reorder_level = 20;
            $product->save();
            
            $this->info("Set reorder level to 20 for: {$product->product_name}");
        } else {
            $this->error("No products found.");
        }

        return Command::SUCCESS;
    }
}
