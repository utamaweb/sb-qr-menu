<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'id' => 4,
                'name' => 'products-edit',
                'guard_name' => 'web',
                'created_at' => '2018-06-03 01:00:09',
                'updated_at' => '2018-06-03 01:00:09',
            ],
            [
                'id' => 5,
                'name' => 'products-delete',
                'guard_name' => 'web',
                'created_at' => '2018-06-03 22:54:22',
                'updated_at' => '2018-06-03 22:54:22',
            ],
            [
                'id' => 6,
                'name' => 'products-add',
                'guard_name' => 'web',
                'created_at' => '2018-06-04 00:34:14',
                'updated_at' => '2018-06-04 00:34:14',
            ],
            ['id' => 7, 'name' => 'products-index', 'guard_name' => 'web', 'created_at' => '2018-06-04 03:34:27', 'updated_at' => '2018-06-04 03:34:27'],
            ['id' => 8, 'name' => 'purchases-index', 'guard_name' => 'web', 'created_at' => '2018-06-04 08:03:19', 'updated_at' => '2018-06-04 08:03:19'],
            ['id' => 9, 'name' => 'purchases-add', 'guard_name' => 'web', 'created_at' => '2018-06-04 08:12:25', 'updated_at' => '2018-06-04 08:12:25'],
            ['id' => 10, 'name' => 'purchases-edit', 'guard_name' => 'web', 'created_at' => '2018-06-04 09:47:36', 'updated_at' => '2018-06-04 09:47:36'],
            ['id' => 11, 'name' => 'purchases-delete', 'guard_name' => 'web', 'created_at' => '2018-06-04 09:47:36', 'updated_at' => '2018-06-04 09:47:36'],
            ['id' => 12, 'name' => 'sales-index', 'guard_name' => 'web', 'created_at' => '2018-06-04 10:49:08', 'updated_at' => '2018-06-04 10:49:08'],
            ['id' => 13, 'name' => 'sales-add', 'guard_name' => 'web', 'created_at' => '2018-06-04 10:49:52', 'updated_at' => '2018-06-04 10:49:52'],
            ['id' => 14, 'name' => 'sales-edit', 'guard_name' => 'web', 'created_at' => '2018-06-04 10:49:52', 'updated_at' => '2018-06-04 10:49:52'],
            ['id' => 15, 'name' => 'sales-delete', 'guard_name' => 'web', 'created_at' => '2018-06-04 10:49:53', 'updated_at' => '2018-06-04 10:49:53'],
            ['id' => 16, 'name' => 'quotes-index', 'guard_name' => 'web', 'created_at' => '2018-06-04 22:05:10', 'updated_at' => '2018-06-04 22:05:10'],
            ['id' => 17, 'name' => 'quotes-add', 'guard_name' => 'web', 'created_at' => '2018-06-04 22:05:10', 'updated_at' => '2018-06-04 22:05:10'],
            ['id' => 18, 'name' => 'quotes-edit', 'guard_name' => 'web', 'created_at' => '2018-06-04 22:05:10', 'updated_at' => '2018-06-04 22:05:10'],
            ['id' => 19, 'name' => 'quotes-delete', 'guard_name' => 'web', 'created_at' => '2018-06-04 22:05:10', 'updated_at' => '2018-06-04 22:05:10'],
            ['id' => 20, 'name' => 'transfers-index', 'guard_name' => 'web', 'created_at' => '2018-06-04 22:30:03', 'updated_at' => '2018-06-04 22:30:03'],
            ['id' => 21, 'name' => 'transfers-add', 'guard_name' => 'web', 'created_at' => '2018-06-04 22:30:03', 'updated_at' => '2018-06-04 22:30:03'],
            ['id' => 22, 'name' => 'transfers-edit', 'guard_name' => 'web', 'created_at' => '2018-06-04 22:30:03', 'updated_at' => '2018-06-04 22:30:03'],
            ['id' => 23, 'name' => 'transfers-delete', 'guard_name' => 'web', 'created_at' => '2018-06-04 22:30:03', 'updated_at' => '2018-06-04 22:30:03'],
            ['id' => 24, 'name' => 'returns-index', 'guard_name' => 'web', 'created_at' => '2018-06-04 22:50:24', 'updated_at' => '2018-06-04 22:50:24'],
            ['id' => 25, 'name' => 'returns-add', 'guard_name' => 'web', 'created_at' => '2018-06-04 22:50:24', 'updated_at' => '2018-06-04 22:50:24'],
            ['id' => 26, 'name' => 'returns-edit', 'guard_name' => 'web', 'created_at' => '2018-06-04 22:50:25', 'updated_at' => '2018-06-04 22:50:25'],
            ['id' => 27, 'name' => 'returns-delete', 'guard_name' => 'web', 'created_at' => '2018-06-04 22:50:25', 'updated_at' => '2018-06-04 22:50:25'],
            ['id' => 28, 'name' => 'customers-index', 'guard_name' => 'web', 'created_at' => '2018-06-04 23:15:54', 'updated_at' => '2018-06-04 23:15:54'],
            ['id' => 29, 'name' => 'customers-add', 'guard_name' => 'web', 'created_at' => '2018-06-04 23:15:55', 'updated_at' => '2018-06-04 23:15:55'],
            ['id' => 30, 'name' => 'customers-edit', 'guard_name' => 'web', 'created_at' => '2018-06-04 23:15:55', 'updated_at' => '2018-06-04 23:15:55'],
            ['id' => 31, 'name' => 'customers-delete', 'guard_name' => 'web', 'created_at' => '2018-06-04 23:15:55', 'updated_at' => '2018-06-04 23:15:55'],
            ['id' => 32, 'name' => 'suppliers-index', 'guard_name' => 'web', 'created_at' => '2018-06-04 23:40:12', 'updated_at' => '2018-06-04 23:40:12'],
            ['id' => 33, 'name' => 'suppliers-add', 'guard_name' => 'web', 'created_at' => '2018-06-04 23:40:12', 'updated_at' => '2018-06-04 23:40:12'],
            ['id' => 34, 'name' => 'suppliers-edit', 'guard_name' => 'web', 'created_at' => '2018-06-04 23:40:12', 'updated_at' => '2018-06-04 23:40:12'],
            ['id' => 35, 'name' => 'suppliers-delete', 'guard_name' => 'web', 'created_at' => '2018-06-04 23:40:12', 'updated_at' => '2018-06-04 23:40:12'],
            ['id' => 36, 'name' => 'product-report', 'guard_name' => 'web', 'created_at' => '2018-06-24 23:05:33', 'updated_at' => '2018-06-24 23:05:33'],
            ['id' => 37, 'name' => 'purchase-report', 'guard_name' => 'web', 'created_at' => '2018-06-24 23:24:56', 'updated_at' => '2018-06-24 23:24:56'],
            ['id' => 38, 'name' => 'sale-report', 'guard_name' => 'web', 'created_at' => '2018-06-24 23:33:13', 'updated_at' => '2018-06-24 23:33:13'],
            ['id' => 39, 'name' => 'customer-report', 'guard_name' => 'web', 'created_at' => '2018-06-24 23:36:51', 'updated_at' => '2018-06-24 23:36:51'],
            ['id' => 40, 'name' => 'due-report', 'guard_name' => 'web', 'created_at' => '2018-06-24 23:39:52', 'updated_at' => '2018-06-24 23:39:52'],
            ['id' => 41, 'name' => 'users-index', 'guard_name' => 'web', 'created_at' => '2018-06-25 00:00:10', 'updated_at' => '2018-06-25 00:00:10'],
            ['id' => 42, 'name' => 'users-add', 'guard_name' => 'web', 'created_at' => '2018-06-25 00:00:10', 'updated_at' => '2018-06-25 00:00:10'],
            ['id' => 43, 'name' => 'users-edit', 'guard_name' => 'web', 'created_at' => '2018-06-25 00:01:30', 'updated_at' => '2018-06-25 00:01:30'],
            ['id' => 44, 'name' => 'users-delete', 'guard_name' => 'web', 'created_at' => '2018-06-25 00:01:30', 'updated_at' => '2018-06-25 00:01:30'],
            ['id' => 45, 'name' => 'profit-loss', 'guard_name' => 'web', 'created_at' => '2018-07-14 21:50:05', 'updated_at' => '2018-07-14 21:50:05'],
            ['id' => 46, 'name' => 'best-seller', 'guard_name' => 'web', 'created_at' => '2018-07-14 22:01:38', 'updated_at' => '2018-07-14 22:01:38'],
            ['id' => 47, 'name' => 'daily-sale', 'guard_name' => 'web', 'created_at' => '2018-07-14 22:24:21', 'updated_at' => '2018-07-14 22:24:21'],
            ['id' => 48, 'name' => 'monthly-sale', 'guard_name' => 'web', 'created_at' => '2018-07-14 22:30:41', 'updated_at' => '2018-07-14 22:30:41'],
            ['id' => 49, 'name' => 'daily-purchase', 'guard_name' => 'web', 'created_at' => '2018-07-14 22:36:46', 'updated_at' => '2018-07-14 22:36:46'],
            ['id' => 50, 'name' => 'monthly-purchase', 'guard_name' => 'web', 'created_at' => '2018-07-14 22:48:17', 'updated_at' => '2018-07-14 22:48:17'],
            ['id' => 51, 'name' => 'payment-report', 'guard_name' => 'web', 'created_at' => '2018-07-14 23:10:41', 'updated_at' => '2018-07-14 23:10:41'],
            ['id' => 52, 'name' => 'warehouse-stock-report', 'guard_name' => 'web', 'created_at' => '2018-07-14 23:16:55', 'updated_at' => '2018-07-14 23:16:55'],
            ['id' => 53, 'name' => 'product-qty-alert', 'guard_name' => 'web', 'created_at' => '2018-07-14 23:33:21', 'updated_at' => '2018-07-14 23:33:21'],
            ['id' => 54, 'name' => 'supplier-report', 'guard_name' => 'web', 'created_at' => '2018-07-30 03:00:01', 'updated_at' => '2018-07-30 03:00:01'],
            ['id' => 55, 'name' => 'expenses-index', 'guard_name' => 'web', 'created_at' => '2018-09-05 01:07:10', 'updated_at' => '2018-09-05 01:07:
(56, 'expenses-add', 'web', '2018-09-05 01:07:10', '2018-09-05 01:07:10'),
(57, 'expenses-edit', 'web', '2018-09-05 01:07:10', '2018-09-05 01:07:10'),
(58, 'expenses-delete', 'web', '2018-09-05 01:07:11', '2018-09-05 01:07:11'),
(59, 'general_setting', 'web', '2018-10-19 23:10:04', '2018-10-19 23:10:04'),
(60, 'mail_setting', 'web', '2018-10-19 23:10:04', '2018-10-19 23:10:04'),
(61, 'pos_setting', 'web', '2018-10-19 23:10:04', '2018-10-19 23:10:04'),
(62, 'hrm_setting', 'web', '2019-01-02 10:30:23', '2019-01-02 10:30:23'),
(63, 'purchase-return-index', 'web', '2019-01-02 21:45:14', '2019-01-02 21:45:14'),
(64, 'purchase-return-add', 'web', '2019-01-02 21:45:14', '2019-01-02 21:45:14'),
(65, 'purchase-return-edit', 'web', '2019-01-02 21:45:14', '2019-01-02 21:45:14'),
(66, 'purchase-return-delete', 'web', '2019-01-02 21:45:14', '2019-01-02 21:45:14'),
(67, 'account-index', 'web', '2019-01-02 22:06:13', '2019-01-02 22:06:13'),
(68, 'balance-sheet', 'web', '2019-01-02 22:06:14', '2019-01-02 22:06:14'),
(69, 'account-statement', 'web', '2019-01-02 22:06:14', '2019-01-02 22:06:14'),
(70, 'department', 'web', '2019-01-02 22:30:01', '2019-01-02 22:30:01'),
(71, 'attendance', 'web', '2019-01-02 22:30:01', '2019-01-02 22:30:01'),
(72, 'payroll', 'web', '2019-01-02 22:30:01', '2019-01-02 22:30:01'),
(73, 'employees-index', 'web', '2019-01-02 22:52:19', '2019-01-02 22:52:19'),
(74, 'employees-add', 'web', '2019-01-02 22:52:19', '2019-01-02 22:52:19'),
(75, 'employees-edit', 'web', '2019-01-02 22:52:19', '2019-01-02 22:52:19'),
(76, 'employees-delete', 'web', '2019-01-02 22:52:19', '2019-01-02 22:52:19'),
(77, 'user-report', 'web', '2019-01-16 06:48:18', '2019-01-16 06:48:18'),
(78, 'stock_count', 'web', '2019-02-17 10:32:01', '2019-02-17 10:32:01'),
(79, 'adjustment', 'web', '2019-02-17 10:32:02', '2019-02-17 10:32:02'),
(80, 'sms_setting', 'web', '2019-02-22 05:18:03', '2019-02-22 05:18:03'),
(81, 'create_sms', 'web', '2019-02-22 05:18:03', '2019-02-22 05:18:03'),
(82, 'print_barcode', 'web', '2019-03-07 05:02:19', '2019-03-07 05:02:19'),
(83, 'empty_database', 'web', '2019-03-07 05:02:19', '2019-03-07 05:02:19'),
(84, 'customer_group', 'web', '2019-03-07 05:37:15', '2019-03-07 05:37:15'),
(85, 'unit', 'web', '2019-03-07 05:37:15', '2019-03-07 05:37:15'),
(86, 'tax', 'web', '2019-03-07 05:37:15', '2019-03-07 05:37:15'),
(87, 'gift_card', 'web', '2019-03-07 06:29:38', '2019-03-07 06:29:38'),
(88, 'coupon', 'web', '2019-03-07 06:29:38', '2019-03-07 06:29:38'),
(89, 'holiday', 'web', '2019-10-19 08:57:15', '2019-10-19 08:57:15'),
(90, 'warehouse-report', 'web', '2019-10-22 06:00:23', '2019-10-22 06:00:23'),
(91, 'warehouse', 'web', '2020-02-26 06:47:32', '2020-02-26 06:47:32'),
(92, 'brand', 'web', '2020-02-26 06:59:59', '2020-02-26 06:59:59'),
(93, 'billers-index', 'web', '2020-02-26 07:11:15', '2020-02-26 07:11:15'),
(94, 'billers-add', 'web', '2020-02-26 07:11:15', '2020-02-26 07:11:15'),
(95, 'billers-edit', 'web', '2020-02-26 07:11:15', '2020-02-26 07:11:15'),
(96, 'billers-delete', 'web', '2020-02-26 07:11:15', '2020-02-26 07:11:15'),
(97, 'money-transfer', 'web', '2020-03-02 05:41:48', '2020-03-02 05:41:48'),
(98, 'category', 'web', '2020-07-13 12:13:16', '2020-07-13 12:13:16'),
(99, 'delivery', 'web', '2020-07-13 12:13:16', '2020-07-13 12:13:16'),
(100, 'send_notification', 'web', '2020-10-31 06:21:31', '2020-10-31 06:21:31'),
(101, 'today_sale', 'web', '2020-10-31 06:57:04', '2020-10-31 06:57:04'),
(102, 'today_profit', 'web', '2020-10-31 06:57:04', '2020-10-31 06:57:04'),
(103, 'currency', 'web', '2020-11-09 00:23:11', '2020-11-09 00:23:11'),
(104, 'backup_database', 'web', '2020-11-15 00:16:55', '2020-11-15 00:16:55'),
(105, 'reward_point_setting', 'web', '2021-06-27 04:34:42', '2021-06-27 04:34:42'),
(106, 'revenue_profit_summary', 'web', '2022-02-08 13:57:21', '2022-02-08 13:57:21'),
(107, 'cash_flow', 'web', '2022-02-08 13:57:22', '2022-02-08 13:57:22'),
(108, 'monthly_summary', 'web', '2022-02-08 13:57:22', '2022-02-08 13:57:22'),
(109, 'yearly_report', 'web', '2022-02-08 13:57:22', '2022-02-08 13:57:22'),
(110, 'discount_plan', 'web', '2022-02-16 09:12:26', '2022-02-16 09:12:26'),
(111, 'discount', 'web', '2022-02-16 09:12:38', '2022-02-16 09:12:38'),
(112, 'product-expiry-report', 'web', '2022-03-30 05:39:20', '2022-03-30 05:39:20'),
(113, 'purchase-payment-index', 'web', '2022-06-05 14:12:27', '2022-06-05 14:12:27'),
(114, 'purchase-payment-add', 'web', '2022-06-05 14:12:28', '2022-06-05 14:12:28'),
(115, 'purchase-payment-edit', 'web', '2022-06-05 14:12:28', '2022-06-05 14:12:28'),
(116, 'purchase-payment-delete', 'web', '2022-06-05 14:12:28', '2022-06-05 14:12:28'),
(117, 'sale-payment-index', 'web', '2022-06-05 14:12:28', '2022-06-05 14:12:28'),
(118, 'sale-payment-add', 'web', '2022-06-05 14:12:28', '2022-06-05 14:12:28'),
(119, 'sale-payment-edit', 'web', '2022-06-05 14:12:28', '2022-06-05 14:12:28'),
(120, 'sale-payment-delete', 'web', '2022-06-05 14:12:28', '2022-06-05 14:12:28'),
(121, 'all_notification', 'web', '2022-06-05 14:12:29', '2022-06-05 14:12:29'),
(122, 'sale-report-chart', 'web', '2022-06-05 14:12:29', '2022-06-05 14:12:29'),
(123, 'dso-report', 'web', '2022-06-05 14:12:29', '2022-06-05 14:12:29'),
(124, 'product_history', 'web', '2022-08-25 14:04:05', '2022-08-25 14:04:05'),
(125, 'supplier-due-report', 'web', '2022-08-31 09:46:33', '2022-08-31 09:46:33'),
(126, 'custom_field', 'web', '2023-05-02 07:41:35', '2023-05-02 07:41:35');
        ];

        foreach ($permissions] as $index => $task) {
            $name = strtolower($task->name);
            $taskDescription = str_replace('-', ' ', strtolower($task->name));

            Permission::insert([
                ["name" => "lihat {$name}", "guard_name" => "web", "task_id" => $task->id],
                ["name" => "tambah {$name}", "guard_name" => "web", "task_id" => $task->id],
                ["name" => "ubah {$name}", "guard_name" => "web", "task_id" => $task->id],
                ["name" => "hapus {$name}", "guard_name" => "web", "task_id" => $task->id],
            ]);
        }

        $superadmin = Role::create(['name' => 'Superadmin']);
        $superadmin->givePermissionTo(Permission::all());
        $owner = Role::create(['name' => 'Admin']);
        $owner->givePermissionTo(Permission::all());

        // $allPermissionExUser = Permission::where("name", "!=", "lihat user")->where("name", "!=", "tambah user")->where("name", "!=", "ubah user")->where("name", "!=", "hapus user")->get();
        // $leader = Role::create(['name' => 'Staff']);
        // $leader->givePermissionTo($allPermissionExUser);
        // $admin = Role::create(['name' => 'Admin']);
        // $admin->givePermissionTo($allPermissionExUser);
    }
}
