<?php

namespace App\Modules\Auth\Actions;

use App\Models\User;
use App\Modules\Auth\Services\Contracts\AuthServiceInterface;
use App\Modules\Auth\Services\Contracts\OtpServiceInterface;
use Laravel\Sanctum\NewAccessToken;

class VerifyOtpAction
{
    public function __construct(
        private readonly OtpServiceInterface $otpService,
        private readonly AuthServiceInterface $authService,
    ) {
    }

    /**
     * Verify OTP and return the user plus a fresh API token.
     *
     * @return array{user: User, token: NewAccessToken}
     */
    public function execute(string $mobile, string $otp, ?string $deviceToken = null, ?string $platform = null): array
    {
        $this->otpService->verify($mobile, $otp, 'login');

        $user = $this->authService->resolveUserByMobile($mobile);

        // Revoke previous tokens to keep a single active session per device flow.
        $user->tokens()->delete();

        $token = $user->createToken('mobile-app');

        if ($deviceToken && $platform) {
            $user->deviceTokens()->updateOrCreate(
                ['token' => $deviceToken],
                ['platform' => $platform, 'last_used_at' => now()],
            );
        }

        return ['user' => $user, 'token' => $token];
    }
}
