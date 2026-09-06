<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Deal */
class DealResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'title' => $this->title,
            'description' => $this->description,
            'deal_price' => (float) $this->deal_price,
            'original_price' => $this->whenLoaded('product', fn () => (float) $this->product->price),
            'image_url' => $this->image_url ?? $this->whenLoaded('product', fn () => $this->product->image_url),
            'badge_color' => $this->badge_color,
            'tags' => $this->tags ?? [],
            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at->toIso8601String(),
            'seconds_remaining' => max(0, now()->diffInSeconds($this->ends_at, false)),
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
