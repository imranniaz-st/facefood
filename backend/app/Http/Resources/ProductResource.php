<?php

namespace App\Http\Resources;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Product */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $storeTax = (float) Setting::get('tax_rate', 0.15);
        $effectiveTax = $this->tax_rate !== null ? (float) $this->tax_rate : $storeTax;

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'woocommerce_sku' => $this->woocommerce_sku,
            'description' => $this->description,
            'ingredients' => $this->ingredients,
            'price' => (float) $this->price,
            'tax_rate' => $this->tax_rate !== null ? (float) $this->tax_rate : null,
            'effective_tax_rate' => $effectiveTax,
            'image_url' => $this->image_url,
            'rating' => (float) $this->rating,
            'calories' => $this->calories,
            'spice_level' => $this->spice_level,
            'prep_time_minutes' => $this->prep_time_minutes,
            'extras' => $this->resolvedExtras(),
            'is_popular' => $this->is_popular,
            'is_available' => $this->is_available,
            'category' => new CategoryResource($this->whenLoaded('category')),
        ];
    }

    /**
     * @return list<array{id:int,name:string,price:float}>
     */
    private function resolvedExtras(): array
    {
        $rows = $this->relationLoaded('productExtras')
            ? $this->productExtras
            : $this->productExtras()->where('is_available', true)->get();

        $fromTable = $rows
            ->where('is_available', true)
            ->values()
            ->map(fn ($extra) => [
                'id' => (int) $extra->id,
                'name' => (string) $extra->name,
                'price' => (float) $extra->price,
            ])
            ->all();

        if ($fromTable !== []) {
            return $fromTable;
        }

        $legacy = $this->getAttributes()['extras'] ?? null;
        if (is_string($legacy)) {
            $legacy = json_decode($legacy, true);
        }
        if (! is_array($legacy)) {
            return [];
        }

        return collect($legacy)
            ->values()
            ->map(fn ($extra, $index) => [
                'id' => (int) ($extra['id'] ?? -$index - 1),
                'name' => (string) ($extra['name'] ?? ''),
                'price' => (float) ($extra['price'] ?? 0),
            ])
            ->all();
    }
}
