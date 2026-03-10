<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {

        return view('home');
    }

    public function home()
    {
        $setting = \DB::table('modulo_settings')->where('nombre_modulo', 'oc')->first();
        $mantenimientoOC = $setting ? $setting->en_mantenimiento : false;

        return view('home', compact('mantenimientoOC'));
    }
}
