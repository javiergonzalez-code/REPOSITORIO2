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
        $user = auth()->user();
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        $usuarios = collect();
        $archivos = collect();

        if ($esProveedor) {
            // Proveedor SOLO ve sus propios archivos
            $filtro = 'archivos';
            $archivos = Archivo::onlyTrashed()->with('user')->where('user_id', $user->id)->get();
        } else {
            // Admins ven todo
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
        $mensaje = 'Acción no válida o tipo desconocido.';

        if ($tipo === 'usuario') {
            if ($esProveedor) abort(403, 'No tienes permiso para restaurar usuarios.');
            User::onlyTrashed()->findOrFail($id)->restore();
            $mensaje = 'Usuario restaurado correctamente.';
        } elseif ($tipo === 'archivo') {
            $archivo = Archivo::onlyTrashed()->findOrFail($id);
            if ($esProveedor && $archivo->user_id !== $user->id) {
                abort(403, 'No puedes restaurar archivos ajenos.');
            }
            $archivo->restore();
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