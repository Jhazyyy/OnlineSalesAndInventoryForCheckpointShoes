<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Report</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111;
        }

        h1 {
            font-size: 20px;
            margin: 0 0 6px;
        }

        .muted {
            color: #666;
        }

        .summary {
            border: 1px solid #ddd;
            padding: 8px 12px;
            margin: 12px 0 18px;
            background: #f9f9f9;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-item {
            display: inline-block;
            margin-right: 30px;
        }

        .summary-item:last-child {
            margin-right: 0;
        }

        .summary-label {
            font-size: 10px;
            color: #666;
            display: inline;
            margin-right: 5px;
        }

        .summary-value {
            font-size: 14px;
            font-weight: 700;
            display: inline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px 6px;
            border-bottom: 1px solid #e5e5e5;
        }

        th {
            text-align: left;
            background: #f7f7f7;
        }

        .right {
            text-align: right;
        }

        .small {
            font-size: 11px;
        }
    </style>
</head>

<body>
    <h1>Purchase Report</h1>
    <div class="muted small">Period: {{ $filters['start_date'] ?? ($report['period']['start_date'] ?? '') }} to
        {{ $filters['end_date'] ?? ($report['period']['end_date'] ?? '') }}</div>

    <div class="summary">
        <div class="summary-row">
            <span class="summary-item">
                <span class="summary-label">Total Orders:</span>
                <span class="summary-value">{{ $report['summary']['total_orders'] ?? 0 }}</span>
            </span>
            <span class="summary-item">
                <span class="summary-label">Total Amount:</span>
                <span class="summary-value">₱{{ number_format($report['summary']['total_amount'] ?? 0, 2) }}</span>
            </span>
            <span class="summary-item">
                <span class="summary-label">Total Paid:</span>
                <span class="summary-value">₱{{ number_format($report['summary']['total_paid'] ?? 0, 2) }}</span>
            </span>
            <span class="summary-item">
                <span class="summary-label">Total Due:</span>
                <span class="summary-value">₱{{ number_format($report['summary']['total_due'] ?? 0, 2) }}</span>
            </span>
        </div>
    </div>

    @if (!empty($report['top_suppliers']))
        <h2 style="margin: 10px 0 6px;">Top Suppliers</h2>
        <table style="margin-bottom: 14px;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Supplier</th>
                    <th class="left">Orders</th>
                    <th class="left">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report['top_suppliers'] ?? [] as $i => $s)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $s['supplier_name'] ?? ($s->supplier_name ?? 'N/A') }}</td>
                        <td class="left">{{ (int) ($s['order_count'] ?? ($s->order_count ?? 0)) }}</td>
                        <td class="left">₱{{ number_format($s['total_amount'] ?? ($s->total_amount ?? 0), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if (!empty($report['product_purchases']))
        <h2 style="margin: 10px 0 6px;">Purchase Order Master (By Product)</h2>
        <table style="margin-bottom: 14px;">
            <thead>
                <tr class="break-words">
                    <th>SKU</th>
                    <th>Product</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th class="left">Ordered</th>
                    <th class="left">Received</th>
                    <th class="left">Avg. Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report['product_purchases'] ?? [] as $p)
                    <tr>
                        <td class="small">{{ $p->sku ?? 'N/A' }}</td>
                        <td>{{ $p->product_name ?? 'N/A' }}</td>
                        <td class="small">{{ $p->product_brand ?? 'N/A' }}</td>
                        <td class="small">{{ $p->product_category ?? 'N/A' }}</td>
                        <td class="left">{{ number_format($p->total_ordered ?? 0) }}</td>
                        <td class="left">{{ number_format($p->total_received ?? 0) }}</td>
                        <td class="left">₱{{ number_format($p->avg_unit_price ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2 style="margin: 10px 0 6px;">Purchase Orders (By Order)</h2>
    <table>
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Order Date</th>
                <th class="left">Amount Due</th>
                <th>Due Date</th>
                <th class="left">Total Paid</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($report['orders'] ?? []) as $order)
                <tr>
                    <td>{{ $order->order_number ?? 'N/A' }}</td>
                    <td>{{ $order->supplier->supplier_name ?? 'N/A' }}</td>
                    <td>{{ $order->order_date ? date('M d, Y', strtotime($order->order_date)) : 'N/A' }}</td>
                    <td class="left">₱{{ number_format($order->total_amount ?? 0, 2) }}</td>
                    <td>{{ $order->expected_date ? date('M d, Y', strtotime($order->expected_date)) : 'N/A' }}</td>
                    <td class="left">₱{{ number_format($order->payments->sum('amount') ?? 0, 2) }}</td>
                    <td>{{ ucfirst($order->status ?? 'N/A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #666; padding: 20px;">No purchase orders found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top: 10px; padding-top: 10px; #ddd; font-size: 10px; color: #666;">
        <p>Report generated by {{ Auth()->user()->name ?? 'Unknown User' }} on
            {{ now()->format('F d, Y \a\t H:i:s') }}</p>
    </div>
</body>

</html>
