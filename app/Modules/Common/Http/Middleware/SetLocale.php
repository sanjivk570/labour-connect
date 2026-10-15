<?php

namespace App\Modules\Common\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Set the application locale from the Accept-Language header, a ?locale=
     * query parameter, or the authenticated user's preference.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language')
            ?? $request->query('locale')
            ?? $request->user()?->preferred_locale;

        if (is_string($locale) && in_array(substr($locale, 0, 2), ['en', 'hi'], true)) {
            App::setLocale(substr($locale, 0, 2));
        }

        return $next($request);
    }
}
