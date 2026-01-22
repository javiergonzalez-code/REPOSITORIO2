<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ErroresController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\OcController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\Usercontroller;
use App\Models\User;


/**
 * REDIRECCIÓN INICIAL
 * Si un usuario entra a la URL base (ej. www.tusistema.com),
 * lo redirigimos automáticamente a la ruta con nombre 'login'.
 */
Route::get('/', function () {
    return redirect()->route('login');
});

/**
 * RUTAS PÚBLICAS (AUTENTICACIÓN)
 * Estas rutas son accesibles sin necesidad de haber iniciado sesión.
 */
// Muestra el formulario de acceso
Route::get('/login', [AuthController::class, 'index'])->name('login');
// Procesa los datos enviados por el formulario de acceso (POST)
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

/**
 * RUTAS PROTEGIDAS (MIDDLEWARE AUTH)
 * Todo lo que esté dentro de este grupo requiere que el usuario esté logueado.
 * Si alguien intenta entrar sin sesión, Laravel lo expulsará al 'login'.
 */
Route::middleware(['auth'])->group(function () {
    
    // Dashboard o página de inicio tras el login
    Route::get('/home', [AuthController::class, 'home'])->name('home');
    
    // Cierre de sesión (se usa POST por seguridad para evitar cierres accidentales)
    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');

    // Gestión de Errores
    Route::get('/errores', [ErroresController::class, 'index'])->name('errores.index');

    // Módulo de Carga de Archivos (Inputs)
    Route::get('/inputs', [InputController::class, 'index'])->name('input.index');
    Route::post('/inputs/store', [InputController::class, 'store'])->name('input.store');

    // Módulo de Auditoría (Logs)
    Route::get('/logs', [LogsController::class, 'index'])->name('logs.index');

    // Módulo de Órdenes de Compra (OC)
    Route::get('/oc', [OcController::class, 'index'])->name('oc.index');
    
    // Descarga de archivos mediante ID
    Route::get('/oc/download/{id}', [OcController::class, 'download'])->name('oc.download');
    
    // Previsualización de archivos (CSV/XML) mediante ID
    Route::get('/oc/preview/{id}', [OcController::class, 'preview'])->name('oc.preview');

    Route::get('/users', [Usercontroller::class, 'index'])->name('users.index');
});