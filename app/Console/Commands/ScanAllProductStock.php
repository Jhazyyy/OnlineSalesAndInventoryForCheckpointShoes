<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Notification;
use App\Models\InventoryAlert;
use Illuminate\Console\Command;

class ScanAllProductStock extends Command
{
    protected $signature = 'stock:scan-all {--force : Force notification creation even if duplicates exist}';
    protected $description = 'Scan all products and create notifications for existing low stock/out of stock items';

    public function handle()
    {
        $this->info('=== SCANNING ALL PRODUCTS FOR STOCK ALERTS ===');
        $this->newLine();

        $force = $this->option('force');
        
        $products = Product::all();
        $totalProducts = $products->count();
        $notificationsCreated = 0;
        $alertsCreated = 0;
        $skipped = 0;

        $this->info("Total products to scan: {$totalProducts}");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($totalProducts);
        $progressBar->start();

        foreach ($products as $product) {
            $progressBar->advance();

            $quantity = $product->quantity;
            
            // Skip if quantity is very high (no alert needed)
            if ($quantity > 100) {
                continue;
            }

            // Calculate thresholds based on initial quantity
            // For existing products, we'll use current quantity as baseline
            // In reality, you might want to store initial_quantity in products table
            $baseQuantity = max($quantity, 20); // Assume at least 20 was initial stock
            
            $reorderLevel = max(1, (int)($baseQuantity * 0.20)); // 20%
            $lowStockLevel = max(1, (int)($baseQuantity * 0.10)); // 10%
            $criticalLevel = max(1, (int)($baseQuantity * 0.05)); // 5%

            // Determine status
            $status = null;
            if ($quantity <= 0) {
                $status = 'out_of_stock';
            } elseif ($quantity <= $criticalLevel) {
                $status = 'critical_stock';
            } elseif ($quantity <= $lowStockLevel) {
                $status = 'low_stock';
            } elseif ($quantity <= $reorderLevel) {
                $status = 'reorder_needed';
            }

            // Skip if no alert needed
            if (!$status) {
                continue;
            }

            // Check if notification already exists (unless force)
            if (!$force) {
                $existingNotification = Notification::where('type', 'inventory.' . $status)
                    ->where('message', 'LIKE', "%{$product->sku}%")
                    ->where('created_at', '>', now()->subHours(24))
                    ->exists();

                if ($existingNotification) {
                    $skipped++;
                    continue;
                }
            }

            // Create notification and alert
            try {
                $notificationData = $this->getNotificationData($product, $status, $quantity, $reorderLevel, $lowStockLevel, $criticalLevel);
                
                if ($notificationData) {
                    Notification::create($notificationData);
                    $notificationsCreated++;

                    InventoryAlert::create([
                        'product_id' => $product->product_id,
                        'alert_type' => $status,
                        'severity' => $this->getSeverity($status),
                        'message' => $notificationData['message'],
                        'alert_data' => json_encode([
                            'current_quantity' => $quantity,
                            'threshold_quantity' => $this->getThreshold($status, $reorderLevel, $lowStockLevel, $criticalLevel),
                            'reorder_level' => $reorderLevel,
                            'low_stock_level' => $lowStockLevel,
                            'critical_level' => $criticalLevel,
                        ]),
                        'status' => 'active',
                    ]);
                    $alertsCreated++;
                }
            } catch (\Exception $e) {
                $this->error("\nError creating notification for product {$product->product_id}: " . $e->getMessage());
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('=== SCAN COMPLETE ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Products Scanned', $totalProducts],
                ['Notifications Created', $notificationsCreated],
                ['Inventory Alerts Created', $alertsCreated],
                ['Skipped (duplicates)', $skipped],
            ]
        );

        $this->newLine();
        $this->info('✓ You can view notifications at: /notifications-list');
        $this->info('✓ View inventory alerts at: /inventory/thresholds/alerts');

        if ($skipped > 0) {
            $this->newLine();
            $this->comment("Tip: Use --force flag to create notifications even for products with recent alerts");
        }

        return Command::SUCCESS;
    }

    protected function getNotificationData(Product $product, string $status, int $currentQuantity, int $reorderLevel, int $lowStockLevel, int $criticalLevel): ?array
    {
        $productName = $product->product_name;
        $sku = $product->sku;

        switch ($status) {
            case 'out_of_stock':
                return [
                    'type' => 'inventory.out_of_stock',
                    'title' => 'Product Out of Stock',
                    'message' => "Product '{$productName}' (SKU: {$sku}) is OUT OF STOCK. Immediate restocking required!",
                    'level' => 'danger',
                    'link' => url('/inventory/products/' . $product->product_id),
                ];
                
            case 'critical_stock':
                return [
                    'type' => 'inventory.critical_stock',
                    'title' => 'Critical Stock Level',
                    'message' => "Product '{$productName}' (SKU: {$sku}) has reached CRITICAL level with only {$currentQuantity} units remaining (Critical threshold: {$criticalLevel}). Urgent action required!",
                    'level' => 'danger',
                    'link' => url('/inventory/products/' . $product->product_id),
                ];
                
            case 'low_stock':
                return [
                    'type' => 'inventory.low_stock',
                    'title' => 'Low Stock Alert',
                    'message' => "Product '{$productName}' (SKU: {$sku}) is running LOW with {$currentQuantity} units remaining (Low stock threshold: {$lowStockLevel}). Please reorder soon.",
                    'level' => 'warning',
                    'link' => url('/inventory/products/' . $product->product_id),
                ];
                
            case 'reorder_needed':
                return [
                    'type' => 'inventory.reorder_needed',
                    'title' => 'Reorder Recommended',
                    'message' => "Product '{$productName}' (SKU: {$sku}) has {$currentQuantity} units remaining. Reorder level ({$reorderLevel}) reached. Consider placing a purchase order.",
                    'level' => 'info',
                    'link' => url('/inventory/products/' . $product->product_id),
                ];
                
            default:
                return null;
        }
    }

    protected function getSeverity(string $status): string
    {
        return match($status) {
            'out_of_stock' => 'urgent',
            'critical_stock' => 'critical',
            'low_stock' => 'warning',
            'reorder_needed' => 'info',
            default => 'info',
        };
    }

    protected function getThreshold(string $status, int $reorderLevel, int $lowStockLevel, int $criticalLevel): ?int
    {
        return match($status) {
            'out_of_stock' => 0,
            'critical_stock' => $criticalLevel,
            'low_stock' => $lowStockLevel,
            'reorder_needed' => $reorderLevel,
            default => null,
        };
    }
}
