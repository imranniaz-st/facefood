<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function show(Category $category): CategoryResource
    {
        $category->loadCount('products');

        return new CategoryResource($category);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:categories,slug'],
            'icon' => ['nullable', 'string', 'max:50'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['sort_order'] = $data['sort_order'] ?? ((int) Category::query()->max('sort_order') + 1);

        $category = Category::create($data);

        return (new CategoryResource($category->loadCount('products')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Category $category): CategoryResource
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'slug' => ['sometimes', 'string', 'max:120', 'unique:categories,slug,'.$category->id],
            'icon' => ['nullable', 'string', 'max:50'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $category->update($data);

        return new CategoryResource($category->fresh()->loadCount('products'));
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }
}
