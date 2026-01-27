<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function index(Request $request)
    {
        // 1. Iniciamos la consulta cargando la relación con el usuario
        $query = Log::with('user');

        // 2. Filtro: Búsqueda General (Acción o Módulo)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('accion', 'like', '%' . $request->search . '%')
                    ->orWhere('modulo', 'like', '%' . $request->search . '%');
            });
        }

        // 3. Filtro: Usuario (desde el datalist/input de texto)
        if ($request->filled('user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%');
            });
        }

        // 4. Filtro: Actividad (Select de acción)
        if ($request->filled('accion')) {
            $query->where('accion', 'like', '%' . $request->accion . '%');
        }

        // 5. Filtro: Fecha
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }
        // 6. Obtenemos resultados ordenados por el más reciente
        $logs = $query->latest()->paginate(20)->withQueryString();

        // 7. Obtenemos los usuarios para que el buscador pueda sugerir nombres
        $usuarios_filtro = User::select('name')->get();

        // 8. Enviamos todo a la vista
        return view('logs.index', compact('logs', 'usuarios_filtro'));
    }
}
