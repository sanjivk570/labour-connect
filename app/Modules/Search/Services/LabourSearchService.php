<?php

namespace App\Modules\Search\Services;

use App\Modules\Search\Repositories\Contracts\LabourSearchRepositoryInterface;
use App\Modules\Search\Services\Contracts\LabourSearchServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LabourSearchService implements LabourSearchServiceInterface
{
    public function __construct(private readonly LabourSearchRepositoryInterface $repository)
    {
    }

    public function search(array $filters): LengthAwarePaginator
    {
        // Guard against huge page sizes.
        $filters['per_page'] = min((int) ($filters['per_page'] ?? 15), 50);

        return $this->repository->search($filters);
    }
}
