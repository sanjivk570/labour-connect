<?php

namespace App\Modules\Customer\Services\Contracts;

use App\Models\CustomerDetail;
use App\Models\User;

interface CustomerServiceInterface
{
    public function profileForUser(User $user): ?CustomerDetail;

    public function updateProfile(User $user, array $data): CustomerDetail;
}
