<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class ErroresController extends Controller
{
    public function index()
    {
        // El listado, filtros y paginación se hacen en la vista con Livewire Volt
        return view('errores.index');
    }

    public function show($id)
    {
        // Busca el log sólo en los módulos permitidos
        $error = Log::whereIn('modulo', ['ERRORES', 'SISTEMA'])->findOrFail($id);
        $user = Auth::user();

        // Bloqueo: Los proveedores sólo pueden ver sus propios errores
        if ($user->role === 'proveedor' && $error->user_id !== $user->CardCode) {
            abort(403, 'No tienes permiso para ver este error.');
        }

        return view('errores.show', compact('error'));
    }

    public function destroy($id)
    {
        // Se queda aquí por si borran desde la vista de detalle (show)
        $error = Log::whereIn('modulo', ['ERRORES', 'SISTEMA'])->findOrFail($id);
        $user = Auth::user();

        // Bloqueo: Un proveedor jamás puede borrar logs
        if ($user->role === 'proveedor') {
            abort(403, 'No tienes permiso para eliminar este registro.');
        }

        $error->delete();

        \RealRashid\SweetAlert\Facades\Alert::success('¡Eliminado!', 'El registro del error ha sido borrado correctamente.');

        return redirect()->route('errores.index');
    }
}