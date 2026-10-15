<?php

namespace App\Modules\Search\Actions;

use App\Modules\Search\Services\Contracts\LabourSearchServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SearchLabourAction
{
    public function __construct(private readonly LabourSearchServiceInterface $service)
    {
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function execute(array $filters): LengthAwarePaginator
    {
        return $this->service->search($filters);
    }
}
