<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 20px; margin: 0 0 6px; }
        .muted { color: #666; }
        .summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 12px 0 18px; }
        .card { border: 1px solid #ddd; padding: 10px; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 6px; border-bottom: 1px solid #e5e5e5; }
        th { text-align: left; background: #f7f7f7; }
        .right { text-align: right; }
        .small { font-size: 11px; }
    </style>
</head>
<body>
    <h1>Sales Order Master</h1>
    <div class="muted small">Period: {{ $filters['start_date'] ?? ($report['period']['start_date'] ?? '') }} to {{ $filters['end_date'] ?? ($report['period']['end_date'] ?? '') }}</div>

    <div class="summary">
        <div class="card">
            <div class="small muted">Total Orders</div>
            <div style="font-size:18px; font-weight:700">{{ $report['summary']['total_orders'] ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="small muted">Total Revenue</div>
            <div style="font-size:18px; font-weight:700">₱{{ number_format($report['summary']['total_revenue'] ?? 0, 2) }}</div>
        </div>
        <div class="card">
            <div class="small muted">Total Profit</div>
            <div style="font-size:18px; font-weight:700">₱{{ number_format($report['summary']['total_profit'] ?? 0, 2) }}</div>
        </div>
        <div class="card">
            <div class="small muted">Profit Margin</div>
            <div style="font-size:18px; font-weight:700">{{ number_format($report['summary']['profit_margin'] ?? 0, 1) }}%</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Product</th>
                <th>Brand</th>
                <th>Category</th>
                <th class="right">Sold Qty</th>
                <th class="right">Sold Amount</th>
                <th class="right">Instock Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach(($report['product_sales'] ?? []) as $row)
                <tr>
                    <td>{{ $row->product_sku ?? ('SKU-' . ($row->product_id ?? '')) }}</td>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->product_brand }}</td>
                    <td>{{ $row->product_category }}</td>
                    <td class="right">{{ (int)($row->total_quantity ?? 0) }}</td>
                    <td class="right">₱{{ number_format($row->total_revenue ?? 0, 2) }}</td>
                    <td class="right">{{ (int)($row->instock_qty ?? 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
