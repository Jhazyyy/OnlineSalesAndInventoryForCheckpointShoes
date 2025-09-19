<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InventoryThresholdService;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckInventoryThresholds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-thresholds 
                           {--force : Force check even during non-business hours}
                           {--product= : Check specific product by ID}
                           {--category= : Check products in specific category}
                           {--silent : Run silently without output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check inventory thresholds and create alerts for products that need attention';

    /**
     * The inventory threshold service instance.
     *
     * @var InventoryThresholdService
     */
    protected $thresholdService;

    /**
     * Create a new command instance.
     *
     * @param InventoryThresholdService $thresholdService
     * @return void
     */
    public function __construct(InventoryThresholdService $thresholdService)
    {
        parent::__construct();
        $this->thresholdService = $thresholdService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = microtime(true);
        
        try {
            // Check if we should run during business hours only
            if (!$this->option('force') && !$this->isBusinessHours()) {
                $this->info('Threshold check skipped - outside business hours. Use --force to override.');
                return Command::SUCCESS;
            }

            if (!$this->option('silent')) {
                $this->info('Starting inventory threshold check...');
            }

            // Determine which products to check
            $products = $this->getProductsToCheck();
            
            if ($products->isEmpty()) {
                $this->info('No products found to check.');
                return Command::SUCCESS;
            }

            // Initialize counters
            $checkedCount = 0;
            $alertsCreated = 0;
            $alertsResolved = 0;
            $errors = [];

            // Check thresholds
            if ($this->option('product') || $this->option('category')) {
                // Check specific products
                foreach ($products as $product) {
                    try {
                        $alerts = $this->thresholdService->checkProductThresholds($product);
                        $checkedCount++;
                        
                        // Count new alerts created
                        $alertsCreated += $alerts->count();
                        
                        // Check for resolved alerts
                        $alertsResolved += $this->thresholdService->resolveObsoleteAlerts($product);
                        
                        if (!$this->option('silent')) {
                            $this->line("✓ Checked: {$product->product_name}");
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Error checking {$product->product_name}: " . $e->getMessage();
                        Log::error("Threshold check failed for product {$product->id}: " . $e->getMessage());
                    }
                }
            } else {
                // Check all products with thresholds
                try {
                    $results = $this->thresholdService->checkAllThresholds();
                    
                    $checkedCount = $results['products_checked'] ?? 0;
                    $alertsCreated = $results['alerts_created'] ?? 0;
                    $alertsResolved = $results['alerts_resolved'] ?? 0;
                    
                    if (!$this->option('silent')) {
                        $this->info("✓ Bulk threshold check completed");
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error in bulk threshold check: " . $e->getMessage();
                    Log::error('Bulk threshold check failed: ' . $e->getMessage());
                }
            }

            // Calculate execution time
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            // Display results
            if (!$this->option('silent')) {
                $this->info('');
                $this->info('=== Threshold Check Results ===');
                $this->info("Products checked: {$checkedCount}");
                $this->info("Alerts created: {$alertsCreated}");
                $this->info("Alerts resolved: {$alertsResolved}");
                $this->info("Execution time: {$executionTime}ms");
                
                if (!empty($errors)) {
                    $this->error('');
                    $this->error('Errors encountered:');
                    foreach ($errors as $error) {
                        $this->error("- {$error}");
                    }
                }
            }

            // Log the results
            Log::info('Inventory threshold check completed', [
                'products_checked' => $checkedCount,
                'alerts_created' => $alertsCreated,
                'alerts_resolved' => $alertsResolved,
                'execution_time_ms' => $executionTime,
                'errors_count' => count($errors)
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Fatal error during threshold check: ' . $e->getMessage());
            Log::error('Fatal error in threshold check command: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Get products to check based on command options.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getProductsToCheck()
    {
        $query = Product::query();

        if ($this->option('product')) {
            return $query->where('id', $this->option('product'))->get();
        }

        // Category filtering is not implemented yet
        if ($this->option('category')) {
            $this->warn('Category filtering is not yet implemented.');
        }

        // Get products with thresholds set or all products for general check
        $query->where(function ($q) {
            $q->whereNotNull('reorder_level')
              ->orWhereNotNull('critical_level')
              ->orWhereNotNull('ceiling_level')
              ->orWhereNotNull('floor_level');
        });

        return $query->get();
    }

    /**
     * Check if current time is within business hours.
     *
     * @return bool
     */
    private function isBusinessHours()
    {
        $now = Carbon::now();
        
        // Skip weekends
        if ($now->isWeekend()) {
            return false;
        }
        
        // Business hours: 6 AM to 10 PM
        $startHour = 6;
        $endHour = 22;
        
        return $now->hour >= $startHour && $now->hour < $endHour;
    }
}
