<?php

namespace App\Modules\Labour\Repositories\Contracts;

use App\Models\LabourDetail;

interface LabourRepositoryInterface
{
    public function findByUserId(int $userId): ?LabourDetail;

    public function createForUser(int $userId, array $data): LabourDetail;
}
