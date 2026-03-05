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
            return back()->withInput();
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
        if (Auth::check()) {
            return view('home');
        }

        Alert::warning('Acceso Restringido', 'Por favor inicia sesión para continuar.');
        return redirect("/");
    }
}