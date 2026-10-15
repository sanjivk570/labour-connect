<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Auth\Services\Contracts\AuthServiceInterface;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function __construct(private readonly UserRepositoryInterface $users)
    {
    }

    public function resolveUserByMobile(string $mobile): User
    {
        $user = $this->users->findByMobile($mobile);

        if ($user) {
            if ($user->mobile_verified_at === null) {
                $user->forceFill(['mobile_verified_at' => now()])->save();
            }

            return $user;
        }

        // New user. Role is assigned later via selectRole(); default to customer
        // so the account is valid until the user chooses "I am a Worker".
        return $this->users->create([
            'mobile' => $mobile,
            'role' => User::ROLE_CUSTOMER,
            'mobile_verified_at' => now(),
            'status' => 'active',
            'last_login_at' => now(),
        ]);
    }

    public function selectRole(User $user, string $role, string $locale): User
    {
        $allowed = [User::ROLE_CUSTOMER, User::ROLE_LABOUR];

        if (! in_array($role, $allowed, true)) {
            $role = User::ROLE_CUSTOMER;
        }

        return $this->users->update($user, [
            'role' => $role,
            'preferred_locale' => $locale,
            'last_login_at' => now(),
        ]);
    }

    public function loginViaSocial(string $provider, string $providerId, ?string $locale = null): User
    {
        // NOTE: Social providers (Google/Facebook) require an additional
        // provider_user_id column. Until that migration is added we mirror the
        // verified identity onto the mobile field so the flow is functional.
        // Replace with a real provider lookup when the column exists.
        $mobile = $provider === 'facebook'
            ? 'fb_'.$providerId
            : 'gg_'.$providerId;

        $mobile = substr($mobile, 0, 16);

        $user = $this->users->findByMobile($mobile);

        if ($user) {
            return $this->users->update($user, [
                'mobile_verified_at' => $user->mobile_verified_at ?? now(),
                'last_login_at' => now(),
                'preferred_locale' => $locale ?? $user->preferred_locale,
            ]);
        }

        return $this->users->create([
            'mobile' => $mobile,
            'role' => User::ROLE_CUSTOMER,
            'mobile_verified_at' => now(),
            'status' => 'active',
            'preferred_locale' => $locale ?? 'en',
            'last_login_at' => now(),
        ]);
    }
}
