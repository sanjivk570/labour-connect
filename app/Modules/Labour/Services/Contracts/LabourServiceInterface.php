<?php

namespace App\Modules\Labour\Services\Contracts;

use App\Models\LabourDetail;
use App\Models\User;

interface LabourServiceInterface
{
    public function profileForUser(User $user): ?LabourDetail;

    /**
     * Save a single registration step (1-6) and advance the step pointer.
     */
    public function saveStep(User $user, int $step, array $data): LabourDetail;

    public function setTodayAvailability(User $user, bool $available): LabourDetail;

    /**
     * Completion percentage (0-100) based on filled profile fields.
     */
    public function completionPercentage(LabourDetail $labour): int;
}
