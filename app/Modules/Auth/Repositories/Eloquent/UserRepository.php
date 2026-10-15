<?php

namespace App\Modules\Auth\Repositories\Eloquent;

use App\Models\User;
use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Common\Repositories\Eloquent\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByMobile(string $mobile): ?User
    {
        return $this->query()->where('mobile', $mobile)->first();
    }
}
