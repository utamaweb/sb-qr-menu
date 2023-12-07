<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bahans = [
            // name, first_stock, stock_in, stock_used, adjustment, last_stock, unit_id
            ['Gula', 0, 16, 12, 0, 4, 5],
            ['Bumbu Kuning', 0, 16, 12, 0, 4, 5],
        ];

        foreach ($bahans as $bahan) {
            Ingredient::create([
                'name'  =>  $bahan[0],
                'first_stock'  =>  $bahan[1],
                'stock_in'  =>  $bahan[2],
                'stock_used'  =>  $bahan[3],
                // 'adjustment'  =>  $bahan[4],
                'last_stock'  =>  $bahan[5],
                'unit_id'  =>  $bahan[6],
            ]);
        }
    }
}
