<?php

namespace App\Modules\Search\Http\Resources;

use App\Models\LabourDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin LabourDetail */
class LabourSearchResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $dayRate = $this->rates->firstWhere('rate_unit', 'day');
        $distance = $this->resource->getAttribute('distance_km');

        return [
            'id' => $this->id,
            'name' => $this->full_name,
            'category' => $this->whenLoaded('category', fn () => $this->category?->name),
            'category_name' => $this->category?->name,
            'experience_years' => $this->experience_years,
            'experience_label' => $this->experience_years !== null
                ? __('messages.experience').' '.$this->experience_years.' '.__('messages.years')
                : null,
            'distance_km' => $distance !== null ? round((float) $distance, 1) : null,
            'distance_label' => $distance !== null
                ? __('messages.km_away', ['km' => round((float) $distance, 1)])
                : null,
            'is_available' => $this->is_available_today,
            'availability_label' => $this->is_available_today
                ? __('messages.available')
                : __('messages.unavailable'),
            'charge_per_day' => $dayRate ? (float) $dayRate->amount : null,
            'charge_per_day_label' => $dayRate
                ? __('messages.per_day_short', ['amount' => '₹'.number_format((float) $dayRate->amount)])
                : null,
            'city' => $this->city,
            'state' => $this->state,
            'skills' => $this->whenLoaded('skills', fn () => $this->skills->map(fn ($s) => $s->skill?->name)->filter()->values()),
        ];
    }
}
