<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            if ($user->status === 'pending') {
                if (!$request->routeIs('waiting.approval') && !$request->routeIs('logout')) {
                    return redirect()->route('waiting.approval');
                }
            } elseif ($user->status === 'inactive') {
                auth()->guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan.',
                ]);
            }
        }

        return $next($request);
    }
}
