<?php

// Define el espacio de nombres para organizar este controlador dentro de la estructura de Laravel
namespace App\Http\Controllers;

// Importa la clase Request para manejar los datos de la sesión y la petición actual
use Illuminate\Http\Request;
// Importa la Facade Auth para gestionar la autenticación (login/logout)
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Cierra la sesión del usuario de forma segura.
     * * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // 1. Cierra la sesión del usuario en el 'guard' que esté usando (normalmente web)
        Auth::logout();

        // 2. Invalida la sesión actual del usuario en el servidor, 
        // borrando todos los datos guardados en ella para que no pueda ser reutilizada.
        $request->session()->invalidate();

        // 3. Regenera el token CSRF (Cross-Site Request Forgery).
        // Esto es una medida de seguridad vital para prevenir ataques de secuestro de sesión.
        $request->session()->regenerateToken();

        // 4. Redirige al usuario a la página de inicio de sesión o a la ruta deseada
        return redirect('/login');
    }
}