# Reports Management Module

## Overview
Comprehensive reporting system with 5 report types, date filtering, and multi-format export capabilities (PDF, Excel, JSON).

## Features

### 1. Reports Dashboard
- Central hub for all reports
- Beautiful gradient cards for each report type
- Quick statistics overview
- Direct navigation to individual reports
- Path: `/reports`

### 2. Sales Report
Analytics for sales performance and customer behavior.

**Metrics**:
- Total orders, revenue, profit, average profit margin
- Orders grouped by status (pending, processing, completed, cancelled)
- Top 10 best-selling products
- Top 10 customers by total spending
- Daily sales breakdown

**Path**: `/reports/sales`

### 3. Purchase Report
Procurement analytics and supplier performance tracking.

**Metrics**:
- Total purchase orders, amount spent, items purchased, average order value
- Orders grouped by status
- Top 10 suppliers by spending
- Most purchased products
- Daily purchase breakdown

**Path**: `/reports/purchases`

### 4. Inventory Report
Stock status, valuation, and movement analysis.

**Metrics**:
- Total products, stock quantity, total value, low stock alerts, out of stock count
- Products by movement category (fast/slow/non-moving)
- Products by stock status (in stock/low stock/out of stock)
- Low stock products with reorder levels
- Out of stock critical alerts
- Top 10 highest value products

**Path**: `/reports/inventory`

### 5. Financial Report (P&L)
Comprehensive profit & loss statement.

**Metrics**:
- Total revenue, cost, net profit, profit margin
- Total orders, payments, invoices, average order value
- Monthly performance breakdown (revenue, cost, profit per month)
- Revenue by payment status
- Revenue by invoice status

**Path**: `/reports/financial`

### 6. Stock Movement Report
Track all inventory movements and adjustments.

**Metrics**:
- Total movements, stock in, stock out, adjustments
- Recent movements table (date, product, type, quantity, reference, notes)
- Most active products with in/out totals
- Movements by type (purchase, sale, adjustment, return)
- Movement by product category
- Daily movement trend

**Path**: `/reports/movement`

## Date Filtering

All reports support date range filtering:
- Start Date selector
- End Date selector
- Apply Filter button
- Reset button to clear filters

Default behavior:
- Start Date: 30 days ago
- End Date: Today

## Export Capabilities

### PDF Export
- Professional formatted PDF documents
- Ready for printing and presentations
- Includes all summary data and details
- Endpoint: `POST /reports/export/pdf`

### Excel Export
- Full data export in XLSX format
- Custom headings per report type
- Styled sheets with bold headers
- Auto-sizing columns
- Freeze panes for easy scrolling
- Endpoint: `POST /reports/export/excel`

### JSON API
- Raw data in JSON format
- Perfect for integrations
- RESTful endpoints
- Endpoint: `GET /reports/data/{type}`

## Usage

### Viewing Reports

1. Navigate to Reports from sidebar
2. Click "Reports Dashboard" or any specific report
3. Select date range (optional)
4. Click "Apply Filter"

### Exporting Reports

**PDF Export**:
1. View any report
2. Click "Export PDF" button
3. PDF opens in new tab
4. Save or print as needed

**Excel Export**:
1. View any report
2. Click "Export Excel" button
3. XLSX file downloads automatically
4. Open in Excel, Google Sheets, etc.

## Architecture

### Service Layer
**ReportService** (`app/Services/ReportService.php`)
- Generates all report data
- Handles complex SQL queries
- Performs aggregations and grouping
- Returns structured data arrays

### Controller Layer
**ReportController** (`app/Http/Controllers/ReportController.php`)
- Handles HTTP requests
- Calls ReportService for data
- Renders views with data
- Manages exports (PDF/Excel)

### Export Layer
**ReportExport** (`app/Exports/ReportExport.php`)
- Formats data for Excel export
- Implements Laravel Excel interfaces
- Applies custom styling
- Handles multiple report types

### View Layer
Blade templates in `resources/views/reports/`:
- `index.blade.php` - Dashboard
- `sales.blade.php` - Sales report
- `purchases.blade.php` - Purchase report
- `inventory.blade.php` - Inventory report
- `financial.blade.php` - Financial report
- `movement.blade.php` - Movement report

## Routes

```php
// Report Views
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
Route::get('/reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
Route::get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
Route::get('/reports/movement', [ReportController::class, 'movement'])->name('reports.movement');

// Export Endpoints
Route::post('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
Route::post('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');

// JSON API
Route::get('/reports/data/{type}', [ReportController::class, 'getData'])->name('reports.data');
```

## Dependencies

Required packages (already installed):
- **maatwebsite/excel**: Excel export functionality
- **barryvdh/laravel-dompdf**: PDF generation
- **laravel/framework**: Core framework (11.x)

## Integration with Other Modules

### Product Movement Module
- Inventory Report uses movement categories
- Stock Movement Report tracks product movements
- Movement velocity data in reports

### Product Costing Module
- Financial Report uses cost data
- Profit calculations from costing fields
- Margin analysis in Sales Report

### Sales Module
- Sales Report aggregates order data
- Customer analytics from sales
- Revenue calculations

### Purchase Module
- Purchase Report analyzes procurement
- Supplier performance metrics
- Cost tracking

## Business Intelligence

### Key Insights
- **Profitability**: Track revenue, cost, and profit trends
- **Inventory Health**: Monitor stock levels and movement
- **Customer Behavior**: Identify top customers and products
- **Supplier Performance**: Evaluate supplier relationships
- **Cash Flow**: Track payments and invoices
- **Operational Efficiency**: Stock movement patterns

### Decision Support
- **Pricing Strategy**: Profit margin analysis
- **Inventory Management**: Fast/slow/non-moving products
- **Procurement Planning**: Purchase patterns and supplier performance
- **Sales Strategy**: Top products and customer insights
- **Financial Planning**: P&L statements and monthly trends

## Security

- All routes require authentication
- Middleware: `auth`, `verified`
- Only authorized users can access reports
- Export functions respect user permissions

## Performance

- Efficient SQL queries with aggregations
- Date range filtering reduces data load
- Lazy loading for large datasets
- Caching opportunities for frequently accessed reports

## Future Enhancements

Potential additions:
- Custom report builder
- Scheduled report emails
- Chart visualizations (Chart.js/ApexCharts)
- Report templates
- Advanced filtering (status, category, supplier, etc.)
- Comparative analysis (period over period)
- Forecasting and trends
- Dashboard widgets

## Troubleshooting

### Excel Export Not Working
- Verify `maatwebsite/excel` is installed: `composer show maatwebsite/excel`
- Check file permissions in `storage/app/exports`
- Review logs: `storage/logs/laravel.log`

### PDF Export Issues
- Verify `barryvdh/laravel-dompdf` is installed: `composer show barryvdh/laravel-dompdf`
- Check DomPDF configuration in `config/dompdf.php`
- Ensure adequate memory: `php.ini` memory_limit >= 256M

### No Data Showing
- Check date range filters
- Verify database has data in selected period
- Check authentication and permissions
- Review query logs for errors

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Review error messages in browser console
3. Verify all migrations are run: `php artisan migrate:status`
4. Clear cache: `php artisan cache:clear`

---

**Module Status**: ✅ FULLY IMPLEMENTED  
**Version**: 1.0  
**Last Updated**: January 2025  
**Developer**: GitHub Copilot
