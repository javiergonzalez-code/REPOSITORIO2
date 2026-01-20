<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InputController extends Controller
{
    /**
     * Muestra la vista del formulario de subida.
     */
    public function index()
    {
        return view('inputs.index');
    }

    /**
     * Procesa la subida del archivo.
     */
    public function store(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|max:5120', // Máximo 5MB
        ]);

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $nombreArchivo = time() . '_' . $file->getClientOriginalName();
            
            // Guardar en storage/app/public/uploads
            $file->storeAs('uploads', $nombreArchivo, 'public');

            return back()->with('success', 'Archivo subido con éxito: ' . $nombreArchivo);
        }

        return back()->with('error', 'No se pudo subir el archivo.');
    }
}