<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfAdmin
{
    /**
     * Answer to unauthorized access request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private function respondToUnauthorizedRequest($request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response(trans('backpack::base.unauthorized'), 401);
        } else {
            return redirect()->guest(backpack_url('login'));
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (backpack_auth()->guest()) {
            return $this->respondToUnauthorizedRequest($request);
        }

        $user = backpack_user();

        // 1. Verificamos por ROL de Spatie
        // 2. O verificamos por columna física 'role' (por si Spatie falla)
        // 3. O verificamos por EMAIL (tu salvavidas para no quedarte fuera)
        if ($user->hasRole('admin') || 
            $user->role === 'admin' || 
            $user->email === 'admin@ragon.com' ||
            $user->email === 'test@example.com') {
            
            return $next($request);
        }

        return $this->respondToUnauthorizedRequest($request);
    }
}