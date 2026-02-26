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
        // 1. VALIDACIÓN ESTRICTA (QA y Seguridad)
        $validator = Validator::make($request->all(), [
            'archivo' => [
                'required',
                'file',
                'mimes:csv,xlsx,xls,xml,txt',      // Valida la firma interna (MIME type real del archivo)
                'extensions:csv,xlsx,xls,xml,txt', // Valida ESTRICTAMENTE la extensión (Laravel 11+)
                'max:5120',                        // Límite máximo de 5MB
            ]
        ], [
            // Mensajes personalizados para ser más claros en el Log y en la vista
            'archivo.mimes' => 'El contenido del archivo no coincide con su extensión o tiene formato malicioso.',
            'archivo.extensions' => 'La extensión del archivo no está permitida por políticas de seguridad.',
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
            
            // Doble validación manual de extensión (Capa de defensa en profundidad)
            $allowedExtensions = ['csv', 'xlsx', 'xls', 'xml'];
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                throw new \Exception("Extensión de archivo maliciosa detectada.");
            }

            $systemName = time() . '_' . uniqid() . '_' . $originalName; // uniqid evita sobrescritura

            // Guardado en disco PRIVADO ('local' en lugar de 'public')
            // Esto evita que un atacante ejecute el archivo navegando a misitio.com/uploads/archivo.php
            $path = $file->storeAs('uploads', $systemName, 'local');
            
            if (!$path) {
                throw new \Exception("El servidor denegó el permiso de escritura en el disco duro.");
            }

            // Registro en Base de datos
            Archivo::create([
                'user_id'         => auth()->id(),
                'nombre_original' => $originalName,
                'nombre_sistema'  => $systemName,
                'tipo_archivo'    => strtolower($extension), 
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
            // Error de servidor (disco lleno, permisos, timeout) o validación manual
            Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Error interno (Seguridad/Servidor): ' . $e->getMessage(),
                'modulo'  => 'INPUTS',
            ]);
            return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Descarga segura de archivos desde el almacenamiento privado
     */
    public function download($id)
    {
        // 1. Buscar el registro del archivo en la base de datos
        $archivo = Archivo::findOrFail($id);

        // (OPCIONAL - RECOMENDADO) Validar permisos: 
        // Verificar que el usuario actual sea el dueño del archivo o un administrador
        /*
        if ($archivo->user_id !== auth()->id()) {
            abort(403, 'Acceso denegado. No tienes permiso para descargar este archivo.');
        }
        */

        // 2. Verificar que el archivo físico realmente exista en el disco 'local'
        if (!Storage::disk('local')->exists($archivo->ruta)) {
            abort(404, 'El archivo físico no se encuentra en el servidor.');
        }

        // 3. Retornar el archivo para su descarga
        // Esto fuerza la descarga en el navegador con el nombre original seguro
        return Storage::disk('local')->download($archivo->ruta, $archivo->nombre_original);

        // ALTERNATIVA: Si quisieras que el archivo (como un TXT o PDF) se abra en el navegador 
        // en lugar de forzar la descarga, usarías esto en vez del return anterior:
        // return Storage::disk('local')->response($archivo->ruta);
    }
}