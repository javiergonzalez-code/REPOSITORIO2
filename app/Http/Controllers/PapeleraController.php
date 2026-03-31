<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Archivo;

class PapeleraController extends Controller
{
    public function index(Request $request)
    {
        $filtro = $request->get('tipo', 'todos');

        $usuarios = collect();
        $archivos = collect();

        if ($filtro === 'todos' || $filtro === 'usuarios') {
            $usuarios = User::onlyTrashed()->get();
        }

        if ($filtro === 'todos' || $filtro === 'archivos') {
            $archivos = Archivo::onlyTrashed()->with('user')->get();
        }

        return view('papelera.index', compact('usuarios', 'archivos', 'filtro'));
    }

    public function restaurar($tipo, $id)
    {
        $mensaje = 'Acción no válida o tipo desconocido.';

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
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            return back()->with('error', 'No tienes permisos de superusuario para borrar permanentemente.');
        }

        $mensaje = 'Acción no válida.';

        if ($tipo === 'usuario') {
            User::onlyTrashed()->findOrFail($id)->forceDelete();
            $mensaje = 'Usuario eliminado de forma permanente.';
        } elseif ($tipo === 'archivo') {
            $archivo = Archivo::onlyTrashed()->findOrFail($id);
            $path = storage_path('app/' . $archivo->ruta);

            if (file_exists($path)) {
                unlink($path);
            }
            $archivo->forceDelete();
            $mensaje = 'Archivo y registro eliminados de forma permanente.';
        }

        return back()->with('success', $mensaje);
    }
}
