<?php

use App\Models\Product;
use App\Models\Inventory;
use App\Services\StockService;
use App\Services\InventoryService;
use App\Services\InventoryThresholdService;

it('syncs inventory and product on stock adjustment', function () {
    // Create a product and set an initial quantity and thresholds
    $product = Product::factory()->create();
    $product->forceFill([
        'quantity' => 10,
        'reorder_level' => 5,
        'critical_level' => 2,
        'threshold_alerts_enabled' => true,
    ])->save();

    // Perform stock adjustment to set new absolute quantity to 3
    $service = new StockService();
    $result = $service->createStockAdjustment([
        'product_id' => $product->product_id,
        'new_quantity' => 3,
        'reason' => 'Test adjustment',
    ]);

    expect($result['success'])->toBeTrue();

    // Inventory sum and product.quantity should both be 3
    $sum = Inventory::where('product_id', $product->product_id)->sum('quantity_on_hand');
    $fresh = $product->fresh();

    expect($sum)->toBe(3);
    expect($fresh->quantity)->toBe(3);

    // Running threshold checks should flag low stock
    $thresholdService = new InventoryThresholdService();
    $alerts = $thresholdService->checkProductThresholds($fresh);

    expect($alerts->pluck('alert_type'))->toContain('low_stock');
});

it('records waste via inventory and updates product quantity', function () {
    $product = Product::factory()->create();

    // Seed inventory to 8 units using InventoryService
    InventoryService::adjust(
        productId: $product->product_id,
        quantityChange: 8,
        unitCost: null,
        movementType: \App\Models\StockMovement::TYPE_INITIAL_STOCK,
        referenceType: 'seed',
    );

    // Record waste of 3 units
    $service = new StockService();
    $result = $service->recordWaste([
        'product_id' => $product->product_id,
        'quantity' => 3,
        'reason' => 'Damaged',
    ]);

    expect($result['success'])->toBeTrue();

    $sum = Inventory::where('product_id', $product->product_id)->sum('quantity_on_hand');
    $fresh = $product->fresh();

    expect($sum)->toBe(5);
    expect($fresh->quantity)->toBe(5);
});

it('transfers stock between products and keeps inventories in sync', function () {
    $from = Product::factory()->create();
    $to = Product::factory()->create();

    // Seed inventories
    InventoryService::adjust(productId: $from->product_id, quantityChange: 7, referenceType: 'seed');
    InventoryService::adjust(productId: $to->product_id, quantityChange: 2, referenceType: 'seed');

    $service = new StockService();
    $res = $service->createStockTransfer([
        'product_id_from' => $from->product_id,
        'product_id_to' => $to->product_id,
        'quantity' => 3,
    ]);

    expect($res['success'])->toBeTrue();

    $fromSum = Inventory::where('product_id', $from->product_id)->sum('quantity_on_hand');
    $toSum = Inventory::where('product_id', $to->product_id)->sum('quantity_on_hand');

    expect($fromSum)->toBe(4);
    expect($toSum)->toBe(5);

    expect($from->fresh()->quantity)->toBe(4);
    expect($to->fresh()->quantity)->toBe(5);
});
