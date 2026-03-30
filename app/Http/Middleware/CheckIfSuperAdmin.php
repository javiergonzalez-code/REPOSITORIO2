<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfSuperAdmin
{
    public function handle($request, Closure $next)
    {
        if (backpack_auth()->guest()) {
            return redirect()->guest(backpack_url('login'));
        }

        $user = backpack_auth()->user();

        // Validación exclusiva para el Súper Usuario
        if ($user->hasRole('superadmin') || $user->role === 'superadmin') {
            return $next($request);
        }

        abort(403, 'Acceso denegado. Solo el Súper Administrador puede ver esta sección.');
    }
}
