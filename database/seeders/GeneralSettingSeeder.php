<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GeneralSetting;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GeneralSetting::create([
            'site_title' => 'SB POS',
            'site_logo' => NULL,
            'is_rtl' => 0,
            'currency' => 1,
            'staff_access' => 'own',
            'without_stock' => 'no',
            'date_format' => 'd-m-Y',
            'developed_by' => 'UTAMAWEB',
            'invoice_format' => 'standard',
            'decimal' => 0,
            'state' => 1,
            'theme' => 'default.css',
            'currency_position' => 'prefix',
        ]);
    }
}
