<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ErroresController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\OcController;
use App\Http\Controllers\LogController;



Route::post('/', [AuthController::class, 'index'])->name('login');

// Ruta para recibir los datos del formulario (POST)
Route::get('/login', [AuthController::class, 'index'])->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/', function () {
    return redirect()->route('login');
});
// Ruta para la página interna
Route::get('/home', [AuthController::class, 'home'])->name('home');

// Ruta para deslogearse
Route::post('/logout', [LogoutController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


Route::get('/errores', [ErroresController::class, 'index'])
    ->middleware('auth')
    ->name('errores.index');

Route::get('/inputs', [InputController::class, 'index'])
    ->middleware('auth')
    ->name('input.index');

    Route::post('/inputs/store', [InputController::class, 'store'])->name('input.store');

Route::get('/logs', [LogController::class, 'index'])
    ->middleware('auth')
    ->name('logs.index');

Route::get('/oc', [OcController::class, 'index'])
    ->middleware('auth')
    ->name('oc.index');
