<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderApiController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer.email' => ['required','email'],
            'customer.name' => ['required','string','max:255'],
            'items' => ['required','array','min:1'],
            'items.*.product_id' => ['required','integer','exists:products,id'],
            'items.*.quantity' => ['required','integer','min:1'],
        ]);

        try {
            $order = $this->orderService->createOrder($validated);

            return response()->json(['success' => true, 'order' => $order], 201);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => $ex->getMessage()], 422);
        }
    }

    public function checkStock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);
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

    public function show(Order $order): JsonResponse
    {
        $order->load(['customer', 'items.product']);

        return response()->json(['success' => true, 'order' => $order]);
    }

    public function history(Request $request, ?Customer $customer = null): JsonResponse
    {
        $email = $customer?->email ?? $request->input('email') ?? $request->query('email');

        if (! $email) {
            return response()->json([
                'success' => false,
                'message' => 'Email is required.',
            ], 422);
        }

        $customer = Customer::query()
            ->where('email', $email)
            ->with(['orders' => fn ($query) => $query->latest()->with('items.product')])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
            ],
            'orders' => $customer->orders,
        ]);
    }

    public function customers(): JsonResponse
    {
        $customers = Customer::with('orders')->get()->map(function (Customer $customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'orders_count' => $customer->orders->count(),
                'total_spent' => (float) $customer->orders->sum('grand_total'),
            ];
        });

        return response()->json([
            'success' => true,
            'customers' => $customers,
        ]);
    }

    public function lowStock(Request $request): JsonResponse
    {
        $threshold = (int) $request->input('threshold', 5);

        $products = Product::query()->where('stock_on_hand', '<', $threshold)->orderBy('stock_on_hand')->get();

        return response()->json(['success' => true, 'threshold' => $threshold, 'products' => $products]);
    }
}
