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
        // 1. Validación: Verifica que el email sea válido y la contraseña tenga al menos 8 caracteres
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        // 2. Intento de autenticación: Auth::attempt busca al usuario por email 
        // y compara la contraseña (hashing automático)
        if (Auth::attempt($credentials)) {

            // 3. Seguridad: Si el login es correcto, regenera el ID de la sesión
            // para evitar ataques de "Session Fixation".
            $request->session()->regenerate();

            // 4. Redirección: Envía al usuario a la ruta protegida 'home'
            return redirect()->route('home');
            Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Inicio de sesión exitoso',
                'modulo'  => 'AUTH'
            ]);
        }

        // 5. Error: Si las credenciales no coinciden, regresa a la página anterior
        // con un mensaje de error para el campo email.
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
