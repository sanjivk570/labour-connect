<?php

namespace App\Modules\Search\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LabourSearchRepositoryInterface
{
    /**
     * @param  array{
     *   category_id?: int|null,
     *   skill_ids?: array<int>|null,
     *   query?: string|null,
     *   city?: string|null,
     *   state?: string|null,
     *   latitude?: float|null,
     *   longitude?: float|null,
     *   within_km?: int|null,
     *   available_today?: bool|null,
     *   max_charge?: float|null,
     *   rate_unit?: string|null,
     *   min_experience?: int|null,
     *   sort?: string|null,
     *   per_page?: int|null,
     * }  $filters
     */
    public function search(array $filters): LengthAwarePaginator;
}
