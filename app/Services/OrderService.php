<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class OrderService
{
    public function createOrder(array $validated): Order
    {
        return DB::transaction(function () use ($validated) {
            $customer = Customer::query()->firstOrCreate(
                ['email' => $validated['customer']['email']],
                ['name' => $validated['customer']['name']]
            );

            $order = $customer->orders()->create([
                'subtotal' => 0,
                'tax' => 0,
                'grand_total' => 0,
            ]);

            $subtotal = 0;
            $tax = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::query()->findOrFail($item['product_id']);

                $lineSubtotal = (float) $product->price * (int) $item['quantity'];
                $lineTax = $lineSubtotal * ((float) $product->tax_percentage / 100);
                $lineTotal = $lineSubtotal + $lineTax;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'tax_percentage' => $product->tax_percentage,
                    'line_subtotal' => $lineSubtotal,
                    'line_tax' => $lineTax,
                    'line_total' => $lineTotal,
                ]);

                $subtotal += $lineSubtotal;
                $tax += $lineTax;

                $product->decrement('stock_on_hand', $item['quantity']);
            }

            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'grand_total' => $subtotal + $tax,
            ]);

            return $order->fresh(['customer', 'items.product']);
        });
    }
}
