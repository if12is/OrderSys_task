<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $ingredients = [
            ['name' => 'Beef', 'stock' => 20000, 'threshold' => 10000], // 20000g of beef
            ['name' => 'Cheese', 'stock' => 5000, 'threshold' => 2500], // 5000g of cheese
            ['name' => 'Onion', 'stock' => 1000, 'threshold' => 500],   // 1000g of Onion
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::create($ingredient);
        }
    }
}
