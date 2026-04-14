<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator; // <-- Esta es la línea que faltaba

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('login');
    }

public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            Alert::error('Datos incompletos', 'Por favor verifica que tu correo y contraseña cumplan con el formato.');
            return back()->withErrors($validator)->withInput($request->except('password'));
        }

        $validData = $validator->validated();

        // AQUÍ ESTÁ LA MAGIA: Mapeamos el input 'email' del formulario a la columna 'E_Mail' de tu BD
        $credentials = [
            'E_Mail'   => $validData['email'],
            'password' => $validData['password'],
        ];

        // Intentamos el login con las credenciales mapeadas
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            \App\Models\Log::create([
                'user_id' => auth()->id(), // Ya te devolverá el CardCode
                'accion'  => 'Inicio de sesión exitoso',
                'modulo'  => 'AUTH'
            ]);

            return redirect()->route('home');
        }

        // Si las credenciales no coinciden
        Alert::error('Acceso Denegado', 'Las credenciales proporcionadas son incorrectas.');
        return back()->withInput($request->except('password'));
    }

    public function home()
    {
        // 1. Consultamos la tabla de configuraciones
        $settings = \Illuminate\Support\Facades\DB::table('modulo_settings')->pluck('en_mantenimiento', 'nombre_modulo');

        // 2. Obtenemos los estados (si el módulo no existe, por defecto es false)
        $mantenimientoOC = $settings->get('oc', false);
        $mantenimientoInputs = $settings->get('inputs', false);
        $mantenimientoUsers = $settings->get('users', false);
        $mantenimientoLogs = $settings->get('logs', false);

        // NUEVOS:
        $mantenimientoErrores = $settings->get('errores', false);
        $mantenimientoSuperusuario = $settings->get('superuser', false);

        // 3. Retornamos la vista enviando TODAS las variables
        return view('home', compact(
            'mantenimientoOC',
            'mantenimientoInputs',
            'mantenimientoUsers',
            'mantenimientoLogs',
            'mantenimientoErrores',
            'mantenimientoSuperusuario'
        ));
    }
}
