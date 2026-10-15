<?php

namespace App\Modules\Auth\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Localised static UI strings for the given screen.
 * Accepts an associative array of translation keys -> values.
 */
class StaticLabelsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var array $labels */
        $labels = $this->resource;

        return ['labels' => $labels];
    }
}
