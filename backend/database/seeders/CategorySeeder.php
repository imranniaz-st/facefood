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
            ['name' => 'Burgers', 'icon' => 'burger', 'sort_order' => 1],
            ['name' => 'Shawarmas', 'icon' => 'shawarma', 'sort_order' => 2],
            ['name' => 'Drinks', 'icon' => 'drink', 'sort_order' => 3],
            ['name' => 'Kids Meal', 'icon' => 'kids', 'sort_order' => 4],
            ['name' => 'Pizza', 'icon' => 'pizza', 'sort_order' => 5],
            ['name' => 'Broast', 'icon' => 'chicken', 'sort_order' => 6],
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
