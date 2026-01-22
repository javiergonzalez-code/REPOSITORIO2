<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Facade para manejar el sistema de archivos
use App\Models\Log; // Modelo para registrar actividades
use App\Models\Archivo; // Modelo para la tabla de archivos
use Illuminate\Support\Facades\Validator; // Para validaciones personalizadas

class InputController extends Controller
{
    /**
     * Muestra la vista principal del formulario de subida.
     */
    public function index() {
        return view('inputs.index');
    }

    /**
     * Procesa la subida del archivo y su registro.
     */
    public function store(Request $request)
    {
        // 1. VALIDACIÓN INICIAL: Verifica que sea un archivo y no supere los 10MB
        $validator = Validator::make($request->all(), [
            'archivo' => 'required|file|max:10240', 
        ]);

        // 2. VALIDACIÓN DE EXTENSIÓN: Obtenemos la extensión y comparamos con las permitidas
        $extension = $request->file('archivo')?->getClientOriginalExtension();
        $allowedExtensions = ['csv', 'xlsx', 'xls', 'xml'];

        // Si la validación básica falla o la extensión no es permitida
        if ($validator->fails() || !in_array(strtolower($extension), $allowedExtensions)) {
            // Registra el error en la tabla de Logs para auditoría
            Log::create([
                'user_id' => auth()->id(), // ID del usuario logueado
                'accion' => 'Intento fallido: Formato .' . $extension . ' no permitido',
                'modulo' => 'INPUTS',
                'ip' => $request->ip() // Captura la IP de quien intentó subirlo
            ]);

            return back()->with('error', 'El archivo .' . $extension . ' no es válido. Solo CSV o Excel.');
        }

        try {
            // 3. PROCESAMIENTO DEL ARCHIVO: Si el archivo existe y es válido
            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $originalName = $file->getClientOriginalName();
                
                // Genera un nombre único usando el tiempo actual para evitar colisiones
                $systemName = time() . '_' . $originalName;
                
                // 4. ALMACENAMIENTO FÍSICO: Guarda en storage/app/public/uploads
                $file->storeAs('uploads', $systemName, 'public');

                // 5. REGISTRO EN BD: Guarda la información para poder recuperar el archivo después
                Archivo::create([
                    'user_id' => auth()->id(),
                    'nombre_original' => $originalName,
                    'nombre_sistema' => $systemName,
                    'ruta' => 'uploads/' . $systemName,
                ]);

                // 6. LOG DE ÉXITO: Registra la actividad positiva
                Log::create([
                    'user_id' => auth()->id(),
                    'accion' => 'Subió con éxito: ' . $originalName,
                    'modulo' => 'INPUTS',
                    'ip' => $request->ip()
                ]);

                return back()->with('success', 'Archivo subido correctamente.');
            }
        } catch (\Exception $e) {
            // 7. MANEJO DE ERRORES: Captura cualquier fallo de base de datos o permisos
            return back()->with('error', 'Error interno: ' . $e->getMessage());
        }
    }
}