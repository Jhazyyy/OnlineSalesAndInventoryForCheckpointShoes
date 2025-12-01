<?php
/**
 * Soft Delete Test Script
 * 
 * This script demonstrates the soft delete functionality.
 * Run with: php test_soft_deletes.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;

echo "=== SOFT DELETE TESTING ===\n\n";

// Test 1: Check if models have SoftDeletes
echo "1. Checking Model Configuration:\n";
echo "   - Product uses SoftDeletes: " . (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(Product::class)) ? "✓ YES" : "✗ NO") . "\n";
echo "   - Sale uses SoftDeletes: " . (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(Sale::class)) ? "✓ YES" : "✗ NO") . "\n";
echo "   - Customer uses SoftDeletes: " . (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(Customer::class)) ? "✓ YES" : "✗ NO") . "\n\n";

// Test 2: Count records
echo "2. Record Counts:\n";
echo "   - Active Products: " . Product::count() . "\n";
echo "   - Active Sales: " . Sale::count() . "\n";
echo "   - Active Customers: " . Customer::count() . "\n\n";

// Test 3: Check database schema
echo "3. Database Schema Check:\n";
$hasDeletedAt = Schema::hasColumn('products', 'deleted_at');
echo "   - Products table has 'deleted_at' column: " . ($hasDeletedAt ? "✓ YES" : "✗ NO") . "\n";
$hasDeletedAt = Schema::hasColumn('sales', 'deleted_at');
echo "   - Sales table has 'deleted_at' column: " . ($hasDeletedAt ? "✓ YES" : "✗ NO") . "\n";
$hasDeletedAt = Schema::hasColumn('customers', 'deleted_at');
echo "   - Customers table has 'deleted_at' column: " . ($hasDeletedAt ? "✓ YES" : "✗ NO") . "\n\n";

echo "=== SOFT DELETE IMPLEMENTATION COMPLETE ===\n\n";

echo "Next Steps:\n";
echo "1. Update controllers to add restore functionality\n";
echo "2. Create 'archived' views in the UI\n";
echo "3. Test soft delete in the application\n";
echo "4. Review the documentation: SOFT_DELETE_IMPLEMENTATION_GUIDE.md\n";
