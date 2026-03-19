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
        if ($tipo === 'usuario') {
            User::onlyTrashed()->findOrFail($id)->forceDelete();
            $mensaje = 'Usuario eliminado de forma permanente.';
        } elseif ($tipo === 'archivo') {
            Archivo::onlyTrashed()->findOrFail($id)->forceDelete();
            $mensaje = 'Archivo eliminado de forma permanente.';
        }

        return back()->with('success', $mensaje);
    }
}