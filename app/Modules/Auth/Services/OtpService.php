<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Exceptions\InvalidOtpException;
use App\Modules\Auth\Exceptions\OtpExpiredException;
use App\Modules\Auth\Repositories\Contracts\OtpRepositoryInterface;
use App\Modules\Auth\Services\Contracts\OtpServiceInterface;
use Illuminate\Support\Facades\Hash;

class OtpService implements OtpServiceInterface
{
    private const MAX_ATTEMPTS = 5;
    private const TTL_MINUTES = 5;

    public function __construct(private readonly OtpRepositoryInterface $otps)
    {
    }

    public function generate(string $mobile, string $purpose = 'login'): string
    {
        // 6-digit numeric OTP. For local/dev a fixed code keeps testing simple.
        $code = app()->environment('local')
            ? '123456'
            : str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->otps->create([
            'mobile' => $mobile,
            'otp_hash' => Hash::make($code),
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(self::TTL_MINUTES),
            'attempts' => 0,
        ]);

        // In production integrate an SMS gateway here (MSG91 / Twilio / etc.).
        return $code;
    }

    public function verify(string $mobile, string $otp, string $purpose = 'login'): void
    {
        $record = $this->otps->latestActive($mobile, $purpose);

        if (! $record) {
            throw new OtpExpiredException('otp_expired');
        }

        if ($record->attempts >= self::MAX_ATTEMPTS) {
            throw new InvalidOtpException('otp_attempts_exceeded');
        }

        if (! Hash::check($otp, $record->otp_hash)) {
            $this->otps->incrementAttempts($record);

            throw new InvalidOtpException('otp_invalid');
        }

        $this->otps->markVerified($record);
    }
}
