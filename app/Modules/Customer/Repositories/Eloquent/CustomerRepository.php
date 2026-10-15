<?php

namespace App\Modules\Customer\Repositories\Eloquent;

use App\Models\CustomerDetail;
use App\Modules\Customer\Repositories\Contracts\CustomerRepositoryInterface;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function findByUserId(int $userId): ?CustomerDetail
    {
        return CustomerDetail::where('user_id', $userId)->first();
    }

    public function firstOrCreateForUser(int $userId, array $data): CustomerDetail
    {
        return CustomerDetail::firstOrCreate(
            ['user_id' => $userId],
            $data,
        );
    }

    public function update(CustomerDetail $customer, array $data): CustomerDetail
    {
        $customer->fill($data)->save();

        return $customer->refresh();
    }
}
