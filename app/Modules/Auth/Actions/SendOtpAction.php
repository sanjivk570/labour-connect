<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Services\Contracts\OtpServiceInterface;

class SendOtpAction
{
    public function __construct(private readonly OtpServiceInterface $otpService)
    {
    }

    /**
     * @return array{otp: string}
     */
    public function execute(string $mobile): array
    {
        $otp = $this->otpService->generate($mobile, 'login');

        return ['otp' => $otp];
    }
}
