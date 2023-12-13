<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IngredientProducts;

class IngredientProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredientProducts = [
            // ingredient_id, product_id
            // AYAM REGULER
            [1, 1],
            [3, 1],

            // AYAM JUMBO
            [2,2],
            [4,2],

            // BEBEK PAKET
            [5, 3],
            [6, 3],
        ];

        foreach ($ingredientProducts as $ingPro) {
            IngredientProducts::create([
                'ingredient_id'  =>  $ingPro[0],
                'product_id'  =>  $ingPro[1],
            ]);
        }
    }
}
