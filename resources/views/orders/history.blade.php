@extends('layouts.app')

@section('title', 'Order History')

@section('content')

<div class="page-header">
    <div>
        <h1>Order History</h1>
        <p>View all orders placed by this customer.</p>
    </div>

    <a href="{{ route('orders.create') }}" class="btn btn-primary">
        + New Order
    </a>
</div>

<div class="history-layout">

    <section class="card customer-summary-card">

        <div class="card-header customer-header">
            <div>
                <h2>Customer</h2>
                <p>Customer information</p>
            </div>
        </div>

        <div class="customer-summary">

            <div class="customer-avatar">
                {{ strtoupper(substr($customer->name, 0, 1)) }}
            </div>

            <div class="customer-details">
                <h3>{{ $customer->name }}</h3>
                <p>{{ $customer->email }}</p>
            </div>

            <div class="customer-stat">
                <span>Total Orders</span>
                <strong>{{ $customer->orders->count() }}</strong>
            </div>

        </div>

    </section>


    <section class="card orders-history-card">

        <div class="card-header card-header-row">
            <div>
                <h2>Orders</h2>
                <p>Recent orders are shown first.</p>
            </div>

            <span class="order-count">
                {{ $customer->orders->count() }}
                {{ Str::plural('Order', $customer->orders->count()) }}
            </span>
        </div>


        @if($customer->orders->isEmpty())

            <div class="history-empty">
                <div class="empty-icon">⌁</div>

                <h3>No orders found</h3>

                <p>
                    This customer has not placed any orders yet.
                </p>

                <a href="{{ route('orders.create') }}" class="btn btn-primary">
                    Create First Order
                </a>
            </div>

        @else

            <div class="history-list">

                @foreach($customer->orders as $order)

                    <article class="history-order">

                        <div class="history-order-top">

                            <div>
                                <span class="order-label">ORDER #{{ $order->id }}</span>

                                <h3>
                                    Order #{{ $order->id }}
                                </h3>

                                <span class="order-date">
                                    {{ $order->created_at->format('d M Y, h:i A') }}
                                </span>
                            </div>

                            <div class="history-order-total">
                                <span>Total</span>

                                <strong>
                                    ₹{{ number_format($order->grand_total, 2) }}
                                </strong>
                            </div>

                        </div>


                        <div class="history-products">

                            @foreach($order->items as $item)

                                <div class="history-product">

                                    <div class="product-info">
                                        <strong>
                                            {{ $item->product->name }}
                                        </strong>

                                        @if($item->product->code)
                                            <span>
                                                {{ $item->product->code }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="product-quantity">
                                        × {{ $item->quantity }}
                                    </div>

                                    <div class="product-price">
                                        ₹{{ number_format($item->line_total, 2) }}
                                    </div>

                                </div>

                            @endforeach

                        </div>


                        <div class="history-order-footer">

                            <span>
                                {{ $order->items->count() }}
                                {{ Str::plural('product', $order->items->count()) }}
                            </span>

                            <a
                                href="{{ route('orders.show', $order) }}"
                                class="btn btn-view"
                            >
                                View Order →
                            </a>

                        </div>

                    </article>

                @endforeach

            </div>

        @endif

    </section>

</div>

@endsection