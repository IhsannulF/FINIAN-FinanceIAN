<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Food', 'color' => '#F97316'],
            ['name' => 'Transportation', 'color' => '#3B82F6'],
            ['name' => 'Shopping', 'color' => '#EC4899'],
            ['name' => 'Entertainment', 'color' => '#8B5CF6'],
            ['name' => 'Education', 'color' => '#10B981'],
            ['name' => 'Bills', 'color' => '#EF4444'],
            ['name' => 'Healthcare', 'color' => '#06B6D4'],
            ['name' => 'Others', 'color' => '#6B7280'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}