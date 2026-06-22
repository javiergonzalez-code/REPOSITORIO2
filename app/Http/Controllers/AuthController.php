<?php

namespace App\Http\Controllers;

// Importación de las dependencias necesarias de Laravel y paquetes de terceros
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use App\Models\Log;

class AuthController extends Controller
{
    /**
     * Muestra la vista de inicio de sesión
     * Si el usuario ya está autenticado, lo redirige al home
     */
    public function index()
    {
        // Verifica si hay una sesión activa
        if (Auth::check()) {
            return redirect()->route('home');
        }
        
        // Si no está autenticado, muestra el formulario de login
        return view('login');
    }

    /**
     * Procesa el intento de inicio de sesión
     */
    public function login(Request $request)
    {
        // 1. Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        // Si la validación falla, se muestra una alerta y se regresa al formulario
        if ($validator->fails()) {
            Alert::error('Datos incompletos', 'Por favor verifica que tu correo y contraseña cumplan con el formato.');
            
            return back()
                ->withErrors($validator) // Envía los errores de validación a la vista
                ->withInput($request->except('password')); // Retorna los inputs, excepto la contraseña
        }

        // Obtiene solo los datos que ya fueron validados
        $validData = $validator->validated();

        // Mapeo de credenciales
        // Laravel Auth busca por defecto la columna 'email', por lo que aquí se cambia explícitamente a 'E_Mail'
        $credentials = [
            'E_Mail'   => $validData['email'],
            'password' => $validData['password'],
        ];

        // Intento de autenticación
        if (Auth::attempt($credentials)) {
            // Si es exitoso, se regenera la sesión para prevenir ataques de fijación de sesión
            $request->session()->regenerate();
            
            // Redirección a la ruta protegida o principal
            return redirect()->route('home');
        }

        // Manejo de error si la autenticación falla (credenciales incorrectas)
        Alert::error('Acceso Denegado', 'Las credenciales proporcionadas son incorrectas.');
        
        return back()->withInput($request->except('password'));
    }
}