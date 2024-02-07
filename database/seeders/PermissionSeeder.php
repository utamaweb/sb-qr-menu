<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = Menu::all();

        foreach ($tasks as $index => $task) {
            $name = strtolower($task->name);
            $taskDescription = str_replace('-', ' ', strtolower($task->name));

            Permission::insert([
                ["name" => "lihat-{$name}", "guard_name" => "web", "menu_id" => $task->id],
                ["name" => "tambah-{$name}", "guard_name" => "web", "menu_id" => $task->id],
                ["name" => "ubah-{$name}", "guard_name" => "web", "menu_id" => $task->id],
                ["name" => "hapus-{$name}", "guard_name" => "web", "menu_id" => $task->id],
            ]);
        }

        $superadmin = Role::create(['name' => 'Superadmin']);
        $superadmin->givePermissionTo(Permission::all());
        $kasir = Role::create(['name' => 'Kasir']);
        $kasir->givePermissionTo(Permission::all());
        $customer = Role::create(['name' => 'Customer']);
        // $customer->givePermissionTo(Permission::all());

        // $allPermissionExUser = Permission::where("name", "!=", "lihat user")->where("name", "!=", "tambah user")->where("name", "!=", "ubah user")->where("name", "!=", "hapus user")->get();
        // $leader = Role::create(['name' => 'Leader']);
        // $leader->givePermissionTo($allPermissionExUser);
        // $admin = Role::create(['name' => 'Admin']);
        // $admin->givePermissionTo($allPermissionExUser);
    }
}
