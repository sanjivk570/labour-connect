<?php

namespace App\Modules\Labour\Http\Controllers\Api;

use App\Modules\Common\Http\Controllers\BaseApiController;
use App\Modules\Labour\Actions\ComputeProfileCompletionAction;
use App\Modules\Labour\Actions\SaveRegistrationStepAction;
use App\Modules\Labour\Actions\ToggleTodayAvailabilityAction;
use App\Modules\Labour\Http\Requests\RegistrationStepRequest;
use App\Modules\Labour\Http\Requests\TodayAvailabilityRequest;
use App\Modules\Labour\Http\Resources\LabourProfileResource;
use App\Modules\Labour\Services\Contracts\LabourServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Labour", description="Worker (labour) profile & multi-step registration")
 */
class LabourProfileController extends BaseApiController
{
    /**
     * Get the labour profile.
     *
     * @OA\Get(
     *     path="/labour/profile",
     *     tags={"Labour"},
     *     summary="Get labour profile",
     *     description="Returns the authenticated labour worker's full profile (category, skills, rates, availability).",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Labour profile", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="full_name", type="string", example="राहुल कुमार"),
     *             @OA\Property(property="gender", type="string", example="male"),
     *             @OA\Property(property="city", type="string", example="सीकर"),
     *             @OA\Property(property="state", type="string", example="राजस्थान"),
     *             @OA\Property(property="experience_years", type="integer", example=5),
     *             @OA\Property(property="registration_step", type="integer", example=6),
     *             @OA\Property(property="is_available_today", type="boolean", example=true),
     *             @OA\Property(property="skills", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="rates", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="availabilities", type="array", @OA\Items(type="object"))
     *         )
     *     )),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Not a labour account")
     * )
     */
    public function show(Request $request, LabourServiceInterface $service): JsonResponse
    {
        $labour = $service->profileForUser($request->user());

        if (! $labour) {
            return $this->success(null, __('api.profile_not_found'), 200);
        }

        $labour->load(['category', 'skills.skill', 'rates', 'availabilities']);

        return $this->success(LabourProfileResource::make($labour));
    }

