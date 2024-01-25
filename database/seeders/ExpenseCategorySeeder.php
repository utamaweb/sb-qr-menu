<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $expenseCategories = [
            'Konsumsi', 'Es Batu', 'Timun', 'Bawang Goreng', 'Suun', 'Wortel', 'Beras', 'Gula', 'Minyak'
        ];

        foreach ($expenseCategories as $expenseCategory) {
            ExpenseCategory::create([
                'name'  =>  $expenseCategory,
            ]);
        }
    }
}
