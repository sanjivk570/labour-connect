<?php

namespace App\Modules\Catalogue\Http\Resources;

use App\Models\LabourCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin LabourCategory */
class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'name_en' => $this->name_en,
            'name_hi' => $this->name_hi,
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
        ];
    }
}
