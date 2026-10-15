<?php

namespace App\Modules\Auth\Actions;

use App\Models\User;
use App\Modules\Auth\Services\Contracts\AuthServiceInterface;
use Laravel\Sanctum\NewAccessToken;

class SocialLoginAction
{
    public function __construct(private readonly AuthServiceInterface $authService)
    {
    }

    /**
     * @return array{user: User, token: NewAccessToken}
     */
    public function execute(string $provider, string $providerId, ?string $locale = null): array
    {
        $user = $this->authService->loginViaSocial($provider, $providerId, $locale);

        $user->tokens()->delete();
        $token = $user->createToken('mobile-app');

        return ['user' => $user, 'token' => $token];
    }
}
