<?php

namespace App\Modules\Labour\Repositories\Contracts;

use App\Models\LabourDetail;

interface LabourProfileRepositoryInterface
{
    /**
     * Replace the labour's skills with the given set.
     *
     * @param  array<int, array{skill_id: int, is_primary: bool}>  $skills
     */
    public function syncSkills(LabourDetail $labour, array $skills): void;

    /**
     * Replace the labour's rates keyed by rate_unit.
     *
     * @param  array<string, float>  $rates
     */
    public function syncRates(LabourDetail $labour, array $rates): void;

    /**
     * Replace the labour's weekly availability keyed by day_of_week (1-7).
     *
     * @param  array<int, array{is_available: bool, start_time: string|null, end_time: string|null}>  $availability
     */
    public function syncAvailability(LabourDetail $labour, array $availability): void;
}
