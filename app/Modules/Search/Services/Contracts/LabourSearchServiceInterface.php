<?php

namespace App\Modules\Search\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LabourSearchServiceInterface
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function search(array $filters): LengthAwarePaginator;
}
