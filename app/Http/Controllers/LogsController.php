<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    /**
     * Muestra la lista de logs con filtros aplicados.
     */
    public function index(Request $request)
    {
        // Inicia la consulta cargando la relación 'user'.
        $query = Log::with('user');

        // Si el usuario escribió algo en el campo 'search', buscamos coincidencias
        // tanto en la descripción de la acción como en el nombre del módulo.
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('accion', 'like', '%' . $request->search . '%')
                    ->orWhere('modulo', 'like', '%' . $request->search . '%');
            });
        }

        // 'whereHas' permite filtrar los logs basandose en una columna de otra tabla (la de users).
        if ($request->filled('user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%');
            });
        }

        // Para buscar solo "Inicios de sesión" o "Subida de archivos".
        if ($request->filled('accion')) {
            $query->where('accion', 'like', '%' . $request->accion . '%');
        }

        // 'whereDate' extrae solo la parte de la fecha (año-mes-día) ignorando la hora
        // para que la comparación con el input de tipo date sea exacta.
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }
    
        // Filtra los logs para ver solo las acciones de, por ejemplo "Admin"
        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        // - latest(): Ordena por 'created_at' del más nuevo al más viejo.
        // - paginate(20): Muestra solo 20 resultados por página
        // - withQueryString(): . Mantiene los filtros activos al cambiar de página
        //   (evita que al ir a la página 2 se pierda la búsqueda que se hizo)
        $logs = $query->latest()->paginate(20)->withQueryString();

        // Traemos solo los nombres de los usuarios para llenar el buscador o sugerencias en la vista.
        $usuarios_filtro = User::select('name')->get();

        // Envia los datos a la vista usando compact() para pasar las variables.
        return view('logs.index', compact('logs', 'usuarios_filtro'));
    }
}