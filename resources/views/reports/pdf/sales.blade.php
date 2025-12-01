<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
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
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin: 12px 0 12px;
        }

        .card {
            border: 1px solid #ddd;
            padding: 8px;
            border-radius: 6px;
        }

        .breakdown {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 12px;
            margin: 12px 0 18px;
            border-radius: 6px;
        }

        .breakdown-title {
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .calc-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 11px;
        }

        .calc-divider {
            border-top: 1px solid #999;
            margin: 4px 0;
            padding-top: 4px;
        }

        .calc-highlight {
            font-weight: 700;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
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
    <h1>Sales Report</h1>
    <div class="muted small">Period: {{ $filters['start_date'] ?? ($report['period']['start_date'] ?? '') }} to
        {{ $filters['end_date'] ?? ($report['period']['end_date'] ?? '') }}</div>

    <div class="summary">
        <div class="card">
            <div class="small muted">Total Orders</div>
            <div style="font-size:16px; font-weight:700">{{ $report['summary']['total_orders'] ?? 0 }}</div>
        </div>
        <div class="card">
            <div class="small muted">Total Revenue</div>
            <div style="font-size:16px; font-weight:700">
                ₱{{ number_format($report['summary']['total_revenue'] ?? 0, 2) }}</div>
            <div class="small muted" style="font-size:9px; margin-top:2px">(Customer Payment)</div>
        </div>
        <div class="card">
            <div class="small muted">Gross Revenue</div>
            <div style="font-size:16px; font-weight:700">
                ₱{{ number_format($report['summary']['gross_revenue'] ?? 0, 2) }}</div>
            <div class="small muted" style="font-size:9px; margin-top:2px">(Product Sales)</div>
        </div>
        <div class="card">
            <div class="small muted">Gross Profit</div>
            <div style="font-size:16px; font-weight:700">
                ₱{{ number_format($report['summary']['total_profit'] ?? 0, 2) }}</div>
            <div class="small muted" style="font-size:9px; margin-top:2px">(Net Revenue - COGS)</div>
        </div>
        <div class="card">
            <div class="small muted">Profit Margin</div>
            <div style="font-size:16px; font-weight:700">
                {{ number_format($report['summary']['profit_margin'] ?? 0, 1) }}%</div>
            <div class="small muted" style="font-size:9px; margin-top:2px">(Profit / Net Revenue)</div>
        </div>
    </div>

    <!-- Revenue & Profit Calculation Breakdown -->
    <div class="breakdown">
        <div class="breakdown-title">Revenue & Profit Calculation</div>
        <div class="calc-row">
            <span>Gross Revenue (Product Sales)</span>
            <span class="calc-highlight">₱{{ number_format($report['summary']['gross_revenue'] ?? 0, 2) }}</span>
        </div>
        <div class="calc-row">
            <span>− Discounts</span>
            <span>₱{{ number_format($report['summary']['total_discount'] ?? 0, 2) }}</span>
        </div>
        <div class="calc-row calc-divider calc-highlight">
            <span>= Net Revenue</span>
            <span>₱{{ number_format($report['summary']['net_revenue'] ?? 0, 2) }}</span>
        </div>
        <div class="calc-row" style="margin-top: 6px;">
            <span>+ Tax</span>
            <span>₱{{ number_format($report['summary']['total_tax'] ?? 0, 2) }}</span>
        </div>
        {{-- <div class="calc-row">
            <span>+ Shipping</span>
            <span>₱{{ number_format($report['summary']['total_shipping'] ?? 0, 2) }}</span>
        </div> --}}
        <div class="calc-row calc-divider calc-highlight">
            <span>= Total Revenue (Customer Payment)</span>
            <span>₱{{ number_format($report['summary']['total_revenue'] ?? 0, 2) }}</span>
        </div>
        <div class="calc-row" style="margin-top: 8px; color: #6b46c1;">
            <span><strong>Net Revenue − COGS
                    (₱{{ number_format($report['summary']['total_cost'] ?? 0, 2) }})</strong></span>
            <span><strong>= Gross Profit
                    (₱{{ number_format($report['summary']['total_profit'] ?? 0, 2) }})</strong></span>
        </div>
        <div class="calc-row" style="color: #d97706;">
            <span><strong>Profit Margin = (Gross Profit ÷ Net Revenue) × 100</strong></span>
            <span><strong>= {{ number_format($report['summary']['profit_margin'] ?? 0, 1) }}%</strong></span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Product</th>
                <th>Brand</th>
                <th>Category</th>
                <th class="left">Sold Qty</th>
                <th class="left">Sold Amount</th>
                <th class="left">Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['product_sales'] ?? [] as $row)
                <tr>
                    <td>{{ $row->product_sku ?? 'SKU-' . ($row->product_id ?? '') }}</td>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->product_brand }}</td>
                    <td>{{ $row->product_category }}</td>
                    <td class="left">{{ (int) ($row->total_quantity ?? 0) }}</td>
                    <td class="left">₱{{ number_format($row->total_revenue ?? 0, 2) }}</td>
                    <td class="left">{{ (int) ($row->instock_qty ?? 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; padding-top: 10px; #ddd; font-size: 10px; color: #666;">
        <p>Report generated by {{ Auth()->user()->name ?? 'Unknown User' }} on
            {{ now()->format('F d, Y \a\t H:i:s') }}</p>
    </div>
</body>

</html>
