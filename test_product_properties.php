<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Product properties relationship...\n\n";

try {
    $product = \App\Models\Product::first();
    
    if (!$product) {
        echo "No products found in database.\n";
        exit(0);
    }
    
    echo "Product: {$product->product_name}\n";
    echo "Product ID: {$product->product_id}\n";
    
    // Test the properties relationship
    $propertiesCount = $product->properties()->count();
    echo "Properties count: {$propertiesCount}\n";
    
    // Test hasProperties method
    $hasProperties = $product->hasProperties() ? 'Yes' : 'No';
    echo "Has properties: {$hasProperties}\n";
    
    // Test actual_quantity attribute
    echo "Actual quantity: {$product->actual_quantity}\n";
    
    echo "\n✅ SUCCESS! The properties relationship is working correctly.\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
