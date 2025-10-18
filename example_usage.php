<?php

/**
 * Example usage of the Product module functionality
 * 
 * This file demonstrates how to use the various features of the
 * Product, Sale, Purchase, and Returns models along with the ProductService.
 */

require_once 'vendor/autoload.php';

use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Returns;
use App\Services\ProductService;

// Example 1: Creating a new product
echo "=== Creating a New Product ===\n";
$product = Product::create([
    'product_name' => 'Laptop Computer',
    'product_brand' => 'TechBrand',
    'quantity' => 50,
    'price' => 999.99
]);
echo "Created product: {$product->full_name} with ID: {$product->product_id}\n\n";

// Example 2: Checking stock status
echo "=== Checking Stock Status ===\n";
echo "Is in stock (5 units): " . ($product->isInStock(5) ? 'Yes' : 'No') . "\n";
echo "Is low stock: " . ($product->isLowStock() ? 'Yes' : 'No') . "\n";
echo "Current stock level: {$product->quantity}\n";
echo "Inventory value: $" . number_format($product->inventory_value, 2) . "\n\n";

// Example 3: Processing a sale
echo "=== Processing a Sale ===\n";
$productService = new ProductService();
$saleResult = $productService->processSale($product->product_id, 5);

if ($saleResult['success']) {
    echo "Sale processed successfully!\n";
    echo "Remaining stock: {$saleResult['data']['remaining_stock']}\n";
    echo "Sale amount: $" . number_format($saleResult['data']['total_amount'], 2) . "\n";
} else {
    echo "Sale failed: {$saleResult['message']}\n";
}
echo "\n";

// Example 4: Processing a purchase (restocking)
echo "=== Processing a Purchase (Restocking) ===\n";
$purchaseResult = $productService->processPurchase(
    supplierId: 1,
    productId: $product->product_id,
    userId: 1,
    price: 899.99, // New purchase price
    quantity: 25,
    purchaseDate: now()
);

if ($purchaseResult['success']) {
    echo "Purchase processed successfully!\n";
    echo "New stock level: {$purchaseResult['data']['new_stock_level']}\n";
    echo "Total cost: $" . number_format($purchaseResult['data']['total_cost'], 2) . "\n";
} else {
    echo "Purchase failed: {$purchaseResult['message']}\n";
}
echo "\n";

// Example 5: Processing a return
echo "=== Processing a Return ===\n";
$returnResult = $productService->processReturn(
    productId: $product->product_id,
    quantity: 2,
    price: 999.99,
    status: Returns::STATUS_PENDING,
    returnDate: now()
);

if ($returnResult['success']) {
    echo "Return processed successfully!\n";
    echo "Return ID: {$returnResult['data']['return_id']}\n";
    echo "Status: {$returnResult['data']['status']}\n";
    echo "Return amount: $" . number_format($returnResult['data']['total_amount'], 2) . "\n";
} else {
    echo "Return failed: {$returnResult['message']}\n";
}
echo "\n";

// Example 6: Approving a return (this adds stock back)
echo "=== Approving a Return ===\n";
$return = Returns::find($returnResult['data']['return']->return_id);
if ($return->approve()) {
    echo "Return approved successfully!\n";
    echo "Stock added back to inventory\n";
    $product->refresh();
    echo "New stock level: {$product->quantity}\n";
} else {
    echo "Failed to approve return\n";
}
echo "\n";

// Example 7: Getting inventory dashboard
echo "=== Inventory Dashboard ===\n";
$dashboard = $productService->getInventoryDashboard();
echo "Total products: {$dashboard['total_products']}\n";
echo "Low stock products: {$dashboard['low_stock_count']}\n";
echo "Out of stock products: {$dashboard['out_of_stock_count']}\n";
echo "Total inventory value: $" . number_format($dashboard['total_inventory_value'], 2) . "\n";
echo "Average product value: $" . number_format($dashboard['average_product_value'], 2) . "\n\n";

// Example 8: Searching products
echo "=== Searching Products ===\n";
$searchResults = $productService->searchProducts([
    'search' => 'Laptop',
    'min_price' => 500,
    'stock_status' => 'in_stock'
]);
echo "Found {$searchResults->count()} products matching search criteria\n";

foreach ($searchResults as $searchProduct) {
    echo "- {$searchProduct->full_name}: {$searchProduct->price} (Stock: {$searchProduct->quantity})\n";
}
echo "\n";

// Example 9: Getting products needing attention
echo "=== Products Needing Attention ===\n";
$attention = $productService->getProductsNeedingAttention();

if ($attention['low_stock']->count() > 0) {
    echo "Low stock products:\n";
    foreach ($attention['low_stock'] as $item) {
        echo "- {$item['name']}: {$item['current_stock']} units\n";
    }
}

if ($attention['out_of_stock']->count() > 0) {
    echo "Out of stock products:\n";
    foreach ($attention['out_of_stock'] as $item) {
        echo "- {$item['name']}\n";
    }
}
echo "\n";

// Example 10: Getting sales analytics
echo "=== Sales Analytics ===\n";
$analytics = $productService->getSalesAnalytics();
echo "Total sales: {$analytics['total_sales']}\n";
echo "Total revenue: $" . number_format($analytics['total_revenue'], 2) . "\n";
echo "Average sale amount: $" . number_format($analytics['average_sale_amount'], 2) . "\n";

if ($analytics['top_selling_products']->count() > 0) {
    echo "Top selling products:\n";
    foreach ($analytics['top_selling_products'] as $item) {
        echo "- {$item->product->full_name}: {$item->total_sold} units sold\n";
    }
}
echo "\n";

// Example 11: Product relationships and attributes
echo "=== Product Relationships and Attributes ===\n";
$product->refresh(); // Refresh to get latest data
echo "Product: {$product->full_name}\n";
echo "Total sold: {$product->total_sold}\n";
echo "Total purchased: {$product->total_purchased}\n";
echo "Total returned: {$product->total_returned}\n";
echo "Total revenue: $" . number_format($product->total_revenue, 2) . "\n";
echo "Profit margin: " . number_format($product->profit_margin, 2) . "%\n";
echo "Stock turnover: " . number_format($product->stock_turnover, 2) . "\n\n";

// Example 12: Bulk stock update
echo "=== Bulk Stock Update ===\n";
$stockUpdates = [
    $product->product_id => 100 // Set stock to 100
];
$bulkResult = $productService->bulkUpdateStock($stockUpdates);
echo "Bulk update result: " . ($bulkResult['success'] ? 'Success' : 'Failed') . "\n";
echo "Updated: {$bulkResult['updated_count']}/{$bulkResult['total_count']} products\n";

$product->refresh();
echo "New stock level: {$product->quantity}\n\n";

echo "=== Product Module Demonstration Complete ===\n";
