<?php

namespace App\Modules\Labour\Http\Resources;

use App\Models\LabourAvailability;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin LabourAvailability */
class LabourAvailabilityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'day_of_week' => $this->day_of_week,
            'day_name' => __('messages.'.$this->dayNameKey()),
            'is_available' => $this->is_available,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ];
    }

    private function dayNameKey(): string
    {
        $days = [1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday'];

        return $days[$this->day_of_week] ?? 'monday';
    }
}
