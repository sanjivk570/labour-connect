<?php

namespace App\Modules\Auth\Repositories\Contracts;

use App\Models\User;
use App\Modules\Common\Repositories\Contracts\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByMobile(string $mobile): ?User;
}
