<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Archivo;
use Illuminate\Support\Facades\Storage; // <-- CORRECCIÓN: Importamos Storage

class PapeleraController extends Controller
{
    public function index(Request $request)
    {
        $filtro = $request->get('tipo', 'todos');
        $user = auth()->user();
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        $usuarios = collect();
        $archivos = collect();

        if ($esProveedor) {
            $filtro = 'archivos';
            $archivos = Archivo::onlyTrashed()->with('user')->where('user_id', $user->id)->get();
        } else {
            if ($filtro === 'todos' || $filtro === 'usuarios') {
                $usuarios = User::onlyTrashed()->get();
            }
            if ($filtro === 'todos' || $filtro === 'archivos') {
                $archivos = Archivo::onlyTrashed()->with('user')->get();
            }
        }

        return view('papelera.index', compact('usuarios', 'archivos', 'filtro'));
    }

    public function restaurar($tipo, $id)
    {
        $user = auth()->user();
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        if ($tipo === 'usuario') {
            if ($esProveedor) abort(403, 'No tienes permiso para restaurar usuarios.');
            User::onlyTrashed()->findOrFail($id)->restore();
            return back()->with('success', 'Usuario restaurado correctamente.');
        } elseif ($tipo === 'archivo') {
            $archivo = Archivo::onlyTrashed()->findOrFail($id);
            if ($esProveedor && $archivo->user_id !== $user->id) {
                abort(403, 'No puedes restaurar archivos ajenos.');
            }
            $archivo->restore();
            return back()->with('success', 'Archivo restaurado correctamente.');
        }

        // Si llega aquí, es porque alteraron la URL
        return back()->with('error', 'Acción no válida o tipo desconocido.');
    }

    public function eliminarPermanente($tipo, $id)
    {
        $user = auth()->user();
        if (!$user || (!$user->hasRole('superadmin') && $user->role !== 'superadmin')) {
            return back()->with('error', 'No tienes permisos para borrar permanentemente.');
        }

        if ($tipo === 'usuario') {
            User::onlyTrashed()->findOrFail($id)->forceDelete();
            return back()->with('success', 'Usuario eliminado de forma permanente.');
        } elseif ($tipo === 'archivo') {
            $archivo = Archivo::onlyTrashed()->findOrFail($id);

            if (Storage::disk('local')->exists($archivo->ruta)) {
                Storage::disk('local')->delete($archivo->ruta);
            }

            $archivo->forceDelete();
            return back()->with('success', 'Archivo y registro eliminados de forma permanente.');
        }

        return back()->with('error', 'Acción no válida.');
    }
}
