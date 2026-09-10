<html>
<body>
    <h2>Order Confirmation — #{{ $order->id }}</h2>

    <p>Hi {{ $order->customer->name }},</p>

    <p>Thank you for your order. Summary:</p>

    <ul>
        @foreach($order->items as $item)
            <li>{{ $item->quantity }} × {{ $item->product->name }} — ₹{{ number_format($item->line_total, 2) }}</li>
        @endforeach
    </ul>

    <p>Subtotal: ₹{{ number_format($order->subtotal, 2) }}<br>
    Tax: ₹{{ number_format($order->tax, 2) }}<br>
    Grand total: ₹{{ number_format($order->grand_total, 2) }}</p>

    <p>Regards,<br>The Store</p>
</body>
</html>
