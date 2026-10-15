<?php

namespace App\Modules\Catalogue\Repositories\Eloquent;

use App\Models\Skill;
use App\Modules\Catalogue\Repositories\Contracts\SkillRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SkillRepository implements SkillRepositoryInterface
{
    public function activeByCategory(int $categoryId): Collection
    {
        return Skill::query()
            ->where('labour_category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name_en')
            ->get();
    }

    public function activeByIds(array $ids): Collection
    {
        return Skill::query()
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->get();
    }
}
