<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "Business Hours Settings Debug Tool\n";
echo "===========================================\n\n";

// Check current business_hours setting in database
echo "1. Current business_hours in database:\n";
echo "----------------------------------------\n";

$setting = SystemSetting::where('category', 'general')
    ->where('key', 'business_hours')
    ->first();

if ($setting) {
    echo "Found setting:\n";
    echo "  ID: {$setting->id}\n";
    echo "  Category: {$setting->category}\n";
    echo "  Key: {$setting->key}\n";
    echo "  Data Type: {$setting->data_type}\n";
    echo "  Raw Value (from DB): " . $setting->getAttributes()['value'] . "\n";
    echo "  Decoded Value: " . print_r($setting->value, true) . "\n";
    echo "  Is Active: " . ($setting->is_active ? 'Yes' : 'No') . "\n";
} else {
    echo "  ❌ No business_hours setting found in database!\n";
}

echo "\n";

// Check what getValue returns
echo "2. What SystemSetting::getValue returns:\n";
echo "----------------------------------------\n";
$retrieved = SystemSetting::getValue('general', 'business_hours');
echo "  Retrieved value: " . print_r($retrieved, true) . "\n";

echo "\n";

// Check getCategory
echo "3. What getCategory returns for 'general':\n";
echo "----------------------------------------\n";
$category = SystemSetting::getByCategory('general');
if (isset($category['business_hours'])) {
    echo "  business_hours found in category:\n";
    echo "  " . print_r($category['business_hours'], true) . "\n";
} else {
    echo "  ❌ business_hours NOT found in getByCategory!\n";
}

echo "\n";

// Test saving and retrieving
echo "4. Test: Save and immediately retrieve:\n";
echo "----------------------------------------\n";

$testData = [
    'monday' => ['open' => '08:00', 'close' => '17:00'],
    'tuesday' => ['open' => '08:00', 'close' => '17:00'],
    'wednesday' => ['open' => '08:00', 'close' => '17:00'],
    'thursday' => ['open' => '08:00', 'close' => '17:00'],
    'friday' => ['open' => '08:00', 'close' => '17:00'],
    'saturday' => ['open' => '09:00', 'close' => '14:00'],
];

echo "  Saving test data (Saturday 9am-2pm)...\n";
$saved = SystemSetting::setValue('general', 'business_hours', $testData, 'json', 'Business operating hours');
echo "  ✓ Saved successfully\n";

// Clear cache
\Illuminate\Support\Facades\Cache::forget('setting_general_business_hours');
\Illuminate\Support\Facades\Cache::forget('settings_category_general');
echo "  ✓ Cache cleared\n";

// Retrieve immediately
$retrieved = SystemSetting::getValue('general', 'business_hours');
echo "  Retrieved value after save:\n";
if (is_array($retrieved)) {
    echo "  ✓ Retrieved as array\n";
    echo "  " . json_encode($retrieved, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "  ❌ Retrieved as: " . gettype($retrieved) . "\n";
    echo "  " . print_r($retrieved, true) . "\n";
}

// Check raw database value
$rawCheck = DB::table('system_settings')
    ->where('category', 'general')
    ->where('key', 'business_hours')
    ->first();
echo "  Raw database value:\n";
echo "  " . $rawCheck->value . "\n";

if (is_array($retrieved) && isset($retrieved['saturday'])) {
    if ($retrieved['saturday']['open'] === '09:00' && $retrieved['saturday']['close'] === '14:00') {
        echo "  ✅ SUCCESS: Data is being saved and retrieved correctly!\n";
    } else {
        echo "  ❌ FAIL: Retrieved data doesn't match what was saved.\n";
        echo "     Expected Saturday: 09:00 - 14:00\n";
        echo "     Got: {$retrieved['saturday']['open']} - {$retrieved['saturday']['close']}\n";
    }
} else {
    echo "  ❌ FAIL: Retrieved data is not an array or Saturday not found.\n";
    echo "     Type: " . gettype($retrieved) . "\n";
}

echo "\n";
echo "===========================================\n";
echo "Debug Complete\n";
echo "===========================================\n";
