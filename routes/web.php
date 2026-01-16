<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// La raíz de tu sitio ahora apunta al método index del controlador
Route::get('/', [AuthController::class, 'index'])->name('login');

// Ruta para recibir los datos del formulario (POST)
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Ruta para la página interna
Route::get('/logeados', [AuthController::class, 'logeados'])->name('logeados');