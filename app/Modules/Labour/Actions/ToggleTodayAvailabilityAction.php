<?php

namespace App\Modules\Labour\Actions;

use App\Models\LabourDetail;
use App\Models\User;
use App\Modules\Labour\Services\Contracts\LabourServiceInterface;

class ToggleTodayAvailabilityAction
{
    public function __construct(private readonly LabourServiceInterface $service)
    {
    }

    public function execute(User $user, bool $available): LabourDetail
    {
        return $this->service->setTodayAvailability($user, $available);
    }
}
