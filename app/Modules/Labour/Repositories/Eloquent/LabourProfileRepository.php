<?php

namespace App\Modules\Labour\Repositories\Eloquent;

use App\Models\LabourAvailability;
use App\Models\LabourDetail;
use App\Models\LabourRate;
use App\Models\LabourSkill;
use App\Modules\Labour\Repositories\Contracts\LabourProfileRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LabourProfileRepository implements LabourProfileRepositoryInterface
{
    public function syncSkills(LabourDetail $labour, array $skills): void
    {
        DB::transaction(function () use ($labour, $skills) {
            LabourSkill::where('labour_detail_id', $labour->id)->delete();

            foreach ($skills as $skill) {
                LabourSkill::create([
                    'labour_detail_id' => $labour->id,
                    'skill_id' => $skill['skill_id'],
                    'is_primary' => $skill['is_primary'] ?? false,
                ]);
            }
        });
    }

    public function syncRates(LabourDetail $labour, array $rates): void
    {
        DB::transaction(function () use ($labour, $rates) {
            foreach ($rates as $unit => $amount) {
                LabourRate::updateOrCreate(
                    ['labour_detail_id' => $labour->id, 'rate_unit' => $unit],
                    ['amount' => $amount, 'is_active' => true],
                );
            }
        });
    }

    public function syncAvailability(LabourDetail $labour, array $availability): void
    {
        DB::transaction(function () use ($labour, $availability) {
            foreach ($availability as $day => $row) {
                LabourAvailability::updateOrCreate(
                    ['labour_detail_id' => $labour->id, 'day_of_week' => $day],
                    [
                        'is_available' => $row['is_available'],
                        'start_time' => $row['start_time'] ?? null,
                        'end_time' => $row['end_time'] ?? null,
                    ],
                );
            }
        });
    }
}
