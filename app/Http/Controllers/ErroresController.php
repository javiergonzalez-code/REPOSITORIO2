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
        $error = Log::findOrFail($id);

        $user = Auth::user();
        
        if ($user->role === 'proveedor' && $error->user_id !== $user->CardCode) {
            abort(403, 'No tienes permiso para ver este error.');
        }

        return view('errores.show', compact('error'));
    }

    public function destroy($id)
    {
        $error = Log::findOrFail($id);
        
        $user = Auth::user();
        
        // Medida de seguridad: Si es proveedor, validamos que el error sea suyo antes de dejarlo borrar
        if ($user->role === 'proveedor' && $error->user_id !== $user->CardCode) {
            abort(403, 'No tienes permiso para eliminar este registro.');
        }

        // Eliminamos el registro
        $error->delete();


        \RealRashid\SweetAlert\Facades\Alert::success('¡Eliminado!', 'El registro del error ha sido borrado correctamente.');
        
        return back();
    }
}