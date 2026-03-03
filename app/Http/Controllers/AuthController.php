<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;                 
use Illuminate\Support\Facades\Hash; 
use RealRashid\SweetAlert\Facades\Alert;

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

            // (Opcional) Alerta de éxito al entrar
            Alert::success('¡Bienvenido!', 'Has iniciado sesión correctamente.');
            return redirect()->route('home');
        }

        // <-- REEMPLAZO: Alerta de credenciales inválidas en lugar de withErrors
        Alert::error('Error de acceso', 'Las credenciales ingresadas son incorrectas.');
        return back();
    }

    public function home()
    {
        if (Auth::check()) {
            return view('home');
        }

        // <-- REEMPLAZO: Alerta de acceso denegado en lugar de withErrors
        Alert::warning('Acceso Restringido', 'Por favor inicia sesión para continuar.');
        return redirect("/");
    }
}