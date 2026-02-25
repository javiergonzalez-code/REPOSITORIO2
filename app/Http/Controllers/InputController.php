<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Log;
use App\Models\Archivo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException; // Para atrapar caídas de base de datos

class InputController extends Controller
{
    public function index()
    {
        return view('inputs.index');
    }

    public function store(Request $request)
    {
        // 1. VALIDACIÓN ESTRICTA (QA)
        $validator = Validator::make($request->all(), [
            'archivo' => [
                'required',
                'file',
                'mimes:csv,xlsx,xls,xml,txt', // Valida la firma interna (evita que un .exe se renombre a .csv)
                //'min:1',                      // Evita archivos vacíos (0 bytes)
                'max:5120',                   // Límite máximo de 5MB
            ]
        ], [
            // Mensajes personalizados para ser más claros en el Log y en la vista
            'archivo.mimes' => 'El contenido del archivo no coincide con su extensión o tiene formato malicioso.',
            'archivo.min' => 'El archivo está dañado o vacío (0 bytes).',
            'archivo.max' => 'El archivo supera el límite máximo de 5MB.'
        ]);

        // Si falla la validación
        if ($validator->fails()) {
            $errores = implode(' | ', $validator->errors()->all());
            
            // Auditoría: Registramos el error de validación o intento de vulneración
            Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Intento fallido (Validación/Seguridad): ' . $errores,
                'modulo'  => 'INPUTS',
            ]);

            return back()->with('error', 'Error en el archivo: ' . $errores);
        }

        try {
            // 2. PROCESAMIENTO SEGURO DEL ARCHIVO
            $file = $request->file('archivo');
            
            // Sanitización del nombre: Quitar caracteres raros y limitar longitud
            $originalName = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $file->getClientOriginalName());
            if (strlen($originalName) > 200) {
                $originalName = substr($originalName, -200); // Evitar colapso en la base de datos por nombre muy largo
            }
            
            $extension = $file->getClientOriginalExtension(); 
            $systemName = time() . '_' . uniqid() . '_' . $originalName; // uniqid evita sobrescritura si suben 2 al mismo milisegundo

            // Guardado en disco
            $path = $file->storeAs('uploads', $systemName, 'public');
            
            if (!$path) {
                throw new \Exception("El servidor denegó el permiso de escritura en el disco duro.");
            }

            // Registro en Base de datos
            Archivo::create([
                'user_id'         => auth()->id(),
                'nombre_original' => $originalName,
                'nombre_sistema'  => $systemName,
                'tipo_archivo'    => $extension, 
                'ruta'            => 'uploads/' . $systemName,
                'modulo'          => 'OC',        
            ]);

            // LOG DE ÉXITO
            Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Subió con éxito: ' . $originalName,
                'modulo'  => 'INPUTS',
            ]);

            return back()->with('success', 'Archivo subido y verificado correctamente.');

        } catch (QueryException $e) {
            // Error si se pierde la conexión a la base de datos en el último segundo
            Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Error interno (Base de Datos): ' . $e->getMessage(),
                'modulo'  => 'INPUTS'
                ]);
            return back()->with('error', 'Error crítico: No se pudo registrar en la base de datos.');

        } catch (\Exception $e) {
            // Error de servidor (disco lleno, permisos, timeout)
            Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Error interno (Servidor): ' . $e->getMessage(),
                'modulo'  => 'INPUTS',
            ]);
            return back()->with('error', 'Error de infraestructura al procesar el archivo.');
        }
    }
}