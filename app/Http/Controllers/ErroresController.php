<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class ErroresController extends Controller
{
    public function index()
    {
        // ¡SUPER LIMPIO!
        // Livewire Volt se encarga de extraer los datos y filtrar.
        // Solo retornamos la vista base vacía.
        return view('errores.index');
    }

    public function show($id)
    {
        // Este se queda igual, ya que muestra el detalle en otra pantalla
        $error = Log::whereIn('modulo', ['ERRORES', 'SISTEMA'])->findOrFail($id);
        $user = Auth::user();

        // Seguridad: Si es proveedor, solo ve sus propios errores
        if ($user->role === 'proveedor' && $error->user_id !== $user->CardCode) {
            abort(403, 'No tienes permiso para ver este error.');
        }

        return view('errores.show', compact('error'));
    }

    public function destroy($id)
    {
        // Este se queda igual, por si borras desde la vista de "show" (Detalle)
        $error = Log::whereIn('modulo', ['ERRORES', 'SISTEMA'])->findOrFail($id);
        $user = Auth::user();

        // Seguridad: Si es proveedor, no puede borrar
        if ($user->role === 'proveedor') {
            abort(403, 'No tienes permiso para eliminar este registro.');
        }

        $error->delete();

        \RealRashid\SweetAlert\Facades\Alert::success('¡Eliminado!', 'El registro del error ha sido borrado correctamente.');

        return redirect()->route('errores.index');
    }
}