<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Archivo;
use App\Models\Log; // Importante añadir el modelo Log

class ErroresController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Filtrar la tabla de Logs por el módulo "ERRORES"
        $query = Log::where('modulo', 'ERRORES')->latest();

        // 2. Si es proveedor, solo ve sus propios errores
        if ($user->hasRole('proveedor')) {
            $query->where('user_id', $user->id);
        }

        $erroresCarga = $query->paginate(10);

        return view('errores.index', compact('erroresCarga'));
    }

    // AÑADE ESTE NUEVO MÉTODO
    public function show($id)
    {
        // Buscamos el log por su ID
        $error = Log::findOrFail($id);

        // Verificamos permisos (el proveedor solo puede ver los suyos)
        $user = auth()->user();
        if ($user->hasRole('proveedor') && $error->user_id !== $user->id) {
            abort(403, 'No tienes permiso para ver este error.');
        }

        return view('errores.show', compact('error'));
    }
}