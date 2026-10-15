<?php

namespace App\Modules\Customer\Repositories\Contracts;

use App\Models\CustomerDetail;
use App\Models\User;

interface CustomerRepositoryInterface
{
    public function findByUserId(int $userId): ?CustomerDetail;

    public function firstOrCreateForUser(int $userId, array $data): CustomerDetail;

    public function update(CustomerDetail $customer, array $data): CustomerDetail;
}
