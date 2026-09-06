<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar_url' => $this->avatar_url,
            'is_premium' => (bool) $this->is_premium,
            'is_admin' => (bool) $this->is_admin,
            'location' => $this->location,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
