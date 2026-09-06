<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Shawarma', 'icon' => 'shawarma', 'sort_order' => 1],
            ['name' => 'Afghani', 'icon' => 'shawarma', 'sort_order' => 2],
            ['name' => 'Burgers', 'icon' => 'burger', 'sort_order' => 3],
            ['name' => 'Roll Paratha', 'icon' => 'restaurant', 'sort_order' => 4],
            ['name' => 'Platters', 'icon' => 'restaurant', 'sort_order' => 5],
            ['name' => 'Wraps', 'icon' => 'restaurant', 'sort_order' => 6],
            ['name' => 'Fries', 'icon' => 'restaurant', 'sort_order' => 7],
            ['name' => 'Crispy', 'icon' => 'chicken', 'sort_order' => 8],
            ['name' => 'Kids Meal', 'icon' => 'kids', 'sort_order' => 9],
            ['name' => 'On Demand', 'icon' => 'restaurant', 'sort_order' => 10],
            ['name' => 'Extras', 'icon' => 'restaurant', 'sort_order' => 11],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                    'image_url' => null,
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
