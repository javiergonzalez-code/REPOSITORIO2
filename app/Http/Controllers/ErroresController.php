<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log; // Importamos el modelo Log

class ErroresController extends Controller
{
    public function index()
    {
        // Traemos los logs que sean errores (Validaciones fallidas o errores internos)
        $errores = Log::with('user')
            ->where('accion', 'LIKE', '%Intento fallido%')
            ->orWhere('accion', 'LIKE', '%Error interno%')
            ->latest() // Del más reciente al más antiguo
            ->paginate(20);

        return view('errores.index', compact('errores')); 
    }
}