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
        // Cambiamos validate() por Validator::make()
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            Alert::error('Datos incompletos', 'Por favor verifica que tu correo y contraseña cumplan con el formato.');
            return back()->withErrors($validator)->withInput($request->except('password'));
        }

        $credentials = $validator->validated();

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            \App\Models\Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Inicio de sesión exitoso',
                'modulo'  => 'AUTH'
            ]);

            return redirect()->route('home');
        }

        Alert::error('Error de acceso', 'Las credenciales ingresadas son incorrectas.');
        return back();
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
