<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display reports dashboard
     */
    public function index(): View
    {
        return view('reports.index');
    }

    /**
     * Display sales report
     */
    public function sales(Request $request): View
    {
        $filters = $request->only(['start_date', 'end_date', 'status', 'customer_id']);
        
        // Set default dates if not provided
        if (!isset($filters['start_date'])) {
            $filters['start_date'] = now()->subDays(30)->format('Y-m-d');
        }
        if (!isset($filters['end_date'])) {
            $filters['end_date'] = now()->format('Y-m-d');
        }
        
        $report = $this->reportService->generateSalesReport($filters);
        
        return view('reports.sales', compact('report', 'filters'));
    }

    /**
     * Display purchase report
     */
    public function purchases(Request $request): View
    {
        $filters = $request->only(['start_date', 'end_date', 'status', 'supplier_id']);
        
        // Set default dates
        if (!isset($filters['start_date'])) {
            $filters['start_date'] = now()->subDays(30)->format('Y-m-d');
        }
        if (!isset($filters['end_date'])) {
            $filters['end_date'] = now()->format('Y-m-d');
        }
        
        $report = $this->reportService->generatePurchaseReport($filters);
        
        return view('reports.purchases', compact('report', 'filters'));
    }

    /**
     * Display inventory report
     */
    public function inventory(Request $request): View
    {
        $filters = $request->only(['category', 'movement_category', 'stock_status']);
        
        $report = $this->reportService->generateInventoryReport($filters);
        
        return view('reports.inventory', compact('report', 'filters'));
    }

    /**
     * Display financial report
     */
    public function financial(Request $request): View
    {
        $filters = $request->only(['start_date', 'end_date']);
        
        // Set default dates
        if (!isset($filters['start_date'])) {
            $filters['start_date'] = now()->subDays(30)->format('Y-m-d');
        }
        if (!isset($filters['end_date'])) {
            $filters['end_date'] = now()->format('Y-m-d');
        }
        
        $report = $this->reportService->generateFinancialReport($filters);
        
        return view('reports.financial', compact('report', 'filters'));
    }

    /**
     * Display movement report
     */
    public function movement(Request $request): View
    {
        $filters = $request->only(['start_date', 'end_date']);
        
        // Set default dates
        if (!isset($filters['start_date'])) {
            $filters['start_date'] = now()->subDays(30)->format('Y-m-d');
        }
        if (!isset($filters['end_date'])) {
            $filters['end_date'] = now()->format('Y-m-d');
        }
        
        $report = $this->reportService->generateMovementReport($filters);
        
        return view('reports.movement', compact('report', 'filters'));
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request, string $reportType): Response
    {
        $filters = $request->all();
        $report = $this->reportService->exportReportData($reportType, $filters);
        
        $pdf = Pdf::loadView('reports.pdf.' . $reportType, compact('report', 'filters'));
        
        $filename = $reportType . '_report_' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export report to Excel
     */
    public function exportExcel(Request $request, string $reportType)
    {
        $filters = $request->all();
        $report = $this->reportService->exportReportData($reportType, $filters);
        
        $filename = $reportType . '_report_' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new ReportExport($report, $reportType), $filename);
    }

    /**
     * Get report data as JSON (for AJAX)
     */
    public function getData(Request $request, string $reportType)
    {
        $filters = $request->all();
        $report = $this->reportService->exportReportData($reportType, $filters);
        
        return response()->json($report);
    }
}

