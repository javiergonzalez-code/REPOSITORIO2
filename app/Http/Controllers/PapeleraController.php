<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Archivo;

class PapeleraController extends Controller
{
    public function index(Request $request)
    {
        // Filtro: 'todos', 'usuarios', 'archivos'
        $filtro = $request->get('tipo', 'todos');

        $usuarios = collect();
        $archivos = collect();

        // Solo consultamos si el filtro lo requiere
        if ($filtro === 'todos' || $filtro === 'usuarios') {
            $usuarios = User::onlyTrashed()->get();
        }

        if ($filtro === 'todos' || $filtro === 'archivos') {
            // Traemos también el usuario que lo subió
            $archivos = Archivo::onlyTrashed()->with('user')->get();
        }

        return view('papelera.index', compact('usuarios', 'archivos', 'filtro'));
    }

    public function restaurar($tipo, $id)
    {
        if ($tipo === 'usuario') {
            User::onlyTrashed()->findOrFail($id)->restore();
            $mensaje = 'Usuario restaurado correctamente.';
        } elseif ($tipo === 'archivo') {
            Archivo::onlyTrashed()->findOrFail($id)->restore();
            $mensaje = 'Archivo restaurado correctamente.';
        }

        return back()->with('success', $mensaje);
    }

public function eliminarPermanente($tipo, $id)
{
    $mensaje = 'No tienes permisos para realizar esta acción o la acción no es válida.';

    if ($tipo === 'usuario') {
        User::onlyTrashed()->findOrFail($id)->forceDelete();
        $mensaje = 'Usuario eliminado de forma permanente.';
    } elseif ($tipo === 'archivo') {
        $archivo = Archivo::onlyTrashed()->findOrFail($id);

        // Validamos por rol usando Spatie en lugar de quemar el correo
        if (auth()->user() && auth()->user()->hasRole('superadmin')) { 
            $path = storage_path('app/private/uploads/' . $archivo->nombre_sistema);
            if (file_exists($path)) {
                unlink($path);
            }
            $archivo->forceDelete();
            $mensaje = 'Archivo y registro eliminados de forma permanente.';
        } else {
            return back()->with('error', 'No tienes permisos para borrar archivos del servidor.');
        }
    }

    return back()->with('success', $mensaje);
}
}
