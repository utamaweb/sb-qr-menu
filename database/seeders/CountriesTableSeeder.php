<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    // php artisan db:seed --class=CountriesTableSeeder

    public function run(): void
    {
       Country::factory()
        ->count(5)
        ->create();
    }
}
