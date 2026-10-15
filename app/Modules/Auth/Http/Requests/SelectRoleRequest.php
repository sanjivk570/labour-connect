<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SelectRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'in:customer,labour'],
            'locale' => ['nullable', 'in:en,hi'],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => __('api.validation.role.required'),
            'role.in' => __('api.validation.role.in'),
        ];
    }
}
