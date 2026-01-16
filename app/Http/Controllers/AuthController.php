<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Este método muestra el formulario
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('logeados');
        }
        return view('login');
    }

    // AQUÍ VA EL CÓDIGO QUE PREGUNTASTE (El que procesa el formulario)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            // Esto guarda la sesión en el navegador
            $request->session()->regenerate();

            // Redirigimos al NOMBRE de la ruta definido en web.php
            return redirect()->route('logeados');
        }

        // Si falla, regresa con error
        return back()->withErrors(['email' => 'Credenciales incorrectas']);
    }

    // Este método muestra la vista de éxito
    public function logeados()
    {
        if (Auth::check()) {
            return view('logeados');
        }

        return redirect("/")->withErrors('No tienes acceso, por favor inicia sesión');
    }
}