    /**
     * Save a registration step (1-6).
     *
     * Step payloads:
     * - 1 Personal: full_name, gender, date_of_birth
     * - 2 Address: city, state, pincode, latitude, longitude, max_travel_distance_km
     * - 3 Skills: labour_category_id, skill_ids[], primary_skill_id
     * - 4 Rates: rates{hour,day,week,month}
     * - 5 Availability: availability{1..7:{is_available,start_time,end_time}}
     * - 6 Other: experience_years, work_type, bio
     *
     * @OA\Put(
     *     path="/labour/registration/step/{step}",
     *     tags={"Labour"},
     *     summary="Save registration step",
     *     description="Saves the given registration step (1-6) and advances the step pointer. Saving step 6 marks the profile as completed.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="step", in="path", required=true, @OA\Schema(type="integer", enum={1,2,3,4,5,6}), example=3),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="full_name", type="string", example="राहुल कुमार", nullable=true),
     *             @OA\Property(property="gender", type="string", enum={"male","female","other","prefer_not_to_say"}, nullable=true),
     *             @OA\Property(property="date_of_birth", type="string", format="date", nullable=true),
     *             @OA\Property(property="city", type="string", example="सीकर", nullable=true),
     *             @OA\Property(property="state", type="string", example="राजस्थान", nullable=true),
     *             @OA\Property(property="pincode", type="string", example="332001", nullable=true),
     *             @OA\Property(property="latitude", type="number", format="float", example=27.6094, nullable=true),
     *             @OA\Property(property="longitude", type="number", format="float", example=75.1399, nullable=true),
     *             @OA\Property(property="max_travel_distance_km", type="integer", example=10, nullable=true),
     *             @OA\Property(property="labour_category_id", type="integer", example=1, nullable=true),
     *             @OA\Property(property="skill_ids", type="array", nullable=true, @OA\Items(type="integer")),
     *             @OA\Property(property="primary_skill_id", type="integer", nullable=true),
     *             @OA\Property(property="rates", type="object", nullable=true,
     *                 @OA\Property(property="hour", type="number", example=150),
     *                 @OA\Property(property="day", type="number", example=700),
     *                 @OA\Property(property="week", type="number", example=4500),
     *                 @OA\Property(property="month", type="number", example=15000)
     *             ),
     *             @OA\Property(property="availability", type="object", nullable=true,
     *                 description="Keyed by day_of_week 1(Monday)-7(Sunday)",
     *                 @OA\AdditionalProperties(
     *                     type="object",
     *                     @OA\Property(property="is_available", type="boolean", example=true),
     *                     @OA\Property(property="start_time", type="string", example="09:00", nullable=true),
     *                     @OA\Property(property="end_time", type="string", example="18:00", nullable=true)
     *                 )
     *             ),
     *             @OA\Property(property="experience_years", type="integer", example=5, nullable=true),
     *             @OA\Property(property="work_type", type="string", enum={"full_time","part_time","contract"}, nullable=true),
     *             @OA\Property(property="bio", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Step saved / registration completed", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="message", type="string", example="Registration completed successfully."),
     *         @OA\Property(property="data", type="object")
     *     )),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function saveStep(RegistrationStepRequest $request, SaveRegistrationStepAction $action): JsonResponse
    {
        $step = (int) $request->route('step');
        $labour = $action->execute($request->user(), $step, $request->validated());
        $labour->load(['category', 'skills.skill', 'rates', 'availabilities']);

        $message = $step === 6
            ? __('api.registration_completed')
            : __('api.registration_step_saved');

        return $this->success(LabourProfileResource::make($labour), $message);
    }

    /**
     * Set today's availability.
     *
     * @OA\Patch(
     *     path="/labour/availability/today",
     *     tags={"Labour"},
     *     summary="Toggle today's availability",
     *     description="Marks the labour worker as available or unavailable for today.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"is_available_today"},
     *             @OA\Property(property="is_available_today", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Availability updated", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="message", type="string", example="Availability updated successfully."),
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="is_available_today", type="boolean", example=true)
     *         )
     *     )),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateToday(TodayAvailabilityRequest $request, ToggleTodayAvailabilityAction $action): JsonResponse
    {
        $labour = $action->execute($request->user(), $request->boolean('is_available_today'));

        return $this->success([
            'is_available_today' => $labour->is_available_today,
        ], __('api.availability_updated'));
    }

    /**
     * Get the labour dashboard summary.
     *
     * @OA\Get(
     *     path="/labour/dashboard",
     *     tags={"Labour"},
     *     summary="Labour dashboard",
     *     description="Returns a greeting, profile completion percentage, today's availability, and the profile summary shown on the worker home screen.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Dashboard", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="greeting", type="string", example="नमस्ते, राहुल!"),
     *             @OA\Property(property="completion_percentage", type="integer", example=90),
     *             @OA\Property(property="is_available_today", type="boolean", example=true),
     *             @OA\Property(property="profile", type="object")
     *         )
     *     )),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function dashboard(Request $request, LabourServiceInterface $service, ComputeProfileCompletionAction $completion): JsonResponse
    {
        $labour = $service->profileForUser($request->user());

        if (! $labour) {
            return $this->success([
                'profile' => null,
                'completion_percentage' => 0,
            ], __('api.profile_not_found'));
        }

        $labour->load(['skills.skill', 'rates', 'availabilities']);

        return $this->success([
            'greeting' => __('messages.greeting', ['name' => $labour->full_name ?? $request->user()->mobile]),
            'completion_percentage' => $completion->execute($labour),
            'is_available_today' => $labour->is_available_today,
            'profile' => LabourProfileResource::make($labour),
        ]);
    }
}
