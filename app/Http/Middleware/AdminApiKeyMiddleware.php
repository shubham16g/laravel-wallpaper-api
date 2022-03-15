<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class AdminApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->bearerToken();
        if ($key == null) {
            throw new AuthenticationException('Admin key is missing');
        }
        if ($key !== config('app.admin_key')) {
            throw new AuthenticationException('Invalid Admin key');
        }
        return $next($request);
    }
}
