<?php

namespace App\Modules\Labour\Actions;

use App\Models\LabourDetail;
use App\Models\User;
use App\Modules\Labour\Services\Contracts\LabourServiceInterface;

class SaveRegistrationStepAction
{
    public function __construct(private readonly LabourServiceInterface $service)
    {
    }

    public function execute(User $user, int $step, array $data): LabourDetail
    {
        return $this->service->saveStep($user, $step, $data);
    }
}
