<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\DealResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Deal;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WordPressSyncController extends Controller
{
    public function catalog(): JsonResponse
    {
        $categories = Category::query()->orderBy('sort_order')->get();
        $products = Product::query()->with(['category', 'productExtras'])->orderBy('name')->get();
        $deals = Deal::query()->with('product')->active()->get();

        return response()->json([
            'data' => [
                'categories' => CategoryResource::collection($categories)->resolve(),
                'products' => ProductResource::collection($products)->resolve(),
                'deals' => DealResource::collection($deals)->resolve(),
                'synced_at' => now()->toIso8601String(),
            ],
        ]);
    }

    public function link(Request $request): JsonResponse
    {
        $data = $request->validate([
            'entity' => ['required', 'string', 'in:category,product,deal'],
            'laravel_id' => ['required', 'integer', 'min:1'],
            'wordpress_id' => ['required', 'integer', 'min:1'],
            'sku' => ['nullable', 'string', 'max:100'],
        ]);

        $now = now();

        match ($data['entity']) {
            'category' => Category::query()
                ->whereKey($data['laravel_id'])
                ->update([
                    'wordpress_term_id' => $data['wordpress_id'],
                    'synced_at' => $now,
                ]),
            'product' => Product::query()
                ->whereKey($data['laravel_id'])
                ->update([
                    'wordpress_product_id' => $data['wordpress_id'],
                    'woocommerce_sku' => $data['sku'] ?? Product::find($data['laravel_id'])?->woocommerce_sku,
                    'synced_at' => $now,
                ]),
            'deal' => Deal::query()
                ->whereKey($data['laravel_id'])
                ->update([
                    'wordpress_post_id' => $data['wordpress_id'],
                    'synced_at' => $now,
                ]),
        };

        return response()->json(['message' => 'Linked successfully.']);
    }

    public function bulkLink(Request $request): JsonResponse
    {
        $data = $request->validate([
            'links' => ['required', 'array'],
            'links.*.entity' => ['required', 'string', 'in:category,product,deal'],
            'links.*.laravel_id' => ['required', 'integer', 'min:1'],
            'links.*.wordpress_id' => ['required', 'integer', 'min:1'],
            'links.*.sku' => ['nullable', 'string', 'max:100'],
        ]);

        foreach ($data['links'] as $link) {
            $request->merge($link);
            $this->link($request);
        }

        return response()->json([
            'message' => 'Bulk link completed.',
            'count' => count($data['links']),
        ]);
    }

    public function upsertProduct(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:100'],
            'category_slug' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'wordpress_product_id' => ['nullable', 'integer', 'min:1'],
            'is_available' => ['sometimes', 'boolean'],
            'is_popular' => ['sometimes', 'boolean'],
            'extras' => ['nullable', 'array'],
            'extras.*.name' => ['required_with:extras', 'string', 'max:80'],
            'extras.*.price' => ['required_with:extras', 'numeric', 'min:0'],
        ]);

        $category = Category::query()->firstOrCreate(
            ['slug' => Str::slug($data['category_slug'])],
            [
                'name' => Str::headline(str_replace('-', ' ', $data['category_slug'])),
                'icon' => 'restaurant',
                'sort_order' => 99,
                'is_active' => true,
            ]
        );

        $product = Product::updateOrCreate(
            ['woocommerce_sku' => $data['sku']],
            [
                'category_id' => $category->id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'image_url' => $data['image_url'] ?? null,
                'wordpress_product_id' => $data['wordpress_product_id'] ?? null,
                'is_available' => $data['is_available'] ?? true,
                'is_popular' => $data['is_popular'] ?? false,
                'synced_at' => now(),
            ]
        );

        if (array_key_exists('extras', $data)) {
            $keepIds = [];
            foreach (array_values($data['extras']) as $index => $extra) {
                $row = $product->productExtras()->updateOrCreate(
                    ['name' => $extra['name']],
                    [
                        'price' => $extra['price'],
                        'sort_order' => $index,
                        'is_available' => true,
                    ]
                );
                $keepIds[] = $row->id;
            }
            $product->productExtras()->whereNotIn('id', $keepIds)->delete();
        }

        $product->load(['category', 'productExtras']);

        return response()->json([
            'data' => (new ProductResource($product))->resolve(),
        ]);
    }
}
