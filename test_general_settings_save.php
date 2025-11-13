<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SystemSetting;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Cache;

echo "====================================================\n";
echo "General Settings Save Debug Tool\n";
echo "====================================================\n\n";

// Simulate what happens when you save
$testBusinessHours = [
    'monday' => ['open' => '08:30', 'close' => '17:30'],
    'tuesday' => ['open' => '08:30', 'close' => '17:30'],
    'wednesday' => ['open' => '08:30', 'close' => '17:30'],
    'thursday' => ['open' => '08:30', 'close' => '17:30'],
    'friday' => ['open' => '08:30', 'close' => '17:30'],
    'saturday' => ['open' => '10:00', 'close' => '15:00'],
];

echo "1. Testing save with custom times (Mon-Fri 8:30am-5:30pm, Sat 10am-3pm)\n";
echo "================================================================\n";

// Clear cache first
Cache::forget('setting_general_business_hours');
Cache::forget('settings_category_general');
echo "✓ Cache cleared\n";

// Save the setting
echo "\nSaving business_hours...\n";
$dataType = 'json'; // This should be json
echo "  Data type: {$dataType}\n";
echo "  Is array: " . (is_array($testBusinessHours) ? 'Yes' : 'No') . "\n";

try {
    $result = SystemSetting::setValue('general', 'business_hours', $testBusinessHours, $dataType, 'Business operating hours');
    echo "✓ Saved successfully (ID: {$result->id})\n";
    
    // Check what was actually saved in DB
    $dbCheck = \Illuminate\Support\Facades\DB::table('system_settings')
        ->where('category', 'general')
        ->where('key', 'business_hours')
        ->first();
    
    echo "\n2. What's in the database:\n";
    echo "==========================\n";
    echo "  Data Type field: {$dbCheck->data_type}\n";
    echo "  Value (raw): " . substr($dbCheck->value, 0, 100) . "...\n";
    
    // Clear cache again to force fresh read
    Cache::forget('setting_general_business_hours');
    Cache::forget('settings_category_general');
    echo "\n✓ Cache cleared again\n";
    
    // Now retrieve using getValue
    echo "\n3. Retrieving via SystemSetting::getValue:\n";
    echo "==========================================\n";
    $retrieved = SystemSetting::getValue('general', 'business_hours');
    echo "  Type returned: " . gettype($retrieved) . "\n";
    
    if (is_array($retrieved)) {
        echo "  ✓ Retrieved as array!\n";
        if (isset($retrieved['saturday'])) {
            echo "  Saturday open: {$retrieved['saturday']['open']}\n";
            echo "  Saturday close: {$retrieved['saturday']['close']}\n";
            
            if ($retrieved['saturday']['open'] === '10:00' && $retrieved['saturday']['close'] === '15:00') {
                echo "\n  ✅ SUCCESS! Data saved and retrieved correctly!\n";
            } else {
                echo "\n  ❌ Data mismatch!\n";
            }
        }
    } else {
        echo "  ❌ Retrieved as string!\n";
        echo "  Value: " . substr(print_r($retrieved, true), 0, 150) . "\n";
    }
    
    // Test getByCategory
    echo "\n4. Retrieving via SystemSetting::getByCategory:\n";
    echo "================================================\n";
    Cache::forget('settings_category_general');
    $category = SystemSetting::getByCategory('general');
    
    if (isset($category['business_hours'])) {
        echo "  Type: " . gettype($category['business_hours']) . "\n";
        
        if (is_array($category['business_hours'])) {
            echo "  ✓ Retrieved as array!\n";
            if (isset($category['business_hours']['saturday'])) {
                echo "  Saturday: {$category['business_hours']['saturday']['open']} - {$category['business_hours']['saturday']['close']}\n";
                
                if ($category['business_hours']['saturday']['open'] === '10:00') {
                    echo "  ✅ getByCategory works correctly!\n";
                } else {
                    echo "  ❌ getByCategory returned wrong data!\n";
                }
            }
        } else {
            echo "  ❌ Retrieved as non-array!\n";
        }
    } else {
        echo "  ❌ business_hours not found in category!\n";
    }
    
    // Test the SettingsService
    echo "\n5. Testing SettingsService:\n";
    echo "===========================\n";
    $settingsService = new SettingsService();
    Cache::forget('settings_category_general');
    $serviceResult = $settingsService->getCategory('general');
    
    if (isset($serviceResult['business_hours'])) {
        echo "  Type: " . gettype($serviceResult['business_hours']) . "\n";
        
        if (is_array($serviceResult['business_hours']) && isset($serviceResult['business_hours']['saturday'])) {
            echo "  ✓ SettingsService returns array!\n";
            echo "  Saturday: {$serviceResult['business_hours']['saturday']['open']} - {$serviceResult['business_hours']['saturday']['close']}\n";
            
            if ($serviceResult['business_hours']['saturday']['open'] === '10:00') {
                echo "  ✅ SettingsService works correctly!\n";
            }
        } else {
            echo "  ❌ SettingsService didn't return proper array!\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n====================================================\n";
echo "Debug Complete\n";
echo "====================================================\n";
