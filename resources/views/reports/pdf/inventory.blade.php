<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 20px; margin: 0 0 6px; }
        h2 { font-size: 16px; margin: 20px 0 10px; border-bottom: 2px solid #333; padding-bottom: 4px; }
        .muted { color: #666; }
        .summary { border: 1px solid #ddd; padding: 8px 12px; margin: 12px 0 18px; background: #f9f9f9; }
        .summary-row { display: flex; justify-content: space-between; align-items: center; }
        .summary-item { display: inline-block; margin-right: 25px; }
        .summary-item:last-child { margin-right: 0; }
        .summary-label { font-size: 9px; color: #666; display: inline; margin-right: 5px; }
        .summary-value { font-size: 13px; font-weight: 700; display: inline; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px 6px; border-bottom: 1px solid #e5e5e5; }
        th { text-align: left; background: #f7f7f7; font-size: 11px; }
        .right { text-align: right; }
        .center { text-align: center; }
        .small { font-size: 11px; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: 600; }
        .badge-danger { background: #fee; color: #c00; }
        .badge-warning { background: #ffc; color: #860; }
        .badge-success { background: #efe; color: #070; }
        .badge-info { background: #def; color: #06c; }
        .section { margin: 20px 0; page-break-inside: avoid; }
    </style>
</head>
<body>
    <h1>Inventory Report</h1>
    <div class="muted small">Generated: {{ now()->format('F d, Y H:i:s') }}</div>
    @if(!empty($filters))
        <div class="muted small">
            Filters: 
            @if(!empty($filters['category'])) Category: {{ $filters['category'] }} @endif
            @if(!empty($filters['movement_category'])) Movement: {{ ucfirst($filters['movement_category']) }} @endif
            @if(!empty($filters['stock_status'])) Status: {{ str_replace('_', ' ', ucfirst($filters['stock_status'])) }} @endif
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="summary">
        <div class="summary-row">
            <span class="summary-item">
                <span class="summary-label">Total Products:</span>
                <span class="summary-value">{{ $report['summary']['total_products'] ?? 0 }}</span>
            </span>
            <span class="summary-item">
                <span class="summary-label">Total Stock:</span>
                <span class="summary-value">{{ number_format($report['summary']['total_stock'] ?? 0) }}</span>
            </span>
            <span class="summary-item">
                <span class="summary-label">Total Value:</span>
                <span class="summary-value">₱{{ number_format($report['summary']['total_value'] ?? 0, 2) }}</span>
            </span>
            <span class="summary-item">
                <span class="summary-label">Low Stock:</span>
                <span class="summary-value" style="color: #ff9800;">{{ $report['summary']['low_stock'] ?? 0 }}</span>
            </span>
            <span class="summary-item">
                <span class="summary-label">Out of Stock:</span>
                <span class="summary-value" style="color: #f44336;">{{ $report['summary']['out_of_stock'] ?? 0 }}</span>
            </span>
        </div>
    </div>

    <!-- Stock Status Breakdown -->
    <div class="section">
        <h2>Stock Status Breakdown</h2>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th class="left">Count</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Out of Stock</td>
                    <td class="left">{{ $report['stock_status']['out_of_stock'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Low Stock</td>
                    <td class="left">{{ $report['stock_status']['low_stock'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Critical Stock</td>
                    <td class="left">{{ $report['stock_status']['critical_stock'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Healthy Stock</td>
                    <td class="left">{{ $report['stock_status']['healthy_stock'] ?? 0 }}</td>
                </tr>
                {{-- <tr>
                    <td>Overstocked</td>
                    <td class="left">{{ $report['stock_status']['overstocked'] ?? 0 }}</td>
                </tr> --}}
            </tbody>
        </table>
    </div>

    <!-- Movement Analysis -->
    <div class="section">
        <h2>Movement Analysis</h2>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="left">Count</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Fast Moving</td>
                    <td class="left">{{ $report['movement_analysis']['fast_moving'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Slow Moving</td>
                    <td class="left">{{ $report['movement_analysis']['slow_moving'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Non Moving</td>
                    <td class="left">{{ $report['movement_analysis']['non_moving'] ?? 0 }}</td>
                </tr>
                {{-- <tr>
                    <td>Uncategorized</td>
                    <td class="right">{{ $report['movement_analysis']['uncategorized'] ?? 0 }}</td>
                </tr> --}}
            </tbody>
        </table>
    </div>

    <!-- Top Value Products -->
    @if(!empty($report['top_value_products']) && count($report['top_value_products']) > 0)
    <div class="section">
        <h2>Top 10 Highest Value Products</h2>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th class="right">Quantity</th>
                    <th class="right">Unit Price</th>
                    <th class="right">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['top_value_products'] as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->sku ?? 'N/A' }}</td>
                    <td>{{ $product->product_name ?? $product->name ?? 'Unknown' }}</td>
                    <td class="left">{{ number_format($product->quantity ?? 0) }}</td>
                    <td class="left">₱{{ number_format($product->price ?? 0, 2) }}</td>
                    <td class="left">₱{{ number_format($product->total_value ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Low Stock Products -->
    @if(!empty($report['low_stock_products']) && count($report['low_stock_products']) > 0)
    <div class="section">
        <h2>Low Stock Products</h2>
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th class="left">Current Qty</th>
                    {{-- <th>Status</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach($report['low_stock_products'] as $product)
                <tr>
                    <td>{{ $product->sku ?? 'N/A' }}</td>
                    <td>{{ $product->product_name ?? $product->name ?? 'Unknown' }}</td>
                    <td class="left">{{ $product->quantity ?? 0 }}</td>
                    {{-- <td class="left">{{ $product->reorder_level ?? 0 }}</td> --}}
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Out of Stock Products -->
    @if(!empty($report['out_of_stock_products']) && count($report['out_of_stock_products']) > 0)
    <div class="section">
        <h2>Out of Stock Products</h2>
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['out_of_stock_products'] as $product)
                <tr>
                    <td>{{ $product->sku ?? 'N/A' }}</td>
                    <td>{{ $product->product_name ?? $product->name ?? 'Unknown' }}</td>
                    <td>{{ $product->product_category ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- All Products -->
    @if(!empty($report['products']) && count($report['products']) > 0)
    <div class="section">
        <h2>All Products</h2>
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th class="left">Quantity</th>
                    <th class="left">Price</th>
                    <th class="left">Value</th>
                    <th>Movement</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['products'] as $product)
                <tr>
                    <td class="small">{{ $product->sku ?? 'N/A' }}</td>
                    <td>{{ $product->product_name ?? $product->name ?? 'Unknown' }}</td>
                    <td class="small">{{ $product->product_category ?? 'N/A' }}</td>
                    <td class="left">{{ number_format($product->quantity ?? 0) }}</td>
                    <td class="left">₱{{ number_format($product->price ?? 0, 2) }}</td>
                    <td class="left">₱{{ number_format(($product->quantity ?? 0) * ($product->price ?? 0), 2) }}</td>
                    <td class="small">{{ ucwords($product->movement_category ?? 'N/A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div style="margin-top: 30px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 10px; color: #666;">
        <p>Report generated by {{ Auth()->user()->name ?? 'Unknown User' }} on {{ now()->format('F d, Y \a\t H:i:s') }}</p>
    </div>
</body>
</html>
