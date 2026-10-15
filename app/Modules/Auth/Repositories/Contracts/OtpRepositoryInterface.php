<?php

namespace App\Modules\Auth\Repositories\Contracts;

use App\Models\OtpVerification;

interface OtpRepositoryInterface
{
    public function create(array $data): OtpVerification;

    public function latestActive(string $mobile, string $purpose): ?OtpVerification;

    public function incrementAttempts(OtpVerification $otp): void;

    public function markVerified(OtpVerification $otp): void;
}
