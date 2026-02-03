<?php

namespace App\Http\Controllers;

/**
 * Define el espacio de nombres para organizar el controlador 
 * dentro de la estructura de Laravel.
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
    use App\Models\User;                 // El modelo de Usuario 
    use Illuminate\Support\Facades\Hash; // Para cifrar y comparar contraseñas

    class AuthController extends Controller
    {
        /**
         * Muestra el formulario de login.
         * Si el usuario ya está autenticado, lo envía directamente al home.
         */
        public function index()
        {
            // Auth::check() devuelve true si el usuario ya inició sesión
            if (Auth::check()) {
                // Redirige a la ruta con nombre 'home' para evitar que logueados vuelvan a ver el login
                return redirect()->route('home');
            }

            // Si no está logueado, carga y devuelve el archivo resources/views/login.blade.php
            return view('login');
        }

        /**
         * Procesa el intento de inicio de sesión (Petición POST).
         */
        public function login(Request $request)
        {
            // 1. Validación de datos: asegura que el email sea válido y el password cumpla el mínimo
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]);

            // 2. Intento de autenticación:
            // Auth::attempt busca al usuario por email y compara el password (hasheado) automáticamente
            if (Auth::attempt($credentials)) {
                
                // 3. Seguridad de sesión:
                // Regenera el ID de la sesión para prevenir ataques de "Session Fixation"
                $request->session()->regenerate();

                // 4. Registro de logs:
                // Crea un registro en la tabla 'logs' para dejar constancia de quién entró y cuándo
                \App\Models\Log::create([
                    'user_id' => auth()->id(), // Obtiene el ID del usuario recién autenticado
                    'accion'  => 'Inicio de sesión exitoso',
                    'modulo'  => 'AUTH'
                ]);

                // 5. Éxito: Redirige al panel principal
                return redirect()->route('home');
            }

            // 6. Error: Si las credenciales no coinciden, vuelve atrás con un mensaje de error
            // Esto se mostrará en la vista usando la variable $errors
            return back()->withErrors(['email' => 'Credenciales incorrectas']);
        }

        /**
         * Muestra la página principal tras el éxito del login.
         * Actúa como una capa extra de protección manual.
         */
        public function home()
        {
            // Verifica manualmente si hay una sesión válida antes de mostrar la vista
            if (Auth::check()) {
                return view('home');
            }

            // Si el usuario intenta entrar a /home escribiendo la URL sin estar logueado, 
            // se le redirige a la raíz con un mensaje de advertencia.
            return redirect("/")->withErrors(['access' => 'No tienes acceso, por favor inicia sesión']);
        }
    }