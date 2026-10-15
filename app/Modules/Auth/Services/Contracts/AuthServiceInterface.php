<?php

namespace App\Modules\Auth\Services\Contracts;

use App\Models\User;

interface AuthServiceInterface
{
    /**
     * Resolve the authenticated user for a verified mobile number,
     * creating the account if it does not exist yet.
     */
    public function resolveUserByMobile(string $mobile): User;

    public function selectRole(User $user, string $role, string $locale): User;

    public function loginViaSocial(string $provider, string $providerId, ?string $locale = null): User;
}
