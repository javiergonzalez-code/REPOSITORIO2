<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckIfSuperAdmin
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->hasRole('superadmin') || $user->role === 'superadmin') {
            return $next($request);
        }

        abort(403, 'Acceso denegado. Solo el Súper Administrador puede ver esta sección.');
    }
}