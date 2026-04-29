<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Log; 

class LogoutController extends Controller
{
    public function destroy(Request $request) 
    {
        // 🚨 1. Extraemos explícitamente el CardCode (String) ANTES de cerrar sesión
        $userCode = Auth::check() ? Auth::user()->CardCode : null;

        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();



        return redirect('/login'); 
    }
}