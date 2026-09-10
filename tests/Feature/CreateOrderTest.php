<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order_reduces_stock_and_returns_order(): void
    {
        $product = Product::create([
            'name' => 'Test Product',
            'code' => 'TP-1',
            'price' => 100.00,
            'tax_percentage' => 10.00,
            'stock_on_hand' => 5,
        ]);

        $payload = [
            'customer' => ['name' => 'Alice','email' => 'alice@example.com'],
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201)->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', ['grand_total' => 220.00]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_on_hand' => 3]);
    }

    public function test_create_order_fails_on_insufficient_stock(): void
    {
        $product = Product::create([
            'name' => 'Test Product 2',
            'code' => 'TP-2',
            'price' => 50.00,
            'tax_percentage' => 20.00,
            'stock_on_hand' => 1,
        ]);

        $payload = [
            'customer' => ['name' => 'Bob','email' => 'bob@example.com'],
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(422)->assertJson(['success' => false]);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_on_hand' => 1]);
    }
}
