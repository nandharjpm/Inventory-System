@extends('layouts.app')

@section('title', 'Order #' . $order->id)

@section('content')

<div class="page-header">
    <div>
        <h1>Order #{{ $order->id }}</h1>

        <p>
            Order placed on
            {{ $order->created_at->format('d M Y, h:i A') }}
        </p>
    </div>

    <div class="page-actions">

        <a
            href="{{ route('orders.history', $order->customer->email) }}"
            class="btn btn-secondary"
        >
            ← Customer History
        </a>

        <a
            href="{{ route('orders.create') }}"
            class="btn btn-primary"
        >
            + New Order
        </a>

    </div>
</div>


<div class="show-layout">

    <div class="show-main">

        <section class="card customer-summary-card">

            <div class="card-header customer-header">
                <div>
                    <h2>Customer Details</h2>
                    <p>Information associated with this order.</p>
                </div>
            </div>

            <div class="show-customer">

                <div class="customer-avatar">
                    {{ strtoupper(substr($order->customer->name, 0, 1)) }}
                </div>

                <div>
                    <span class="detail-label">Customer Name</span>

                    <strong class="customer-name">
                        {{ $order->customer->name }}
                    </strong>
                </div>

                <div>
                    <span class="detail-label">Email Address</span>

                    <a
                        href="mailto:{{ $order->customer->email }}"
                        class="customer-email"
                    >
                        {{ $order->customer->email }}
                    </a>
                </div>

            </div>

        </section>


        <section class="card">

            <div class="card-header">
                <h2>Order Items</h2>
                <p>Products included in this order.</p>
            </div>

            @if($order->items->isEmpty())

                <div class="history-empty">
                    <div class="empty-icon">⌁</div>
                    <h3>No products</h3>
                    <p>This order does not contain any products.</p>
                </div>

            @else

                <div class="table-wrapper">

                    <table class="order-details-table">

                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Code</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Tax</th>
                                <th>Line Total</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($order->items as $item)

                                <tr>

                                    <td>
                                        <div class="show-product">
                                            <div class="product-icon">
                                                {{ strtoupper(substr($item->product->name, 0, 1)) }}
                                            </div>

                                            <strong>
                                                {{ $item->product->name }}
                                            </strong>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="product-code">
                                            {{ $item->product->code }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $item->quantity }}
                                    </td>

                                    <td>
                                        ₹{{ number_format($item->unit_price, 2) }}
                                    </td>

                                    <td>
                                        ₹{{ number_format($item->tax_amount, 2) }}
                                    </td>

                                    <td>
                                        <strong class="line-total-value">
                                            ₹{{ number_format($item->line_total, 2) }}
                                        </strong>
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </section>

    </div>


    <aside class="show-sidebar">

        <section class="card order-summary-card">

            <div class="card-header summary-header">
                <h2>Payment Summary</h2>
            </div>

            <div class="summary-body">

                <div class="summary-row">
                    <span>Subtotal</span>

                    <strong>
                        ₹{{ number_format($order->subtotal, 2) }}
                    </strong>
                </div>

                <div class="summary-row">
                    <span>Tax</span>

                    <strong>
                        ₹{{ number_format($order->tax, 2) }}
                    </strong>
                </div>

                <div class="summary-grand">

                    <span>Grand Total</span>

                    <strong>
                        ₹{{ number_format($order->grand_total, 2) }}
                    </strong>

                </div>

                <div class="summary-paid">

                    <span>Amount Paid</span>

                    <strong>
                        ₹{{ number_format($order->amount_paid, 2) }}
                    </strong>

                </div>

                <div class="summary-balance">

                    <span>Balance to Return</span>

                    <strong>
                        ₹{{ number_format($order->balance, 2) }}
                    </strong>

                </div>

            </div>

        </section>


        <section class="card order-info-card">

            <div class="card-header">
                <h2>Order Information</h2>
            </div>

            <div class="order-info">

                <div class="info-row">
                    <span>Order ID</span>
                    <strong>#{{ $order->id }}</strong>
                </div>

                <div class="info-row">
                    <span>Date</span>

                    <strong>
                        {{ $order->created_at->format('d M Y') }}
                    </strong>
                </div>

                <div class="info-row">
                    <span>Time</span>

                    <strong>
                        {{ $order->created_at->format('h:i A') }}
                    </strong>
                </div>

                <div class="info-row">
                    <span>Products</span>

                    <strong>
                        {{ $order->items->count() }}
                    </strong>
                </div>

            </div>

        </section>

    </aside>

</div>

@endsection