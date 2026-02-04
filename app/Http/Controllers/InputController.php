<?php

namespace App\Http\Controllers;

// Importación de herramientas de Laravel
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;     // Para interactuar con el disco duro/nube
use App\Models\Log;                         // Para registrar auditoría de lo que pasa
use App\Models\Archivo;                     // El modelo para la tabla donde guardamos los nombres de archivos
use Illuminate\Support\Facades\Validator;   // Para validar que los datos sean correctos

class InputController extends Controller
{
    /**
     * Muestra la vista principal del formulario de subida.
     */
    public function index()
    {
        // Retorna la vista ubicada en resources/views/inputs/index.blade.php
        return view('inputs.index');
    }

    /**
     * Procesa la subida del archivo y su registro.
     */
    public function store(Request $request)
    {
        // 1. VALIDACIÓN INICIAL: 
        // Verifica que el campo 'archivo' esté presente, sea un archivo real y no pese más de 10MB (10240 KB)
        $validator = Validator::make($request->all(), [
            'archivo' => 'required|file|max:5120',
        ]);

        // 2. VALIDACIÓN DE EXTENSIÓN: 
        // ?-> es un operador seguro: si no hay archivo, no intenta sacar la extensión (evita errores)
        $extension = $request->file('archivo')?->getClientOriginalExtension();
        $allowedExtensions = ['csv', 'xlsx', 'xls', 'xml'];

        // Si la validación de Laravel falla O la extensión no está en nuestra lista blanca
        if ($validator->fails() || !in_array(strtolower($extension), $allowedExtensions)) {

            // Auditoría: Registramos que alguien intentó subir algo no permitido
            Log::create([
                'user_id' => auth()->id(), // Quién fue (null si no está logueado)
                'accion' => 'Intento fallido: Formato .' . $extension . ' no permitido',
                'modulo' => 'INPUTS',
                'ip' => $request->ip() // Guardamos su dirección IP por seguridad
            ]);

            // Regresa a la página anterior con un mensaje de error para el usuario
            return back()->with('error', 'El archivo .' . $extension . ' no es válido. Solo CSV o Excel.');
        }

        try {
            // 3. PROCESAMIENTO DEL ARCHIVO:
            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension(); // Extraer extensión

                $systemName = time() . '_' . $originalName;
                $file->storeAs('uploads', $systemName, 'public');

                // REGISTRO EN BASE DE DATOS CORREGIDO
                Archivo::create([
                    'user_id'         => auth()->id(),
                    'nombre_original' => $originalName,
                    'nombre_sistema'  => $systemName,
                    'tipo_archivo'    => $extension, // <--- CAMBIO: Guardar la extensión
                    'ruta'            => 'uploads/' . $systemName,
                    'modulo'          => 'OC',        // <--- CAMBIO: Asignar el módulo
                ]);

                // 7. LOG DE ÉXITO: 
                // Guardamos en el historial que todo salió bien
                Log::create([
                    'user_id' => auth()->id(),
                    'accion' => 'Subió con éxito: ' . $originalName,
                    'modulo' => 'INPUTS',
                    'ip' => $request->ip()
                ]);

                return back()->with('success', 'Archivo subido correctamente.');
            }
        } catch (\Exception $e) {
            // 8. CONTROL DE EMERGENCIAS:
            // Si algo falla (ej: disco lleno, error de BD), atrapamos el error para que la web no "explote"
            return back()->with('error', 'Error interno: ' . $e->getMessage());
        }
    }
}
