<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login.
     * Si el usuario ya está autenticado, lo envía directamente al home.
     */
    public function index()
    {
        // Verifica si el usuario ya tiene una sesión activa
        if (Auth::check()) {
            return redirect()->route('home');
        }

        // Si no está autenticado, muestra la vista 'login.blade.php'
        return view('login');
    }

    /**
     * Procesa el intento de inicio de sesión.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();


            \App\Models\Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Inicio de sesión exitoso',
                'modulo'  => 'AUTH'
            ]);
            // ---------------------------------------------------

            return redirect()->route('home');
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas']);
    }

    /**
     * Muestra la página principal tras el éxito del login.
     * Actúa como una capa extra de protección.
     */
    public function home()
    {
        // Verifica si hay una sesión válida
        if (Auth::check()) {
            return view('home');
        }

        // Si intenta entrar al home sin estar logueado, lo expulsa al login con un mensaje
        return redirect("/")->withErrors(['access' => 'No tienes acceso, por favor inicia sesión']);
    }
}
