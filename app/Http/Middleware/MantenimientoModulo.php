<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MantenimientoModulo
{
    /**
     * Maneja la solicitud entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $modulo
     */
    public function handle(Request $request, Closure $next, $modulo = 'general'): Response
    {

        $setting = \DB::table('modulo_settings')->where('nombre_modulo', $modulo)->first();

        if ($setting && $setting->en_mantenimiento) {
            // Solo permitimos el paso al Administrador o Super Admin
            if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Administrador')) {
                abort(503, 'El módulo ' . strtoupper($modulo) . ' está en mantenimiento.');
            }
        }
        return $next($request);
    }
}
