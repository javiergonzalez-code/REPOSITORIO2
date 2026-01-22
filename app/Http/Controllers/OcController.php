<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Archivo;


class OcController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    $ordenes = \App\Models\Archivo::with('user')
        ->when($search, function ($query, $search) {
            return $query->where('nombre_original', 'like', "%{$search}%")
                         ->orWhereHas('user', function ($q) use ($search) {
                             $q->where('name', 'like', "%{$search}%");
                         });
        })
        ->latest()
        ->paginate(10   );

    return view('oc.index', compact('ordenes', 'search'));
}

// En OcController.php
public function download($id) {
    // return "HOLA, el ID es: " . $id; // <-- Descomenta esto para probar
    
    $oc = Archivo::findOrFail($id);
    $path = storage_path('app/public/uploads/' . $oc->nombre_sistema);

    if (!file_exists($path)) {
        return back()->with('error', 'El archivo no existe en la ruta: ' . $path);
    }

    return response()->download($path, $oc->nombre_original);
}
}
