<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ErroresController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\OcController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\Usercontroller;

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
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

/**
 * RUTAS PROTEGIDAS (MIDDLEWARE AUTH)
 */
Route::middleware(['auth'])->group(function () {

    // Dashboard o página de inicio
    Route::get('/home', [AuthController::class, 'home'])->name('home');

    // Cierre de sesión
    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');

    // Gestión de Errores (puedes añadir middleware 'can' si lo deseas)
    Route::get('/errores', [ErroresController::class, 'index'])->name('errores.index');

    // Módulo de Carga de Archivos (Inputs)
    Route::get('/inputs', [InputController::class, 'index'])->name('input.index');
    Route::post('/inputs/store', [InputController::class, 'store'])->name('input.store');

    // Módulo de Auditoría (Logs)
    Route::get('/logs', [LogsController::class, 'index'])->name('logs.index');

    // Módulo de Órdenes de Compra (OC)
    Route::get('/oc', [OcController::class, 'index'])->name('oc.index');
    Route::get('/oc/download/{id}', [OcController::class, 'download'])->name('oc.download');
    Route::get('/oc/preview/{id}', [OcController::class, 'preview'])->name('oc.preview');
    Route::delete('/oc/{id}', [App\Http\Controllers\OcController::class, 'destroy'])->name('oc.destroy');
    
    // Ruta para descargar archivos de forma segura
    Route::get('/archivos/descargar/{id}', [InputController::class, 'download'])->name('archivos.download');
    /**
     * GESTIÓN DE USUARIOS (PROTEGIDA POR PERMISOS)
     * Aquí aplicamos la lógica de "can:nombre-del-permiso"
     */

    // Ver lista de usuarios
    Route::get('users', [Usercontroller::class, 'index'])
        ->name('users.index')
        ->middleware('can:list users');

    // Crear usuarios
    Route::get('users/create', [Usercontroller::class, 'create'])
        ->name('users.create')
        ->middleware('can:create users');

    Route::post('users', [Usercontroller::class, 'store'])
        ->name('users.store')
        ->middleware('can:create users');

    // Editar usuarios
    Route::get('users/{user}/edit', [Usercontroller::class, 'edit'])
        ->name('users.edit')
        ->middleware('can:edit users'); // Nota: Asegúrate de añadir 'edit users' a tu Seeder

    Route::put('users/{user}', [Usercontroller::class, 'update'])
        ->name('users.update')
        ->middleware('can:edit users');

    // Eliminar usuarios
    Route::delete('users/{user}', [Usercontroller::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('can:delete users'); // Nota: Asegúrate de añadir 'delete users' a tu Seeder

});
