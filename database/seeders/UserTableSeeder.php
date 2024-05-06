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
            'username' => 'superadmin',
            'email' => 'superadmin@admin.com',
            'password' => bcrypt('secret'),
            'phone' => '0888',
            'role_id' => '1',
        ]);
        $superadmin->assignRole('Superadmin');

        // $kasir = User::create([
        //     'name' => 'kasir',
        //     'username' => 'kasir',
        //     'email' => 'kasir@kasir.com',
        //     'password' => bcrypt('kasir'),
        //     'phone' => '0888',
        //     'warehouse_id' => 1,
        //     'role_id' => '2',
        // ]);
        // $kasir->assignRole('Kasir');

        // $customer = User::create([
        //     'name' => 'customer',
        //     'username' => 'customer',
        //     'email' => 'customer@customer.com',
        //     'password' => bcrypt('customer'),
        //     'phone' => '0888',
        //     'warehouse_id' => 1,
        //     'role_id' => '3',
        // ]);
        // $customer->assignRole('Customer');
    }
}
