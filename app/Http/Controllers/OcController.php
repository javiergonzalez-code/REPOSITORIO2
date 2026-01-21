<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class OcController extends Controller
{
    public function index()
{
    //Obtenemo todas las OC con la información del usuario (Proveedor)
    // Usamos 'with' para cargar los archivos asociados de una vez
    $ordenes= \App\Models\Archivo::with('user')
        ->select('nombre_original', 'user_id', 'created_at', 'nombre_sistema')
        ->latest()
        ->get();
    return view('oc.index',compact ('ordenes')); 
}
}
