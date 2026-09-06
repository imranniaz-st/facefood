<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class AddressController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $addresses = $request->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->get();

        return AddressResource::collection($addresses);
    }

    public function store(Request $request): AddressResource
    {
        $data = $request->validate([
            'label' => ['nullable', 'string', 'max:50'],
            'line1' => ['required', 'string', 'max:255'],
            'line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        $user = $request->user();

        if (($data['is_default'] ?? false) || $user->addresses()->count() === 0) {
            $user->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $address = $user->addresses()->create([
            'label' => $data['label'] ?? 'Home',
            'line1' => $data['line1'],
            'line2' => $data['line2'] ?? null,
            'city' => $data['city'] ?? 'Karachi',
            'area' => $data['area'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'is_default' => $data['is_default'] ?? false,
        ]);

        return new AddressResource($address);
    }

    public function show(Request $request, Address $address): AddressResource
    {
        $this->authorizeAddress($request, $address);

        return new AddressResource($address);
    }

    public function update(Request $request, Address $address): AddressResource
    {
        $this->authorizeAddress($request, $address);

        $data = $request->validate([
            'label' => ['sometimes', 'string', 'max:50'],
            'line1' => ['sometimes', 'string', 'max:255'],
            'line2' => ['nullable', 'string', 'max:255'],
            'city' => ['sometimes', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        if (! empty($data['is_default'])) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return new AddressResource($address->fresh());
    }

    public function destroy(Request $request, Address $address): JsonResponse
    {
        $this->authorizeAddress($request, $address);
        $address->delete();

        return response()->json(['message' => 'Address deleted']);
    }

    private function authorizeAddress(Request $request, Address $address): void
    {
        if ($address->user_id !== $request->user()->id) {
            throw ValidationException::withMessages([
                'address' => ['Address not found.'],
            ]);
        }
    }
}
