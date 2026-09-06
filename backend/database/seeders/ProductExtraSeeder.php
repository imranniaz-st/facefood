<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductExtra;
use Illuminate\Database\Seeder;

class ProductExtraSeeder extends Seeder
{
    public function run(): void
    {
        $globalExtras = [
            ['name' => 'Extra Topping', 'price' => 150],
            ['name' => 'Extra Dip of Sauce', 'price' => 50],
            ['name' => 'Chicken Items W/O Salad', 'price' => 50],
        ];

        $sizeExtras = [
            'Crispy Fries' => [
                ['name' => 'Large', 'price' => 200],
            ],
            'Garlic Mayo Fries' => [
                ['name' => 'Large', 'price' => 150],
            ],
            'Pizza Fries' => [
                ['name' => 'Medium', 'price' => 250],
                ['name' => 'Large', 'price' => 650],
            ],
            'Loaded Fries' => [
                ['name' => 'Medium', 'price' => 300],
                ['name' => 'Large', 'price' => 600],
            ],
            'Crunchy Strips + Fries' => [
                ['name' => '12 Pcs', 'price' => 450],
                ['name' => '1 KG', 'price' => 1550],
            ],
            'Nuggets + Fries' => [
                ['name' => '1 KG', 'price' => 350],
            ],
            'Shawarma Grilled Chicken' => [
                ['name' => '1 KG', 'price' => 900],
            ],
        ];

        $products = Product::query()->with('category')->get();

        foreach ($products as $product) {
            if ($product->category?->slug === 'extras') {
                continue;
            }

            $extras = $sizeExtras[$product->name] ?? [];
            if ($product->category?->slug !== 'extras') {
                $extras = array_merge($extras, $globalExtras);
            }

            foreach ($extras as $index => $extra) {
                ProductExtra::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'name' => $extra['name'],
                    ],
                    [
                        'price' => $extra['price'],
                        'sort_order' => $index,
                        'is_available' => true,
                    ]
                );
            }
        }
    }
}
