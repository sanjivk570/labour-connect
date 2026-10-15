<?php

namespace App\Modules\Labour\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrationStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules per registration step (1-6).
     */
    public function rules(): array
    {
        $step = (int) $this->route('step');

        return match ($step) {
            1 => $this->personalRules(),
            2 => $this->addressRules(),
            3 => $this->skillRules(),
            4 => $this->rateRules(),
            5 => $this->availabilityRules(),
            6 => $this->otherRules(),
            default => [],
        };
    }

    private function personalRules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'age' => ['nullable', 'integer', 'min:14', 'max:99'],
            'gender' => ['required', 'in:male,female,other,prefer_not_to_say'],
        ];
    }

    private function addressRules(): array
    {
        return [
            'address_line' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'max_travel_distance_km' => ['nullable', 'integer', Rule::in([5, 10, 20, 999])],
        ];
    }

    private function skillRules(): array
    {
        return [
            'labour_category_id' => ['required', 'integer', 'exists:labour_categories,id'],
            'skill_ids' => ['required', 'array', 'min:1'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
            'primary_skill_id' => ['nullable', 'integer'],
        ];
    }

    private function rateRules(): array
    {
        return [
            'rates' => ['required', 'array', 'min:1'],
            'rates.hour' => ['nullable', 'numeric', 'gt:0'],
            'rates.day' => ['nullable', 'numeric', 'gt:0'],
            'rates.week' => ['nullable', 'numeric', 'gt:0'],
            'rates.month' => ['nullable', 'numeric', 'gt:0'],
        ];
    }

    private function availabilityRules(): array
    {
        return [
            'availability' => ['required', 'array', 'min:1'],
            'availability.*.is_available' => ['required', 'boolean'],
            'availability.*.start_time' => ['nullable', 'date_format:H:i'],
            'availability.*.end_time' => ['nullable', 'date_format:H:i', 'after:availability.*.start_time'],
        ];
    }

    private function otherRules(): array
    {
        return [
            'experience_years' => ['required', 'integer', 'min:0', 'max:80'],
            'work_type' => ['required', 'in:full_time,part_time,contract'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Normalise input: map "age" to date_of_birth is handled client-side,
     * and keep max_travel_distance_km numeric-friendly.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('max_travel_distance_km') && $this->input('max_travel_distance_km') === 'anywhere') {
            $this->merge(['max_travel_distance_km' => 999]);
        }
    }
}
