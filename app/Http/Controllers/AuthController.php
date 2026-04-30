<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use App\Models\Log; // 🚨 Importamos el modelo Log

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
            return redirect()->route('home');
        }

        // Si las credenciales no coinciden
        Alert::error('Acceso Denegado', 'Las credenciales proporcionadas son incorrectas.');
        return back()->withInput($request->except('password'));
    }
}