<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- Importante agregar esta línea

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function home()
    {
        // 1. Consultamos toda la tabla de configuraciones de golpe
        $settings = DB::table('modulo_settings')->pluck('en_mantenimiento', 'nombre_modulo');

        // 2. Obtenemos los estados de cada switch (si no existe, por defecto es false)
        $mantenimientoOC = $settings->get('oc', false);
        $mantenimientoInputs = $settings->get('inputs', false);
        $mantenimientoUsers = $settings->get('users', false);
        $mantenimientoLogs = $settings->get('logs', false);
        $mantenimientoErrores = $settings->get('errores', false);
        $mantenimientoSuperusuario = $settings->get('superuser', false);

        // 3. Retornamos la vista enviando TODAS las variables que necesita el blade
        return view('home', compact(
            'mantenimientoOC',
            'mantenimientoInputs',
            'mantenimientoUsers',
            'mantenimientoLogs',
            'mantenimientoErrores',
            'mantenimientoSuperusuario'
        ));
    }
}