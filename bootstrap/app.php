<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use App\Models\Log;

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
        
        $exceptions->renderable(function (PostTooLargeException $e, Request $request) {
            
            Log::create([
                'user_id' => auth()->id(), 
                'accion'  => 'Error interno: Intento de subir archivo más grande que el límite del servidor (php.ini)',
                'modulo'  => 'SISTEMA',
                'ip'      => $request->ip()
            ]);

            return redirect()->back()->with('error', 'El archivo es demasiado colosal. El servidor web bloqueó la subida antes de procesarla.');
        });

    })->create();