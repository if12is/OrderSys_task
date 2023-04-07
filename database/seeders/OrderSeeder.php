<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
           // Create an order with a customer name and email using the factory method
           $order = Order::create([
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com'
        ]);

        // Get an existing product from the products table
        $product = Product::first();

        // Attach the product to the order with a quantity
        $order->products()->attach([
            $product->id => ['quantity' => 2]
        ]);

    }
}
