<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin') // Aquí entran TODOS los admins
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { 
    
    // ---------------------------------------------------------
    // RUTAS PARA ADMINISTRADORES NORMALES
    // ---------------------------------------------------------
    Route::crud('archivo', 'ArchivoCrudController');
    
    // ---------------------------------------------------------
    // RUTAS EXCLUSIVAS PARA EL SÚPER USUARIO
    // ---------------------------------------------------------
    // Aplicamos el middleware que acabamos de crear a este subgrupo
    Route::group(['middleware' => [\App\Http\Middleware\CheckIfSuperAdmin::class]], function () {
        Route::crud('user', 'UserCrudController');
        Route::crud('role', 'RoleCrudController');
        Route::crud('permission', 'PermissionCrudController');
        Route::crud('log', 'LogCrudController');
    });
    
});