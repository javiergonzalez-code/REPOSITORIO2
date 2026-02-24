<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfSuperAdmin
{
    public function handle($request, Closure $next)
    {
        // Si no está logueado, lo mandamos al login
        if (backpack_auth()->guest()) {
            return redirect()->guest(backpack_url('login'));
        }

        $user = backpack_auth()->user();

        // AQUÍ VALIDAMOS AL SÚPER USUARIO. 
        // Solo pasará si es el correo específico (o si le agregas un rol 'superadmin')
        if ($user->email === 'admin@ragon.com' || $user->role === 'superadmin') {
            return $next($request);
        }

        // Si es un admin normal pero NO el super usuario, le mostramos un error 403 (Prohibido)
        abort(403, 'Acceso denegado. Solo el Súper Administrador puede ver esta sección.');
    }
}