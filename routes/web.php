<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ErroresController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\OcController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\Usercontroller;
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
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

/**
 * RUTAS PROTEGIDAS (MIDDLEWARE AUTH)
 */
Route::middleware(['auth'])->group(function () {

    // Dashboard o página de inicio (Apunta a HomeController ahora)
    Route::get('/home', [AuthController::class, 'home'])->name('home');

    // Cierre de sesión
    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');

    // Gestión de Errores
    Route::get('/errores', [ErroresController::class, 'index'])->name('errores.index');

    // ==========================================
    // RUTA PARA EL SWITCH DE MANTENIMIENTO (AJAX)
    // ==========================================
    Route::post('/mantenimiento/toggle/{modulo}', function ($modulo) {
        // Validar que solo un administrador pueda hacer esto (Ajusta los roles según tu BD)
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Administrador') && auth()->user()->email !== 'admin@ragon.com') {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        // Buscamos si ya existe el registro en la BD
        $setting = \DB::table('modulo_settings')->where('nombre_modulo', $modulo)->first();

        if ($setting) {
            // Invertimos el valor actual
            \DB::table('modulo_settings')->where('nombre_modulo', $modulo)->update([
                'en_mantenimiento' => !$setting->en_mantenimiento,
                'updated_at' => now()
            ]);
            $nuevoEstado = !$setting->en_mantenimiento;
        } else {
            \DB::table('modulo_settings')->insert([
                'nombre_modulo' => $modulo,
                'en_mantenimiento' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $nuevoEstado = true;
        }

        $mensaje = $nuevoEstado ? 'Módulo en mantenimiento' : 'Módulo abierto al público';

        return response()->json(['success' => true, 'message' => $mensaje]);
    })->name('mantenimiento.toggle');

    // ==========================================
    // MÓDULO DE PAPELERA DE RECICLAJE
    // ==========================================
    Route::middleware(['auth'])->group(function () {
        Route::get('/papelera', [App\Http\Controllers\PapeleraController::class, 'index'])->name('papelera.index');
        Route::post('/papelera/restaurar/{tipo}/{id}', [App\Http\Controllers\PapeleraController::class, 'restaurar'])->name('papelera.restaurar');
        Route::delete('/papelera/eliminar/{tipo}/{id}', [App\Http\Controllers\PapeleraController::class, 'eliminarPermanente'])->name('papelera.eliminar');
    });
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
            ->middleware('can:edit users');

        Route::put('users/{user}', [Usercontroller::class, 'update'])
            ->name('users.update')
            ->middleware('can:edit users');

        // Eliminar usuarios
        Route::delete('users/{user}', [Usercontroller::class, 'destroy'])
            ->name('users.destroy')
            ->middleware('can:delete users');


        // Ver detalles de un usuario
        Route::get('users/{user}/show', [Usercontroller::class, 'show'])
            ->name('users.show')
            ->middleware('can:list users');
    });
});
