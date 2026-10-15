<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="LabourConnect API",
 *     version="1.0.0",
 *     description="REST API for the LabourConnect platform. Supports OTP-based auth, worker (labour) registration, customer profiles, and worker search. All static text is localised in English (en) and Hindi (hi) via the Accept-Language header or the ?locale= query parameter.",
 *     @OA\Contact(name="LabourConnect Support", email="support@labourconnect.app")
 * ),
 * @OA\Server(
 *     url="/api",
 *     description="API base path"
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum",
 *     description="Authenticate using a Sanctum bearer token returned by the OTP verify, social login, or role selection endpoints."
 * ),
 * @OA\Tag(name="Auth", description="OTP, social and session authentication"),
 * @OA\Tag(name="Catalogue", description="Public labour categories and skills"),
 * @OA\Tag(name="Labour", description="Worker (labour) profile & registration"),
 * @OA\Tag(name="Search", description="Find workers with filters"),
 * @OA\Tag(name="Customer", description="Customer profile management"),
 * @OA\Tag(name="Localization", description="Localised static UI strings")
 */
final class OpenApiInfo
{
}
