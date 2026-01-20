<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Log;

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
    // 1. Definir las reglas (Solo CSV)
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'archivo' => 'required|file|mimes:csv|max:5120',
    ]);

    // 2. Si la validación falla (ej: subió un .docx)
    if ($validator->fails()) {
        // REGISTRAMOS EL ERROR EN EL LOG
        Log::create([
            'user_id' => auth()->id(),
            'accion' => 'Intento fallido: archivo no permitido o muy pesado',
            'modulo' => 'INPUTS',
            'ip' => $request->ip()
        ]);

        return back()->withErrors($validator)->withInput()->with('error', 'Formato no permitido. Solo se aceptan archivos .CSV');
    }

    // 3. Si pasa la validación, procedemos normal
    if ($request->hasFile('archivo')) {
        $file = $request->file('archivo');
        $nombreArchivo = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('uploads', $nombreArchivo, 'public');

        Log::create([
            'user_id' => auth()->id(),
            'accion' => 'Subió un archivo: ' . $nombreArchivo,
            'modulo' => 'INPUTS',
            'ip' => $request->ip()
        ]);

        return back()->with('success', 'Archivo subido con éxito: ' . $nombreArchivo);
    }
}
}
