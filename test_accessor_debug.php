<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SystemSetting;

echo "Accessor Debug Test\n";
echo "===================\n\n";

// Get the setting directly
$setting = SystemSetting::where('category', 'general')
    ->where('key', 'business_hours')
    ->first();

if ($setting) {
    echo "Setting found:\n";
    echo "  ID: {$setting->id}\n";
    echo "  data_type attribute: " . $setting->getAttributes()['data_type'] . "\n";
    echo "  data_type property: {$setting->data_type}\n";
    echo "  Raw value (from getAttributes): " . substr($setting->getAttributes()['value'], 0, 100) . "...\n";
    echo "  Accessed value (via \$setting->value): \n";
    
    $accessedValue = $setting->value;
    echo "    Type: " . gettype($accessedValue) . "\n";
    
    if (is_array($accessedValue)) {
        echo "    ✓ It's an array!\n";
        echo "    Keys: " . implode(', ', array_keys($accessedValue)) . "\n";
    } else {
        echo "    ❌ It's NOT an array, it's a: " . gettype($accessedValue) . "\n";
        echo "    Content: " . substr($accessedValue, 0, 150) . "\n";
    }
    
    // Try to manually decode
    echo "\n  Manual decoding test:\n";
    $manualDecode = json_decode($setting->getAttributes()['value'], true);
    echo "    Type: " . gettype($manualDecode) . "\n";
    if (is_array($manualDecode)) {
        echo "    ✓ Manual decode works!\n";
    }
} else {
    echo "Setting not found!\n";
}
