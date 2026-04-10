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

    // Gestión de Errores
    Route::get('/errores', [ErroresController::class, 'index'])->name('errores.index');

    // ==========================================
    // RUTA PARA EL SWITCH DE MANTENIMIENTO (AJAX)
    // ==========================================
    Route::post('/mantenimiento/toggle/{modulo}', [MantenimientoController::class, 'toggle'])->name('mantenimiento.toggle');


    // ==========================================
    // MÓDULO DE PAPELERA DE RECICLAJE - PROTEGIDO
    // ==========================================
    Route::get('/papelera', [App\Http\Controllers\PapeleraController::class, 'index'])->name('papelera.index');
    Route::post('/papelera/restaurar/{tipo}/{id}', [App\Http\Controllers\PapeleraController::class, 'restaurar'])->name('papelera.restaurar');
    Route::delete('/papelera/eliminar/{tipo}/{id}', [App\Http\Controllers\PapeleraController::class, 'eliminarPermanente'])->name('papelera.eliminar');
    // EL `});` SE ELIMINÓ DE AQUÍ PARA NO ROMPER LA SEGURIDAD

    // ==========================================
    // MÓDULO DE CARGA DE ARCHIVOS (INPUTS) - PROTEGIDO
    // ==========================================
    Route::middleware(['mantenimiento:inputs'])->group(function () {
        Route::get('/inputs', [InputController::class, 'index'])->name('input.index');
        Route::post('/inputs/store', [InputController::class, 'store'])->name('input.store');
        // Ruta para descargar archivos de forma segura
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
    // GESTIÓN DE USUARIOS (PROTEGIDA POR MANTENIMIENTO)
    // ==========================================
    Route::middleware(['mantenimiento:users'])->group(function () {

        // Ver lista de usuarios
        Route::get('users', [UserController::class, 'index'])
            ->name('users.index')
            ->middleware('can:list users');

        // Crear usuarios
        Route::get('users/create', [UserController::class, 'create'])
            ->name('users.create')
            ->middleware('can:create users');

        Route::post('users', [UserController::class, 'store'])
            ->name('users.store')
            ->middleware('can:create users');

        // Editar usuarios
        Route::get('users/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit')
            ->middleware('can:edit users');

        Route::put('users/{user}', [UserController::class, 'update'])
            ->name('users.update')
            ->middleware('can:edit users');

        // Eliminar usuarios
        Route::delete('users/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy')
            ->middleware('can:delete users');

        // Ver detalles de un usuario
        Route::get('users/{user}/show', [UserController::class, 'show'])
            ->name('users.show')
            ->middleware('can:list users');
    });
});
