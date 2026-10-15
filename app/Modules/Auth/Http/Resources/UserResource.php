<?php

namespace App\Modules\Auth\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mobile' => $this->mobile,
            'role' => $this->role,
            'preferred_locale' => $this->preferred_locale,
            'status' => $this->status,
            'mobile_verified' => $this->mobile_verified_at !== null,
            'name' => $this->when(
                $this->relationLoaded('labourDetail') || $this->relationLoaded('customerDetail'),
                fn () => $this->labourDetail?->full_name ?? $this->customerDetail?->full_name,
            ),
            'created_at' => $this->created_at,
        ];
    }
}
