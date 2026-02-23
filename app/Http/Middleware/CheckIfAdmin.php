<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfAdmin
{
    private function respondToUnauthorizedRequest($request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response(trans('backpack::base.unauthorized'), 401);
        } else {
            return redirect()->guest(backpack_url('login'));
        }
    }

public function handle($request, Closure $next)
    {
        if (backpack_auth()->guest()) {
            return $this->respondToUnauthorizedRequest($request);
        }

        $user = backpack_auth()->user();

        // Validamos usando TU columna 'role' en la base de datos
        if ($user->role === 'admin' || $user->email === 'admin@ragon.com') {
            return $next($request);
        }

        return $this->respondToUnauthorizedRequest($request);
    }
}