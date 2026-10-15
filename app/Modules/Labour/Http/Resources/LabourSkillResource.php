<?php

namespace App\Modules\Labour\Http\Resources;

use App\Models\LabourSkill;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin LabourSkill */
class LabourSkillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'skill_id' => $this->skill_id,
            'name' => $this->whenLoaded('skill', fn () => $this->skill?->name),
            'is_primary' => $this->is_primary,
        ];
    }
}
