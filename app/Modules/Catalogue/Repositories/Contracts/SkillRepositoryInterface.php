<?php

namespace App\Modules\Catalogue\Repositories\Contracts;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Collection;

interface SkillRepositoryInterface
{
    public function activeByCategory(int $categoryId): Collection;

    public function activeByIds(array $ids): Collection;
}
