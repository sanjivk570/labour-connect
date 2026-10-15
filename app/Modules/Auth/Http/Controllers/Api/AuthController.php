<?php

namespace App\Modules\Auth\Http\Controllers\Api;

use App\Modules\Auth\Actions\SelectRoleAction;
use App\Modules\Auth\Actions\SendOtpAction;
use App\Modules\Auth\Actions\SocialLoginAction;
use App\Modules\Auth\Actions\VerifyOtpAction;
use App\Modules\Auth\Http\Requests\SelectRoleRequest;
use App\Modules\Auth\Http\Requests\SendOtpRequest;
use App\Modules\Auth\Http\Requests\SocialLoginRequest;
use App\Modules\Auth\Http\Requests\VerifyOtpRequest;
use App\Modules\Auth\Http\Resources\AuthResource;
use App\Modules\Auth\Http\Resources\UserResource;
use App\Modules\Common\Http\Controllers\BaseApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\NewAccessToken;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Auth", description="OTP, social and session authentication")
 */
class AuthController extends BaseApiController
{
    /**
     * Send a login OTP to a mobile number.
     *
     * @OA\Post(
     *     path="/auth/otp",
     *     tags={"Auth"},
     *     summary="Send OTP",
     *     description="Generates and sends a 6-digit OTP to the given mobile number. In non-production environments the OTP is also returned as debug_otp for testing.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mobile"},
     *             @OA\Property(property="mobile", type="string", example="9876543210", description="10-digit Indian mobile number starting 6-9")
     *         )
     *     ),
     *     @OA\Response(response=200, description="OTP sent", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="message", type="string", example="OTP sent successfully."),
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="otp_expires_in_minutes", type="integer", example=5),
     *             @OA\Property(property="debug_otp", type="string", example="123456", nullable=true)
     *         )
     *     )),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function sendOtp(SendOtpRequest $request, SendOtpAction $action): JsonResponse
    {
        $result = $action->execute($request->string('mobile')->toString());

        $data = ['otp_expires_in_minutes' => 5];

        // Only expose the generated OTP in non-production environments.
        if (! app()->environment('production')) {
            $data['debug_otp'] = $result['otp'];
        }

        return $this->success($data, __('api.otp_sent'));
    }

    /**
     * Verify OTP and obtain an API token.
     *
     * @OA\Post(
     *     path="/auth/otp/verify",
     *     tags={"Auth"},
     *     summary="Verify OTP",
     *     description="Verifies the OTP, creates/updates the user, and returns a bearer token. If the user has not yet chosen a role, a role must be selected afterwards.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mobile", "otp"},
     *             @OA\Property(property="mobile", type="string", example="9876543210"),
     *             @OA\Property(property="otp", type="string", example="246813", description="6-digit OTP"),
     *             @OA\Property(property="device_token", type="string", nullable=true, example="fcm-token-xyz"),
     *             @OA\Property(property="platform", type="string", enum={"android","ios"}, nullable=true, example="android")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Logged in", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="message", type="string", example="Logged in successfully."),
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="mobile", type="string", example="9876543210"),
     *                 @OA\Property(property="role", type="string", enum={"customer","labour"}, example="labour"),
     *                 @OA\Property(property="preferred_locale", type="string", example="hi"),
     *                 @OA\Property(property="mobile_verified", type="boolean", example=true)
     *             ),
     *             @OA\Property(property="access_token", type="string", example="1|abcdef..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     )),
     *     @OA\Response(response=422, description="Invalid or expired OTP")
     * )
     */
    public function verifyOtp(VerifyOtpRequest $request, VerifyOtpAction $action): JsonResponse
    {
        $result = $action->execute(
            $request->string('mobile')->toString(),
            $request->string('otp')->toString(),
            $request->input('device_token'),
            $request->input('platform'),
        );

        /** @var NewAccessToken $token */
        $token = $result['token'];

        $payload = [
            'user' => UserResource::make($result['user']),
            'token' => $token->plainTextToken,
        ];

        return $this->success(AuthResource::make($payload), __('api.login_success'));
    }

    /**
     * Choose the user's role and receive a fresh token.
     *
     * @OA\Post(
     *     path="/auth/role",
     *     tags={"Auth"},
     *     summary="Select role",
     *     description="Sets whether the authenticated user is a customer or a labour worker, stores the preferred locale, and returns a fresh bearer token.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role"},
     *             @OA\Property(property="role", type="string", enum={"customer","labour"}, example="labour"),
     *             @OA\Property(property="locale", type="string", enum={"en","hi"}, example="hi")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Role selected", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="message", type="string", example="Role selected successfully."),
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     )),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function selectRole(SelectRoleRequest $request, SelectRoleAction $action): JsonResponse
    {
        $result = $action->execute(
            $request->user(),
            $request->string('role')->toString(),
            $request->input('locale', app()->getLocale()),
        );

        /** @var NewAccessToken $token */
        $token = $result['token'];

        $payload = [
            'user' => UserResource::make($result['user']),
            'token' => $token->plainTextToken,
        ];

        return $this->success(AuthResource::make($payload), __('api.role_selected'));
    }

    /**
     * Continue with a social provider (Google / Facebook).
     *
     * @OA\Post(
     *     path="/auth/social",
     *     tags={"Auth"},
     *     summary="Social login",
     *     description="Logs in or registers a user via a social provider identity and returns a bearer token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"provider", "provider_id"},
     *             @OA\Property(property="provider", type="string", enum={"google","facebook"}, example="google"),
     *             @OA\Property(property="provider_id", type="string", example="109384759283"),
     *             @OA\Property(property="locale", type="string", enum={"en","hi"}, nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Logged in", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     )),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function socialLogin(SocialLoginRequest $request, SocialLoginAction $action): JsonResponse
    {
        $result = $action->execute(
            $request->string('provider')->toString(),
            $request->string('provider_id')->toString(),
            $request->input('locale'),
        );

        /** @var NewAccessToken $token */
        $token = $result['token'];

        $payload = [
            'user' => UserResource::make($result['user']),
            'token' => $token->plainTextToken,
        ];

        return $this->success(AuthResource::make($payload), __('api.login_success'));
    }

    /**
     * Log out the current device/session.
     *
     * @OA\Post(
     *     path="/auth/logout",
     *     tags={"Auth"},
     *     summary="Logout",
     *     description="Revokes the current access token.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Logged out", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="message", type="string", example="Logged out successfully.")
     *     )),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, __('api.logout_success'));
    }

    /**
     * Get the authenticated user.
     *
     * @OA\Get(
     *     path="/auth/me",
     *     tags={"Auth"},
     *     summary="Current user",
     *     description="Returns the currently authenticated user's profile.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Authenticated user", @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="mobile", type="string", example="9876543210"),
     *             @OA\Property(property="role", type="string", example="labour")
     *         )
     *     )),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return $this->success(UserResource::make($request->user()));
    }
}
