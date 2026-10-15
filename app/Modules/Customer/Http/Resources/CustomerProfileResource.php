<?php

namespace App\Modules\Customer\Http\Resources;

use App\Models\CustomerDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin CustomerDetail */
class CustomerProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'photo_path' => $this->photo_path,
        ];
    }
}
