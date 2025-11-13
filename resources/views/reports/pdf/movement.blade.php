<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Movement Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 20px; margin: 0 0 6px; }
        h2 { font-size: 16px; margin: 20px 0 10px; border-bottom: 2px solid #333; padding-bottom: 4px; }
        .muted { color: #666; }
        .summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 12px 0 18px; }
        .card { border: 1px solid #ddd; padding: 10px; border-radius: 6px; }
        .card-title { font-size: 10px; color: #666; margin-bottom: 4px; }
        .card-value { font-size: 16px; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px 6px; border-bottom: 1px solid #e5e5e5; }
        th { text-align: left; background: #f7f7f7; font-size: 11px; }
        .right { text-align: right; }
        .center { text-align: center; }
        .small { font-size: 11px; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: 600; }
        .badge-in { background: #efe; color: #070; }
        .badge-out { background: #fee; color: #c00; }
        .badge-adjustment { background: #ffc; color: #860; }
        .section { margin: 20px 0; page-break-inside: avoid; }
    </style>
</head>
<body>
    <h1>Stock Movement Report</h1>
    <div class="muted small">Period: {{ $filters['start_date'] ?? ($report['period']['start_date'] ?? '') }} to {{ $filters['end_date'] ?? ($report['period']['end_date'] ?? '') }}</div>
    <div class="muted small">Generated: {{ now()->format('F d, Y H:i:s') }}</div>

    <!-- Summary Cards -->
    <div class="summary">
        <div class="card">
            <div class="card-title">Total Movements</div>
            <div class="card-value">{{ number_format($report['summary']['total_movements'] ?? 0) }}</div>
        </div>
        <div class="card">
            <div class="card-title">Stock In</div>
            <div class="card-value" style="color: #4caf50;">{{ number_format($report['summary']['stock_in'] ?? 0) }}</div>
        </div>
        <div class="card">
            <div class="card-title">Stock Out</div>
            <div class="card-value" style="color: #f44336;">{{ number_format($report['summary']['stock_out'] ?? 0) }}</div>
        </div>
        <div class="card">
            <div class="card-title">Adjustments</div>
            <div class="card-value" style="color: #ff9800;">{{ number_format($report['summary']['adjustments'] ?? 0) }}</div>
        </div>
    </div>

    <!-- Movements by Type -->
    @if(!empty($report['movements_by_type']) && count($report['movements_by_type']) > 0)
    <div class="section">
        <h2>Movements by Type</h2>
        <table>
            <thead>
                <tr>
                    <th>Movement Type</th>
                    <th class="right">Count</th>
                    <th class="right">Total Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['movements_by_type'] as $type)
                <tr>
                    <td class="small">{{ ucfirst(str_replace('_', ' ', $type->type)) }}</td>
                    <td class="right">{{ number_format($type->count ?? 0) }}</td>
                    <td class="right">{{ number_format($type->total_quantity ?? 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Most Active Products -->
    @if(!empty($report['most_active_products']) && count($report['most_active_products']) > 0)
    <div class="section">
        <h2>Top 10 Most Active Products</h2>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Product Name</th>
                    <th class="right">Movements</th>
                    <th class="right">Stock In</th>
                    <th class="right">Stock Out</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['most_active_products'] as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->name ?? 'Unknown' }}</td>
                    <td class="right">{{ number_format($product->movement_count ?? 0) }}</td>
                    <td class="right" style="color: #4caf50;">+{{ number_format($product->total_in ?? 0) }}</td>
                    <td class="right" style="color: #f44336;">-{{ number_format($product->total_out ?? 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Movements by Category -->
    @if(!empty($report['movements_by_category']) && count($report['movements_by_category']) > 0)
    <div class="section">
        <h2>Movements by Product Category</h2>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="right">Products</th>
                    <th class="right">Total Movements</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['movements_by_category'] as $category)
                <tr>
                    <td>{{ $category->movement_category ?? 'Unknown' }}</td>
                    <td class="right">{{ number_format($category->product_count ?? 0) }}</td>
                    <td class="right">{{ number_format($category->total_movements ?? 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Recent Movements -->
    @if(!empty($report['recent_movements']) && count($report['recent_movements']) > 0)
    <div class="section">
        <h2>Recent Stock Movements (Last 50)</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th class="right">Quantity</th>
                    <th>Reference</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['recent_movements'] as $movement)
                <tr>
                    <td class="small">{{ \Carbon\Carbon::parse($movement->date)->format('M d, Y H:i') }}</td>
                    <td>{{ $movement->product_name ?? 'Unknown' }}</td>
                    <td>
                        <span class="badge {{ $movement->type == 'in' ? 'badge-in' : 'badge-out' }}">
                            {{ strtoupper($movement->type) }}
                        </span>
                    </td>
                    <td class="right">
                        {{ $movement->type == 'in' ? '+' : '-' }}{{ number_format($movement->quantity ?? 0) }}
                    </td>
                    <td class="small">{{ $movement->reference ?? 'N/A' }}</td>
                    <td class="small">{{ Str::limit($movement->notes ?? '-', 40) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Daily Movement Trend -->
    @if(!empty($report['movements_by_date']) && count($report['movements_by_date']) > 0)
    <div class="section">
        <h2>Daily Movement Trend</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="right">In</th>
                    <th class="right">Out</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['movements_by_date']->take(30) as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                    <td class="right" style="color: #4caf50;">{{ number_format($day->in_count ?? 0) }}</td>
                    <td class="right" style="color: #f44336;">{{ number_format($day->out_count ?? 0) }}</td>
                    <td class="right">{{ number_format($day->total_count ?? 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div style="margin-top: 30px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 10px; color: #666;">
        <p>Report generated by Checkpoint Sales and Inventory System on {{ now()->format('F d, Y \a\t H:i:s') }}</p>
    </div>
</body>
</html>
