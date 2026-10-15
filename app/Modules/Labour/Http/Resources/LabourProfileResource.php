<?php

namespace App\Modules\Labour\Http\Resources;

use App\Models\LabourDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin LabourDetail */
class LabourProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'photo_path' => $this->photo_path,
            'address_line' => $this->address_line,
            'city' => $this->city,
            'district' => $this->district,
            'state' => $this->state,
            'pincode' => $this->pincode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'max_travel_distance_km' => $this->max_travel_distance_km,
            'experience_years' => $this->experience_years,
            'work_type' => $this->work_type,
            'bio' => $this->bio,
            'registration_step' => $this->registration_step,
            'is_available_today' => $this->is_available_today,
            'profile_completed' => $this->profile_completed_at !== null,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ]),
            'skills' => LabourSkillResource::collection($this->whenLoaded('skills')),
            'rates' => LabourRateResource::collection($this->whenLoaded('rates')),
            'availabilities' => LabourAvailabilityResource::collection($this->whenLoaded('availabilities')),
        ];
    }
}
