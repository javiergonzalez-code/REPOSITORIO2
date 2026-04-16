<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use App\Models\Log;
use Illuminate\Support\Facades\Auth; // 🚨 Importamos Auth

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
            
            // 🚨 1. Validamos que exista la sesión y extraemos el CardCode
            $userCode = Auth::check() ? Auth::user()->CardCode : null;
            
            Log::create([
                'user_id' => $userCode, 
                // 🚨 2. Concatenamos la IP en la acción porque 'ip' no está en el $fillable del modelo Log
                'accion'  => 'Error interno: Intento de subir archivo más grande que el límite del servidor (php.ini) | IP: ' . $request->ip(),
                'modulo'  => 'SISTEMA'
            ]);

            return redirect()->back()->with('error', 'El archivo es demasiado colosal. El servidor web bloqueó la subida antes de procesarla.');
        });

    })->create();