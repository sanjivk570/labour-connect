<?php

namespace App\Modules\Auth\Services\Contracts;

interface OtpServiceInterface
{
    /**
     * Generate, persist and return a fresh OTP code for the mobile number.
     */
    public function generate(string $mobile, string $purpose = 'login'): string;

    /**
     * @throws \App\Modules\Auth\Exceptions\InvalidOtpException
     * @throws \App\Modules\Auth\Exceptions\OtpExpiredException
     */
    public function verify(string $mobile, string $otp, string $purpose = 'login'): void;
}
