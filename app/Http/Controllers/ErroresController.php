<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log; 

class ErroresController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Iniciamos la consulta agrupando las condiciones OR
        $query = Log::with('user')
            ->where(function ($q) {
                $q->where('accion', 'LIKE', '%Intento fallido%')
                    ->orWhere('accion', 'LIKE', '%Error interno%');
            });

        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        if ($esProveedor) {
            // El proveedor solo ve sus propios errores
            $query->where('user_id', $user->id);
        }

        $errores = $query->latest()->paginate(20);

        return view('errores.index', compact('errores')); 
    }
}