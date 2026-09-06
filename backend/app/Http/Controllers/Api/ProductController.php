<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::query()
            ->with(['category', 'productExtras'])
            ->where('is_available', true);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->string('category'));
            });
        }

        if ($request->boolean('popular')) {
            $query->where('is_popular', true);
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ingredients', 'like', "%{$search}%");
            });
        }

        $products = $query->orderByDesc('is_popular')->orderBy('name')->get();

        return ProductResource::collection($products);
    }

    public function show(Product $product): ProductResource
    {
        $product->load(['category', 'productExtras']);

        return new ProductResource($product);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $extras = $data['extras'] ?? null;
        unset($data['extras']);

        $product = Product::create($data);
        $this->syncExtras($product, $extras);
        $product->load(['category', 'productExtras']);

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Product $product): ProductResource
    {
        $data = $this->validated($request, updating: true);
        $extras = array_key_exists('extras', $data) ? $data['extras'] : null;
        unset($data['extras']);

        $product->update($data);
        if ($extras !== null) {
            $this->syncExtras($product, $extras);
        }

        return new ProductResource($product->fresh(['category', 'productExtras']));
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }

    private function validated(Request $request, bool $updating = false): array
    {
        $required = $updating ? 'sometimes' : 'required';

        return $request->validate([
            'category_id' => [$required, 'integer', 'exists:categories,id'],
            'name' => [$required, 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'ingredients' => ['nullable', 'string'],
            'price' => [$required, 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'calories' => ['nullable', 'integer', 'min:0'],
            'spice_level' => ['nullable', 'string', 'in:mild,medium,hot'],
            'prep_time_minutes' => ['nullable', 'integer', 'min:0', 'max:240'],
            'extras' => ['nullable', 'array'],
            'extras.*.name' => ['required_with:extras', 'string', 'max:80'],
            'extras.*.price' => ['required_with:extras', 'numeric', 'min:0'],
            'is_popular' => ['sometimes', 'boolean'],
            'is_available' => ['sometimes', 'boolean'],
        ]);
    }

    /**
     * @param  array<int, array{name:string, price:mixed}>|null  $extras
     */
    private function syncExtras(Product $product, ?array $extras): void
    {
        if ($extras === null) {
            return;
        }

        $keepIds = [];
        foreach (array_values($extras) as $index => $extra) {
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
}
