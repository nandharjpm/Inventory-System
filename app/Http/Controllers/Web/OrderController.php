<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {
    }

    public function create(): View
    {
        $products = Product::query()->orderBy('name')->get();

        $lowStockProducts = Product::query()->where(
                'stock_on_hand',
                '<',
                5
            )
            ->orderBy('stock_on_hand')
            ->get();

        return view('orders.create', [
            'products' => $products,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer.email' => [
                'required',
                'email',
            ],
            'customer.name' => [
                'required',
                'string',
                'max:255',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $order = $this->orderService->createOrder($validated);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order created successfully.');
    }

    public function show(Order $order): View
    {
        $order->load([
            'customer',
            'items.product',
        ]);

        return view('orders.show', compact('order'));
    }

    public function history(string $email): View
    {
        $customer = Customer::query()
            ->where('email', $email)
            ->with([
                'orders' => fn ($query) => $query
                    ->latest()
                    ->with('items.product'),
            ])
            ->firstOrFail();

        return view('orders.history', compact('customer'));
    }
}