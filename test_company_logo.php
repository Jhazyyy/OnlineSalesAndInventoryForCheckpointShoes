<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SystemSetting;
use App\Services\SettingsService;

echo "====================================\n";
echo "Company Logo Debug Test\n";
echo "====================================\n\n";

// Check if logo setting exists
$logoSetting = SystemSetting::where('category', 'general')
    ->where('key', 'company_logo')
    ->first();

if ($logoSetting) {
    echo "Logo setting found in database:\n";
    echo "  ID: {$logoSetting->id}\n";
    echo "  Data Type: {$logoSetting->data_type}\n";
    echo "  Raw Value: {$logoSetting->getAttributes()['value']}\n";
    echo "  Accessed Value: {$logoSetting->value}\n";
    echo "  Is Active: " . ($logoSetting->is_active ? 'Yes' : 'No') . "\n";
} else {
    echo "❌ No logo setting found in database\n";
}

echo "\n";

// Test getValue
echo "SystemSetting::getValue('general', 'company_logo'):\n";
$logoPath = SystemSetting::getValue('general', 'company_logo');
echo "  Result: " . ($logoPath ?: '(empty)') . "\n";
echo "  Type: " . gettype($logoPath) . "\n";

echo "\n";

// Test getByCategory
echo "SystemSetting::getByCategory('general'):\n";
$settings = SystemSetting::getByCategory('general');
if (isset($settings['company_logo'])) {
    echo "  company_logo found: {$settings['company_logo']}\n";
    echo "  Type: " . gettype($settings['company_logo']) . "\n";
} else {
    echo "  ❌ company_logo not found in category\n";
}

echo "\n";

// Test SettingsService
echo "SettingsService::getCategory('general'):\n";
$service = new SettingsService();
$serviceSettings = $service->getCategory('general');
if (isset($serviceSettings['company_logo'])) {
    echo "  company_logo found: {$serviceSettings['company_logo']}\n";
    echo "  Type: " . gettype($serviceSettings['company_logo']) . "\n";
} else {
    echo "  ❌ company_logo not found\n";
}

echo "\n";

// Test the helper function
echo "Helper function companyLogoUrl():\n";
$logoUrl = companyLogoUrl();
echo "  Result: {$logoUrl}\n";

echo "\n";

// Check if file exists in storage
if ($logoPath) {
    $fullPath = storage_path('app/public/' . $logoPath);
    echo "Checking file existence:\n";
    echo "  Path: {$fullPath}\n";
    echo "  Exists: " . (file_exists($fullPath) ? 'Yes' : 'No') . "\n";
}
