<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Setting::publicPayload(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tax_rate' => ['sometimes', 'numeric', 'min:0', 'max:1'],
            'tax_percent' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'tax_label' => ['sometimes', 'string', 'max:30'],
            'delivery_fee' => ['sometimes', 'numeric', 'min:0'],
            'estimated_delivery' => ['sometimes', 'string', 'max:50'],
            'estimated_minutes' => ['sometimes', 'integer', 'min:1', 'max:240'],
            'currency' => ['sometimes', 'string', 'max:10'],
            'currency_code' => ['sometimes', 'string', 'max:10'],
            'store_name' => ['sometimes', 'string', 'max:80'],
        ]);

        if (array_key_exists('tax_percent', $data) && ! array_key_exists('tax_rate', $data)) {
            $data['tax_rate'] = round(((float) $data['tax_percent']) / 100, 4);
        }
        unset($data['tax_percent']);

        foreach ($data as $key => $value) {
            Setting::put($key, $value);
        }

        return response()->json([
            'data' => Setting::publicPayload(),
            'message' => 'Settings updated',
        ]);
    }
}
