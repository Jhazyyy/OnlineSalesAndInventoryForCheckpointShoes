<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $report;
    protected $reportType;

    public function __construct($report, $reportType)
    {
        $this->report = $report;
        $this->reportType = $reportType;
    }

    /**
     * Convert report data to collection
     */
    public function collection()
    {
        $data = collect();
        
        switch ($this->reportType) {
            case 'sales':
                return $this->formatSalesData();
            case 'purchases':
                return $this->formatPurchasesData();
            case 'inventory':
                return $this->formatInventoryData();
            case 'financial':
                return $this->formatFinancialData();
            case 'movement':
                return $this->formatMovementData();
            default:
                return $data;
        }
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        switch ($this->reportType) {
            case 'sales':
                return ['Order ID', 'Date', 'Customer', 'Status', 'Total Amount', 'Payment Status'];
            case 'purchases':
                return ['Order ID', 'Date', 'Supplier', 'Status', 'Total Amount', 'Items'];
            case 'inventory':
                return ['Product ID', 'Product Name', 'Brand', 'Category', 'Quantity', 'Price', 'Stock Value', 'Movement Category'];
            case 'financial':
                return ['Period', 'Revenue', 'Expenses', 'Profit', 'Margin %'];
            case 'movement':
                return ['Date', 'Product', 'Movement Type', 'Quantity Change', 'Reason'];
            default:
                return [];
        }
    }

    /**
     * Define worksheet title
     */
    public function title(): string
    {
        return ucfirst($this->reportType) . ' Report';
    }

    /**
     * Apply styles
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Format sales data
     */
    protected function formatSalesData()
    {
        $orders = $this->report['orders'] ?? collect();
        
        return $orders->map(function ($order) {
            return [
                $order->sales_order_id ?? $order->order_id,
                $order->order_date ? date('Y-m-d', strtotime($order->order_date)) : 'N/A',
                $order->customer->customer_name ?? 'N/A',
                $order->order_status ?? 'N/A',
                number_format($order->total_amount ?? 0, 2),
                $order->payment_status ?? 'N/A',
            ];
        });
    }

    /**
     * Format purchases data
     */
    protected function formatPurchasesData()
    {
        $orders = $this->report['orders'] ?? collect();
        
        return $orders->map(function ($order) {
            return [
                $order->purchase_order_id ?? $order->order_id,
                $order->order_date ? date('Y-m-d', strtotime($order->order_date)) : 'N/A',
                $order->supplier->supplier_name ?? 'N/A',
                $order->status ?? 'N/A',
                number_format($order->total_amount ?? 0, 2),
                $order->items->count() ?? 0,
            ];
        });
    }

    /**
     * Format inventory data
     */
    protected function formatInventoryData()
    {
        $products = $this->report['products'] ?? collect();
        
        return $products->map(function ($product) {
            $stockValue = $product->quantity * ($product->total_cost ?? $product->price);
            return [
                $product->product_id,
                $product->product_name,
                $product->product_brand ?? 'N/A',
                $product->product_category ?? 'N/A',
                $product->quantity,
                number_format($product->price, 2),
                number_format($stockValue, 2),
                $product->movement_category ?? 'Uncategorized',
            ];
        });
    }

    /**
     * Format financial data
     */
    protected function formatFinancialData()
    {
        $monthly = $this->report['monthly_breakdown'] ?? [];
        
        return collect($monthly)->map(function ($data, $period) {
            return [
                $period,
                number_format($data['revenue'], 2),
                number_format($data['expenses'], 2),
                number_format($data['profit'], 2),
                $data['revenue'] > 0 ? number_format(($data['profit'] / $data['revenue']) * 100, 2) : '0.00',
            ];
        });
    }

    /**
     * Format movement data
     */
    protected function formatMovementData()
    {
        $movements = $this->report['movements'] ?? collect();
        
        return $movements->map(function ($movement) {
            return [
                $movement->movement_date ? date('Y-m-d', strtotime($movement->movement_date)) : 'N/A',
                $movement->product->product_name ?? 'N/A',
                $movement->movement_type ?? 'N/A',
                $movement->quantity_change ?? 0,
                $movement->reason ?? 'N/A',
            ];
        });
    }
}
