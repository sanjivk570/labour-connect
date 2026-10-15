<?php

namespace App\Modules\Auth\Actions;

use App\Models\User;
use App\Modules\Auth\Services\Contracts\AuthServiceInterface;
use Laravel\Sanctum\NewAccessToken;

class SelectRoleAction
{
    public function __construct(private readonly AuthServiceInterface $authService)
    {
    }

    /**
     * @return array{user: User, token: NewAccessToken}
     */
    public function execute(User $user, string $role, string $locale): array
    {
        $user->tokens()->delete();

        $user = $this->authService->selectRole($user, $role, $locale);

        $token = $user->createToken('mobile-app');

        return ['user' => $user, 'token' => $token];
    }
}
