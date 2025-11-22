<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\InventoryAlert;
use Illuminate\Console\Command;

class CheckStockLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check-levels 
                            {--force : Force check even if recently checked}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all products stock levels and create alerts based on formula thresholds';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking stock levels for all products...');

        $products = Product::all();
        $alertsCreated = 0;
        $alertsUpdated = 0;
        $alertsResolved = 0;

        $progressBar = $this->output->createProgressBar($products->count());
        $progressBar->start();

        foreach ($products as $product) {
            try {
                $stockStatus = $product->getStockStatus();
                $available = $stockStatus['available'];
                $criticalLevel = $stockStatus['critical_level'];
                $reorderPoint = $stockStatus['reorder_point'];
                
                // Determine alert type and severity
                $alertType = null;
                $severity = null;
                $message = null;

                if ($available <= 0) {
                    $alertType = 'out_of_stock';
                    $severity = 'critical';
                    $message = "Product '{$product->product_name}' is OUT OF STOCK. Current quantity: {$available}";
                } elseif ($available <= $criticalLevel) {
                    $alertType = 'critical_stock';
                    $severity = 'urgent';
                    $message = "Product '{$product->product_name}' has CRITICAL STOCK. Current: {$available}, Critical Level: {$criticalLevel} (based on demand × lead time)";
                } elseif ($available <= $reorderPoint) {
                    $alertType = 'low_stock';
                    $severity = 'warning';
                    $message = "Product '{$product->product_name}' has LOW STOCK. Current: {$available}, Reorder Point: {$reorderPoint} (includes safety stock)";
                }

                // Create or update alert if needed
                if ($alertType) {
                    $existingAlert = InventoryAlert::where('product_id', $product->product_id)
                        ->where('alert_type', $alertType)
                        ->where('status', 'active')
                        ->first();

                    if ($existingAlert) {
                        // Update existing alert
                        $existingAlert->update([
                            'message' => $message,
                            'alert_data' => [
                                'current_quantity' => $available,
                                'critical_level' => $criticalLevel,
                                'reorder_point' => $reorderPoint,
                                'safety_stock' => $stockStatus['safety_stock'],
                                'average_daily_demand' => $stockStatus['average_daily_demand'],
                                'checked_at' => now(),
                            ],
                        ]);
                        $alertsUpdated++;
                    } else {
                        // Create new alert
                        InventoryAlert::create([
                            'product_id' => $product->product_id,
                            'alert_type' => $alertType,
                            'severity' => $severity,
                            'message' => $message,
                            'alert_data' => [
                                'current_quantity' => $available,
                                'critical_level' => $criticalLevel,
                                'reorder_point' => $reorderPoint,
                                'safety_stock' => $stockStatus['safety_stock'],
                                'average_daily_demand' => $stockStatus['average_daily_demand'],
                                'checked_at' => now(),
                            ],
                            'status' => 'active',
                            'auto_dismiss' => false,
                        ]);
                        $alertsCreated++;
                    }
                } else {
                    // Product is in good stock - resolve any existing alerts
                    $resolved = InventoryAlert::where('product_id', $product->product_id)
                        ->where('status', 'active')
                        ->whereIn('alert_type', ['out_of_stock', 'critical_stock', 'low_stock'])
                        ->update([
                            'status' => 'resolved',
                            'resolved_at' => now(),
                            'resolution_notes' => 'Stock level returned to normal (above reorder point)',
                        ]);
                    $alertsResolved += $resolved;
                }
            } catch (\Exception $e) {
                $this->error("Error processing product {$product->product_id}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("✓ Stock check complete!");
        $this->info("  - Products checked: {$products->count()}");
        $this->info("  - New alerts created: {$alertsCreated}");
        $this->info("  - Alerts updated: {$alertsUpdated}");
        $this->info("  - Alerts resolved: {$alertsResolved}");

        return Command::SUCCESS;
    }
}
