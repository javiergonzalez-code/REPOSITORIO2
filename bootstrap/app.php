<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; 
use Illuminate\Http\Request;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        // Captura el error de PHP cuando un archivo supera el límite antes de que rompa la app
        $exceptions->renderable(function (PostTooLargeException $e, Request $request) {
            
            $userCode = Auth::check() ? Auth::user()->CardCode : 'DESCONOCIDO';
            
            try {
                Log::create([
                    'user_id' => $userCode, 
                    'accion'  => 'Ataque/Error: Archivo colosal bloqueado por el servidor | IP: ' . $request->ip(),
                    'modulo'  => 'ERRORES'
                ]);
            } catch (\Exception $ex) {
                // Respaldo físico si la Base de Datos está caída o saturada
                \Illuminate\Support\Facades\Log::error('Archivo colosal bloqueado | IP: ' . $request->ip());
            }

            // Regresa al usuario a la pantalla anterior con un mensaje flash de error
            return redirect()->back()->with('error', 'El archivo es demasiado colosal. El servidor web bloqueó la subida antes de procesarla.');
        });

        // Captura todos los errores 404 para analizar si están buscando rutas sospechosas
        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            
            $path = strtolower($request->path());
            
            // Si la ruta buscada contiene palabras clave de hackeo o carpetas comunes de WordPress/PHP
            if (str_contains($path, 'env') || str_contains($path, 'phpmyadmin') || str_contains($path, 'admin/login') || str_contains($path, 'wp-admin')) {
                
                $userCode = Auth::check() ? Auth::user()->CardCode : 'BOT/HACKER';
                
                try {
                    Log::create([
                        'user_id' => $userCode,
                        'accion'  => 'ALERTA DE SEGURIDAD: Intento de escaneo a ruta prohibida (/' . $path . ') | IP: ' . $request->ip(),
                        'modulo'  => 'ERRORES'
                    ]);
                } catch (\Exception $ex) {
                    \Illuminate\Support\Facades\Log::warning('Escaneo malicioso detectado en /' . $path . ' | IP: ' . $request->ip());
                }
            }
            
            return null; 
        });

    })->create();