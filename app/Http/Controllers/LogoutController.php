<?php

// Define el espacio de nombres para organizar el controlador
namespace App\Http\Controllers;

// Importa la clase Request para manipular la sesión
use Illuminate\Http\Request;
// Importa la Facade Auth para gestionar la autenticación
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Cierra la sesión del usuario.
     */
    public function destroy(Request $request)
    {
        // 1. Cierra el acceso del usuario a través del sistema de autenticación de Laravel
        Auth::logout();

        // 2. Invalida la sesión actual en el servidor. 
        // Esto borra todos los datos guardados en la sesión y genera un nuevo ID de sesión vacío.
        $request->session()->invalidate();

        // 3. Regenera el token CSRF (Cross-Site Request Forgery).
        // Es una medida de seguridad vital para que los tokens antiguos no puedan ser usados
        // en un ataque después de que el usuario haya salido.
        $request->session()->regenerateToken();

        // 4. Redirige al usuario a la página de login (o la que tú definas)
        return redirect('/login');
    }
}