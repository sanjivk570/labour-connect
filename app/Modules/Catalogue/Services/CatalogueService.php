<?php

namespace App\Modules\Catalogue\Services;

use App\Models\Skill;
use App\Modules\Catalogue\Repositories\Contracts\CategoryRepositoryInterface;
use App\Modules\Catalogue\Repositories\Contracts\SkillRepositoryInterface;
use App\Modules\Catalogue\Services\Contracts\CatalogueServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class CatalogueService implements CatalogueServiceInterface
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categories,
        private readonly SkillRepositoryInterface $skills,
    ) {
    }

    public function listCategories(): Collection
    {
        return $this->categories->activeOrdered();
    }

    public function listSkills(int $categoryId): Collection
    {
        return $this->skills->activeByCategory($categoryId);
    }

    public function resolveSkills(array $skillIds): Collection
    {
        return $this->skills->activeByIds($skillIds);
    }
}
