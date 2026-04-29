<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Loyer',
            'Salaires',
            'Électricité',
            'Eau',
            'Transport',
            'Télécommunications',
            'Fournitures de bureau',
            'Maintenance',
            'Marketing',
            'Assurances',
            'Impôts',
            'Frais bancaires',
            'Repas',
            'Formation',
            'Divers',
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(
                ['name' => $category],
                ['is_active' => true]
            );
        }
    }
}
