<?php

namespace App\Modules\Common\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Restrict a route to users with one of the given roles.
     * Usage: middleware('role:labour') or middleware('role:labour,customer').
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            return response()->json([
                'success' => false,
                'message' => __('api.unauthorized'),
            ], 403);
        }

        return $next($request);
    }
}
