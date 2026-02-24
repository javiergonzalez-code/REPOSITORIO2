<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException; // Importar esta clase
use Illuminate\Http\Request;
use App\Models\Log; // Importar tu modelo de logs

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ...
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        // ATRAPAR EL ERROR DE ARCHIVO MÁS GRANDE QUE EL LÍMITE DE PHP
        $exceptions->renderable(function (PostTooLargeException $e, Request $request) {
            
            // Registramos la infracción
            Log::create([
                'user_id' => auth()->id(), 
                'accion'  => 'Error interno: Intento de subir archivo más grande que el límite del servidor (php.ini)',
                'modulo'  => 'SISTEMA',
                'ip'      => $request->ip()
            ]);

            // Redirigir de vuelta con el mensaje de error
            return redirect()->back()->with('error', 'El archivo es demasiado colosal. El servidor web bloqueó la subida antes de procesarla.');
        });

    })->create();