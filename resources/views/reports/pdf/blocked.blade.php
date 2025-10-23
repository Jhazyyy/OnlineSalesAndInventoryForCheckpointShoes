<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blocked Items Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 20px; margin: 0 0 6px; }
        .muted { color: #666; }
        .summary { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin: 12px 0 18px; }
        .card { border: 1px solid #ddd; padding: 10px; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 6px; border-bottom: 1px solid #e5e5e5; }
        th { text-align: left; background: #f7f7f7; }
        .right { text-align: right; }
        .small { font-size: 11px; }
    </style>
</head>
<body>
    <h1>Blocked Items</h1>
    <div class="muted small">Period: {{ $filters['start_date'] ?? ($report['period']['start_date'] ?? '') }} to {{ $filters['end_date'] ?? ($report['period']['end_date'] ?? '') }}</div>

    <div class="summary">
        <div class="card">
            <div class="small muted">Refurbished</div>
            <div style="font-size:18px; font-weight:700">{{ $report['summary']['total_refurbished_qty'] ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="small muted">Damaged (Shipped)</div>
            <div style="font-size:18px; font-weight:700">{{ $report['summary']['total_damaged_shipped_qty'] ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="small muted">Damaged (Received)</div>
            <div style="font-size:18px; font-weight:700">{{ $report['summary']['total_inbound_damaged_qty'] ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="small muted">Waste Qty</div>
            <div style="font-size:18px; font-weight:700">{{ $report['summary']['total_waste_qty'] ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="small muted">Waste Value</div>
            <div style="font-size:18px; font-weight:700">₱{{ number_format($report['summary']['total_waste_value'] ?? 0, 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Brand</th>
                <th>Category</th>
                <th class="right">Refurbished</th>
                <th class="right">Damaged (Shipped)</th>
                <th class="right">Damaged (Received)</th>
                <th class="right">Waste Qty</th>
                <th class="right">Waste Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach(($report['products'] ?? []) as $row)
                <tr>
                    <td>{{ $row['product_name'] }}</td>
                    <td>{{ $row['product_brand'] ?? 'N/A' }}</td>
                    <td>{{ $row['product_category'] ?? 'N/A' }}</td>
                    <td class="right">{{ (int)($row['refurbished_qty'] ?? 0) }}</td>
                    <td class="right">{{ (int)($row['damaged_shipped_qty'] ?? 0) }}</td>
                    <td class="right">{{ (int)($row['inbound_damaged_qty'] ?? 0) }}</td>
                    <td class="right">{{ (int)($row['waste_qty'] ?? 0) }}</td>
                    <td class="right">₱{{ number_format(($row['waste_value'] ?? 0), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
