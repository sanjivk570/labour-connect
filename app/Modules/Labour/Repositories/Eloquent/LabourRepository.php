<?php

namespace App\Modules\Labour\Repositories\Eloquent;

use App\Models\LabourDetail;
use App\Modules\Common\Repositories\Eloquent\BaseRepository;
use App\Modules\Labour\Repositories\Contracts\LabourRepositoryInterface;

class LabourRepository extends BaseRepository implements LabourRepositoryInterface
{
    public function __construct(LabourDetail $model)
    {
        parent::__construct($model);
    }

    public function findByUserId(int $userId): ?LabourDetail
    {
        return $this->query()->where('user_id', $userId)->first();
    }

    public function createForUser(int $userId, array $data): LabourDetail
    {
        return $this->query()->create(array_merge($data, ['user_id' => $userId]));
    }
}
