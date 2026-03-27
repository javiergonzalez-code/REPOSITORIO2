<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log; 

class ErroresController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $query = Log::with('user')
            ->where(function ($q) {
                $q->where('accion', 'LIKE', '%Intento fallido%')
                    ->orWhere('accion', 'LIKE', '%Error interno%');
            });

        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        if ($esProveedor) {
            $query->where('user_id', $user->id);
        }

        $errores = $query->latest()->paginate(10);

        return view('errores.index', compact('errores')); 
    }
}