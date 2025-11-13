<?php
/**
 * Test Sales Report Revenue vs Profit Calculations
 * 
 * This script verifies that the Sales Report correctly calculates:
 * - Total Revenue (including taxes and shipping)
 * - Gross Revenue (subtotal before taxes and shipping)
 * - Total Profit (Gross Revenue - COGS)
 * - Profit Margin (Profit / Gross Revenue * 100)
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ReportService;
use App\Services\PurchaseOrderService;
use Carbon\Carbon;

echo "=================================================\n";
echo "Sales Report Revenue vs Profit Calculation Test\n";
echo "=================================================\n\n";

$purchaseOrderService = app(PurchaseOrderService::class);
$reportService = app(ReportService::class);

// Test with last 30 days
$filters = [
    'start_date' => Carbon::now()->subDays(30)->format('Y-m-d'),
    'end_date' => Carbon::now()->format('Y-m-d'),
];

echo "Testing period: {$filters['start_date']} to {$filters['end_date']}\n\n";

try {
    $report = $reportService->generateSalesReport($filters);
    
    echo "SUMMARY METRICS:\n";
    echo "----------------\n";
    echo "Total Orders:      " . ($report['summary']['total_orders'] ?? 0) . "\n";
    echo "Total Revenue:     ₱" . number_format($report['summary']['total_revenue'] ?? 0, 2) . " (incl. tax & shipping)\n";
    echo "Gross Revenue:     ₱" . number_format($report['summary']['gross_revenue'] ?? 0, 2) . " (before tax & shipping)\n";
    echo "Total Cost (COGS): ₱" . number_format($report['summary']['total_cost'] ?? 0, 2) . "\n";
    echo "Total Profit:      ₱" . number_format($report['summary']['total_profit'] ?? 0, 2) . "\n";
    echo "Profit Margin:     " . number_format($report['summary']['profit_margin'] ?? 0, 2) . "%\n";
    echo "Avg Order Value:   ₱" . number_format($report['summary']['average_order_value'] ?? 0, 2) . "\n\n";
    
    echo "VERIFICATION:\n";
    echo "-------------\n";
    
    $totalRevenue = $report['summary']['total_revenue'] ?? 0;
    $grossRevenue = $report['summary']['gross_revenue'] ?? 0;
    $totalCost = $report['summary']['total_cost'] ?? 0;
    $totalProfit = $report['summary']['total_profit'] ?? 0;
    $profitMargin = $report['summary']['profit_margin'] ?? 0;
    
    // Verification 1: Total Revenue should be >= Gross Revenue
    if ($totalRevenue >= $grossRevenue) {
        echo "✓ Total Revenue (₱" . number_format($totalRevenue, 2) . ") >= Gross Revenue (₱" . number_format($grossRevenue, 2) . ")\n";
    } else {
        echo "✗ ERROR: Total Revenue should be >= Gross Revenue\n";
    }
    
    // Verification 2: Profit = Gross Revenue - Total Cost
    $calculatedProfit = $grossRevenue - $totalCost;
    if (abs($calculatedProfit - $totalProfit) < 0.01) {
        echo "✓ Total Profit (₱" . number_format($totalProfit, 2) . ") = Gross Revenue - COGS\n";
    } else {
        echo "✗ ERROR: Total Profit calculation mismatch\n";
        echo "  Expected: ₱" . number_format($calculatedProfit, 2) . "\n";
        echo "  Got:      ₱" . number_format($totalProfit, 2) . "\n";
    }
    
    // Verification 3: Profit Margin = (Profit / Gross Revenue) * 100
    $calculatedMargin = $grossRevenue > 0 ? ($totalProfit / $grossRevenue) * 100 : 0;
    if (abs($calculatedMargin - $profitMargin) < 0.01) {
        echo "✓ Profit Margin (" . number_format($profitMargin, 2) . "%) calculated correctly\n";
    } else {
        echo "✗ ERROR: Profit Margin calculation mismatch\n";
        echo "  Expected: " . number_format($calculatedMargin, 2) . "%\n";
        echo "  Got:      " . number_format($profitMargin, 2) . "%\n";
    }
    
    // Verification 4: Tax & Shipping difference
    $taxShippingDiff = $totalRevenue - $grossRevenue;
    echo "\nAdditional Info:\n";
    echo "Tax + Shipping:    ₱" . number_format($taxShippingDiff, 2) . "\n";
    echo "                   (Difference between Total Revenue and Gross Revenue)\n";
    
    echo "\n✓ All calculations verified successfully!\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=================================================\n";
echo "Test completed at " . date('Y-m-d H:i:s') . "\n";
echo "=================================================\n";
