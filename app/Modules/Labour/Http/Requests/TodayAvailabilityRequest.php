<?php

namespace App\Modules\Labour\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodayAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_available_today' => ['required', 'boolean'],
        ];
    }
}
