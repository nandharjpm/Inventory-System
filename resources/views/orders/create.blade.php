@extends('layouts.app')

@section('title', 'New Order')

@section('content')

<div class="page-header">
    <div>
        <h1>New Order</h1>
        <p>Create a new customer order.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>

        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    action="{{ route('orders.store') }}"
    method="POST"
    id="order-form"
>
    @csrf

    <div class="order-layout">

        {{-- Main order section --}}
        <div class="order-main">

            {{-- Customer --}}
            <section class="card">
                <div class="card-header">
                    <h2>Customer</h2>
                </div>

                <div class="form-grid">

                    <div class="form-group">
                        <label for="customer_email">
                            Email
                        </label>

                        <input
                            type="email"
                            id="customer_email"
                            name="customer[email]"
                            value="{{ old('customer.email') }}"
                            placeholder="customer@example.com"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="customer_name">
                            Name
                        </label>

                        <input
                            type="text"
                            id="customer_name"
                            name="customer[name]"
                            value="{{ old('customer.name') }}"
                            placeholder="Customer name"
                            required
                        >
                    </div>

                </div>
            </section>

            {{-- Products --}}
            <section class="card">

                <div class="card-header card-header-row">
                    <div>
                        <h2>Products</h2>
                        <p>Add products to this order.</p>
                    </div>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        id="add-product"
                    >
                        <span style="font-size: 16px; line-height: 1;">+</span>
                        Add Product
                    </button>
                </div>

                <div class="table-wrapper">

                    <table class="order-table">

                        <thead>
                            <tr>
                                <th>Product</th>
                                <th width="120">Qty</th>
                                <th width="130">Price</th>
                                <th width="140">Tax</th>
                                <th width="150">Line Total</th>
                                <th width="50"></th>
                            </tr>
                        </thead>

                        <tbody id="order-items">

                            {{-- Product rows inserted by JavaScript --}}

                        </tbody>

                    </table>

                </div>

                <div
                    id="empty-items"
                    class="empty-state"
                >
                    No products added yet.
                    Click <strong>Add Product</strong> to begin.
                </div>

            </section>

            {{-- Payment --}}
            <section class="card payment-card">

                <div class="card-header">
                    <h2>Payment</h2>
                </div>

                <div class="totals">

                    <div class="total-row">
                        <span>Subtotal</span>
                        <strong id="subtotal">₹0.00</strong>
                    </div>

                    <div class="total-row">
                        <span>Tax</span>
                        <strong id="tax">₹0.00</strong>
                    </div>

                    <div class="total-row total-grand">
                        <span>Grand Total</span>
                        <strong id="grand-total">₹0.00</strong>
                    </div>

                    <div class="form-group payment-input">
                        <label for="amount_paid">
                            Amount Given
                        </label>

                        <input
                            type="number"
                            id="amount_paid"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                        >
                    </div>

                    <div class="balance-row">
                        <span>Balance to Return</span>
                        <strong id="balance">₹0.00</strong>
                    </div>

                </div>

                <div class="form-actions">

                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="submit-order"
                    >
                        Generate Bill
                    </button>

                </div>

            </section>

        </div>

        {{-- Sidebar --}}
        <aside class="order-sidebar">

            <section class="card low-stock-card">

                <div class="card-header">
                    <h2>⚠ Low Stock Alert</h2>
                </div>

                @if($lowStockProducts->isEmpty())

                    <div class="empty-state">
                        All products have sufficient stock.
                    </div>

                @else

                    <ul class="low-stock-list">

                        @foreach($lowStockProducts as $product)

                            <li>
                                <span>{{ $product->name }}</span>

                                <strong>
                                    {{ $product->stock_on_hand }}
                                    left
                                </strong>
                            </li>

                        @endforeach

                    </ul>

                @endif

            </section>

        </aside>

    </div>

</form>

@endsection


@push('scripts')
@php
    $productData = $products->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'code' => $product->code,
            'price' => (float) $product->price,
            'tax_percentage' => (float) $product->tax_percentage,
            'stock_on_hand' => $product->stock_on_hand,
        ];
    })->values();
