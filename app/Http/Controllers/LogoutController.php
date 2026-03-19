<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert; 
use App\Models\Log; // <-- IMPORTANTE: Agregar el modelo Log

class LogoutController extends Controller
{
    public function destroy(Request $request) 
    {
        // 1. Guardamos el ID del usuario ANTES de que se destruya la sesión
        $userId = Auth::id();

        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 2. Registramos la acción en la base de datos
        if ($userId) {
            Log::create([
                'user_id' => $userId,
                'accion'  => 'Cierre de sesión exitoso',
                'modulo'  => 'AUTH'
            ]);
        }

        return redirect('/login'); 
    }
}