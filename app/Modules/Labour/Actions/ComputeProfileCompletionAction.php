<?php

namespace App\Modules\Labour\Actions;

use App\Models\LabourDetail;
use App\Modules\Labour\Services\Contracts\LabourServiceInterface;

class ComputeProfileCompletionAction
{
    public function __construct(private readonly LabourServiceInterface $service)
    {
    }

    public function execute(LabourDetail $labour): int
    {
        return $this->service->completionPercentage($labour);
    }
}
