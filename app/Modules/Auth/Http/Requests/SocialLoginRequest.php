<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', 'in:google,facebook'],
            'provider_id' => ['required', 'string', 'max:191'],
            'locale' => ['nullable', 'in:en,hi'],
        ];
    }
}
