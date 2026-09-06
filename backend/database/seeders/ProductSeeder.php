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
            // Shawarma
            ['category' => 'shawarma', 'sku' => 'FF-SW-001', 'name' => 'Chicken Shawarma', 'price' => 300, 'is_popular' => true, 'image' => 'photo-1529006557810-274b9b2fc783'],
            ['category' => 'shawarma', 'sku' => 'FF-SW-002', 'name' => 'Zinger Shawarma', 'price' => 400, 'is_popular' => true, 'image' => 'photo-1529006557810-274b9b2fc783'],
            ['category' => 'shawarma', 'sku' => 'FF-SW-003', 'name' => 'Loaded Shawarma', 'price' => 550, 'is_popular' => true, 'image' => 'photo-1603360946369-dc9bb6258143'],

            // Afghani
            ['category' => 'afghani', 'sku' => 'FF-AF-001', 'name' => 'Simple Afghani', 'price' => 300, 'image' => 'photo-1599487488170-d11ec708c0f7'],
            ['category' => 'afghani', 'sku' => 'FF-AF-002', 'name' => 'Chicken Afghani', 'price' => 500, 'is_popular' => true, 'image' => 'photo-1599487488170-d11ec708c0f7'],
            ['category' => 'afghani', 'sku' => 'FF-AF-003', 'name' => 'Sausage Afghani', 'price' => 500, 'image' => 'photo-1599487488170-d11ec708c0f7'],
            ['category' => 'afghani', 'sku' => 'FF-AF-004', 'name' => 'Zinger Afghani', 'price' => 500, 'image' => 'photo-1599487488170-d11ec708c0f7'],
            ['category' => 'afghani', 'sku' => 'FF-AF-005', 'name' => 'Special Afghani', 'price' => 700, 'is_popular' => true, 'image' => 'photo-1599487488170-d11ec708c0f7'],

            // Burgers
            ['category' => 'burgers', 'sku' => 'FF-BG-001', 'name' => 'Zinger Burger', 'price' => 550, 'is_popular' => true, 'image' => 'photo-1568901346375-23c9450c58cd'],
            ['category' => 'burgers', 'sku' => 'FF-BG-002', 'name' => 'Patty Burger', 'price' => 450, 'image' => 'photo-1550547660-d9450f859349'],
            ['category' => 'burgers', 'sku' => 'FF-BG-003', 'name' => 'Chicken Bite Burger', 'price' => 450, 'is_popular' => true, 'image' => 'photo-1594212699903-ec8a3eca50f5'],
            ['category' => 'burgers', 'sku' => 'FF-BG-004', 'name' => 'Fishinger Burger', 'price' => 750, 'image' => 'photo-1553979459-d2229ba7433b'],

            // Roll Paratha
            ['category' => 'roll-paratha', 'sku' => 'FF-RP-001', 'name' => 'Chicken Roll', 'price' => 400, 'image' => 'photo-1626700051175-6818013e5789'],
            ['category' => 'roll-paratha', 'sku' => 'FF-RP-002', 'name' => 'Zinger Roll', 'price' => 450, 'is_popular' => true, 'image' => 'photo-1626700051175-6818013e5789'],
            ['category' => 'roll-paratha', 'sku' => 'FF-RP-003', 'name' => 'Special Roll', 'price' => 600, 'image' => 'photo-1626700051175-6818013e5789'],

            // Platters
            ['category' => 'platters', 'sku' => 'FF-PL-001', 'name' => 'Shawarma Platter', 'price' => 1000, 'is_popular' => true, 'image' => 'photo-1603360946369-dc9bb6258143'],
            ['category' => 'platters', 'sku' => 'FF-PL-002', 'name' => 'Zinger Platter', 'price' => 1100, 'is_popular' => true, 'image' => 'photo-1603360946369-dc9bb6258143'],
            ['category' => 'platters', 'sku' => 'FF-PL-003', 'name' => 'Special Platter', 'price' => 1650, 'image' => 'photo-1603360946369-dc9bb6258143'],

            // Wraps
            ['category' => 'wraps', 'sku' => 'FF-WR-001', 'name' => 'Chicken Wrap', 'price' => 800, 'image' => 'photo-1626700051175-6818013e5789'],
            ['category' => 'wraps', 'sku' => 'FF-WR-002', 'name' => 'Broast Wrap', 'price' => 900, 'is_popular' => true, 'image' => 'photo-1626700051175-6818013e5789'],

            // Fries (base = regular/smallest size)
            ['category' => 'fries', 'sku' => 'FF-FR-001', 'name' => 'Crispy Fries', 'price' => 250, 'is_popular' => true, 'image' => 'photo-1573080496219-bb080dd4f877'],
            ['category' => 'fries', 'sku' => 'FF-FR-002', 'name' => 'Garlic Mayo Fries', 'price' => 400, 'image' => 'photo-1573080496219-bb080dd4f877'],
            ['category' => 'fries', 'sku' => 'FF-FR-003', 'name' => 'Pizza Fries', 'price' => 650, 'is_popular' => true, 'image' => 'photo-1573080496219-bb080dd4f877'],
            ['category' => 'fries', 'sku' => 'FF-FR-004', 'name' => 'Loaded Fries', 'price' => 800, 'image' => 'photo-1573080496219-bb080dd4f877'],

            // Crispy
            ['category' => 'crispy', 'sku' => 'FF-CR-001', 'name' => 'Broast Piece + Fries', 'price' => 600, 'is_popular' => true, 'image' => 'photo-1626645738196-c2a7c87a8f58'],
            ['category' => 'crispy', 'sku' => 'FF-CR-002', 'name' => 'Crunchy Strips + Fries', 'price' => 650, 'image' => 'photo-1562967914-608f82629710'],
            ['category' => 'crispy', 'sku' => 'FF-CR-003', 'name' => 'Nuggets + Fries', 'price' => 450, 'is_popular' => true, 'image' => 'photo-1562967916-eb82221dfb92'],

            // Kids Meal
            ['category' => 'kids-meal', 'sku' => 'FF-KM-001', 'name' => 'Mini Burger', 'price' => 300, 'is_popular' => true, 'image' => 'photo-1615367423047-f69378c2f830'],
            ['category' => 'kids-meal', 'sku' => 'FF-KM-002', 'name' => 'Nuggets (3 Pcs) + Fries', 'price' => 300, 'image' => 'photo-1562967916-eb82221dfb92'],

            // On Demand
            ['category' => 'on-demand', 'sku' => 'FF-OD-001', 'name' => 'Pizza Shawarma', 'price' => 650, 'is_popular' => true, 'image' => 'photo-1513104890138-7c749659a591'],
            ['category' => 'on-demand', 'sku' => 'FF-OD-002', 'name' => 'Shawarma Grilled Chicken', 'price' => 950, 'image' => 'photo-1529006557810-274b9b2fc783'],

            // Extras
            ['category' => 'extras', 'sku' => 'FF-EX-001', 'name' => 'Extra Topping', 'price' => 150, 'image' => 'photo-1568901346375-23c9450c58cd'],
            ['category' => 'extras', 'sku' => 'FF-EX-002', 'name' => 'Chicken Items W/O Salad', 'price' => 50, 'image' => 'photo-1568901346375-23c9450c58cd'],
            ['category' => 'extras', 'sku' => 'FF-EX-003', 'name' => 'Extra Dip of Sauce', 'price' => 50, 'image' => 'photo-1568901346375-23c9450c58cd'],
        ];

        foreach ($products as $item) {
            $categoryId = $bySlug[$item['category']] ?? null;
            if (! $categoryId) {
                continue;
            }

            Product::updateOrCreate(
                ['woocommerce_sku' => $item['sku']],
                [
                    'category_id' => $categoryId,
                    'name' => $item['name'],
                    'description' => $item['description'] ?? 'Fresh from Facefood — order now!',
                    'ingredients' => $item['ingredients'] ?? null,
                    'price' => $item['price'],
                    'image_url' => 'https://images.unsplash.com/'.$item['image'].'?w=800&q=80',
                    'rating' => $item['rating'] ?? 4.7,
                    'calories' => $item['calories'] ?? null,
                    'spice_level' => $item['spice_level'] ?? 'medium',
                    'prep_time_minutes' => $item['prep_time_minutes'] ?? 10,
                    'is_popular' => $item['is_popular'] ?? false,
                    'is_available' => true,
                ]
            );
        }
    }
}
