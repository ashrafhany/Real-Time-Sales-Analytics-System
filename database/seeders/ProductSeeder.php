<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop Pro 15"',
                'description' => 'High-performance laptop with 16GB RAM and 512GB SSD',
                'price' => 1299.99,
                'stock_quantity' => 50
            ],
            [
                'name' => 'Wireless Headphones',
                'description' => 'Noise-cancelling wireless headphones with 30-hour battery',
                'price' => 199.99,
                'stock_quantity' => 100
            ],
            [
                'name' => 'Smartphone X',
                'description' => 'Latest smartphone with 128GB storage and triple camera',
                'price' => 699.99,
                'stock_quantity' => 75
            ],
            [
                'name' => 'Gaming Mouse',
                'description' => 'RGB gaming mouse with programmable buttons',
                'price' => 79.99,
                'stock_quantity' => 200
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'Cherry MX Blue switches with RGB backlighting',
                'price' => 129.99,
                'stock_quantity' => 150
            ],
            [
                'name' => '4K Monitor',
                'description' => '27-inch 4K IPS monitor with USB-C connectivity',
                'price' => 399.99,
                'stock_quantity' => 30
            ],
            [
                'name' => 'Bluetooth Speaker',
                'description' => 'Portable waterproof speaker with 12-hour battery',
                'price' => 59.99,
                'stock_quantity' => 120
            ],
            [
                'name' => 'Tablet Air',
                'description' => '10.9-inch tablet with Apple Pencil support',
                'price' => 549.99,
                'stock_quantity' => 80
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
