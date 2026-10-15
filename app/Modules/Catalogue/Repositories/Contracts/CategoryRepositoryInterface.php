<?php

namespace App\Modules\Catalogue\Repositories\Contracts;

use App\Models\LabourCategory;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function activeOrdered(): Collection;
}
