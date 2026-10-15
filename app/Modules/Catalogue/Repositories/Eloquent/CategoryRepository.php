<?php

namespace App\Modules\Catalogue\Repositories\Eloquent;

use App\Models\LabourCategory;
use App\Modules\Catalogue\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function activeOrdered(): Collection
    {
        return LabourCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name_en')
            ->get();
    }
}
