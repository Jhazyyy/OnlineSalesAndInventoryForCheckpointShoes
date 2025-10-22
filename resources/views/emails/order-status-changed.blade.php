<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Status Update</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #fff;
            padding: 30px;
            border: 1px solid #e2e8f0;
            border-top: none;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            margin: 10px 0;
        }
        .status-confirmed, .status-delivered {
            background: #10b981;
            color: white;
        }
        .status-shipped, .status-processing {
            background: #3b82f6;
            color: white;
        }
        .status-cancelled {
            background: #f59e0b;
            color: white;
        }
        .status-failed {
            background: #ef4444;
            color: white;
        }
        .order-details {
            background: #f7fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #64748b;
        }
        .detail-value {
            color: #1e293b;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #64748b;
            font-size: 14px;
            border-top: 1px solid #e2e8f0;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 24px;">Order Status Update</h1>
        <p style="margin: 10px 0 0; opacity: 0.9;">{{ config('app.name') }}</p>
    </div>

    <div class="content">
        <p>Hello {{ $order->customer->display_name }},</p>

        <p>Your order status has been updated:</p>

        <div style="text-align: center; margin: 20px 0;">
            <span class="status-badge status-{{ $newStatus }}">{{ ucfirst($newStatus) }}</span>
        </div>

        <div class="order-details">
            <h3 style="margin-top: 0; color: #1e293b;">Order Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">Order Number:</span>
                <span class="detail-value">{{ $order->order_number }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Order Date:</span>
                <span class="detail-value">{{ $order->order_date->format('M d, Y') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Total Amount:</span>
                <span class="detail-value">₱{{ number_format($order->total_amount, 2) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Previous Status:</span>
                <span class="detail-value">{{ ucfirst($oldStatus) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">New Status:</span>
                <span class="detail-value"><strong>{{ ucfirst($newStatus) }}</strong></span>
            </div>
        </div>

        @if($newStatus === 'delivered')
            <p style="color: #10b981; font-weight: 600;">✓ Your order has been successfully delivered!</p>
            <p>Thank you for your purchase. We hope you enjoy your products!</p>
        @elseif($newStatus === 'shipped')
            <p style="color: #3b82f6; font-weight: 600;">📦 Your order is on its way!</p>
            @if($order->tracking_number)
                <p>Tracking Number: <strong>{{ $order->tracking_number }}</strong></p>
            @endif
        @elseif($newStatus === 'confirmed')
            <p style="color: #10b981; font-weight: 600;">✓ Your order has been confirmed!</p>
            <p>We're preparing your items for shipment.</p>
        @elseif($newStatus === 'cancelled')
            <p style="color: #f59e0b; font-weight: 600;">⚠ Your order has been cancelled.</p>
            <p>If you have any questions, please contact our support team.</p>
        @elseif($newStatus === 'failed')
            <p style="color: #ef4444; font-weight: 600;">✗ There was an issue with your order.</p>
            <p>Please contact our support team for assistance.</p>
        @endif

        <div style="text-align: center;">
            <a href="{{ route('sales.orders.show', $order->order_id) }}" class="button">View Order Details</a>
        </div>

        <p style="margin-top: 30px; color: #64748b; font-size: 14px;">
            If you have any questions about your order, please don't hesitate to contact us.
        </p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p>This is an automated email. Please do not reply directly to this message.</p>
    </div>
</body>
</html>
