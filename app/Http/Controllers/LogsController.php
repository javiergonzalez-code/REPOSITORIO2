<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Log::with('user');
        
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        // Lógica de aislamiento para proveedores
        if ($esProveedor) {
            $query->where('user_id', $user->id);
            // Solo mandar su propio nombre para las sugerencias de la vista
            $usuarios_filtro = collect([$user]); 
        } else {
            // Administradores obtienen la lista completa
            $usuarios_filtro = User::select('name')->get();
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('accion', 'like', '%' . $request->search . '%')
                    ->orWhere('modulo', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%');
            });
        }

        if ($request->filled('accion')) {
            $query->where('accion', 'like', '%' . $request->accion . '%');
        }

        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }
    
        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        return view('logs.index', compact('logs', 'usuarios_filtro'));
    }
}