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

    // ==========================================
    // MÓDULO DE ERRORES
    // ==========================================
    Route::get('/errores', [ErroresController::class, 'index'])->name('errores.index');
    Route::get('/errores/{id}', [ErroresController::class, 'show'])->name('errores.show');
    Route::delete('/errores/{id}', [ErroresController::class, 'destroy'])->name('errores.destroy');

    // ==========================================
    // MÓDULO DE CARGA DE ARCHIVOS (INPUTS)
    // ==========================================
    Route::get('/inputs', [InputController::class, 'index'])->name('input.index');
    Route::post('/inputs/store', [InputController::class, 'store'])->name('input.store');
    Route::get('/archivos/descargar/{id}', [InputController::class, 'download'])->name('archivos.download');

    // ==========================================
    // MÓDULO DE LOGS
    // ==========================================
    Route::get('/logs', [LogsController::class, 'index'])->name('logs.index');
    Route::get('/logs/{id}', [LogsController::class, 'show'])->name('logs.show');
    Route::delete('/logs/{id}', [LogsController::class, 'destroy'])->name('logs.destroy');

    // ==========================================
    // MÓDULO DE ÓRDENES DE COMPRA (OC)
    // ==========================================
    Route::get('/oc', [OcController::class, 'index'])->name('oc.index');
    Route::get('/oc/download/{id}', [OcController::class, 'download'])->name('oc.download');
    Route::get('/oc/preview/{id}', [OcController::class, 'preview'])->name('oc.preview');
    Route::delete('/oc/{id}', [OcController::class, 'destroy'])->name('oc.destroy');

    // ==========================================
    // GESTIÓN DE USUARIOS (PROTEGIDA POR ADMIN)
    // ==========================================
    // El middleware CheckIfAdmin asegura que solo administradores entren a gestionar usuarios.
    Route::middleware([\App\Http\Middleware\CheckIfAdmin::class])->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('users/{id}/show', [UserController::class, 'show'])->name('users.show');
    });

}); 