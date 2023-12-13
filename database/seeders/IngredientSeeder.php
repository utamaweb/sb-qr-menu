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
            // name, first_stock, unit_id
            ['Ayam Reguler', 100, 1],
            ['Ayam Jumbo', 100, 1],
            ['Bebek', 100, 1],
        ];

        foreach ($bahans as $bahan) {
            Ingredient::create([
                'name'  =>  $bahan[0],
                'first_stock'  =>  $bahan[1],
                'unit_id'  =>  $bahan[2],
            ]);
        }
    }
}
