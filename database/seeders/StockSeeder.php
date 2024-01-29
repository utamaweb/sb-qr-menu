<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Ingredient;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = Ingredient::get();

        foreach ($ingredients as $stock) {
            Stock::create([
                'warehouse_id'  =>  1,
                'ingredient_id'  =>  $stock->id,
                'first_stock'  =>  0,
                'stock_in'  =>  0,
                'stock_used'  =>  0,
                'last_stock'  =>  0,
            ]);
        }
    }
}
