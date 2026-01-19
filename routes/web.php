<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ErroresController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\OcController;




Route::get('/', [AuthController::class, 'index'])->name('login');

// Ruta para recibir los datos del formulario (POST)
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Ruta para la página interna
Route::get('/home', [AuthController::class, 'home'])->name('home');

// Ruta para deslogearse
Route::post('/logout', [LogoutController::class, 'destroy'])->middleware('auth');


Route::get('/errores', [ErroresController::class, 'index'])
    ->middleware('auth')
    ->name('errores.index');

Route::get('/inputs', [InputController::class, 'index'])
    ->middleware('auth') 
    ->name('input.index');

Route::get('/logs', [LogController::class, 'index'])
    ->middleware('auth') 
    ->name('logs.index');

Route::get('/oc', [OcController::class, 'index'])
    ->middleware('auth') 
    ->name('oc.index');