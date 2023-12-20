<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = [
            // name, logo, description, is_active
            ['Outlet 1', 'Jl. Pahlawan', 1],
            ['Outlet 2', 'Jl. Merbabu', 1],
            ['Outlet 3', 'Jl. Selili', 1],
            ['Outlet 4', 'Jl. Juanda', 1],
            ['Outlet 5', 'Jl. Antasari', 1],
        ];

        foreach ($outlets as $outlet) {
            Warehouse::create([
                'name'  =>  $outlet[0],
                // 'logo'  =>  $outlet[1],
                // 'description'  =>  $outlet[2],
                'address'  =>  $outlet[1],
                'is_active'  =>  $outlet[2],
            ]);
        }
    }
}
