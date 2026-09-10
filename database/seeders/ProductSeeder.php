<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Wireless Mouse',
                'code' => 'WM-001',
                'price' => 799.00,
                'tax_percentage' => 18.00,
                'stock_on_hand' => 25,
            ],
            [
                'name' => 'Mechanical Keyboard',
                'code' => 'MK-002',
                'price' => 2499.00,
                'tax_percentage' => 18.00,
                'stock_on_hand' => 15,
            ],
            [
                'name' => 'USB-C Hub',
                'code' => 'UCH-003',
                'price' => 1299.00,
                'tax_percentage' => 18.00,
                'stock_on_hand' => 8,
            ],
            [
                'name' => 'Laptop Stand',
                'code' => 'LS-004',
                'price' => 1499.00,
                'tax_percentage' => 18.00,
                'stock_on_hand' => 4,
            ],
            [
                'name' => 'Webcam',
                'code' => 'WC-005',
                'price' => 1899.00,
                'tax_percentage' => 18.00,
                'stock_on_hand' => 3,
            ],
            [
                'name' => 'Bluetooth Speaker',
                'code' => 'BS-006',
                'price' => 2199.00,
                'tax_percentage' => 18.00,
                'stock_on_hand' => 12,
            ],
            [
                'name' => 'HDMI Cable',
                'code' => 'HC-007',
                'price' => 499.00,
                'tax_percentage' => 18.00,
                'stock_on_hand' => 2,
            ],
            [
                'name' => 'Power Bank',
                'code' => 'PB-008',
                'price' => 1599.00,
                'tax_percentage' => 18.00,
                'stock_on_hand' => 20,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
