<?php

namespace App\Modules\Labour\Http\Resources;

use App\Models\LabourRate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin LabourRate */
class LabourRateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rate_unit' => $this->rate_unit,
            'amount' => (float) $this->amount,
            'is_active' => $this->is_active,
        ];
    }
}
