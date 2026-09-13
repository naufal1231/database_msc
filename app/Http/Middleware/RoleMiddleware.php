<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            abort(401, 'Unauthorized.');
        }

        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'Forbidden. Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
