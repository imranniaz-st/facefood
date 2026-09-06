<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductExtra;
use Illuminate\Database\Seeder;

class ProductExtraSeeder extends Seeder
{
    public function run(): void
    {
        $burgerToppings = [
            ['name' => 'Extra cheese', 'price' => 80],
            ['name' => 'Cheddar slice', 'price' => 60],
            ['name' => 'Extra patty', 'price' => 220],
            ['name' => 'Bacon', 'price' => 150],
            ['name' => 'Fried egg', 'price' => 70],
            ['name' => 'Jalapeños', 'price' => 40],
            ['name' => 'Onion rings', 'price' => 90],
            ['name' => 'Extra mayo', 'price' => 25],
            ['name' => 'Garlic sauce', 'price' => 35],
            ['name' => 'BBQ sauce', 'price' => 30],
            ['name' => 'Chipotle sauce', 'price' => 45],
            ['name' => 'Pickles', 'price' => 20],
        ];

        $pizzaToppings = [
            ['name' => 'Extra mozzarella', 'price' => 120],
            ['name' => 'Olives', 'price' => 80],
            ['name' => 'Mushrooms', 'price' => 70],
            ['name' => 'Pepperoni', 'price' => 150],
            ['name' => 'Jalapeños', 'price' => 50],
        ];

        $shawarmaToppings = [
            ['name' => 'Extra garlic sauce', 'price' => 40],
            ['name' => 'Extra meat', 'price' => 180],
            ['name' => 'Cheese', 'price' => 70],
            ['name' => 'Fries inside', 'price' => 60],
        ];

        $broastToppings = [
            ['name' => 'Spicy dip', 'price' => 40],
            ['name' => 'Garlic mayo', 'price' => 35],
            ['name' => 'Extra piece', 'price' => 280],
        ];

        $kidsToppings = [
            ['name' => 'Extra ketchup', 'price' => 15],
            ['name' => 'Extra fries', 'price' => 80],
        ];

        $byCategory = [
            'burgers' => $burgerToppings,
            'pizza' => $pizzaToppings,
            'shawarmas' => $shawarmaToppings,
            'broast' => $broastToppings,
            'kids-meal' => $kidsToppings,
        ];

        $products = Product::query()->with('category')->get();

        foreach ($products as $product) {
            $slug = $product->category?->slug;
            $extras = $byCategory[$slug] ?? [];
            if ($extras === []) {
                continue;
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
