<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value', 'type', 'group', 'label'])]
class Setting extends Model
{
    public const CACHE_KEY = 'facefood.settings';

    public static function defaults(): array
    {
        return [
            'tax_rate' => ['value' => '0.15', 'type' => 'number', 'label' => 'Tax rate (fraction, e.g. 0.15 = 15%)'],
            'tax_label' => ['value' => 'GST', 'type' => 'string', 'label' => 'Tax label'],
            'delivery_fee' => ['value' => '50', 'type' => 'number', 'label' => 'Delivery fee'],
            'estimated_delivery' => ['value' => '30-45 MINS', 'type' => 'string', 'label' => 'ETA text'],
            'estimated_minutes' => ['value' => '40', 'type' => 'number', 'label' => 'ETA minutes (stored on orders)'],
            'currency' => ['value' => 'Rs.', 'type' => 'string', 'label' => 'Currency symbol'],
            'currency_code' => ['value' => 'PKR', 'type' => 'string', 'label' => 'Currency code'],
            'store_name' => ['value' => 'Facefood', 'type' => 'string', 'label' => 'Store name'],
        ];
    }

    public function typedValue(): mixed
    {
        return match ($this->type) {
            'number' => is_numeric($this->value) ? $this->value + 0 : 0,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode((string) $this->value, true) ?? [],
            default => $this->value,
        };
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::map();

        return $all[$key] ?? $default;
    }

    public static function map(): array
    {
        return Cache::remember(self::CACHE_KEY, 60, function () {
            $rows = static::query()->get()->keyBy('key');
            $out = [];
            foreach (static::defaults() as $key => $meta) {
                $out[$key] = $rows->has($key)
                    ? $rows[$key]->typedValue()
                    : (new static(['value' => $meta['value'], 'type' => $meta['type']]))->typedValue();
            }
            foreach ($rows as $key => $row) {
                if (! array_key_exists($key, $out)) {
                    $out[$key] = $row->typedValue();
                }
            }

            return $out;
        });
    }

    public static function put(string $key, mixed $value, ?string $type = null, ?string $label = null): self
    {
        $defaults = static::defaults()[$key] ?? null;
        $type ??= $defaults['type'] ?? (is_numeric($value) ? 'number' : 'string');
        $stored = is_array($value) ? json_encode($value) : (string) $value;

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stored,
                'type' => $type,
                'group' => 'store',
                'label' => $label ?? $defaults['label'] ?? $key,
            ]
        );

        Cache::forget(self::CACHE_KEY);

        return $setting;
    }

    public static function publicPayload(): array
    {
        $map = static::map();
        $taxRate = (float) ($map['tax_rate'] ?? 0.15);

        return [
            'tax_rate' => $taxRate,
            'tax_percent' => round($taxRate * 100, 2),
            'tax_label' => (string) ($map['tax_label'] ?? 'GST'),
            'delivery_fee' => (float) ($map['delivery_fee'] ?? 50),
            'estimated_delivery' => (string) ($map['estimated_delivery'] ?? '30-45 MINS'),
            'estimated_minutes' => (int) ($map['estimated_minutes'] ?? 40),
            'currency' => (string) ($map['currency'] ?? 'Rs.'),
            'currency_code' => (string) ($map['currency_code'] ?? 'PKR'),
            'store_name' => (string) ($map['store_name'] ?? 'Facefood'),
        ];
    }
}
