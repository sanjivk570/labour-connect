<?php

namespace App\Modules\Auth\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Authentication response carrying the user and a bearer token.
 * Expects an array payload: ['user' => UserResource, 'token' => plainTextToken].
 */
class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var array{user: UserResource, token: string} $resource */
        $resource = $this->resource;

        return [
            'user' => $resource['user'],
            'access_token' => $resource['token'],
            'token_type' => 'Bearer',
        ];
    }
}
