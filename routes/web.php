<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ErroresController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\OcController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MantenimientoController;

/**
 * REDIRECCIÓN INICIAL
 */
Route::get('/', function () {
    return redirect()->route('login');
});

/**
 * RUTAS PÚBLICAS (AUTENTICACIÓN)
 */
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');

/**
 * RUTAS PROTEGIDAS (MIDDLEWARE AUTH)
 * ¡Todo el sistema debe estar dentro de este grupo!
 */
Route::middleware(['auth'])->group(function () {

    // Dashboard o página de inicio
    Route::get('/home', [HomeController::class, 'home'])->name('home');

    // Cierre de sesión
    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');

    // Módulo de Errores 
    Route::get('/errores', [App\Http\Controllers\ErroresController::class, 'index'])->name('errores.index');
    Route::get('/errores/{id}', [App\Http\Controllers\ErroresController::class, 'show'])->name('errores.show');

    // ==========================================
    // RUTA PARA EL SWITCH DE MANTENIMIENTO (AJAX)
    // ==========================================
    // 🚨 Usamos la clase de tu Middleware en lugar de 'can:'
    Route::post('/mantenimiento/toggle/{modulo}', [MantenimientoController::class, 'toggle'])
        ->name('mantenimiento.toggle')
        ->middleware(\App\Http\Middleware\CheckIfAdmin::class);

    // ==========================================
    // MÓDULO DE CARGA DE ARCHIVOS (INPUTS) - PROTEGIDO
    // ==========================================
    Route::middleware(['mantenimiento:inputs'])->group(function () {
        Route::get('/inputs', [InputController::class, 'index'])->name('input.index');
        Route::post('/inputs/store', [InputController::class, 'store'])->name('input.store');
        Route::get('/archivos/descargar/{id}', [InputController::class, 'download'])->name('archivos.download');
    });

    // ==========================================
    // MÓDULO DE AUDITORÍA (LOGS) - PROTEGIDO
    // ==========================================
    Route::middleware(['mantenimiento:logs'])->group(function () {
        Route::get('/logs', [LogsController::class, 'index'])->name('logs.index');
    });

    // ==========================================
    // MÓDULO DE ÓRDENES DE COMPRA (OC) - PROTEGIDO
    // ==========================================
    Route::middleware(['mantenimiento:oc'])->group(function () {
        Route::get('/oc', [OcController::class, 'index'])->name('oc.index');
        Route::get('/oc/download/{id}', [OcController::class, 'download'])->name('oc.download');
        Route::get('/oc/preview/{id}', [OcController::class, 'preview'])->name('oc.preview');
        Route::delete('/oc/{id}', [OcController::class, 'destroy'])->name('oc.destroy');
    });

    // ==========================================
    // GESTIÓN DE USUARIOS (PROTEGIDA POR MANTENIMIENTO Y ADMIN)
    // ==========================================
    // 🚨 Agregamos CheckIfAdmin al grupo y quitamos los 'can:' individuales
    Route::middleware(['mantenimiento:users', \App\Http\Middleware\CheckIfAdmin::class])->group(function () {

        // Ver lista de usuarios
        Route::get('users', [UserController::class, 'index'])->name('users.index');

        // Crear usuarios
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');

        // Editar usuarios (🚨 Cambiamos {user} por {id})
        Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');

        // Eliminar usuarios (🚨 Cambiamos {user} por {id})
        Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        // Ver detalles de un usuario (🚨 Cambiamos {user} por {id})
        Route::get('users/{id}/show', [UserController::class, 'show'])->name('users.show');
    });
});