@endphp
<script>
    const products = @json($productData);

    let itemIndex = 0;

    const itemsContainer = document.getElementById('order-items');
    const emptyItems = document.getElementById('empty-items');

    const subtotalElement = document.getElementById('subtotal');
    const taxElement = document.getElementById('tax');
    const grandTotalElement = document.getElementById('grand-total');

    const amountPaidElement = document.getElementById('amount_paid');
    const balanceElement = document.getElementById('balance');

    function formatCurrency(value) {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR'
        }).format(value);
    }

    function productOptions() {
        return products.map(product => `
            <option value="${product.id}">
                ${product.name} (${product.code})
            </option>
        `).join('');
    }

    function addProductRow() {
        const row = document.createElement('tr');

        row.dataset.index = itemIndex;

        row.innerHTML = `
            <td>
                <select
                    name="items[${itemIndex}][product_id]"
                    class="product-select"
                    required
                >
                    <option value="">
                        Select product
                    </option>

                    ${productOptions()}
                </select>
            </td>

            <td>
                <input
                    type="number"
                    name="items[${itemIndex}][quantity]"
                    class="quantity-input"
                    min="1"
                    value="1"
                    required
                >
            </td>

            <td class="unit-price">
                ₹0.00
            </td>

            <td class="line-tax">
                ₹0.00
            </td>

            <td class="line-total">
                ₹0.00
            </td>

            <td>
                <button
                    type="button"
                    class="btn-remove remove-item"
                    title="Remove product"
                >
                    ×
                </button>
            </td>
        `;

        itemsContainer.appendChild(row);

        itemIndex++;

        updateEmptyState();
        updateTotals();
    }

    function getProduct(productId) {
        return products.find(
            product => product.id === Number(productId)
        );
    }

    function updateRow(row) {
        const select = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');

        const product = getProduct(select.value);
        const quantity = Number(quantityInput.value) || 0;

        if (!product) {
            row.querySelector('.unit-price').textContent = '₹0.00';
            row.querySelector('.line-tax').textContent = '₹0.00';
            row.querySelector('.line-total').textContent = '₹0.00';

            row.dataset.subtotal = 0;
            row.dataset.tax = 0;

            return;
        }

        const lineSubtotal = product.price * quantity;
        const lineTax =
            lineSubtotal * (product.tax_percentage / 100);

        const lineTotal = lineSubtotal + lineTax;

        row.querySelector('.unit-price').textContent =
            formatCurrency(product.price);

        row.querySelector('.line-tax').textContent =
            formatCurrency(lineTax);

        row.querySelector('.line-total').textContent =
            formatCurrency(lineTotal);

        row.dataset.subtotal = lineSubtotal;
        row.dataset.tax = lineTax;
    }

    function updateTotals() {
        const rows = itemsContainer.querySelectorAll('tr');

        let subtotal = 0;
        let tax = 0;

        rows.forEach(row => {
            updateRow(row);

            subtotal += Number(row.dataset.subtotal || 0);
            tax += Number(row.dataset.tax || 0);
        });

        const grandTotal = subtotal + tax;

        subtotalElement.textContent =
            formatCurrency(subtotal);

        taxElement.textContent =
            formatCurrency(tax);

        grandTotalElement.textContent =
            formatCurrency(grandTotal);

        updateBalance(grandTotal);
    }

    function updateBalance(grandTotal = null) {

        if (grandTotal === null) {
            grandTotal = calculateGrandTotal();
        }

        const amountPaid =
            Number(amountPaidElement.value) || 0;

        const balance =
            Math.max(amountPaid - grandTotal, 0);

        balanceElement.textContent =
            formatCurrency(balance);
    }

    function calculateGrandTotal() {

        let subtotal = 0;
        let tax = 0;

        itemsContainer
            .querySelectorAll('tr')
            .forEach(row => {
                subtotal += Number(row.dataset.subtotal || 0);
                tax += Number(row.dataset.tax || 0);
            });

        return subtotal + tax;
    }

    function updateEmptyState() {
        const hasItems =
            itemsContainer.querySelectorAll('tr').length > 0;

        emptyItems.style.display =
            hasItems ? 'none' : 'block';
    }

    document
        .getElementById('add-product')
        .addEventListener('click', addProductRow);

    itemsContainer.addEventListener('change', event => {

        if (
            event.target.classList.contains('product-select')
        ) {
            updateTotals();
        }
    });

    itemsContainer.addEventListener('input', event => {

        if (
            event.target.classList.contains('quantity-input')
        ) {
            updateTotals();
        }
    });

    itemsContainer.addEventListener('click', event => {

        if (
            event.target.classList.contains('remove-item')
        ) {
            event.target.closest('tr').remove();

            updateEmptyState();
            updateTotals();
        }
    });

    amountPaidElement.addEventListener('input', () => {
        updateBalance();
    });

    // Start with one product row.
    addProductRow();
</script>

@endpush