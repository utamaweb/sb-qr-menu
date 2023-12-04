<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            // unit_code, unit_name, is_active
            ['Pcs', 'Pieces', 1],
            ['Porsi', 'Porsi', 1],
            ['Gelas', 'Gelas', 1],
            ['Gr', 'Gram', 1],
            ['Kg', 'Kilogram', 1],
            ['Ltr', 'Liter', 1],
            ['Set', 'Set', 1],
            ['Bungkus', 'Bungkus', 1],
            ['Botol', 'Botol', 1],
            ['Box', 'Box', 1],
        ];

        foreach ($units as $unit) {
            Unit::create([
                'unit_code'  =>  $unit[0],
                'unit_name'  =>  $unit[1],
                'is_active'  =>  $unit[2],
            ]);
        }
    }
}
