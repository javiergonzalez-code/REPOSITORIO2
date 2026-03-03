<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert; // <-- 1. Importar la fachada

class LogoutController extends Controller
{
    public function logout(Request $request) // o el nombre del método que tengas
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // <-- 2. Añadir la alerta antes de redirigir
        Alert::success('Sesión cerrada con éxito');

        return redirect('/login'); // o la ruta a la que redirijas normalmente
    }
}