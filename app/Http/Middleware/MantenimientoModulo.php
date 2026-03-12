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
                // Agregué opciones en minúscula por si en la base de datos están guardados así.
                if (auth()->user()->hasAnyRole(['Super Admin', 'Administrador', 'super-admin', 'admin'])) {
                    return $next($request); // Permite pasar a los administradores
                }
            }

            // 2. Si el usuario no es admin o no está logueado, lanzamos SweetAlert
            // Asumiendo que usas el helper global alert() del paquete realrashid/sweet-alert
            alert()->warning('Mantenimiento', 'El módulo ' . strtoupper($modulo) . ' está en mantenimiento.');

            // 3. Redirigimos a la vista home en lugar de mostrar el error 503
            // Puedes cambiar redirect('/home') por redirect()->route('home') si tienes la ruta nombrada
            return redirect('/home'); 
        }

        return $next($request);
    }
}