<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = User::create([
            'name' => 'superadmin',
            'email' => 'superadmin@admin.com',
            'password' => bcrypt('secret'),
            'phone' => '0888',
            'company_name' => '',
            'role_id' => '1',
            'is_active' => '1',
        ]);

        $superAdmin->assignRole('superadmin');
    }
}
