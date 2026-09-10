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

    public function create()
    {
        $products = Product::query()->get();

        $lowStockProducts = Product::query()->where('stock_on_hand', '<', 5)
            ->orderBy('stock_on_hand')
            ->get();

        return view('orders.create', [
            'products' => $products,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }

    public function checkStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $product = Product::query()->findOrFail(
            $validated['product_id']
        );

        $available = $product->stock_on_hand >= $validated['quantity'];

        return response()->json([
            'success' => true,
            'available' => $available,
            'product' => $product->name,
            'requested' => $validated['quantity'],
            'available_stock' => $product->stock_on_hand,
            'message' => $available
                ? 'Stock available.'
                : "Only {$product->stock_on_hand} unit(s) available.",
        ]);
    }


    public function store(Request $request)
    {
        try{
        
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
                'amount_paid' => [
                    'required',
                    'numeric',
                    'min:0',
                ],
            ]);

            $order = $this->orderService->createOrder($validated);

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Order created successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors(['error' => $ex->getMessage()])->withInput();
        }
    }

    public function show(Order $order)
    {
        $order->load([
            'customer',
            'items.product',
        ]);

        return view('orders.show', compact('order'));
    }

    public function history(string $email)
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