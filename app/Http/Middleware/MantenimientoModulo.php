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

            // 1. Validar primero que haya un usuario en sesión para evitar errores si auth()->user() es null
            if (auth()->check()) {
                // Usamos hasAnyRole de Spatie para validar múltiples opciones a la vez.
                if (auth()->user()->hasAnyRole(['superadmin', 'admin']) || in_array(auth()->user()->role, ['superadmin', 'admin'])) {
                    return $next($request); // Permite pasar a los administradores
                }
            }

            alert()->warning('Mantenimiento', 'El módulo ' . strtoupper($modulo) . ' está en mantenimiento.');

            return redirect('/home');
        }

        return $next($request);
    }
}
