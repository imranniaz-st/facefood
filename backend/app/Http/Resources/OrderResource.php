<?php

namespace App\Http\Resources;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $settings = Setting::publicPayload();

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'delivery_address' => $this->delivery_address,
            'subtotal' => (float) $this->subtotal,
            'delivery_fee' => (float) $this->delivery_fee,
            'tax' => (float) $this->tax,
            'tax_rate' => $settings['tax_rate'],
            'tax_percent' => $settings['tax_percent'],
            'tax_label' => $settings['tax_label'],
            'currency' => $settings['currency'],
            'total' => (float) $this->total,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'verification_code' => $this->verification_code,
            'note' => $this->note,
            'estimated_minutes' => $this->estimated_minutes,
            'estimated_delivery' => $settings['estimated_delivery'],
            'created_at' => $this->created_at?->toIso8601String(),
            'address' => new AddressResource($this->whenLoaded('address')),
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(fn ($item) => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'deal_id' => $item->deal_id,
                    'product_name' => $item->product_name,
                    'extras' => $item->extras ?? [],
                    'unit_price' => (float) $item->unit_price,
                    'quantity' => $item->quantity,
                    'line_total' => (float) $item->line_total,
                    'image_url' => $item->product?->image_url,
                ]);
            }),
        ];
    }
}
