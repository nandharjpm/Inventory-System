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

    public function test_api_order_stock_check_route_returns_json(): void
    {
        $product = Product::create([
            'name' => 'API Stock Item',
            'code' => 'API-1',
            'price' => 80.00,
            'tax_percentage' => 5.00,
            'stock_on_hand' => 4,
        ]);

        $response = $this->postJson('/api/orders/check-stock', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('available', true)
            ->assertJsonPath('requested', 3);
    }

    public function test_api_customer_summary_route_returns_json(): void
    {
        $product = Product::create([
            'name' => 'Customer Summary Product',
            'code' => 'CS-1',
            'price' => 100.00,
            'tax_percentage' => 10.00,
            'stock_on_hand' => 10,
        ]);

        $customer = Customer::create([
            'name' => 'Charlie',
            'email' => 'charlie@example.com',
        ]);

        $customer->orders()->create([
            'subtotal' => 100.00,
            'tax' => 10.00,
            'grand_total' => 110.00,
            'amount_paid' => 110.00,
            'balance' => 0.00,
        ]);

        $response = $this->getJson('/api/orders/customers');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['email' => 'charlie@example.com']);
    }
}
