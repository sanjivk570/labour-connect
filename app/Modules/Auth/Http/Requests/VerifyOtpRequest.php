<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mobile' => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
            'otp' => ['required', 'string', 'digits:6'],
            'device_token' => ['nullable', 'string', 'max:191'],
            'platform' => ['nullable', 'in:android,ios'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => __('api.validation.mobile.required'),
            'mobile.regex' => __('api.validation.mobile.regex'),
            'otp.required' => __('api.validation.otp.required'),
            'otp.digits' => __('api.validation.otp.digits'),
        ];
    }
}
