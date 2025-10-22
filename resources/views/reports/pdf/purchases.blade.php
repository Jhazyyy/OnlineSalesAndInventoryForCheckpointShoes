<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Report</title>
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
    <h1>Purchase Report</h1>
    <div class="muted small">Period: {{ $filters['start_date'] ?? ($report['period']['start_date'] ?? '') }} to {{ $filters['end_date'] ?? ($report['period']['end_date'] ?? '') }}</div>

    <div class="summary">
        <div class="card">
            <div class="small muted">Total Orders</div>
            <div style="font-size:18px; font-weight:700">{{ $report['summary']['total_orders'] ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="small muted">Total Amount</div>
            <div style="font-size:18px; font-weight:700">₱{{ number_format($report['summary']['total_amount'] ?? 0, 2) }}</div>
        </div>
        <div class="card">
            <div class="small muted">Items Purchased</div>
            <div style="font-size:18px; font-weight:700">{{ $report['summary']['total_items'] ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="small muted">Avg. Order Value</div>
            <div style="font-size:18px; font-weight:700">₱{{ number_format($report['summary']['average_order_value'] ?? $report['summary']['avg_order_value'] ?? 0, 2) }}</div>
        </div>
    </div>

    @if(!empty($report['top_suppliers']))
        <h2 style="margin: 10px 0 6px;">Top Suppliers</h2>
        <table style="margin-bottom: 14px;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Supplier</th>
                    <th class="right">Orders</th>
                    <th class="right">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($report['top_suppliers'] ?? []) as $i => $s)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $s['supplier_name'] ?? $s->supplier_name ?? 'N/A' }}</td>
                        <td class="right">{{ (int)($s['order_count'] ?? $s->order_count ?? 0) }}</td>
                        <td class="right">₱{{ number_format($s['total_amount'] ?? $s->total_amount ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Supplier</th>
                <th>Status</th>
                <th class="right">Total Amount</th>
                <th class="right">Items</th>
            </tr>
        </thead>
        <tbody>
            @foreach(($report['orders'] ?? []) as $order)
                <tr>
                    <td>{{ $order->order_id ?? $order->purchase_order_id ?? 'N/A' }}</td>
                    <td>{{ $order->order_date ? date('Y-m-d', strtotime($order->order_date)) : 'N/A' }}</td>
                    <td>{{ $order->supplier->supplier_name ?? 'N/A' }}</td>
                    <td>{{ $order->status ?? 'N/A' }}</td>
                    <td class="right">₱{{ number_format($order->total_amount ?? 0, 2) }}</td>
                    <td class="right">{{ $order->items->count() ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
