<?php

namespace App\Modules\Labour\Services;

use App\Models\LabourDetail;
use App\Models\User;
use App\Modules\Catalogue\Services\Contracts\CatalogueServiceInterface;
use App\Modules\Labour\Repositories\Contracts\LabourProfileRepositoryInterface;
use App\Modules\Labour\Repositories\Contracts\LabourRepositoryInterface;
use App\Modules\Labour\Services\Contracts\LabourServiceInterface;
use Illuminate\Support\Facades\DB;

class LabourService implements LabourServiceInterface
{
    public function __construct(
        private readonly LabourRepositoryInterface $labours,
        private readonly LabourProfileRepositoryInterface $profile,
        private readonly CatalogueServiceInterface $catalogue,
    ) {
    }

    public function profileForUser(User $user): ?LabourDetail
    {
        return $this->labours->findByUserId($user->id);
    }

    public function saveStep(User $user, int $step, array $data): LabourDetail
    {
        $labour = $this->labours->findByUserId($user->id)
            ?? $this->labours->createForUser($user->id, ['registration_step' => $step]);

        return DB::transaction(function () use ($labour, $step, $data) {
            // Step 3: category + skills.
            if ($step === 3) {
                if (isset($data['labour_category_id'])) {
                    $labour->labour_category_id = $data['labour_category_id'];
                }

                if (! empty($data['skill_ids'])) {
                    $primaryId = $data['primary_skill_id'] ?? null;
                    $skills = array_map(fn ($id) => [
                        'skill_id' => (int) $id,
                        'is_primary' => (int) $id === (int) $primaryId,
                    ], $data['skill_ids']);

                    $this->profile->syncSkills($labour, $skills);
                }
            }

            // Step 4: rates.
            if ($step === 4 && ! empty($data['rates'])) {
                $this->profile->syncRates($labour, $data['rates']);
            }

            // Step 5: availability.
            if ($step === 5 && ! empty($data['availability'])) {
                $this->profile->syncAvailability($labour, $data['availability']);
            }

            // Generic scalar fields shared by all steps.
            $scalar = collect($data)->except([
                'skill_ids', 'primary_skill_id', 'rates', 'availability',
            ])->toArray();

            if (! empty($scalar)) {
                $labour->fill($scalar);
            }

            $labour->registration_step = min(max($step, 1), 6);
            $labour->save();

            if ($step === 6) {
                $labour->profile_completed_at = now();
                $labour->save();
            }

            return $labour->refresh();
        });
    }

    public function setTodayAvailability(User $user, bool $available): LabourDetail
    {
        $labour = $this->labours->findByUserId($user->id)
            ?? $this->labours->createForUser($user->id, []);

        $labour->is_available_today = $available;
        $labour->save();

        return $labour->refresh();
    }

    public function completionPercentage(LabourDetail $labour): int
    {
        $checks = [
            ! empty($labour->full_name),
            ! empty($labour->gender),
            ! empty($labour->date_of_birth),
            ! empty($labour->city),
            ! empty($labour->state),
            ! empty($labour->labour_category_id),
            $labour->skills()->count() > 0,
            $labour->rates()->count() > 0,
            $labour->availabilities()->count() > 0,
            ! empty($labour->experience_years),
            ! empty($labour->work_type),
        ];

        $filled = count(array_filter($checks));
        $total = count($checks);

        return (int) round(($filled / $total) * 100);
    }
}
