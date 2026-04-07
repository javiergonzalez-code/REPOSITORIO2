<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfAdmin
{
    private function respondToUnauthorizedRequest($request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response(trans('auth.failed'), 401);
        } else {
            return redirect()->guest(route('login')); // Usar route() nativo
        }
    }

    public function handle($request, Closure $next)
    {
        if (auth()->guest()) { // Usar auth() nativo
            return $this->respondToUnauthorizedRequest($request);
        }

        $user = auth()->user(); // Usar auth() nativo

        if ($user->hasRole('admin') || $user->hasRole('superadmin') || in_array($user->role, ['admin', 'superadmin'])) {
            return $next($request);
        }

        return $this->respondToUnauthorizedRequest($request);
    }
}
