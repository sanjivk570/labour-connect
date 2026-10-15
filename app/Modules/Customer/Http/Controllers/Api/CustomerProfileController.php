<?php

namespace App\Modules\Customer\Http\Controllers\Api;

use App\Modules\Common\Http\Controllers\BaseApiController;
use App\Modules\Customer\Actions\UpdateCustomerProfileAction;
use App\Modules\Customer\Http\Requests\UpdateCustomerProfileRequest;
use App\Modules\Customer\Http\Resources\CustomerProfileResource;
use App\Modules\Customer\Services\Contracts\CustomerServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Customer", description="Customer profile management")
 */
class CustomerProfileController extends BaseApiController
{
    /**
     * Get the authenticated customer's profile.
     *
     * @OA\Get(
     *     path="/customer/profile",
     *     tags={"Customer"},
     *     summary="Get customer profile",
     *     description="Returns the authenticated customer's profile details.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Customer profile", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="data", type="object", nullable=true,
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=2),
     *             @OA\Property(property="full_name", type="string", example="Ravi Sharma"),
     *             @OA\Property(property="photo_path", type="string", nullable=true)
     *         )
     *     )),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Not a customer")
     * )
     */
    public function show(Request $request, CustomerServiceInterface $service): JsonResponse
    {
        $customer = $service->profileForUser($request->user());

        return $this->success($customer ? CustomerProfileResource::make($customer) : null);
    }

    /**
     * Create or update the authenticated customer's profile.
     *
     * @OA\Put(
     *     path="/customer/profile",
     *     tags={"Customer"},
     *     summary="Update customer profile",
     *     description="Creates the profile if it does not exist, otherwise updates it.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"full_name"},
     *             @OA\Property(property="full_name", type="string", maxLength=120, example="Ravi Sharma"),
     *             @OA\Property(property="photo_path", type="string", nullable=true, example="photos/ravi.jpg")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Profile updated", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="message", type="string", example="Profile updated successfully."),
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="full_name", type="string", example="Ravi Sharma")
     *         )
     *     )),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateCustomerProfileRequest $request, UpdateCustomerProfileAction $action): JsonResponse
    {
        $customer = $action->execute($request->user(), $request->validated());

        return $this->success(CustomerProfileResource::make($customer), __('api.profile_updated'));
    }
}
