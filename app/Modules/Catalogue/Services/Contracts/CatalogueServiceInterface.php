<?php

namespace App\Modules\Catalogue\Services\Contracts;

use App\Models\LabourCategory;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Collection;

interface CatalogueServiceInterface
{
    public function listCategories(): Collection;

    public function listSkills(int $categoryId): Collection;

    public function resolveSkills(array $skillIds): Collection;
}
