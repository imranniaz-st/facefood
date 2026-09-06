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
                'product' => 'Double Cheese Pizza',
                'title' => 'Double Cheese Pizza',
                'description' => 'Today only — cheesy feast at a special price!',
                'deal_price' => 1025,
                'badge_color' => '#1B5E20',
                'tags' => ['TODAY SPECIAL', 'PIZZA'],
                'ends_in_hours' => 26,
            ],
            [
                'product' => 'Chicken Bite Burger',
                'title' => 'Chicken Bite Burger Deal',
                'description' => 'Order now and save on every bite.',
                'deal_price' => 499,
                'badge_color' => '#0D47A1',
                'tags' => ['BURGER', 'LIMITED'],
                'ends_in_hours' => 18,
            ],
            [
                'product' => 'Broast Piece',
                'title' => 'Broast Piece Special',
                'description' => 'Crispy broast at a limited-time price.',
                'deal_price' => 249,
                'badge_color' => '#B71C1C',
                'tags' => ['BROAST', 'ORDER NOW'],
                'ends_in_hours' => 12,
            ],
            [
                'product' => 'Zinger Burger',
                'title' => 'Zinger Rush Hour',
                'description' => 'Taste the best fast food in town today!',
                'deal_price' => 549,
                'badge_color' => '#006064',
                'tags' => ['ZINGER', 'RUSH HOUR'],
                'ends_in_hours' => 48,
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
