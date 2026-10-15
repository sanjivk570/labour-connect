<?php

namespace App\Modules\Customer\Services;

use App\Models\CustomerDetail;
use App\Models\User;
use App\Modules\Customer\Repositories\Contracts\CustomerRepositoryInterface;
use App\Modules\Customer\Services\Contracts\CustomerServiceInterface;

class CustomerService implements CustomerServiceInterface
{
    public function __construct(private readonly CustomerRepositoryInterface $repository)
    {
    }

    public function profileForUser(User $user): ?CustomerDetail
    {
        return $this->repository->findByUserId($user->id);
    }

    public function updateProfile(User $user, array $data): CustomerDetail
    {
        $customer = $this->repository->findByUserId($user->id);

        if ($customer) {
            return $this->repository->update($customer, $data);
        }

        return $this->repository->firstOrCreateForUser($user->id, $data);
    }
}
