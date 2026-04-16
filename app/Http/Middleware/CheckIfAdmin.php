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

    public function handle(Request $request, Closure $next): Response
    {
        // Validamos directamente contra la columna 'role'
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            return $next($request);
        }

        abort(403, 'Acceso denegado. Se requiere rol de Administrador.');
    }
}
