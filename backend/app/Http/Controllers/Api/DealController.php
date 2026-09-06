<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DealResource;
use App\Models\Deal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DealController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $deals = Deal::query()
            ->active()
            ->with(['product.category', 'product.productExtras'])
            ->orderBy('ends_at')
            ->get();

        return DealResource::collection($deals);
    }

    public function show(Deal $deal): DealResource
    {
        $deal->load(['product.category', 'product.productExtras']);

        return new DealResource($deal);
    }

    public function store(Request $request): JsonResponse
    {
        $deal = Deal::create($this->validated($request));
        $deal->load(['product.category', 'product.productExtras']);

        return (new DealResource($deal))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Deal $deal): DealResource
    {
        $deal->update($this->validated($request, updating: true));

        return new DealResource($deal->fresh(['product.category', 'product.productExtras']));
    }

    public function destroy(Deal $deal): JsonResponse
    {
        $deal->delete();

        return response()->json(['message' => 'Deal deleted']);
    }

    private function validated(Request $request, bool $updating = false): array
    {
        $required = $updating ? 'sometimes' : 'required';

        return $request->validate([
            'product_id' => [$required, 'integer', 'exists:products,id'],
            'title' => [$required, 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deal_price' => [$required, 'numeric', 'min:0'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'badge_color' => ['nullable', 'string', 'max:20'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:40'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => [$required, 'date', 'after:now'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
