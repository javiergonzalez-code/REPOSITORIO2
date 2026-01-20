<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ErroresController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\OcController;
use App\Http\Controllers\LogsController;

// Si alguien entra a la raíz, lo mandamos al login
Route::get('/', function () {
    return redirect()->route('login');
});

// RUTAS DE AUTENTICACIÓN
// GET para mostrar el formulario, POST para procesar
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// RUTAS PROTEGIDAS
Route::middleware(['auth'])->group(function () {
    
    Route::get('/home', [AuthController::class, 'home'])->name('home');
    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');

    Route::get('/errores', [ErroresController::class, 'index'])->name('errores.index');
    Route::get('/inputs', [InputController::class, 'index'])->name('input.index');
    Route::post('/inputs/store', [InputController::class, 'store'])->name('input.store');
    Route::get('/logs', [LogsController::class, 'index'])->name('logs.index');
    Route::get('/oc', [OcController::class, 'index'])->name('oc.index');
});