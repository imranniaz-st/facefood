<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\Product;
use App\Models\ProductExtra;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class OrderCalculator
{
    /**
     * @param  array<int, array{product_id:int, quantity:int, deal_id?:int|null, extra_ids?:array<int,int>}>  $items
     * @param  Collection<int, Product>  $products  keyed by id
     */
    public function quote(array $items, Collection $products): array
    {
        $settings = Setting::publicPayload();
        $storeTax = (float) $settings['tax_rate'];
        $subtotal = 0.0;
        $tax = 0.0;
        $lines = [];

        foreach ($items as $item) {
            $product = $products[$item['product_id']];
            $qty = (int) $item['quantity'];
            $unit = (float) $product->price;
            $deal = null;

            if (! empty($item['deal_id'])) {
                $deal = Deal::query()
                    ->active()
                    ->where('id', $item['deal_id'])
                    ->where('product_id', $product->id)
                    ->first();

                if (! $deal) {
                    throw ValidationException::withMessages([
                        'items' => ['This deal is no longer available.'],
                    ]);
                }

                $unit = (float) $deal->deal_price;
            }

            $extraIds = collect($item['extra_ids'] ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->values();

            $extras = collect();
            if ($extraIds->isNotEmpty()) {
                $extras = ProductExtra::query()
                    ->where('product_id', $product->id)
                    ->where('is_available', true)
                    ->whereIn('id', $extraIds)
                    ->orderBy('sort_order')
                    ->get();

                if ($extras->count() !== $extraIds->count()) {
                    throw ValidationException::withMessages([
                        'items' => ['One or more extras are unavailable.'],
                    ]);
                }

                $unit += (float) $extras->sum('price');
            }

            $unit = round($unit, 2);
            $lineTotal = round($unit * $qty, 2);
            $rate = $product->tax_rate !== null ? (float) $product->tax_rate : $storeTax;
            $lineTax = round($lineTotal * $rate, 2);

            $subtotal += $lineTotal;
            $tax += $lineTax;

            $lines[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price' => $unit,
                'quantity' => $qty,
                'line_total' => $lineTotal,
                'tax_rate' => $rate,
                'tax' => $lineTax,
                'deal_id' => $deal?->id,
                'extras' => $extras->map(fn (ProductExtra $extra) => [
                    'id' => $extra->id,
                    'name' => $extra->name,
                    'price' => (float) $extra->price,
                ])->values()->all(),
            ];
        }

        $deliveryFee = $subtotal > 0 ? (float) $settings['delivery_fee'] : 0.0;
        $tax = round($tax, 2);
        $total = round($subtotal + $deliveryFee + $tax, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'delivery_fee' => $deliveryFee,
            'tax' => $tax,
            'tax_rate' => $storeTax,
            'tax_percent' => $settings['tax_percent'],
            'tax_label' => $settings['tax_label'],
            'total' => $total,
            'currency' => $settings['currency'],
            'estimated_delivery' => $settings['estimated_delivery'],
            'estimated_minutes' => $settings['estimated_minutes'],
            'items' => $lines,
        ];
    }
}
