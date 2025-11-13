<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Raw Database Value Test\n";
echo "=======================\n\n";

$raw = DB::table('system_settings')->where('key', 'business_hours')->first();

echo "Raw DB value:\n";
echo $raw->value . "\n\n";

echo "Is valid JSON: " . (json_last_error() === JSON_ERROR_NONE ? 'Yes' : 'No') . "\n";

$decoded = json_decode($raw->value, true);
echo "First decode type: " . gettype($decoded) . "\n";

if (is_array($decoded)) {
    echo "✓ First decode returns array\n";
    echo "Array keys: " . implode(', ', array_keys($decoded)) . "\n";
} else if (is_string($decoded)) {
    echo "❌ First decode returns string (double encoded!)\n";
    echo "Trying second decode...\n";
    $decoded2 = json_decode($decoded, true);
    echo "Second decode type: " . gettype($decoded2) . "\n";
    if (is_array($decoded2)) {
        echo "✓ Second decode returns array (data is DOUBLE ENCODED!)\n";
    }
}
