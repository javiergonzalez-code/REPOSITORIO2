<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::get('/', [AuthController::class, 'index'])->name('login');

// Ruta para recibir los datos del formulario (POST)
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Ruta para la página interna
Route::get('/logeados', [AuthController::class, 'home'])->name('home');

Route::post('/logout', [Logoutcontroller::class, 'destroy'])->middleware('auth');