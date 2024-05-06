<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // name, image, is_active
            ['Ayam', '', 1],
            ['Ikan', '', 1],
            ['Bebek', '', 1],
            ['Sapi', '', 1],
            ['Kambing', '', 1],
            ['Cumi', '', 1],
            ['Minuman', '', 1],
            ['Makanan Ringan', '', 1],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name'  =>  $category[0],
                'is_active'  =>  $category[2],
            ]);
        }
    }
}
