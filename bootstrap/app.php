<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; // 🚨 Importación nueva
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
        
        $middleware->alias([
            'mantenimiento' => \App\Http\Middleware\MantenimientoModulo::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        // 🚨 ESCENARIO 1 RESUELTO: Mostrar el ataque de archivos pesados en el panel
        $exceptions->renderable(function (PostTooLargeException $e, Request $request) {
            
            $userCode = Auth::check() ? Auth::user()->CardCode : 'DESCONOCIDO';
            
            try {
                Log::create([
                    'user_id' => $userCode, 
                    'accion'  => 'Ataque/Error: Archivo colosal bloqueado por el servidor | IP: ' . $request->ip(),
                    'modulo'  => 'ERRORES' // Cambiado a ERRORES para que se vea en la UI
                ]);
            } catch (\Exception $ex) {
                \Illuminate\Support\Facades\Log::error('Archivo colosal bloqueado | IP: ' . $request->ip());
            }

            return redirect()->back()->with('error', 'El archivo es demasiado colosal. El servidor web bloqueó la subida antes de procesarla.');
        });

        // 🚨 ESCENARIO 4 RESUELTO: Detector de bots y escaneos de vulnerabilidades
        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            
            $path = strtolower($request->path());
            
            // Si buscan estas rutas, definitivamente es un atacante o bot
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
            
            return null; // Dejamos que Laravel muestre su vista 404 normal pero ya lo registramos
        });

    })->create();