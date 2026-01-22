<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Log;
use App\Models\Archivo; // <--- PASO 1: Verifica que este modelo existe
use Illuminate\Support\Facades\Validator;

class InputController extends Controller
{
    public function index() {
        return view('inputs.index');
    }

    public function store(Request $request)
    {
        // PASO 2: Validación flexible (acepta csv, txt y excel)
        $validator = Validator::make($request->all(), [
            'archivo' => 'required|file|max:10240', // Aumentamos a 10MB por si acaso
        ]);

        // Validación extra por extensión manual si el MIME falla
        $extension = $request->file('archivo')?->getClientOriginalExtension();
        $allowedExtensions = ['csv', 'xlsx', 'xls', 'xml'];

        if ($validator->fails() || !in_array(strtolower($extension), $allowedExtensions)) {
            Log::create([
                'user_id' => auth()->id(),
                'accion' => 'Intento fallido: Formato .' . $extension . ' no permitido',
                'modulo' => 'INPUTS',
                'ip' => $request->ip()
            ]);

            return back()->with('error', 'El archivo .' . $extension . ' no es válido. Solo CSV o Excel.');
        }

        try {
            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $originalName = $file->getClientOriginalName();
                $systemName = time() . '_' . $originalName;
                
                // Guardar físico
                $file->storeAs('uploads', $systemName, 'public');

                // Guardar en BD
                Archivo::create([
                    'user_id' => auth()->id(),
                    'nombre_original' => $originalName,
                    'nombre_sistema' => $systemName,
                    'ruta' => 'uploads/' . $systemName,
                ]);

                Log::create([
                    'user_id' => auth()->id(),
                    'accion' => 'Subió con éxito: ' . $originalName,
                    'modulo' => 'INPUTS',
                    'ip' => $request->ip()
                ]);

                return back()->with('success', 'Archivo subido correctamente.');
            }
        } catch (\Exception $e) {
            // Si el error es "Class Archivo not found" o "Table not found", lo veremos aquí
            return back()->with('error', 'Error interno: ' . $e->getMessage());
        }
    }
}