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

        $user = backpack_user();

        // Verificamos por Spatie o por los correos de salvavidas
        // NOTA: Asegúrate de que el rol en tu base de datos se llame exactamente 'admin' (en minúsculas)
        if ($user->hasRole('admin') || 
            $user->email === 'admin@ragon.com' ||
            $user->email === 'test@example.com') {
            
            return $next($request);
        }

        return $this->respondToUnauthorizedRequest($request);
    }
}