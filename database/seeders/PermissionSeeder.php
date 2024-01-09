<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
            ['id' => 55, 'name' => 'expenses-index', 'guard_name' => 'web', 'created_at' => '2018-09-05 01:07:10', 'updated_at' => '2018-09-05 01:07:00'],
                ['id' => 56, 'name' => 'expenses-add', 'guard_name' => 'web', 'created_at' => '2018-09-05 01:07:10', 'updated_at' => '2018-09-05 01:07:10'],
                ['id' => 57, 'name' => 'expenses-edit', 'guard_name' => 'web', 'created_at' => '2018-09-05 01:07:10', 'updated_at' => '2018-09-05 01:07:10'],
                ['id' => 58, 'name' => 'expenses-delete', 'guard_name' => 'web', 'created_at' => '2018-09-05 01:07:11', 'updated_at' => '2018-09-05 01:07:11'],
                ['id' => 59, 'name' => 'general_setting', 'guard_name' => 'web', 'created_at' => '2018-10-19 23:10:04', 'updated_at' => '2018-10-19 23:10:04'],
                ['id' => 60, 'name' => 'mail_setting', 'guard_name' => 'web', 'created_at' => '2018-10-19 23:10:04', 'updated_at' => '2018-10-19 23:10:04'],
                ['id' => 61, 'name' => 'pos_setting', 'guard_name' => 'web', 'created_at' => '2018-10-19 23:10:04', 'updated_at' => '2018-10-19 23:10:04'],
                ['id' => 62, 'name' => 'hrm_setting', 'guard_name' => 'web', 'created_at' => '2019-01-02 10:30:23', 'updated_at' => '2019-01-02 10:30:23'],
                ['id' => 63, 'name' => 'purchase-return-index', 'guard_name' => 'web', 'created_at' => '2019-01-02 21:45:14', 'updated_at' => '2019-01-02 21:45:14'],
                ['id' => 64, 'name' => 'purchase-return-add', 'guard_name' => 'web', 'created_at' => '2019-01-02 21:45:14', 'updated_at' => '2019-01-02 21:45:14'],
                ['id' => 65, 'name' => 'purchase-return-edit', 'guard_name' => 'web', 'created_at' => '2019-01-02 21:45:14', 'updated_at' => '2019-01-02 21:45:14'],
                ['id' => 66, 'name' => 'purchase-return-delete', 'guard_name' => 'web', 'created_at' => '2019-01-02 21:45:14', 'updated_at' => '2019-01-02 21:45:14'],
                ['id' => 67, 'name' => 'account-index', 'guard_name' => 'web', 'created_at' => '2019-01-02 22:06:13', 'updated_at' => '2019-01-02 22:06:13'],
                ['id' => 68, 'name' => 'balance-sheet', 'guard_name' => 'web', 'created_at' => '2019-01-02 22:06:14', 'updated_at' => '2019-01-02 22:06:14'],
                ['id' => 69, 'name' => 'account-statement', 'guard_name' => 'web', 'created_at' => '2019-01-02 22:06:14', 'updated_at' => '2019-01-02 22:06:14'],
                ['id' => 70, 'name' => 'department', 'guard_name' => 'web', 'created_at' => '2019-01-02 22:30:01', 'updated_at' => '2019-01-02 22:30:01'],
                ['id' => 71, 'name' => 'attendance', 'guard_name' => 'web', 'created_at' => '2019-01-02 22:30:01', 'updated_at' => '2019-01-02 22:30:01'],
                ['id' => 72, 'name' => 'payroll', 'guard_name' => 'web', 'created_at' => '2019-01-02 22:30:01', 'updated_at' => '2019-01-02 22:30:01'],
                ['id' => 73, 'name' => 'employees-index', 'guard_name' => 'web', 'created_at' => '2019-01-02 22:52:19', 'updated_at' => '2019-01-02 22:52:19'],
                ['id' => 74, 'name' => 'employees-add', 'guard_name' => 'web', 'created_at' => '2019-01-02 22:52:19', 'updated_at' => '2019-01-02 22:52:19'],
                ['id' => 75, 'name' => 'employees-edit', 'guard_name' => 'web', 'created_at' => '2019-01-02 22:52:19', 'updated_at' => '2019-01-02 22:52:19'],
                ['id' => 76, 'name' => 'employees-delete', 'guard_name' => 'web', 'created_at' => '2019-01-02 22:52:19', 'updated_at' => '2019-01-02 22:52:19'],
                ['id' => 77, 'name' => 'user-report', 'guard_name' => 'web', 'created_at' => '2019-01-16 06:48:18', 'updated_at' => '2019-01-16 06:48:18'],
                ['id' => 78, 'name' => 'stock_count', 'guard_name' => 'web', 'created_at' => '2019-02-17 10:32:01', 'updated_at' => '2019-02-17 10:32:01'],
                ['id' => 79, 'name' => 'adjustment', 'guard_name' => 'web', 'created_at' => '2019-02-17 10:32:02', 'updated_at' => '2019-02-17 10:32:02'],
                ['id' => 80, 'name' => 'sms_setting', 'guard_name' => 'web', 'created_at' => '2019-02-22 05:18:03', 'updated_at' => '2019-02-22 05:18:03'],
                ['id' => 81, 'name' => 'create_sms', 'guard_name' => 'web', 'created_at' => '2019-02-22 05:18:03', 'updated_at' => '2019-02-22 05:18:03'],
                ['id' => 82, 'name' => 'print_barcode', 'guard_name' => 'web', 'created_at' => '2019-03-07 05:02:19', 'updated_at' => '2019-03-07 05:02:19'],
                ['id' => 83, 'name' => 'empty_database', 'guard_name' => 'web', 'created_at' => '2019-03-07 05:02:19', 'updated_at' => '2019-03-07 05:02:19'],
                ['id' => 84, 'name' => 'customer_group', 'guard_name' => 'web', 'created_at' => '2019-03-07 05:37:15', 'updated_at' => '2019-03-07 05:37:15'],
                ['id' => 85, 'name' => 'unit', 'guard_name' => 'web', 'created_at' => '2019-03-07 05:37:15', 'updated_at' => '2019-03-07 05:37:15'],
                ['id' => 86, 'name' => 'tax', 'guard_name' => 'web', 'created_at' => '2019-03-07 05:37:15', 'updated_at' => '2019-03-07 05:37:15'],
                ['id' => 87, 'name' => 'gift_card', 'guard_name' => 'web', 'created_at' => '2019-03-07 06:29:38', 'updated_at' => '2019-03-07 06:29:38'],
                ['id' => 88, 'name' => 'coupon', 'guard_name' => 'web', 'created_at' => '2019-03-07 06:29:38', 'updated_at' => '2019-03-07 06:29:38'],
                ['id' => 89, 'name' => 'holiday', 'guard_name' => 'web', 'created_at' => '2019-10-19 08:57:15', 'updated_at' => '2019-10-19 08:57:15'],
                ['id' => 90, 'name' => 'warehouse-report', 'guard_name' => 'web', 'created_at' => '2019-10-22 06:00:23', 'updated_at' => '2019-10-22 06:00:23'],
                ['id' => 91, 'name' => 'warehouse', 'guard_name' => 'web', 'created_at' => '2020-02-26 06:47:32', 'updated_at' => '2020-02-26 06:47:32'],
                ['id' => 92, 'name' => 'brand', 'guard_name' => 'web', 'created_at' => '2020-02-26 06:59:59', 'updated_at' => '2020-02-26 06:59:59'],
                ['id' => 93, 'name' => 'billers-index', 'guard_name' => 'web', 'created_at' => '2020-02-26 07:11:15', 'updated_at' => '2020-02-26 07:11:15'],
                ['id' => 94, 'name' => 'billers-add', 'guard_name' => 'web', 'created_at' => '2020-02-26 07:11:15', 'updated_at' => '2020-02-26 07:11:15'],
                ['id' => 95, 'name' => 'billers-edit', 'guard_name' => 'web', 'created_at' => '2020-02-26 07:11:15', 'updated_at' => '2020-02-26 07:11:15'],
                ['id' => 96, 'name' => 'billers-delete', 'guard_name' => 'web', 'created_at' => '2020-02-26 07:11:15', 'updated_at' => '2020-02-26 07:11:15'],
                ['id' => 97, 'name' => 'money-transfer', 'guard_name' => 'web', 'created_at' => '2020-03-02 05:41:48', 'updated_at' => '2020-03-02 05:41:48'],
                ['id' => 98, 'name' => 'category', 'guard_name' => 'web', 'created_at' => '2020-07-13 12:13:16', 'updated_at' => '2020-07-13 12:13:16'],
                ['id' => 99, 'name' => 'delivery', 'guard_name' => 'web', 'created_at' => '2020-07-13 12:13:16', 'updated_at' => '2020-07-13 12:13:16'],
                ['id' => 100, 'name' => 'send_notification', 'guard_name' => 'web', 'created_at' => '2020-10-31 06:21:31', 'updated_at' => '2020-10-31 06:21:31'],
                ['id' => 101, 'name' => 'today_sale', 'guard_name' => 'web', 'created_at' => '2020-10-31 06:57:04', 'updated_at' => '2020-10-31 06:57:04'],
                ['id' => 102, 'name' => 'today_profit', 'guard_name' => 'web', 'created_at' => '2020-10-31 06:57:04', 'updated_at' => '2020-10-31 06:57:04'],
                ['id' => 103, 'name' => 'currency', 'guard_name' => 'web', 'created_at' => '2020-11-09 00:23:11', 'updated_at' => '2020-11-09 00:23:11'],
                ['id' => 104, 'name' => 'backup_database', 'guard_name' => 'web', 'created_at' => '2020-11-15 00:16:55', 'updated_at' => '2020-11-15 00:16:55'],
                [
                    'id' => 105,
                    'name' => 'reward_point_setting',
                    'guard_name' => 'web',
                    'created_at' => '2021-06-27 04:34:42',
                    'updated_at' => '2021-06-27 04:34:42',
                ],
                [
                    'id' => 106,
                    'name' => 'revenue_profit_summary',
                    'guard_name' => 'web',
                    'created_at' => '2022-02-08 13:57:21',
                    'updated_at' => '2022-02-08 13:57:21',
                ],
                [
                    'id' => 107,
                    'name' => 'cash_flow',
                    'guard_name' => 'web',
                    'created_at' => '2022-02-08 13:57:22',
                    'updated_at' => '2022-02-08 13:57:22',
                ],
                [
                    'id' => 108,
                    'name' => 'monthly_summary',
                    'guard_name' => 'web',
                    'created_at' => '2022-02-08 13:57:22',
                    'updated_at' => '2022-02-08 13:57:22',
                ],
                [
                    'id' => 109,
                    'name' => 'yearly_report',
                    'guard_name' => 'web',
                    'created_at' => '2022-02-08 13:57:22',
                    'updated_at' => '2022-02-08 13:57:22',
                ],
                [
                    'id' => 110,
                    'name' => 'discount_plan',
                    'guard_name' => 'web',
                    'created_at' => '2022-02-16 09:12:26',
                    'updated_at' => '2022-02-16 09:12:26',
                ],
                [
                    'id' => 111,
                    'name' => 'discount',
                    'guard_name' => 'web',
                    'created_at' => '2022-02-16 09:12:38',
                    'updated_at' => '2022-02-16 09:12:38',
                ],
                [
                    'id' => 112,
                    'name' => 'product-expiry-report',
                    'guard_name' => 'web',
                    'created_at' => '2022-03-30 05:39:20',
                    'updated_at' => '2022-03-30 05:39:20',
                ],
                [
                    'id' => 113,
                    'name' => 'purchase-payment-index',
                    'guard_name' => 'web',
                    'created_at' => '2022-06-05 14:12:27',
                    'updated_at' => '2022-06-05 14:12:27',
                ],
                [
                    'id' => 114,
                    'name' => 'purchase-payment-add',
                    'guard_name' => 'web',
                    'created_at' => '2022-06-05 14:12:28',
                    'updated_at' => '2022-06-05 14:12:28',
                ],
                [
                    'id' => 115,
                    'name' => 'purchase-payment-edit',
                    'guard_name' => 'web',
                    'created_at' => '2022-06-05 14:12:28',
                    'updated_at' => '2022-06-05 14:12:28',
                ],
                [
                    'id' => 116,
                    'name' => 'purchase-payment-delete',
                    'guard_name' => 'web',
                    'created_at' => '2022-06-05 14:12:28',
                    'updated_at' => '2022-06-05 14:12:28',
                ],
                [
                    'id' => 117,
                    'name' => 'sale-payment-index',
                    'guard_name' => 'web',
                    'created_at' => '2022-06-05 14:12:28',
                    'updated_at' => '2022-06-05 14:12:28',
                ],
                [
                    'id' => 118,
                    'name' => 'sale-payment-add',
                    'guard_name' => 'web',
                    'created_at' => '2022-06-05 14:12:28',
                    'updated_at' => '2022-06-05 14:12:28',
                ],
                [
                    'id' => 119,
                    'name' => 'sale-payment-edit',
                    'guard_name' => 'web',
                    'created_at' => '2022-06-05 14:12:28',
                    'updated_at' => '2022-06-05 14:12:28',
                ],
                [
                    'id' => 120,
                    'name' => 'sale-payment-delete',
                    'guard_name' => 'web',
                    'created_at' => '2022-06-05 14:12:28',
                    'updated_at' => '2022-06-05 14:12:28',
                ],
                [
                    'id' => 121,
                    'name' => 'all_notification',
                    'guard_name' => 'web',
                    'created_at' => '2022-06-05 14:12:29',
                    'updated_at' => '2022-06-05 14:12:29',
                ],
                [
                    'id' => 122,
                    'name' => 'sale-report-chart',
                    'guard_name' => 'web',
                    'created_at' => '2022-06-05 14:12:29',
                    'updated_at' => '2022-06-05 14:12:29',
                ],
                [
                    'id' => 123,
                    'name' => 'dso-report',
                    'guard_name' => 'web',
                    'created_at' => '2022-06-05 14:12:29',
                    'updated_at' => '2022-06-05 14:12:29',
                ],
                [
                    'id' => 124,
                    'name' => 'product_history',
                    'guard_name' => 'web',
                    'created_at' => '2022-08-25 14:04:05',
                    'updated_at' => '2022-08-25 14:04:05',
                ],
                [
                    'id' => 125,
                    'name' => 'supplier-due-report',
                    'guard_name' => 'web',
                    'created_at' => '2022-08-31 09:46:33',
                    'updated_at' => '2022-08-31 09:46:33',
                ],
                [
                    'id' => 126,
                    'name' => 'custom_field',
                    'guard_name' => 'web',
                    'created_at' => '2023-05-02 07:41:35',
                    'updated_at' => '2023-05-02 07:41:35',
                ]

        ];

        foreach ($permissions as $index => $permission) {
            $name = strtolower($permission['name']);

            Permission::insert([
                ["name" => $name, "guard_name" => "api"],
                ["name" => $name, "guard_name" => "api"],
                ["name" => $name, "guard_name" => "api"],
                ["name" => $name, "guard_name" => "api"],
            ]);
        }

        $superadmin = Role::create(['name' => 'Superadmin']);
        $superadmin->givePermissionTo(Permission::all());
        $owner = Role::create(['name' => 'Admin']);
        $owner->givePermissionTo(Permission::all());
        $kasir1 = Role::create(['name' => 'Kasir']);
        $kasir1->givePermissionTo(Permission::all());

        // $allPermissionExUser = Permission::where("name", "!=", "lihat user")->where("name", "!=", "tambah user")->where("name", "!=", "ubah user")->where("name", "!=", "hapus user")->get();
        // $leader = Role::create(['name' => 'Staff']);
        // $leader->givePermissionTo($allPermissionExUser);
        // $admin = Role::create(['name' => 'Admin']);
        // $admin->givePermissionTo($allPermissionExUser);
    }
}
