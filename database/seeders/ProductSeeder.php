<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing products and related records to avoid duplicate SKU errors
        $this->command->info('Clearing existing products and inventory...');
        Product::query()->delete();
        // Clear orphaned inventory records (cascading delete may not work)
        Inventory::query()->delete();
        
        // Shoe Products - Various brands and styles
        $shoeProducts = [
            [
                'product_name' => 'Nike Air Max 270',
                'sku' => 'NIKE-AM270-001',
                'product_brand' => 'Nike',
                'product_category' => 'Shoes',
                'price' => 5999.00,
                'description' => 'Comfortable running shoes with excellent cushioning and breathable mesh upper.',
                'quantity' => 45,
            ],
            [
                'product_name' => 'Adidas Ultraboost 22',
                'sku' => 'ADIDAS-UB22-001',
                'product_brand' => 'Adidas',
                'product_category' => 'Shoes',
                'price' => 8999.00,
                'description' => 'Premium running shoes with responsive Boost cushioning technology.',
                'quantity' => 30,
            ],
            [
                'product_name' => 'Converse Chuck Taylor All Star',
                'sku' => 'CONV-CTAS-001',
                'product_brand' => 'Converse',
                'product_category' => 'Shoes',
                'price' => 2999.00,
                'description' => 'Classic canvas sneakers, timeless style for everyday wear.',
                'quantity' => 60,
            ],
            [
                'product_name' => 'Vans Old Skool',
                'sku' => 'VANS-OS-001',
                'product_brand' => 'Vans',
                'product_category' => 'Shoes',
                'price' => 3499.00,
                'description' => 'Iconic skate shoes with signature side stripe and durable canvas.',
                'quantity' => 50,
            ],
            [
                'product_name' => 'New Balance 574',
                'sku' => 'NB-574-001',
                'product_brand' => 'New Balance',
                'product_category' => 'Shoes',
                'price' => 4299.00,
                'description' => 'Versatile lifestyle sneakers with ENCAP cushioning and suede/mesh upper.',
                'quantity' => 35,
            ],
            [
                'product_name' => 'Puma Suede Classic',
                'sku' => 'PUMA-SC-001',
                'product_brand' => 'Puma',
                'product_category' => 'Shoes',
                'price' => 3799.00,
                'description' => 'Retro-style suede sneakers, perfect for casual styling.',
                'quantity' => 40,
            ],
            [
                'product_name' => 'Reebok Classic Leather',
                'sku' => 'REEBOK-CL-001',
                'product_brand' => 'Reebok',
                'product_category' => 'Shoes',
                'price' => 3299.00,
                'description' => 'Clean and simple leather sneakers with timeless appeal.',
                'quantity' => 55,
            ],
            [
                'product_name' => 'Under Armour HOVR Phantom 3',
                'sku' => 'UA-HP3-001',
                'product_brand' => 'Under Armour',
                'product_category' => 'Shoes',
                'price' => 6499.00,
                'description' => 'High-performance running shoes with zero-gravity feel.',
                'quantity' => 25,
            ],
            [
                'product_name' => 'Skechers Go Walk 6',
                'sku' => 'SKECHERS-GW6-001',
                'product_brand' => 'Skechers',
                'product_category' => 'Shoes',
                'price' => 4599.00,
                'description' => 'Ultra-lightweight walking shoes with Air Cooled Goga Mat insole.',
                'quantity' => 48,
            ],
            [
                'product_name' => 'Asics Gel-Kayano 29',
                'sku' => 'ASICS-GK29-001',
                'product_brand' => 'Asics',
                'product_category' => 'Shoes',
                'price' => 7999.00,
                'description' => 'Premium stability running shoes with GEL technology cushioning.',
                'quantity' => 20,
            ],
            [
                'product_name' => 'Nike Air Force 1',
                'sku' => 'NIKE-AF1-001',
                'product_brand' => 'Nike',
                'product_category' => 'Shoes',
                'price' => 5499.00,
                'description' => 'Legendary basketball-inspired sneakers with clean, classic design.',
                'quantity' => 42,
            ],
            [
                'product_name' => 'Adidas Stan Smith',
                'sku' => 'ADIDAS-SS-001',
                'product_brand' => 'Adidas',
                'product_category' => 'Shoes',
                'price' => 4199.00,
                'description' => 'Minimalist tennis shoes with premium leather construction.',
                'quantity' => 38,
            ],
        ];

        // Garment Products
        $garmentProducts = [
            [
                'product_name' => 'Nike Dri-FIT Training Shirt',
                'sku' => 'NIKE-DFIT-TS-001',
                'product_brand' => 'Nike',
                'product_category' => 'Garments',
                'price' => 1299.00,
                'description' => 'Moisture-wicking training shirt for optimal performance.',
                'quantity' => 70,
            ],
            [
                'product_name' => 'Adidas Tiro 23 Track Pants',
                'sku' => 'ADIDAS-T23-TP-001',
                'product_brand' => 'Adidas',
                'product_category' => 'Garments',
                'price' => 2199.00,
                'description' => 'Classic soccer-inspired track pants with tapered fit.',
                'quantity' => 55,
            ],
            [
                'product_name' => 'Under Armour Tech 2.0 T-Shirt',
                'sku' => 'UA-T20-TS-001',
                'product_brand' => 'Under Armour',
                'product_category' => 'Garments',
                'price' => 1199.00,
                'description' => 'Loose fit training shirt with anti-odor technology.',
                'quantity' => 80,
            ],
            [
                'product_name' => 'Puma Essential Fleece Hoodie',
                'sku' => 'PUMA-EF-HD-001',
                'product_brand' => 'Puma',
                'product_category' => 'Garments',
                'price' => 2499.00,
                'description' => 'Comfortable cotton blend hoodie for everyday wear.',
                'quantity' => 45,
            ],
            [
                'product_name' => 'Reebok Training Essentials Shorts',
                'sku' => 'REEBOK-TE-SH-001',
                'product_brand' => 'Reebok',
                'product_category' => 'Garments',
                'price' => 999.00,
                'description' => 'Breathable workout shorts with side pockets.',
                'quantity' => 65,
            ],
        ];

        // Accessory Products
        $accessoryProducts = [
            [
                'product_name' => 'Nike Brasilia Training Backpack',
                'sku' => 'NIKE-BR-BP-001',
                'product_brand' => 'Nike',
                'product_category' => 'Accessories',
                'price' => 1799.00,
                'description' => 'Spacious backpack with multiple compartments for organized storage.',
                'quantity' => 40,
            ],
            [
                'product_name' => 'Adidas Defender IV Duffel Bag',
                'sku' => 'ADIDAS-D4-DB-001',
                'product_brand' => 'Adidas',
                'product_category' => 'Accessories',
                'price' => 2299.00,
                'description' => 'Durable duffel bag with reinforced base and adjustable shoulder strap.',
                'quantity' => 32,
            ],
            [
                'product_name' => 'Under Armour Sportstyle Logo Cap',
                'sku' => 'UA-SSL-CAP-001',
                'product_brand' => 'Under Armour',
                'product_category' => 'Accessories',
                'price' => 899.00,
                'description' => 'Structured cap with moisture-wicking sweatband.',
                'quantity' => 75,
            ],
            [
                'product_name' => 'Puma Training Waist Bag',
                'sku' => 'PUMA-TW-BAG-001',
                'product_brand' => 'Puma',
                'product_category' => 'Accessories',
                'price' => 1199.00,
                'description' => 'Compact waist bag for hands-free storage during workouts.',
                'quantity' => 50,
            ],
            [
                'product_name' => 'Nike Everyday Cushion Socks (3 Pack)',
                'sku' => 'NIKE-EC-SOCK-001',
                'product_brand' => 'Nike',
                'product_category' => 'Accessories',
                'price' => 699.00,
                'description' => 'Comfortable athletic socks with arch support.',
                'quantity' => 120,
            ],
            [
                'product_name' => 'Adidas Performance Wristbands',
                'sku' => 'ADIDAS-PW-WB-001',
                'product_brand' => 'Adidas',
                'product_category' => 'Accessories',
                'price' => 399.00,
                'description' => 'Sweat-absorbing wristbands for intense workouts.',
                'quantity' => 90,
            ],
        ];

        // Low stock products (for testing reorder alerts)
        $lowStockProducts = [
            [
                'product_name' => 'Nike ZoomX Vaporfly NEXT% 2',
                'sku' => 'NIKE-ZV2-001',
                'product_brand' => 'Nike',
                'product_category' => 'Shoes',
                'price' => 12999.00,
                'description' => 'Elite racing shoes designed for marathon performance.',
                'quantity' => 5,
            ],
            [
                'product_name' => 'Adidas Yeezy Boost 350 V2',
                'sku' => 'ADIDAS-YB350-001',
                'product_brand' => 'Adidas',
                'product_category' => 'Shoes',
                'price' => 11999.00,
                'description' => 'Limited edition lifestyle sneakers with Boost cushioning.',
                'quantity' => 3,
            ],
        ];

        // Out of stock products
        $outOfStockProducts = [
            [
                'product_name' => 'Jordan Retro 1 High',
                'sku' => 'JORDAN-R1H-001',
                'product_brand' => 'Nike',
                'product_category' => 'Shoes',
                'price' => 9999.00,
                'description' => 'Classic basketball shoes with iconic design.',
                'quantity' => 0,
            ],
            [
                'product_name' => 'New Balance 990v5',
                'sku' => 'NB-990V5-001',
                'product_brand' => 'New Balance',
                'product_category' => 'Shoes',
                'price' => 8999.00,
                'description' => 'Premium Made in USA running shoes.',
                'quantity' => 0,
            ],
        ];

        // Combine all products
        $allProducts = array_merge(
            $shoeProducts,
            $garmentProducts,
            $accessoryProducts,
            $lowStockProducts,
            $outOfStockProducts
        );

        // Create products and their initial stock
        foreach ($allProducts as $productData) {
            $quantity = $productData['quantity'];
            unset($productData['quantity']);

            // Create the product
            $product = Product::create($productData);

            // Use InventoryService to set initial stock if quantity > 0
            // This will update both the Inventory table and create a StockMovement record
            if ($quantity > 0) {
                \App\Services\InventoryService::adjust(
                    productId: $product->product_id,
                    quantityChange: $quantity,
                    unitCost: null,
                    movementType: StockMovement::TYPE_INITIAL_STOCK,
                    referenceType: null,
                    referenceId: null,
                    propertyId: null,
                    location: null,
                    syncProductQuantity: true // This will sync products.quantity
                );
            }
        }

        $this->command->info('Created ' . count($allProducts) . ' products successfully!');
        $this->command->info('- Shoes: ' . count($shoeProducts) . ' products');
        $this->command->info('- Garments: ' . count($garmentProducts) . ' products');
        $this->command->info('- Accessories: ' . count($accessoryProducts) . ' products');
        $this->command->info('- Low Stock: ' . count($lowStockProducts) . ' products');
        $this->command->info('- Out of Stock: ' . count($outOfStockProducts) . ' products');
    }
}