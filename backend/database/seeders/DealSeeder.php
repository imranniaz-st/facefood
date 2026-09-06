<?php

namespace Database\Seeders;

use App\Models\Deal;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DealSeeder extends Seeder
{
    public function run(): void
    {
        $deals = [
            [
                'product' => 'Zinger Shawarma',
                'title' => 'Zinger Shawarma Deal',
                'description' => 'Spicy zinger shawarma at a special price — order now!',
                'deal_price' => 350,
                'badge_color' => '#0D47A1',
                'tags' => ['SHAWARMA', 'ORDER NOW'],
                'ends_in_hours' => 48,
            ],
            [
                'product' => 'Zinger Burger',
                'title' => 'Zinger Burger Rush',
                'description' => 'Crispy zinger burger deal — limited time only.',
                'deal_price' => 499,
                'badge_color' => '#B71C1C',
                'tags' => ['BURGER', 'DEAL'],
                'ends_in_hours' => 24,
            ],
            [
                'product' => 'Pizza Shawarma',
                'title' => 'Pizza Shawarma Special',
                'description' => 'On-demand pizza shawarma fusion — today only!',
                'deal_price' => 599,
                'badge_color' => '#1B5E20',
                'tags' => ['ON DEMAND', 'SPECIAL'],
                'ends_in_hours' => 36,
            ],
            [
                'product' => 'Broast Piece + Fries',
                'title' => 'Broast Combo Deal',
                'description' => 'Crispy broast with fries — family favourite.',
                'deal_price' => 549,
                'badge_color' => '#006064',
                'tags' => ['CRISPY', 'COMBO'],
                'ends_in_hours' => 18,
            ],
            [
                'product' => 'Special Platter',
                'title' => 'Special Platter Feast',
                'description' => 'Share the special platter at an amazing price.',
                'deal_price' => 1499,
                'badge_color' => '#4A148C',
                'tags' => ['PLATTER', 'FEAST'],
                'ends_in_hours' => 72,
            ],
        ];

        foreach ($deals as $deal) {
            $product = Product::query()->where('name', $deal['product'])->first();
            if (! $product) {
                continue;
            }

            Deal::updateOrCreate(
                ['title' => $deal['title'], 'product_id' => $product->id],
                [
                    'description' => $deal['description'],
                    'deal_price' => $deal['deal_price'],
                    'image_url' => $product->image_url,
                    'badge_color' => $deal['badge_color'],
                    'tags' => $deal['tags'] ?? [],
                    'starts_at' => now()->subHour(),
                    'ends_at' => now()->addHours($deal['ends_in_hours']),
                    'is_active' => true,
                ]
            );
        }
    }
}
