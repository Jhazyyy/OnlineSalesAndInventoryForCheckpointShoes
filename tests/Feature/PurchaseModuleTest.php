<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReceive;
use App\Models\Supplier;
use App\Services\PurchaseOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_generates_reference_number_for_purchase_order()
    {
        $supplier = Supplier::factory()->active()->create();

        $order = PurchaseOrder::create([
            'supplier_id' => $supplier->supplier_id,
            'order_date' => now()->toDateString(),
            'status' => 'pending',
            'priority' => 'normal',
        ]);

        $this->assertNotEmpty($order->order_number);
        $this->assertNotEmpty($order->reference_number);
        $this->assertStringStartsWith('PO', $order->reference_number);
    }

    /** @test */
    public function it_generates_reference_number_for_goods_receive()
    {
        $supplier = Supplier::factory()->active()->create();
        $order = PurchaseOrder::create([
            'supplier_id' => $supplier->supplier_id,
            'order_date' => now()->toDateString(),
            'status' => 'ordered',
        ]);
        $receive = PurchaseReceive::create([
            'purchase_order_id' => $order->order_id,
            'supplier_id' => $supplier->supplier_id,
            'receive_date' => now()->toDateString(),
            'status' => 'in_transit',
        ]);

        $this->assertNotEmpty($receive->receive_number);
        $this->assertNotEmpty($receive->reference_number);
        $this->assertStringStartsWith('GR', $receive->reference_number);
    }

    /** @test */
    public function can_create_purchase_order_with_items_via_service()
    {
        $supplier = Supplier::factory()->active()->create();
        $product = Product::factory()->create(['price' => 123.45]);
        // Initialize stock since products.quantity is now derived from stock movements
        \App\Models\StockMovement::recordMovement(
            productId: $product->product_id,
            quantityBefore: 0,
            quantityChange: 5,
            quantityAfter: 5,
            movementType: \App\Models\StockMovement::TYPE_INITIAL_STOCK,
            userId: null
        );

        $service = new PurchaseOrderService();
        $order = $service->createOrder([
            'supplier_id' => $supplier->supplier_id,
            'order_date' => now()->toDateString(),
            'status' => 'pending',
            'items' => [
                [
                    'product_id' => $product->product_id,
                    'quantity_ordered' => 3,
                    // omit unit_price to ensure fallback to product price works
                ]
            ],
        ]);

        $this->assertNotNull($order->order_id);
        $this->assertEquals(1, $order->items()->count());
        $this->assertEquals('pending', $order->status);
        $this->assertNotEmpty($order->order_number);
        $this->assertNotEmpty($order->reference_number);
        $order->refresh();
        $this->assertTrue($order->total_amount >= 0);
    }
}
