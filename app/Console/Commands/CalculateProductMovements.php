<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductMovementService;

class CalculateProductMovements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:calculate-movements {--days=90 : Number of days to analyze}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate product movement categories (fast/slow/non-moving) based on sales data';

    protected ProductMovementService $movementService;

    /**
     * Create a new command instance.
     */
    public function __construct(ProductMovementService $movementService)
    {
        parent::__construct();
        $this->movementService = $movementService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        
        $this->info("Calculating product movements for the last {$days} days...");
        $this->newLine();
        
        $stats = $this->movementService->calculateAllProductMovements($days);
        
        $this->info("✓ Movement calculation completed!");
        $this->newLine();
        
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Products', $stats['total_products']],
                ['Fast Moving', $stats['fast_moving']],
                ['Slow Moving', $stats['slow_moving']],
                ['Non-Moving', $stats['non_moving']],
                ['Updated', $stats['updated']],
            ]
        );
        
        $this->newLine();
        $this->info('You can view the results at: /inventory/product-movement');
        
        return Command::SUCCESS;
    }
}

