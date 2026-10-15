<?php

namespace App\Modules\Search\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchLabourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'query' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:labour_categories,id'],
            'skill_ids' => ['nullable', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'within_km' => ['nullable', 'integer', 'in:5,10,20,50'],
            'available_today' => ['nullable', 'boolean'],
            'max_charge' => ['nullable', 'numeric', 'gt:0'],
            'rate_unit' => ['nullable', 'in:hour,day,week,month'],
            'min_experience' => ['nullable', 'integer', 'min:0', 'max:80'],
            'sort' => ['nullable', 'in:distance,charge_low,charge_high,experience,newest'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('sort') && $this->input('sort') === 'newest') {
            $this->merge(['sort' => null]);
        }
    }
}
