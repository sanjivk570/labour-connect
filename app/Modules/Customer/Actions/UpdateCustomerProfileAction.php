<?php

namespace App\Modules\Customer\Actions;

use App\Models\CustomerDetail;
use App\Models\User;
use App\Modules\Customer\Services\Contracts\CustomerServiceInterface;

class UpdateCustomerProfileAction
{
    public function __construct(private readonly CustomerServiceInterface $service)
    {
    }

    public function execute(User $user, array $data): CustomerDetail
    {
        return $this->service->updateProfile($user, $data);
    }
}
