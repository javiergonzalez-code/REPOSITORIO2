<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use Illuminate\Support\Facades\Auth; // <-- Importamos la fachada Auth para mayor seguridad

class ErroresController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Filtrar la tabla de Logs por el módulo "ERRORES"
        $query = Log::where('modulo', 'ERRORES')->latest();

        // 🚨 2. Si es proveedor, solo ve sus propios errores (Validación nativa y búsqueda por CardCode)
        if ($user->role === 'proveedor') {
            $query->where('user_id', $user->CardCode);
        }

        $erroresCarga = $query->paginate(10);

        return view('errores.index', compact('erroresCarga'));
    }

    public function show($id)
    {
        // Buscamos el log por su ID (El ID de la tabla logs sigue siendo autoincrementable numérico)
        $error = Log::findOrFail($id);

        // 🚨 Verificamos permisos usando la columna nativa 'role' y comparando contra 'CardCode'
        $user = Auth::user();
        
        if ($user->role === 'proveedor' && $error->user_id !== $user->CardCode) {
            abort(403, 'No tienes permiso para ver este error.');
        }

        return view('errores.show', compact('error'));
    }
}