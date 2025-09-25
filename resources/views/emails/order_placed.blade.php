<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>Hello {{ $order->name }},</h2>

    <p>Thank you for shopping with <strong>Fritters Store</strong>! 🎉</p>

    <p>Your order has been placed successfully. Here are the details:</p>

    <p><strong>Order ID:</strong> #{{ $order->id }}</p>
    <p><strong>Date:</strong> {{ $order->created_at->format('d-m-Y h:i A') }}</p>
    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
    <p><strong>Total Amount:</strong> ₹{{ number_format($order->total, 2) }}</p>
    <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</p>


    <h3>Items Ordered:</h3>
    <ul>
        @foreach($order->orderItems as $item)
            <li>
                {{ $item->product->title ?? 'Product' }} - Qty: {{ $item->qty }} - 
                ₹{{ number_format($item->price, 2) }}
            </li>
        @endforeach
    </ul>

    <p>We will notify you once your order is shipped.</p>

    <p>Thanks again for your purchase! <br>
    <strong>- Fritters Store Team</strong></p>

    <hr>
    <p style="font-size: 12px; color: #666;">&copy; 2025 Fritters Store. All rights reserved.</p>
</body>
</html>
