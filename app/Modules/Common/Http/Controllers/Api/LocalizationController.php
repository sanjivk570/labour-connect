<?php

namespace App\Modules\Common\Http\Controllers\Api;

use App\Modules\Common\Http\Controllers\BaseApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Localization", description="Localised static UI strings")
 */
class LocalizationController extends BaseApiController
{
    /**
     * Get all localised static UI strings.
     *
     * @OA\Get(
     *     path="/localization/strings",
     *     tags={"Localization"},
     *     summary="Get localised UI strings",
     *     description="Returns every static message for the active locale, chosen via the ?locale= query parameter (en|hi) or the Accept-Language header. Useful for apps that render screens from server-provided labels.",
     *     @OA\Parameter(name="locale", in="query", required=false, description="Language code", @OA\Schema(type="string", enum={"en","hi"}, default="en")),
     *     @OA\Response(response=200, description="Localised strings", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="locale", type="string", example="hi"),
     *             @OA\Property(property="messages", type="object", additionalProperties=true, example={"app_title":"LabourConnect","available":"उपलब्ध"})
     *         )
     *     ))
     * )
     */
    public function strings(Request $request): JsonResponse
    {
        App::setLocale($request->input('locale', App::getLocale()));

        return $this->success([
            'locale' => App::getLocale(),
            'messages' => __('messages'),
        ]);
    }
}
