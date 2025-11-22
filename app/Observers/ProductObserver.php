<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Notification;
use App\Models\InventoryAlert;
use App\Services\InventoryService;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     * Automatically create an inventory record when a product is created.
     */
    public function created(Product $product): void
    {
        // Create an initial inventory record for the product
        InventoryService::getOrCreate(
            productId: $product->product_id,
            propertyId: null,
            location: null
        );
    }

    /**
     * Handle the Product "updated" event.
     * Check stock levels and create notifications when thresholds are crossed.
     */
    public function updated(Product $product): void
    {
        // Check if quantity was changed
        if ($product->isDirty('quantity')) {
            $oldQuantity = $product->getOriginal('quantity');
            $newQuantity = $product->quantity;
            
            // Only check if quantity decreased or if it's a significant change
            if ($newQuantity != $oldQuantity) {
                $this->checkStockLevelsAndNotify($product, $oldQuantity, $newQuantity);
            }
        }
    }

    /**
     * Check stock levels and create notifications based on thresholds.
     * 
     * Thresholds (as percentage of initial/reference stock):
     * - Reorder Level: 20% (stock is getting low, time to reorder)
     * - Low Stock: 10% (stock is low, urgent attention needed)
     * - Critical: 5% (stock is critically low, immediate action required)
     * - Out of Stock: 0 (stock depleted)
     */
    protected function checkStockLevelsAndNotify(Product $product, int $oldQuantity, int $newQuantity): void
    {
        // Calculate thresholds based on reorder_level or use standard percentages
        $reorderLevel = $product->reorder_level ?? max(20, (int)($newQuantity * 0.2)); // 20% or at least 20 units
        $lowStockLevel = max(10, (int)($reorderLevel * 0.5)); // 10 units or 50% of reorder level
        $criticalLevel = max(5, (int)($lowStockLevel * 0.5)); // 5 units or 50% of low stock level
        
        // Determine the stock status
        $status = $this->determineStockStatus($newQuantity, $reorderLevel, $lowStockLevel, $criticalLevel);
        $oldStatus = $this->determineStockStatus($oldQuantity, $reorderLevel, $lowStockLevel, $criticalLevel);
        
        // Only create notification if status changed to a worse condition
        if ($status !== $oldStatus && $this->shouldNotify($status, $oldStatus)) {
            $this->createStockNotification($product, $status, $newQuantity, $reorderLevel, $lowStockLevel, $criticalLevel);
        }
    }

    /**
     * Determine the stock status based on quantity and thresholds.
     */
    protected function determineStockStatus(int $quantity, int $reorderLevel, int $lowStockLevel, int $criticalLevel): string
    {
        if ($quantity <= 0) {
            return 'out_of_stock';
        } elseif ($quantity <= $criticalLevel) {
            return 'critical_stock';
        } elseif ($quantity <= $lowStockLevel) {
            return 'low_stock';
        } elseif ($quantity <= $reorderLevel) {
            return 'reorder_needed';
        }
        
        return 'normal';
    }

    /**
     * Check if we should notify based on status change.
     * Only notify when moving to a worse condition.
     */
    protected function shouldNotify(string $newStatus, string $oldStatus): bool
    {
        $statusHierarchy = [
            'normal' => 0,
            'reorder_needed' => 1,
            'low_stock' => 2,
            'critical_stock' => 3,
            'out_of_stock' => 4,
        ];
        
        $newLevel = $statusHierarchy[$newStatus] ?? 0;
        $oldLevel = $statusHierarchy[$oldStatus] ?? 0;
        
        // Only notify if status got worse
        return $newLevel > $oldLevel;
    }

    /**
     * Create stock notification and inventory alert.
     */
    protected function createStockNotification(
        Product $product,
        string $status,
        int $currentQuantity,
        int $reorderLevel,
        int $lowStockLevel,
        int $criticalLevel
    ): void {
        // Prepare notification data based on status
        $notificationData = $this->getNotificationData($product, $status, $currentQuantity, $reorderLevel, $lowStockLevel, $criticalLevel);
        
        if (!$notificationData) {
            return;
        }

        // Check if similar notification already exists (avoid duplicates)
        $existingNotification = Notification::where('type', $notificationData['type'])
            ->where('title', $notificationData['title'])
            ->whereNull('read_at')
            ->where('created_at', '>', now()->subHours(24)) // Only check last 24 hours
            ->first();

        if (!$existingNotification) {
            // Create notification
            Notification::create($notificationData);
        }

        // Check if similar alert already exists
        $existingAlert = InventoryAlert::where('product_id', $product->product_id)
            ->where('alert_type', $status)
            ->where('status', 'active')
            ->first();

        if (!$existingAlert) {
            // Create inventory alert
            InventoryAlert::create([
                'product_id' => $product->product_id,
                'alert_type' => $status,
                'severity' => $this->getSeverity($status),
                'message' => $notificationData['message'],
                'alert_data' => [
                    'current_quantity' => $currentQuantity,
                    'reorder_level' => $reorderLevel,
                    'low_stock_level' => $lowStockLevel,
                    'critical_level' => $criticalLevel,
                    'product_name' => $product->product_name,
                    'sku' => $product->sku,
                ],
                'status' => 'active',
                'auto_dismiss' => false,
            ]);
        }
    }

    /**
     * Get notification data based on stock status.
     */
    protected function getNotificationData(
        Product $product,
        string $status,
        int $currentQuantity,
        int $reorderLevel,
        int $lowStockLevel,
        int $criticalLevel
    ): ?array {
        $productName = $product->product_name;
        $sku = $product->sku ?: 'N/A';
        
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

    /**
     * Get severity level for inventory alert.
     */
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
}
