<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FavoriteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = $request->user()
            ->favoriteProducts()
            ->with(['category', 'productExtras'])
            ->orderByPivot('created_at', 'desc')
            ->get();

        return ProductResource::collection($products);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $request->user()->favorites()->firstOrCreate([
            'product_id' => $data['product_id'],
        ]);

        $product = Product::with(['category', 'productExtras'])->findOrFail($data['product_id']);

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        Favorite::query()
            ->where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->delete();

        return response()->json(['message' => 'Removed from favorites']);
    }

    public function toggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $user = $request->user();
        $existing = Favorite::query()
            ->where('user_id', $user->id)
            ->where('product_id', $data['product_id'])
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json([
                'favorited' => false,
                'product_id' => (int) $data['product_id'],
            ]);
        }

        Favorite::create([
            'user_id' => $user->id,
            'product_id' => $data['product_id'],
        ]);

        return response()->json([
            'favorited' => true,
            'product_id' => (int) $data['product_id'],
        ], 201);
    }
}
