<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckIfSuperAdmin
{
public function handle(Request $request, Closure $next): Response
{
    if (Auth::check() && Auth::user()->role === 'superadmin') {
        return $next($request);
    }

    abort(403, 'Acceso denegado. Solo el Super Admin tiene permiso aquí.');
}
}