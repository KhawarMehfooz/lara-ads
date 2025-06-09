<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCategories = [
            'Vehicles',
            'Real Estate',
            'Electronics',
            'Furniture',
            'Jobs',
            'Services',
            'Fashion',
            'Pets',
            'Books',
            'Others',
        ];

        foreach ($defaultCategories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
