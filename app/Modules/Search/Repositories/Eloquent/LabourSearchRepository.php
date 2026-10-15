<?php

namespace App\Modules\Search\Repositories\Eloquent;

use App\Models\LabourDetail;
use App\Modules\Search\Repositories\Contracts\LabourSearchRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LabourSearchRepository implements LabourSearchRepositoryInterface
{
    public function search(array $filters): LengthAwarePaginator
    {
        $query = LabourDetail::query()
            ->whereNotNull('profile_completed_at')
            ->with(['user:id,mobile', 'category', 'skills.skill', 'rates']);

        // Keyword search on name / category / skill names.
        if (! empty($filters['query'])) {
            $term = $filters['query'];
            $query->where(function (Builder $q) use ($term) {
                $q->where('full_name', 'like', "%{$term}%")
                    ->orWhereHas('category', fn (Builder $c) => $c->where('name_en', 'like', "%{$term}%")->orWhere('name_hi', 'like', "%{$term}%"))
                    ->orWhereHas('skills.skill', fn (Builder $s) => $s->where('name_en', 'like', "%{$term}%")->orWhere('name_hi', 'like', "%{$term}%"));
            });
        }

        if (! empty($filters['category_id'])) {
            $query->where('labour_category_id', $filters['category_id']);
        }

        if (! empty($filters['skill_ids'])) {
            $query->whereHas('skills', fn (Builder $q) => $q->whereIn('skill_id', $filters['skill_ids']));
        }

        if (! empty($filters['city'])) {
            $query->where('city', 'like', '%'.$filters['city'].'%');
        }

        if (! empty($filters['state'])) {
            $query->where('state', 'like', '%'.$filters['state'].'%');
        }

        if (isset($filters['available_today']) && $filters['available_today'] !== null) {
            $query->where('is_available_today', (bool) $filters['available_today']);
        }

        if (! empty($filters['min_experience'])) {
            $query->where('experience_years', '>=', (int) $filters['min_experience']);
        }

        // Distance filter using the haversine formula (km).
        if (! empty($filters['latitude']) && ! empty($filters['longitude'])) {
            $lat = (float) $filters['latitude'];
            $lng = (float) $filters['longitude'];

            $haversine = '(6371 * acos(least(1, greatest(-1,
                cos(radians('.$lat.')) * cos(radians(latitude)) *
                cos(radians(longitude) - radians('.$lng.')) +
                sin(radians('.$lat.')) * sin(radians(latitude))
            ))))';

            $query->addSelect(['*', DB::raw($haversine.' as distance_km')]);

            if (! empty($filters['within_km'])) {
                $query->havingRaw('distance_km <= ?', [(int) $filters['within_km']]);
            }
        }

        // Charge filter (join rates, only active rows).
        if (! empty($filters['max_charge']) || ! empty($filters['rate_unit'])) {
            $query->whereHas('rates', function (Builder $r) use ($filters) {
                $r->where('is_active', true);

                if (! empty($filters['rate_unit'])) {
                    $r->where('rate_unit', $filters['rate_unit']);
                }

                if (! empty($filters['max_charge'])) {
                    $r->where('amount', '<=', (float) $filters['max_charge']);
                }
            });
        }

        // Sorting. Use correlated subqueries for charge sorting so the
        // computed distance_km column (added above) is preserved.
        $cheapest = '(select coalesce(min(r.amount), 999999999) from labour_rates r where r.labour_detail_id = labour_details.id and r.is_active = 1)';

        match ($filters['sort'] ?? null) {
            'charge_low' => $query->orderByRaw($cheapest.' asc'),
            'charge_high' => $query->orderByRaw($cheapest.' desc'),
            'experience' => $query->orderBy('experience_years', 'desc'),
            'distance' => $query->orderByRaw('distance_km is null, distance_km asc'),
            default => $query->orderBy('is_available_today', 'desc')->orderBy('updated_at', 'desc'),
        };

        $perPage = (int) ($filters['per_page'] ?? 15);

        return $query->paginate($perPage);
    }
}
