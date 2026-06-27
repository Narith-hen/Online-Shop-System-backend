<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #1e3a5f; color: #fff; padding: 24px 32px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 4px 0 0; font-size: 13px; opacity: 0.85; }
        .body { padding: 32px; }
        .section-title { font-size: 14px; font-weight: 700; color: #1e3a5f; margin: 0 0 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-table { width: 100%; font-size: 13px; margin-bottom: 24px; }
        .info-table td { padding: 4px 0; color: #555; }
        .info-table td:last-child { text-align: right; font-weight: 600; color: #222; }
        .items-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 24px; }
        .items-table th { text-align: left; padding: 8px 0; border-bottom: 2px solid #eee; color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .items-table th:last-child, .items-table td:last-child { text-align: right; }
        .items-table td { padding: 10px 0; border-bottom: 1px solid #f0f0f0; color: #333; }
        .total-row td { font-weight: 700; font-size: 15px; color: #1e3a5f; border-bottom: none; padding-top: 12px; }
        .divider { border: 0; border-top: 2px solid #1e3a5f; margin: 16px 0; }
        .footer { text-align: center; padding: 24px 32px; font-size: 12px; color: #999; border-top: 1px solid #eee; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
        .badge-unpaid { background: #fef3cd; color: #856404; }
        .badge-pending { background: #cce5ff; color: #004085; }
        .btn { display: inline-block; padding: 10px 24px; background: #1e3a5f; color: #fff; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Receipt</h1>
            <p>Thank you for your purchase!</p>
        </div>
        <div class="body">
            <table class="info-table">
                <tr><td>Order #</td><td>{{ $order->id }}</td></tr>
                <tr><td>Date</td><td>{{ $order->created_at->format('F j, Y') }}</td></tr>
                <tr><td>Customer</td><td>{{ $order->user->name ?? 'N/A' }}</td></tr>
                <tr><td>Email</td><td>{{ $order->user->email ?? 'N/A' }}</td></tr>
                <tr><td>Payment</td><td>{{ $order->payment_method === 'aba' ? 'ABA Bank' : ($order->payment_method === 'acleda' ? 'ACLEDA Bank' : 'N/A') }}</td></tr>
                <tr><td>Payment Status</td>
                    <td><span class="badge badge-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span></td></tr>
            </table>

            <h2 class="section-title">Items</h2>
            <table class="items-table">
                <thead>
                    <tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr>
                </thead>
                <tbody>
                    @php $subtotal = 0; @endphp
                    @foreach ($order->items as $item)
                        @php $lineTotal = $item->quantity * $item->price; $subtotal += $lineTotal; @endphp
                        <tr>
                            <td>{{ $item->product->name ?? 'Product' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>${{ number_format($lineTotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="info-table">
                <tr><td>Subtotal</td><td>${{ number_format($subtotal, 2) }}</td></tr>
                <tr><td>Tax (10%)</td><td>${{ number_format($subtotal * 0.1, 2) }}</td></tr>
                <tr><td>Shipping</td><td style="color:#16a34a;">Free</td></tr>
                <hr class="divider">
                <tr class="total-row"><td>Total</td><td>${{ number_format($order->total, 2) }}</td></tr>
            </table>

            <div style="text-align:center; margin-top: 24px;">
                <a href="{{ config('app.url') }}/orders/{{ $order->id }}/receipt" class="btn">View Online Receipt</a>
            </div>
        </div>
        <div class="footer">
            <p style="margin:0;">&copy; {{ date('Y') }} Online Shop. All rights reserved.</p>
            <p style="margin:4px 0 0;">Quality products delivered to your door.</p>
        </div>
    </div>
</body>
</html>
