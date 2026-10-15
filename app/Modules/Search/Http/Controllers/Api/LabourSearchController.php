<?php

namespace App\Modules\Search\Http\Controllers\Api;

use App\Modules\Common\Http\Controllers\BaseApiController;
use App\Modules\Search\Actions\SearchLabourAction;
use App\Modules\Search\Http\Requests\SearchLabourRequest;
use App\Modules\Search\Http\Resources\LabourSearchResultResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Search", description="Find workers with filters")
 */
class LabourSearchController extends BaseApiController
{
    /**
     * Search for labour workers.
     *
     * @OA\Get(
     *     path="/labour/search",
     *     tags={"Search"},
     *     summary="Search labour workers",
     *     description="Searches completed labour profiles by keyword, category, skills, location (haversine distance), charge, experience, and availability. Returns a paginated list sorted by the chosen criteria.",
     *     @OA\Parameter(name="query", in="query", description="Keyword on name / category / skill", @OA\Schema(type="string", example="राजमिस्त्री")),
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="skill_ids[]", in="query", @OA\Schema(type="array", @OA\Items(type="integer"))),
     *     @OA\Parameter(name="city", in="query", @OA\Schema(type="string", example="सीकर")),
     *     @OA\Parameter(name="state", in="query", @OA\Schema(type="string", example="राजस्थान")),
     *     @OA\Parameter(name="latitude", in="query", @OA\Schema(type="number", format="double", example=27.6094)),
     *     @OA\Parameter(name="longitude", in="query", @OA\Schema(type="number", format="double", example=75.1399)),
     *     @OA\Parameter(name="within_km", in="query", description="Max distance in km", @OA\Schema(type="integer", enum={5,10,20,50}, example=10)),
     *     @OA\Parameter(name="available_today", in="query", @OA\Schema(type="boolean", example=true)),
     *     @OA\Parameter(name="max_charge", in="query", description="Max charge for the given rate_unit", @OA\Schema(type="number", example=800)),
     *     @OA\Parameter(name="rate_unit", in="query", @OA\Schema(type="string", enum={"hour","day","week","month"}, example="day")),
     *     @OA\Parameter(name="min_experience", in="query", @OA\Schema(type="integer", example=3)),
     *     @OA\Parameter(name="sort", in="query", description="Sort order", @OA\Schema(type="string", enum={"distance","charge_low","charge_high","experience","newest"}, default="newest")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15, maximum=50)),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     *     @OA\Response(response=200, description="Paginated results", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="data", type="array", @OA\Items(type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="राहुल कुमार"),
     *             @OA\Property(property="category_name", type="string", example="राजमिस्त्री"),
     *             @OA\Property(property="experience_label", type="string", example="अनुभव 5 वर्ष"),
     *             @OA\Property(property="distance_km", type="number", format="float", example=2.5),
     *             @OA\Property(property="distance_label", type="string", example="2.5 KM दूर"),
     *             @OA\Property(property="is_available", type="boolean", example=true),
     *             @OA\Property(property="availability_label", type="string", example="उपलब्ध"),
     *             @OA\Property(property="charge_per_day", type="number", format="float", example=700),
     *             @OA\Property(property="charge_per_day_label", type="string", example="₹700 / दिन")
     *         )),
     *         @OA\Property(property="meta", type="object",
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=3),
     *             @OA\Property(property="per_page", type="integer", example=15),
     *             @OA\Property(property="total", type="integer", example=42)
     *         )
     *     )),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function __invoke(SearchLabourRequest $request, SearchLabourAction $action): JsonResponse
    {
        $results = $action->execute($request->validated());

        return $this->success(
            LabourSearchResultResource::collection($results->items()),
            '',
            200,
            [
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
            ],
        );
    }
}
