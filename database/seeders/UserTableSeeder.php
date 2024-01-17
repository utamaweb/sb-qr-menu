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
            'role_id' => '1',
        ]);
        $superadmin->assignRole('superadmin');

        $kasir = User::create([
            'name' => 'kasir',
            'email' => 'kasir@kasir.com',
            'password' => bcrypt('kasir'),
            'phone' => '0888',
            'warehouse_id' => 1,
            'role_id' => '2',
        ]);
        $kasir->assignRole('kasir');

        $customer = User::create([
            'name' => 'customer',
            'email' => 'customer@customer.com',
            'password' => bcrypt('customer'),
            'phone' => '0888',
            'warehouse_id' => 1,
            'role_id' => '3',
        ]);
        $kasir->assignRole('customer');
    }
}
