<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogsController extends Controller
{
    /**
     * Muestra la lista de actividades registradas en el sistema.
     */
    public function index() {
        // 1. Consulta a la base de datos usando el modelo Log.
        // - with('user'): Carga la relación 'user' (Eager Loading) para evitar hacer 
        //   una consulta extra por cada log al mostrar el nombre del usuario.
        // - latest(): Ordena los registros del más reciente al más antiguo (descendente por fecha).
        // - paginate(15): Divide los resultados en páginas de 15 registros cada una.
        $logs = \App\Models\Log::with('user')->latest()->paginate(15);

        // 2. Retorna la vista 'logs.index' pasando la variable $logs mediante compact().
        return view('logs.index', compact('logs'));
    }
}