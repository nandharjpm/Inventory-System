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

    public function history(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required','email']
        ]);

        $customer = Customer::query()->where('email', $validated['email'])->with(['orders' => fn($q) => $q->latest()->with('items.product')])->firstOrFail();

        return response()->json(['success' => true, 'orders' => $customer->orders]);
    }

    public function lowStock(Request $request): JsonResponse
    {
        $threshold = (int) $request->input('threshold', 5);

        $products = Product::query()->where('stock_on_hand', '<', $threshold)->orderBy('stock_on_hand')->get();

        return response()->json(['success' => true, 'threshold' => $threshold, 'products' => $products]);
    }
}
