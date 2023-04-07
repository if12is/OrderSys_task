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
        // Create a burger product with a price of 10
        $burger = Product::create(['name' => 'Burger', 'price' => 10]);

        // Attach some ingredients to the burger with their amounts
        $burger->ingredients()->attach([
            1 => ['amount' => 150], // 150g of beef
            2 => ['amount' => 30], // 30g of cheese
            3 => ['amount' => 20], // 20g of onion
        ]);
    }
}
