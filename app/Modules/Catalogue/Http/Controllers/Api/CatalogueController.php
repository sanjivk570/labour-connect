<?php

namespace App\Modules\Catalogue\Http\Controllers\Api;

use App\Modules\Catalogue\Http\Resources\CategoryResource;
use App\Modules\Catalogue\Http\Resources\SkillResource;
use App\Modules\Catalogue\Services\Contracts\CatalogueServiceInterface;
use App\Modules\Common\Http\Controllers\BaseApiController;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Catalogue", description="Public labour categories and skills")
 */
class CatalogueController extends BaseApiController
{
    /**
     * List all active labour categories.
     *
     * @OA\Get(
     *     path="/categories",
     *     tags={"Catalogue"},
     *     summary="List categories",
     *     description="Returns all active labour categories with localised names (respects Accept-Language / ?locale).",
     *     @OA\Parameter(name="locale", in="query", required=false, description="Language for names", @OA\Schema(type="string", enum={"en","hi"}, default="en")),
     *     @OA\Response(response=200, description="Categories", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="data", type="array", @OA\Items(type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="slug", type="string", example="mason"),
     *             @OA\Property(property="name", type="string", example="राजमिस्त्री"),
     *             @OA\Property(property="name_en", type="string", example="Mason"),
     *             @OA\Property(property="name_hi", type="string", example="राजमिस्त्री")
     *         ))
     *     ))
     * )
     */
    public function categories(CatalogueServiceInterface $service): JsonResponse
    {
        return $this->success(CategoryResource::collection($service->listCategories()));
    }

    /**
     * List skills for a category.
     *
     * @OA\Get(
     *     path="/categories/{categoryId}/skills",
     *     tags={"Catalogue"},
     *     summary="List skills for a category",
     *     description="Returns all active skills belonging to the given category, with localised names.",
     *     @OA\Parameter(name="categoryId", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *     @OA\Parameter(name="locale", in="query", required=false, @OA\Schema(type="string", enum={"en","hi"})),
     *     @OA\Response(response=200, description="Skills", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="data", type="array", @OA\Items(type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="slug", type="string", example="masonry"),
     *             @OA\Property(property="name", type="string", example="राजमिस्त्री"),
     *             @OA\Property(property="name_en", type="string", example="Masonry"),
     *             @OA\Property(property="name_hi", type="string", example="राजमिस्त्री")
     *         ))
     *     ))
     * )
     */
    public function skills(int $categoryId, CatalogueServiceInterface $service): JsonResponse
    {
        return $this->success(SkillResource::collection($service->listSkills($categoryId)));
    }
}
