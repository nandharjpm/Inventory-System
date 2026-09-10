@extends('layouts.app')

@section('title', 'Customers')

@section('content')

<div class="page-header">
    <div>
        <h1>Customers</h1>
        <p>All customers and their order summaries.</p>
    </div>

    <a href="{{ route('orders.create') }}" class="btn btn-primary">
        + Back
    </a>
</div>

<div class="card card-list">
    <div class="card-header">
        <h2>Customers</h2>
    </div>

    <div class="card-body">
        @if($customers->isEmpty())
            <p>No customers found.</p>
        @else
            <div style="overflow:auto;">
                <table class="table" style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="text-align:left;">
                            <th style="padding:12px;border-bottom:1px solid #eee;">Name</th>
                            <th style="padding:12px;border-bottom:1px solid #eee;">Email</th>
                            <th style="padding:12px;border-bottom:1px solid #eee;">Orders</th>
                            <th style="padding:12px;border-bottom:1px solid #eee;">Total Spent</th>
                            <th style="padding:12px;border-bottom:1px solid #eee;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $c)
                            <tr>
                                <td style="padding:12px;border-bottom:1px solid #f6f6f6;">{{ $c->name }}</td>
                                <td style="padding:12px;border-bottom:1px solid #f6f6f6;">{{ $c->email }}</td>
                                <td style="padding:12px;border-bottom:1px solid #f6f6f6;">{{ $c->orders_count }}</td>
                                <td style="padding:12px;border-bottom:1px solid #f6f6f6;">₹{{ number_format($c->total_spent, 2) }}</td>
                                <td style="padding:12px;border-bottom:1px solid #f6f6f6;">
                                    <a href="{{ route('orders.history', $c->email) }}" class="btn btn-sm btn-link">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection
