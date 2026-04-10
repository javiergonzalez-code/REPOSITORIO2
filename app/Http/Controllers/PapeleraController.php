<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Archivo;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert; // <-- PARA TUS ALERTAS
use Illuminate\Support\Facades\Log;

class PapeleraController extends Controller
{
    public function index(Request $request)
    {
        $filtro = $request->get('tipo', 'todos');
        $user = auth()->user();
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        $usuarios = collect();
        $archivos = collect();

        // SEGURIDAD DE MEMORIA: Se cambió get() por paginate()
        if ($esProveedor) {
            $filtro = 'archivos';
            $archivos = Archivo::onlyTrashed()->with('user')->where('user_id', $user->id)->paginate(50);
        } else {
            if ($filtro === 'todos' || $filtro === 'usuarios') {
                $usuarios = User::onlyTrashed()->paginate(50);
            }
            if ($filtro === 'todos' || $filtro === 'archivos') {
                $archivos = Archivo::onlyTrashed()->with('user')->paginate(50);
            }
        }

        return view('papelera.index', compact('usuarios', 'archivos', 'filtro'));
    }

    public function restaurar($tipo, $id)
    {
        try {
            $user = auth()->user();
            $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

            if ($tipo === 'usuario') {
                if ($esProveedor) abort(403, 'No tienes permiso para restaurar usuarios.');
                User::onlyTrashed()->findOrFail($id)->restore();
                Alert::success('¡Éxito!', 'Usuario restaurado correctamente.');
                return back();
            } elseif ($tipo === 'archivo') {
                $archivo = Archivo::onlyTrashed()->findOrFail($id);
                if ($esProveedor && $archivo->user_id !== $user->id) {
                    abort(403, 'No puedes restaurar archivos ajenos.');
                }
                $archivo->restore();
                Alert::success('¡Éxito!', 'Archivo restaurado correctamente.');
                return back();
            }

            Alert::warning('Atención', 'Acción no válida o tipo desconocido.');
            return back();
        } catch (\Exception $e) {
            Log::error("Error restaurando en papelera: " . $e->getMessage());
            Alert::error('Error Crítico', 'Hubo un fallo al restaurar el registro.');
            return back();
        }
    }

    public function eliminarPermanente($tipo, $id)
    {
        try {
            $user = auth()->user();
            if (!$user || (!$user->hasRole('superadmin') && $user->role !== 'superadmin')) {
                Alert::error('Acceso Denegado', 'No tienes permisos para borrar permanentemente.');
                return back();
            }

            if ($tipo === 'usuario') {
                User::onlyTrashed()->findOrFail($id)->forceDelete();
                Alert::success('¡Eliminado!', 'Usuario eliminado de forma permanente.');
                return back();
            } elseif ($tipo === 'archivo') {
                $archivo = Archivo::onlyTrashed()->findOrFail($id);

                if (Storage::disk('local')->exists($archivo->ruta)) {
                    Storage::disk('local')->delete($archivo->ruta);
                }

                $archivo->forceDelete();
                Alert::success('¡Eliminado!', 'Archivo y registro eliminados permanentemente.');
                return back();
            }

            Alert::warning('Atención', 'Acción no válida.');
            return back();
        } catch (\Exception $e) {
            Log::error("Error eliminando en papelera: " . $e->getMessage());
            Alert::error('Error Crítico', 'Hubo un fallo al borrar permanentemente.');
            return back();
        }
    }
}