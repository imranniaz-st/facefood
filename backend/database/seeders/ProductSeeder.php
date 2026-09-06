<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $bySlug = Category::query()->pluck('id', 'slug');

        $products = [
            [
                'category' => 'burgers',
                'name' => 'Zinger Burger',
                'description' => 'Crispy spicy chicken fillet with mayo, lettuce & signature sauce.',
                'ingredients' => 'Chicken fillet, bun, mayo, lettuce, signature sauce',
                'price' => 650,
                'rating' => 4.9,
                'calories' => 620,
                'spice_level' => 'hot',
                'prep_time_minutes' => 12,
                'is_popular' => true,
                'image_url' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&q=80',
            ],
            [
                'category' => 'burgers',
                'name' => 'Cheese Crispy Zinger',
                'description' => 'Double cheese melt over golden crispy zinger patty.',
                'ingredients' => 'Chicken fillet, cheddar, mozzarella, bun',
                'price' => 750,
                'rating' => 4.8,
                'calories' => 710,
                'spice_level' => 'medium',
                'prep_time_minutes' => 12,
                'is_popular' => true,
                'image_url' => 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=800&q=80',
            ],
            [
                'category' => 'burgers',
                'name' => 'Chicken Bite Burger',
                'description' => 'Juicy chicken bites stacked with pickles and special Facefood sauce.',
                'ingredients' => 'Chicken bites, pickles, Facefood sauce, bun',
                'price' => 580,
                'rating' => 4.7,
                'calories' => 540,
                'spice_level' => 'mild',
                'prep_time_minutes' => 10,
                'is_popular' => true,
                'image_url' => 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=800&q=80',
            ],
            [
                'category' => 'burgers',
                'name' => 'Beef Smash Burger',
                'description' => 'Double smashed beef patties with cheddar & caramelized onions.',
                'ingredients' => 'Beef patties, cheddar, onions, bun',
                'price' => 890,
                'rating' => 4.6,
                'calories' => 830,
                'spice_level' => 'medium',
                'prep_time_minutes' => 14,
                'is_popular' => false,
                'image_url' => 'https://images.unsplash.com/photo-1553979459-d2229ba7433b?w=800&q=80',
            ],
            [
                'category' => 'shawarmas',
                'name' => 'Chicken Shawarma',
                'description' => 'Tender chicken shawarma wrapped in soft pita with garlic sauce.',
                'ingredients' => 'Chicken, pita, garlic sauce, salad',
                'price' => 450,
                'rating' => 4.8,
                'calories' => 480,
                'spice_level' => 'medium',
                'prep_time_minutes' => 8,
                'is_popular' => true,
                'image_url' => 'https://images.unsplash.com/photo-1529006557810-274b9b2fc783?w=800&q=80',
            ],
            [
                'category' => 'shawarmas',
                'name' => 'Loaded Plate Shawarma',
                'description' => 'Shawarma plate with fries, salad & generous garlic drizzle.',
                'ingredients' => 'Chicken, fries, salad, garlic sauce',
                'price' => 720,
                'rating' => 4.7,
                'calories' => 890,
                'spice_level' => 'medium',
                'prep_time_minutes' => 12,
                'is_popular' => true,
                'image_url' => 'https://images.unsplash.com/photo-1603360946369-dc9bb6258143?w=800&q=80',
            ],
            [
                'category' => 'pizza',
                'name' => 'Double Cheese Pizza',
                'description' => 'Extra mozzarella & cheddar on a thin crispy base.',
                'ingredients' => 'Mozzarella, cheddar, tomato sauce, dough',
                'price' => 1200,
                'rating' => 4.9,
                'calories' => 980,
                'spice_level' => 'mild',
                'prep_time_minutes' => 18,
                'is_popular' => true,
                'image_url' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800&q=80',
            ],
            [
                'category' => 'pizza',
                'name' => 'Chicken Fajita Pizza',
                'description' => 'Spicy fajita chicken, peppers & onions on golden crust.',
                'ingredients' => 'Fajita chicken, peppers, onions, mozzarella',
                'price' => 1350,
                'rating' => 4.6,
                'calories' => 1050,
                'spice_level' => 'hot',
                'prep_time_minutes' => 20,
                'is_popular' => false,
                'image_url' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d264?w=800&q=80',
            ],
            [
                'category' => 'broast',
                'name' => 'Broast Piece',
                'description' => 'Crispy golden broast piece with house spices.',
                'ingredients' => 'Chicken, house spices, flour coating',
                'price' => 320,
                'rating' => 4.5,
                'calories' => 310,
                'spice_level' => 'medium',
                'prep_time_minutes' => 8,
                'is_popular' => true,
                'image_url' => 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=800&q=80',
            ],
            [
                'category' => 'broast',
                'name' => 'Family Broast Bucket',
                'description' => '8-piece crispy broast bucket perfect for sharing.',
                'ingredients' => '8 chicken pieces, house spices',
                'price' => 1890,
                'rating' => 4.8,
                'calories' => 2480,
                'spice_level' => 'medium',
                'prep_time_minutes' => 22,
                'is_popular' => false,
                'image_url' => 'https://images.unsplash.com/photo-1562967914-608f82629710?w=800&q=80',
            ],
            [
                'category' => 'drinks',
                'name' => 'Cold Drink (Regular)',
                'description' => 'Chilled soft drink — Coke, Sprite or Fanta.',
                'ingredients' => 'Carbonated soft drink',
                'price' => 120,
                'rating' => 4.4,
                'calories' => 140,
                'prep_time_minutes' => 1,
                'is_popular' => false,
                'image_url' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=800&q=80',
            ],
            [
                'category' => 'drinks',
                'name' => 'Mint Lemonade',
                'description' => 'Fresh mint lemonade — cool & refreshing.',
                'ingredients' => 'Lemon, mint, sugar, ice',
                'price' => 180,
                'rating' => 4.7,
                'calories' => 90,
                'prep_time_minutes' => 3,
                'is_popular' => true,
                'image_url' => 'https://images.unsplash.com/photo-1622597467836-f3285f2131b8?w=800&q=80',
            ],
            [
                'category' => 'kids-meal',
                'name' => 'Kids Happy Meal',
                'description' => 'Mini burger, fries, juice box & a surprise toy.',
                'ingredients' => 'Mini burger, fries, juice',
                'price' => 499,
                'rating' => 4.6,
                'calories' => 520,
                'spice_level' => 'mild',
                'prep_time_minutes' => 8,
                'is_popular' => true,
                'image_url' => 'https://images.unsplash.com/photo-1615367423047-f69378c2f830?w=800&q=80',
            ],
            [
                'category' => 'kids-meal',
                'name' => 'Kids Nuggets Box',
                'description' => '6 crispy chicken nuggets with ketchup & fries.',
                'ingredients' => 'Chicken nuggets, fries, ketchup',
                'price' => 420,
                'rating' => 4.5,
                'calories' => 480,
                'spice_level' => 'mild',
                'prep_time_minutes' => 8,
                'is_popular' => false,
                'image_url' => 'https://images.unsplash.com/photo-1562967916-eb82221dfb92?w=800&q=80',
            ],
        ];

        foreach ($products as $item) {
            $categoryId = $bySlug[$item['category']] ?? null;
            if (! $categoryId) {
                continue;
            }

            Product::updateOrCreate(
                ['name' => $item['name'], 'category_id' => $categoryId],
                [
                    'description' => $item['description'],
                    'ingredients' => $item['ingredients'] ?? null,
                    'price' => $item['price'],
                    'image_url' => $item['image_url'],
                    'rating' => $item['rating'],
                    'calories' => $item['calories'] ?? null,
                    'spice_level' => $item['spice_level'] ?? null,
                    'prep_time_minutes' => $item['prep_time_minutes'] ?? null,
                    'extras' => $item['extras'] ?? null,
                    'is_popular' => $item['is_popular'],
                    'is_available' => true,
                ]
            );
        }
    }
}
