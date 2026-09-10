<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Jobs\SendOrderConfirmation;

class OrderService
{
    public function createOrder(array $validated)
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
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);

                $requestedQty = (int) $item['quantity'];

                if ($product->stock_on_hand < $requestedQty) {
                    throw new \Exception(
                        "The product '{$product->name}' has insufficient stock. " .
                        "Available stock: {$product->stock_on_hand}, " .
                        "requested: {$requestedQty}."
                    );
                }

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

            $grandTotal = $subtotal + $tax;

                $amountPaid = isset($validated['amount_paid']) ? (float) $validated['amount_paid'] : 0.0;

                if ($amountPaid < $grandTotal) {
                    throw new \Exception(
                        "The amount given ({$amountPaid}) is less than the order total ({$grandTotal})."
                    );
                }

                $balance = max($amountPaid - $grandTotal, 0);

            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'grand_total' => $grandTotal,
                'amount_paid' => $amountPaid,
                'balance' => $balance,
            ]);
            // dispatch a queued job to simulate sending confirmation
            SendOrderConfirmation::dispatch($order->fresh(['customer', 'items.product']));

            return $order->fresh(['customer', 'items.product']);
        });
    }
}
