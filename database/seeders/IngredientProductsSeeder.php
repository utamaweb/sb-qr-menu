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
            [1, 1],
            [1, 2],
            [1, 3],
            [2, 1],
            [2, 2],
        ];

        foreach ($ingredientProducts as $ingPro) {
            IngredientProducts::create([
                'ingredient_id'  =>  $ingPro[0],
                'product_id'  =>  $ingPro[1],
            ]);
        }
    }
}
