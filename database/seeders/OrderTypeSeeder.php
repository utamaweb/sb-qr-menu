<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderType;

class OrderTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            // name
            ['Makan Di Tempat'],
            ['Bungkus'],
        ];

        foreach ($types as $type) {
            OrderType::create([
                'name'  =>  $type[0],
            ]);
        }
    }
}
